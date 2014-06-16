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

define('YT_REQUEST_PREFIX',        'https://www.googleapis.com/youtube/v3/');
define('YT_PLAYLISTS_MAXRESULTS',  '10');

/* ***************************************************************  */

function _yt_api_list($method, $part, $params_nokey='')
{
  $request = YT_REQUEST_PREFIX .$method. '?key=' .CONFIG_YT_APIKEY
    .'&part=' .$part. ($params_nokey == ''? '': '&' .$params_nokey);

  $response = file_get_contents($request);
  if ($response === false) return false;

  return $response;
}

function _yt_get_pagetoken(&$page, $method, $params_nokey='')
{
  $page_token = '';

  for ($i=1; $i<$page; $i++) {
    $json = _yt_api_list($method, 'id', $params_nokey
      .'&fields=nextPageToken&pageToken=' .$page_token);
    $tmp = json_decode($json, true);

    if (!isset($tmp['nextPageToken'])) {
      $page = $i;
      break;
    }

    $page_token = json_decode($json, true)['nextPageToken'];
  }

  return $page_token;
}

/* ***************************************************************  */

function yt_get_playlists(&$page)
{
  $page_token = _yt_get_pagetoken($page, 'playlists',
    'channelId=' .CONFIG_YT_CHANNELID. '&maxResults='.YT_PLAYLISTS_MAXRESULTS);

  $json = _yt_api_list('playlists', 'status,contentDetails,snippet',
      'fields=pageInfo,items('
      .'status/privacyStatus,contentDetails/itemCount'
      .',id,contentDetails/itemCount,status,player'
      .',snippet(title,description,thumbnails/medium/url))'
    .'&channelId=' .CONFIG_YT_CHANNELID. '&maxResults='
    .YT_PLAYLISTS_MAXRESULTS. '&pageToken=' .$page_token);
  if ($json === false) return false;

  $result = json_decode($json, true);
  if ($result === false) return false;

  return $result;
}
