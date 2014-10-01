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
include_once dirname(__FILE__). '/google_api.inc.php';

/* Youtube Data API v3 Reference:
 *
 * https://developers.google.com/youtube/v3/docs/
 */

define('_YT_REQUEST_METHOD_PREFIX',     'youtube/v3/');
define('YT_PLAYLISTS_MAXRESULTS',       3);
define('YT_PLAYLISTS_MAXRESULTS_NEXT',  10);

define('YT_CHAN_ACTIV_MAXRESULTS',      4);
define('YT_CHAN_ACTIV_MAXRESULTS_NEXT', 10);

/* Must be odd (3, 5, 7, ...) */
define('YT_PLVIDEOS_MAXRESULTS',        7);
define('YT_PLVIDEOS_MAXRESULTS_HALF',   YT_PLVIDEOS_MAXRESULTS >> 1);

define('_YT_REQUEST_FIELDS_PAGING',
       'pageInfo,nextPageToken,prevPageToken');

define('_YT_RECV_PLAYLIST_50PAGES',     3);

define('YT_URL_WATCH',        'https://www.youtube.com/watch');
define('YT_URL_CHANNEL',      'https://www.youtube.com/channel/');

/* ***************************************************************  */

function _yt_api_list($method, $part, $params='')
{
  return google_api_recv(_YT_REQUEST_METHOD_PREFIX .$method,
    'part=' .$part. ($params == ''? '': '&' .$params));
}

/* ***************************************************************  */

