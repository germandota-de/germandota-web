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

/* Youtube Data API !v2! comment reference:
 *
 * https://developers.google.com/youtube/2.0/developers_guide_protocol_comments
 * https://developers.google.com/youtube/articles/changes_to_comments
 */

define('YT_COMMENTS_PERPAGE',           10);
define('YT_COMMENTS_PXPERCOMMENT',      30);
define('YT_COMMENTS_OFFSET_PX',         200);

/* HTTPS:
 *
 * Peer certificate CN=`*.google.com' did not match expected CN=`gdata.youtube.com'
 */
define('YT_COMMENTS_REQUEST_PREFIX', 'https://gdata.youtube.com/feeds/api/');
define('YT_COMMENTS_SSL_CNMATCH',    '*.google.com');

/* ***************************************************************  */

function _yt_comments_apiv2_list($method, $start_index, $max_results,
                                 $params_nokey='')
{
  /* ?prettyprint=true for debugging ...  */
  $request = YT_COMMENTS_REQUEST_PREFIX .$method. '?alt=json&start-index='
    .$start_index. '&max-results=' .$max_results
    . ($params_nokey == ''? '': '&' .$params_nokey);

  $json = file_get_contents($request, false, stream_context_create(
    array('ssl' => array('CN_match' => YT_COMMENTS_SSL_CNMATCH))
  ));
  if (!$json) return false;

  $result = json_decode($json, true);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */

function yt_comments_recv($vid, $page=0)
{
  /* relevant-to-me=true only with OAuth ...  */
  $result = _yt_comments_apiv2_list(
    sprintf('videos/%s/comments', $vid), 1 + $page*YT_COMMENTS_PERPAGE,
    YT_COMMENTS_PERPAGE);
  if (!$result) return false;

  return array(
    'totalResults' => $result['feed']['openSearch$totalResults']['$t'],
    'results' => $result['feed']['entry']
  );
}

/* ***************************************************************  */

function yt_comments_iframeheight($comment_count)
{
  $cnt = $comment_count>YT_COMMENTS_PXPERCOMMENT
    ? YT_COMMENTS_PXPERCOMMENT: $comment_count;

  return YT_COMMENTS_OFFSET_PX + (YT_COMMENTS_PXPERCOMMENT*$cnt);
}

/* ***************************************************************  */
