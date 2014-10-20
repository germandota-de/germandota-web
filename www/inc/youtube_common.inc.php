<?

/* germandota.de - Sources of the website
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

include_once dirname(__FILE__). '/common.inc.php';
include_once dirname(__FILE__). '/youtube_constants.inc.php';

/* ***************************************************************  */

define('_YT_DAY_IN_SECS',               24*3600);

/* Without the weekday that is same as today  */
define('_YT_WEEK_IN_SECS',              6*_YT_DAY_IN_SECS);

function yt_str2date_html($yt_time_str)
{
  $stamp = strtotime($yt_time_str, 0);
  $cur_time = time();

  $title = '-no date-'; $text = $title;
  if ($stamp) {
    $date_str = date(CONFIG_DATE_FORMAT, $stamp);
    $title = date(CONFIG_DATE_LONG_FORMAT, $stamp);

    if ($date_str == date(CONFIG_DATE_FORMAT))
      $text = 'today';
    else if ($date_str == date(CONFIG_DATE_FORMAT, $cur_time-_YT_DAY_IN_SECS))
      $text = 'yesterday';
    else if ($stamp + _YT_WEEK_IN_SECS > $cur_time)
      $text = date('l', $stamp);
    else
      $text = $date_str;
  }

  return '<span title="' .$title. '">' .$text. '</span>';
}
function yt_str2time_html($yt_time_str)
{
  $stamp = strtotime($yt_time_str, 0);

  $title =  '-timeless-'; $text = $title;
  if ($stamp) {
    $time_str = date(CONFIG_TIME_FORMAT, $stamp);
    $title = date(CONFIG_TIME_LONG_FORMAT, $stamp);

    $diff = time() - $stamp;
    if ($diff < 60) $text = floor($diff) .' seconds ago';
    else if ($diff < 3600) $text = floor($diff/60) .' minutes ago';
    else if ($diff < 24*3600) $text = floor($diff/3600) .' hours ago';
    else $text = $time_str;
  }

  return '<span title="' .$title. '">' .$text. '</span>';
}

function yt_get_likedlist_plid()
{
  return preg_replace('/^..(.*)$/', 'LL\1', CONFIG_YT_CHANNELID);
}

/* ***************************************************************  */

/* Format: PThhHmmMssS (example: PT25M2S)  */
function yt_time2timeat($s=0, $min=0, $h=0)
{
  return common_time2url($s, $min, $h);
}

function _yt_timeat2($str)
{
  return array(
    'h'   => round(preg_replace('/^PT([0-9.]*)H.*$/', '\1', $str)),
    'min' => round(preg_replace('/^P.*[TH]([0-9.]*)M.*$/', '\1', $str)),
    'sec' => round(preg_replace('/^P.*[THM]([0-9.]*)S$/', '\1', $str))
  );
}
function yt_timeat2sec($str)
{
  $x = _yt_timeat2($str);

  return $x['h']*60*60 + $x['min']*60 + $x['sec'];
}
function yt_timeat2readable($str)
{
  $x = _yt_timeat2($str);

  if ($x['h'] > 0)
    return $x['h'] .':'. sprintf('%02u', $x['min']) .':'
      .sprintf('%02u', $x['sec']) .'h';

  return $x['min'] .':'. sprintf('%02u', $x['sec']) .' min';
}

/* ***************************************************************  */

function yt_print_chanlink($chan_name, $chan_id)
{
  ?><a class="yt_channellink" target="_blank"<?
  ?> href="https://www.youtube.com/channel/<?
    echo $chan_id;
  ?>" title="View this channel at Youtube"><?
    _o($chan_name);
  ?></a><?
}

/* Requires to include
 *
 * <script src="https://apis.google.com/js/platform.js"></script>
 *
 *   * data-layout="{default,full}"
 *
 *   * data-theme="{default,dark}"
 *
 *   * data-count="{default,hidden}"
 */
function yt_print_subscribe($chan_id)
{
  ?><div class="g-ytsubscribe" data-layout="default" data-count="default"<?
  ?> data-channelid="<? echo $chan_id; ?>"></div><?
}

/* ***************************************************************  */

