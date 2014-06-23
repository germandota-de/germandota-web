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

/* Youtube Data API v3 Reference:
 *
 * https://developers.google.com/youtube/v3/docs/
 */

define('YT_REQUEST_PREFIX',
       'https://www.googleapis.com/youtube/v3/');
define('YT_PLAYLISTS_MAXRESULTS',       '3');
define('YT_PLAYLISTS_MAXRESULTS_NEXT',  '10');

/* Must be odd (3, 5, 7, ...) */
define('YT_PLVIDEOS_MAXRESULTS',        '7');
define('YT_PLVIDEOS_MAXRESULTS_HALF',   YT_PLVIDEOS_MAXRESULTS >> 1);

/* ***************************************************************  */

function _yt_api_list($method, $part, $params_nokey='')
{
  $request = YT_REQUEST_PREFIX .$method. '?key=' .CONFIG_YT_APIKEY
    .'&part=' .$part. ($params_nokey == ''? '': '&' .$params_nokey);

  $json = file_get_contents($request);
  if (!$json) return false;

  $result = json_decode($json, true);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */

function yt_recv_playlists($page_token, $plid='')
{
  $max_result = ($page_token === '')? YT_PLAYLISTS_MAXRESULTS
    : YT_PLAYLISTS_MAXRESULTS_NEXT;

  $result = _yt_api_list('playlists', 'status,contentDetails,snippet',
    'fields=pageInfo,nextPageToken,prevPageToken,items('
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
    'fields=pageInfo,nextPageToken,prevPageToken,items('
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

function yt_recv_playlist_items_video($playlist_id, $video_id,
                                      $fix_index=false)
{
  $result = _yt_api_list('playlistItems', 'snippet',
    'fields=items/snippet/position'
    .'&playlistId='.$playlist_id. '&videoId=' .$video_id);
  if (!$result) return false;

  $position = intval($result['items'][0]['snippet']['position']);

  if (COMMON_FIX_YT_LIKELIST && $fix_index !== false)
    $position = $fix_index-1;

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

function _yt_timeat2($str)
{
  return array(
    'h'   => round(preg_replace('/^.*[PT]([0-9.]*)H.*$/', '\1', $str)),
    'min' => round(preg_replace('/^.*[PTH]([0-9.]*)M.*$/', '\1', $str)),
    'sec' => round(preg_replace('/^.*[PTHM]([0-9.]*)S$/', '\1', $str))
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
    echo 'First 1+';
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
