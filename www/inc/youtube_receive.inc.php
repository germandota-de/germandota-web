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

if (!defined('YT_INCLUDED')) die('Include youtube.inc.php!');

/* Youtube Data API v3 Reference:
 *
 * https://developers.google.com/youtube/v3/docs/
 */

/* ***************************************************************  */

function _yt_api_list($method, $part, $params='')
{
  debug_api_info_incr('cnt_youtube_list', 1,
                      $method .' - '. $part. ' ' .$params);

  return google_api_recv(_YT_REQUEST_METHOD_PREFIX .$method,
    'part=' .$part. ($params == ''? '': '&' .$params));
}

function _yt_api_rate_auth($access_array, $item, $params)
{
  debug_api_info_incr('cnt_youtube_rate', 1, $item .' - '. $params);

  return google_api_post(_YT_REQUEST_METHOD_PREFIX .$item. '/rate',
                         $params, false, false, $access_array);
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
  if (!$result || count($result['items']) == 0) return false;

  return $result;
}
function yt_recv_playlist_short($plid)
{
  $result = _yt_api_list('playlists', 'snippet',
    'fields=items(snippet(publishedAt,title'
    .',thumbnails/maxres(url,width,height)))&id=' .$plid);
  if (!$result || count($result['items']) == 0) return false;

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
  if (!$result || count($result['items']) == 0) return false;

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
  for ($i=0, $i_page = ''; $i<_YT_RECV_PLIST_50PAGES_LIMIT; $i++) {
    $result = _yt_api_list('playlistItems', 'snippet',
      'fields=nextPageToken,items/snippet(position,resourceId/videoId)'
      .'&playlistId=' .$playlist_id. '&maxResults=50&pageToken=' .$i_page);
    if (!$result || count($result['items']) == 0) return false;

    foreach ($result['items'] as $plitem) {
      if ($plitem['snippet']['resourceId']['videoId'] == $video_id) {
        $position = intval($plitem['snippet']['position']);
        break 2;
      }
    }

    if (!isset($result['nextPageToken'])) return false;
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
      .'snippet(publishedAt,channelId,channelTitle,title'
      .',thumbnails/maxres(url,width,height),description)'
      .',contentDetails(duration),statistics(viewCount,likeCount'
      .',dislikeCount,commentCount))&id=' .$vid);
  if (!$result || count($result['items']) == 0) return false;

  return $result;
}

function yt_recv_video_rate_auth($access_array, $vid, $rate)
{
  $result = _yt_api_rate_auth($access_array, 'videos',
                              'id='. $vid .'&rating=' .$rate);

  return $result !== false;
}

/* ***************************************************************  */

function yt_recv_channel($chan_id)
{
  $result = _yt_api_list('channels', 'snippet',
    'fields=items('
      .'snippet(title,description,thumbnails/medium)'
    .')&id=' .$chan_id);
  if (!$result || count($result['items']) == 0) return false;

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
  if (!$result || count($result['items']) == 0) return false;

  return $result;
}
