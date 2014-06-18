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

define('YT_REQUEST_PREFIX',        'https://www.googleapis.com/youtube/v3/');
define('YT_PLAYLISTS_MAXRESULTS',       '4');
define('YT_PLAYLISTS_MAXRESULTS_NEXT',  '10');

/* ***************************************************************  */

function _yt_api_list($method, $part, $params_nokey='')
{
  $request = YT_REQUEST_PREFIX .$method. '?key=' .CONFIG_YT_APIKEY
    .'&part=' .$part. ($params_nokey == ''? '': '&' .$params_nokey);

  $response = file_get_contents($request);
  if (!$response) return false;

  return $response;
}

/* ***************************************************************  */

function yt_get_playlists($page_token)
{
  $max_result = ($page_token === '')? YT_PLAYLISTS_MAXRESULTS
    : YT_PLAYLISTS_MAXRESULTS_NEXT;

  $json = _yt_api_list('playlists', 'status,contentDetails,snippet',
    'fields=pageInfo,nextPageToken,prevPageToken,items('
      .'id,status/privacyStatus,contentDetails/itemCount'
      .',snippet(publishedAt,title,description,thumbnails/medium/url))'
    .'&channelId=' .CONFIG_YT_CHANNELID. '&maxResults='
    .$max_result. '&pageToken=' .$page_token);
  if ($json === false) return false;

  $result = json_decode($json, true);
  if ($result === false) return false;

  return $result;
}

function yt_str2date($yt_time_str)
{
  return date(CONFIG_DATE_FORMAT, strtotime($yt_time_str, 0));
}
function yt_str2time($yt_time_str)
{
  return date(CONFIG_TIME_FORMAT, strtotime($yt_time_str, 0));
}

function yt_print_pageinfo($page_token, $yt_response, $items_str,
                           $url_pre, $url_post='')
{
  if (isset($yt_response['prevPageToken'])) {
    echo '<a title="Previous ' .YT_PLAYLISTS_MAXRESULTS_NEXT. ' '
      .$items_str. '" class="page_nextlink" href="' .$url_pre. '?p='
      .$yt_response['prevPageToken']
      .($url_post===''? '': '&amp;' .$url_post) .'">&laquo;-'
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