function yt_print_pageinfo($yt_response, $items_str, $url_pre,
                           $url_post='')
{
  if (isset($yt_response['prevPageToken'])) {
    echo '<a title="Previous ' .YT_PLAYLISTS_MAXRESULTS_NEXT. ' '
      .$items_str. '" class="page_nextlink" href="' .$url_pre. '?p='
      .$yt_response['prevPageToken']
      .($url_post===''? '': '&amp;' .$url_1post) .'">&laquo;-'
      .YT_PLAYLISTS_MAXRESULTS_NEXT.'</a> ';
  } else {
    echo 'First ';
  }

  echo count($yt_response['items']) .' of '
    .$yt_response['pageInfo']['totalResults'] .' '. $items_str;

  if (isset($yt_response['nextPageToken'])) {
    echo ' <a title="Next ' .YT_PLAYLISTS_MAXRESULTS_NEXT. ' '
      .$items_str. '" class="page_nextlink" href="' .$url_pre. '?p='
      .$yt_response['nextPageToken']
      .($url_post===''? '': '&amp;' .$url_post) .'">'
      .YT_PLAYLISTS_MAXRESULTS_NEXT.'+&raquo;</a>';
  }
}

/* ***************************************************************  */

function yt_activity_recv_channel($yt_activity)
{
  $type = $yt_activity['snippet']['type'];
  $content = $yt_activity['contentDetails'][$type];

  if (isset($content['resourceId'])
      && $content['resourceId']['kind'] == 'youtube#channel') {

    $result = yt_recv_channel($content['resourceId']['channelId']);
    return $result['items'][0];
  }

  return false;
}

function yt_activity_group($yt_activities, $i)
{
  $cur_activ = $yt_activities[$i];

  if (!isset($cur_activ['snippet']['groupId']))
    return array($yt_activities, array($cur_activ));

  $group_id = $cur_activ['snippet']['groupId'];
  $result = array_slice($yt_activities, 0, $i+1);
  $result_selected = array($cur_activ);

  for ($k=$i+1; $k<count($yt_activities); $k++) {
    $cur_activ = $yt_activities[$k];

    if (isset($cur_activ['snippet']['groupId'])
        && $cur_activ['snippet']['groupId'] == $group_id)
      $result_selected[count($result_selected)] = $cur_activ;
    else
      $result[count($result)] = $cur_activ;
  } /* for ($k=$i+1; $k<count($yt_activities); $k++)  */

  return array($result, $result_selected);
}

function _yt_activity_url_resourceid($resource_id, $playlist_id,
                                     $local_url)
{
  if ($resource_id['kind'] == 'youtube#video') {
    if ($local_url) {
      if ($playlist_id == '')
        return array(false, '/' .COMMON_DIR_WATCH_ABS. '?v='
                     .$resource_id['videoId']);
      else
        return array(false, '/' .COMMON_DIR_WATCH_ABS. '?list='
                     .$playlist_id. '&amp;v=' .$resource_id['videoId']);
    } else {
      return array(true, YT_URL_WATCH. '?v=' .$resource_id['videoId']);
    }
  } else if ($resource_id['kind'] == 'youtube#channel') {
    return array(true, YT_URL_CHANNEL. $resource_id['channelId']);
  }

  return array(false, '.'); // array($blank, $url);
}
function yt_activity_url($yt_activity)
{
  $type = $yt_activity['snippet']['type'];
  $content = $yt_activity['contentDetails'][$type];

  if ($type == 'like') {
    return
      _yt_activity_url_resourceid($content['resourceId'],
                                  yt_get_likedlist_plid(), true);
  } else if ($type == 'upload') {
    return array(false,
                 '/' .COMMON_DIR_WATCH_ABS. '?v=' .$content['videoId']);
  } else if ($type == 'favorite') {
    return _yt_activity_url_resourceid($content['resourceId'], '', false);
  } else if ($type == 'comment') {
    return _yt_activity_url_resourceid($content['resourceId'], '', false);
  } else if ($type == 'subscription') {
    return _yt_activity_url_resourceid($content['resourceId'], '', false);
  } else if ($type == 'playlistItem') {
    return _yt_activity_url_resourceid($content['resourceId'],
                                       $content['playlistId'], true);
  } else if ($type == 'bulletin') {
    /* First post of video  */
    return _yt_activity_url_resourceid($content['resourceId'], '', true);
  }

  return array(false, '.'); // array($blank, $url);
}