function yt_recv_playlists($page_token, $plid='')
{
  $max_result = ($page_token === '')? YT_PLAYLISTS_MAXRESULTS
    : YT_PLAYLISTS_MAXRESULTS_NEXT;

  $result = _yt_api_list('playlists', 'status,contentDetails,snippet',
    'fields=' ._YT_REQUEST_FIELDS_PAGING. ',items('
      .'id,status/privacyStatus,contentDetails/itemCount'
      .',snippet(publishedAt,title,description,thumbnails/medium/url))'
    .($plid? '&id=' .$plid: '&channelId=' .CONFIG_YT_CHANNELID)
    .'&maxResults=' .$max_result .'&pageToken=' .$page_token);
  if (!$result) return false;

  return $result;
}
function yt_recv_playlist_short($plid)
{
  $result = _yt_api_list('playlists', 'snippet',
    'fields=items(snippet(publishedAt,title))&id=' .$plid);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */

function yt_recv_playlist_items($playlist_id, $page_token='')
{
  /* The channelId is the ID who owns the list, not the video.  */

  $result = _yt_api_list('playlistItems', 'status,contentDetails,snippet',
    'fields=' ._YT_REQUEST_FIELDS_PAGING. ',items('
      .'status/privacyStatus,contentDetails(videoId,startAt,endAt)'
      .',snippet(publishedAt,channelId,title,thumbnails/default/url'
      .',position))'
    .'&playlistId=' .$playlist_id. '&maxResults=' .YT_PLVIDEOS_MAXRESULTS
    .'&pageToken=' .$page_token);
  if (!$result) return false;

  return array(
    'correction' => -YT_PLVIDEOS_MAXRESULTS_HALF,
    'result' => $result
  );
}

function yt_recv_playlist_items_video($playlist_id, $video_id)
{
  /* Youtube SELECT the maxResults playlistItems first and AFTER that
   * the videoId is filtered!  For this reason that code does NOT work
   * :( ...
   *
   * nextPageToken is only available if there are maxResults items
   * sent to us.  So we can't use the videoId parameter :(( ...
   *
     var_dump(_yt_api_list('playlistItems', 'snippet',
       'fields=items/snippet/position'
       .'&playlistId='.$playlist_id. '&videoId=' .$video_id
       .'&maxResults=1'));
   */

  $position = 0;
  for ($i=0, $i_page = ''; $i<_YT_RECV_PLAYLIST_50PAGES; $i++) {
    $result = _yt_api_list('playlistItems', 'snippet',
      'fields=nextPageToken,items/snippet(position,resourceId/videoId)'
      .'&playlistId=' .$playlist_id. '&maxResults=50&pageToken=' .$i_page);
    if (!$result) return false;

    foreach ($result['items'] as $plitem) {
      if ($plitem['snippet']['resourceId']['videoId'] == $video_id) {
        $position = intval($plitem['snippet']['position']);
        break 2;
      }
    }

    $i_page = $result['nextPageToken'];
  }

  /* ***  */

  $start = $position - YT_PLVIDEOS_MAXRESULTS_HALF;

  /* Youtube Data API v3 - maxResults:
   *
   * Acceptable values are 0 to 50, inclusive.
   */
  for ($page_token=''; $start>0; $start-=50) {
    $result =  _yt_api_list('playlistItems', 'id',
      'fields=nextPageToken'
      .'&playlistId=' .$playlist_id. '&maxResults='
      .($start>50? 50: $start). '&pageToken=' .$page_token);
    if (!$result) return false;

    $page_token = $result['nextPageToken'];

    if ($start <= 50) break;
  }

  /* ***  */

  $result = yt_recv_playlist_items($playlist_id, $page_token);
  if (!$result) return false;

  return array(
    'correction' => $start > 0? 0: $start,
    'result' => $result['result']
  );
}

/* ***************************************************************  */

function yt_recv_video($vid)
{
  $result = _yt_api_list('videos', 'snippet,contentDetails,statistics',
    'fields=items('
      .'snippet(publishedAt,channelId,channelTitle,title,description)'
      .',contentDetails(duration),statistics(viewCount,likeCount'
      .',dislikeCount,commentCount))&id=' .$vid);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */

function yt_recv_channel($chan_id)
{
  $result = _yt_api_list('channels', 'snippet',
    'fields=items('
      .'snippet(title,description,thumbnails/medium)'
    .')&id=' .$chan_id);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */

function yt_recv_chan_activity($page_token)
{
  $max_result = ($page_token === '')? YT_CHAN_ACTIV_MAXRESULTS
    : YT_CHAN_ACTIV_MAXRESULTS_NEXT;

  $result = _yt_api_list('activities', 'snippet,contentDetails',
    'fields=' ._YT_REQUEST_FIELDS_PAGING. ',items('
        .'snippet(publishedAt,title,description,thumbnails/medium/url'
        .',type,groupId),contentDetails('
          .'upload(videoId)'

           /* kind=youtube#[video]  */
          .',like/resourceId'

           /* kind=youtube#[video]  */
           // NOT TESTED
          .',favorite/resourceId'

           /* kind=youtube#[video,channel]  */
          .',comment/resourceId'

           /* kind=youtube#[channel]  */
          .',subscription/resourceId'

           /* kind=youtube#[video]  */
          .',playlistItem(resourceId,playlistId,playlistItemId)'

           /* kind=youtube#[video,channel]  */
           // NOT IMPLEMENTED
          .',recommendation(resourceId,reason'
            .',seedResourceId(kind,videoId,channelId))'

           /* kind=youtube#[video,channel,playlist]  */
          .',bulletin/resourceId(kind,videoId,channelId,playlistId)'

           /* kind=youtube#[video,channel,playlist]  */
           /* type=[facebook,googlePlus,twitter,unspecified]  */
           // NOT IMPLEMENTED
          .',social(type,resourceId(kind,videoId,channelId,playlistId)'
            .',author,referenceUrl,imageUrl)'

           /* kind=youtube#[video,channel,playlist]  */
           // NOT IMPLEMENTED
          .',channelItem(resourceId)'

        .')'
      .')&channelId=' .CONFIG_YT_CHANNELID
    .'&maxResults=' .$max_result. '&pageToken=' .$page_token);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */

define('_YT_DAY_IN_SECS',               24*3600);

function yt_str2date($yt_time_str)
{
  $stamp = strtotime($yt_time_str, 0);

  if (!$stamp) return '-no date-';

  $result = date(CONFIG_DATE_FORMAT, $stamp);
  if ($result == date(CONFIG_DATE_FORMAT)) return 'today';
  if ($result == date(CONFIG_DATE_FORMAT, time()-_YT_DAY_IN_SECS))
    return 'yesterday';

  return $result;
}
function yt_str2time($yt_time_str)
{
  $stamp = strtotime($yt_time_str, 0);

  if (!$stamp) return '-timeless-';

  $result = date(CONFIG_TIME_FORMAT, $stamp);
  $diff = time() - $stamp;
  if ($diff < 60) return floor($diff) .' seconds ago';
  if ($diff < 3600) return floor($diff/60) .' minutes ago';
  if ($diff < 24*3600) return floor($diff/3600) .' hours ago';

  return $result;
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
  ?>" title="View this channel at youtube.com"><?
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
  ?><a class="yt_activity_link"<?
    if ($blank) echo ' target="_blank"';
  ?> title="Watch it!" href="<?
    echo $url;
  ?>"><img class="icon_default" alt="(video)" src="/<?
    echo COMMON_DIR_THEMECUR_IMG_ABS;
  ?>icon_video.32.png"><span class="icon_text"><?
    if ($yt_channel)
      _o($yt_channel['snippet']['title']);
    else
      _o($yt_activity['snippet']['title']);
  ?></span></a><?
}

function yt_print_activity_thumblink($yt_activity, $yt_channel, $blank,
                                     $url)
{
  ?><a class="img_link"<?
    if ($blank) echo ' target="_blank"';
  ?> title="Watch it!" href="<?
    echo $url;
  ?>"><img class="yt_activity_thumb" alt="(thumb)" src="<?
    if ($yt_channel)
      echo $yt_channel['snippet']['thumbnails']['medium']['url'];
    else
      echo $yt_activity['snippet']['thumbnails']['medium']['url'];
  ?>"></a><?
}

function yt_printshort_activity_type($activ_selected)
{
  for ($i=count($activ_selected)-1; $i>=0; $i--) {
    if ($i < count($activ_selected)-1) echo ' '; // No &nbsp;

    $cur_activity = $activ_selected[$i];
    $type = $cur_activity['snippet']['type'];
    if ($type == 'upload') {
      ?><img class="yt_activity_type" alt="Uploaded"<?
      ?> title="Uploaded" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_upload.32.png"><?
    } else if ($type == 'like') {
      ?><img class="yt_activity_type" alt="Liked"<?
      ?> title="Liked" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_like.32.png"><?
    } else if ($type == 'favorite') {
      ?><img class="yt_activity_type" alt="Favorited"<?
      ?> title="Favorited" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_favorite.32.png"><?
    } else if ($type == 'comment') {
      ?><img class="yt_activity_type" alt="Commented"<?
      ?> title="Commented" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_comment.32.png"><?
    } else if ($type == 'subscription') {
      ?><img class="yt_activity_type" alt="Subscribed"<?
      ?> title="Subscribed" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_subscribe.32.png"><?
    } else if ($type == 'playlistItem') {
      list($blank, $url) = yt_activity_url($cur_activity);

      ?><a class="img_link"<?
        if ($blank) echo ' target="_blank"';
      ?> title="Watch in playlist!" href="<? echo $url; ?>"><?
      ?><img class="yt_activity_type" alt="Playlist"<?
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