function yt_print_activity_link($yt_activity, $yt_channel, $blank,
                                $url)
{
  $item = $yt_channel? $yt_channel: $yt_activity;

  ?><a class="yt_activity_link"<?
    if ($blank) echo ' target="_blank"';
  ?> href="<?
    echo $url;
  ?>"><img class="icon_default" alt="(video)" src="/<?
    echo COMMON_DIR_THEMECUR_IMG_ABS;
  ?>icon_video.32.png"><span class="icon_text"><?
    _o($item['snippet']['title']);
  ?></span></a><?
}

function yt_print_activity_thumblink($yt_activity, $yt_channel, $blank,
                                     $url)
{
  $item = $yt_channel? $yt_channel: $yt_activity;

  ?><a class="img_link"<?
    if ($blank) echo ' target="_blank"';
  ?> title="<?_o($item['snippet']['title']); ?>" href="<?
    echo $url;
  ?>"><img class="yt_activity_thumb" alt="(thumb)" src="<?
    echo $item['snippet']['thumbnails']['medium']['url'];
  ?>"></a><?
}

function yt_printshort_activity_type($activ_selected)
{
  for ($i=count($activ_selected)-1; $i>=0; $i--) {
    if ($i < count($activ_selected)-1) echo ' '; // No &nbsp;

    $cur_activity = $activ_selected[$i];
    $type = $cur_activity['snippet']['type'];
    if ($type == 'upload') {
      ?><img class="yt_activity_type" alt="uploaded"<?
      ?> title="uploaded" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_upload.32.png"><?
    } else if ($type == 'like') {
      ?><img class="yt_activity_type" alt="liked"<?
      ?> title="liked" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_like.32.png"><?
    } else if ($type == 'favorite') {
      ?><img class="yt_activity_type" alt="favorited"<?
      ?> title="favorited" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_favorite.32.png"><?
    } else if ($type == 'comment') {
      ?><img class="yt_activity_type" alt="commented"<?
      ?> title="commented" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_comment.32.png"><?
    } else if ($type == 'subscription') {
      ?><img class="yt_activity_type" alt="subscribed"<?
      ?> title="subscribed" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_subscribe.32.png"><?
    } else if ($type == 'playlistItem') {
      list($blank, $url) = yt_activity_url($cur_activity);

      ?><a class="img_link"<?
        if ($blank) echo ' target="_blank"';
      ?> title="Watch in playlist!" href="<? echo $url; ?>"><?
      ?><img class="yt_activity_type" alt="playlist"<?
      ?> title="Added to playlist" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_playlist_add.35.32.png"><?
      ?></a><?
    } else if ($type == 'bulletin') {
      // Icon shown in yt_print_activity_desc()
    } else {
      _o($type);
    } // if ($type == ...)
  }
}

function yt_print_activity_desc($activ_selected, $yt_channel, $blank,
                                $url)
{
  for ($i=count($activ_selected)-1; $i>=0; $i--) {
    $yt_activity = $activ_selected[$i];

    if ($yt_channel) {
      $title = $yt_channel['snippet']['title'];
      $desc = $yt_channel['snippet']['description'];
      if (!$desc) $desc = 'Channel "'.$title. '".';

      $more_url = $url. '/about';
      $time_url = '';
    } else {
      $title = $yt_activity['snippet']['title'];
      $desc = $yt_activity['snippet']['description'];
      if (!$desc) $desc = 'Channel "'.$title. '".';

      $more_url = $url. '#description';
      $time_url = $url;
    }
    $target = $blank? '_blank': '_self';

    $type = $yt_activity['snippet']['type'];
    if ($i == 0) {
    ?><div class="description activity_table_box activity_table_descr"><?
    common_user_output($desc, $more_url, $target, 2, $time_url, $target);
    ?></div><?
    } else if ($type == 'bulletin') {
    ?><div class="description activity_table_box activity_table_bulletin"><?
      ?><img class="yt_activity_type" alt="Bulletin"<?
      ?> title="Bulletin" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_bulletin.32.png">&nbsp;<?
    common_user_output($desc, $more_url, $target, 2, $time_url, $target);
    ?></div><?
    }
  } /* for ($i=count($activ_selected)-1; $i>=0; $i--)  */
}

/* ***************************************************************  */
