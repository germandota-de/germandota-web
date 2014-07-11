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

/* Google Plus API v3 Reference:
 *
 * https://developers.google.com/+/api/latest/
 */

define('GPLUS_REQUEST_PREFIX',
       'https://www.googleapis.com/plus/v1/');
define('GPLUS_COMMENTS_MAXREPLIES',       '2');

/* ***************************************************************  */

define('_GPLUS_COMMENTS_REQUEST_DEFAULT',
       '?key=' .CONFIG_YT_APIKEY. '&quotaUser=' .COMMON_SESSION_ID);
define('_GPLUS_COMMENTS_REQUEST_FIELDS',
       'id,published,updated,actor(id,displayName,image/url)');

function gplus_api_comments_list($activity_id, $max_results)
{
  $request = GPLUS_REQUEST_PREFIX. 'activities/' .$activity_id
    .'/comments' ._GPLUS_COMMENTS_REQUEST_DEFAULT. '&fields=items('
    ._GPLUS_COMMENTS_REQUEST_FIELDS. ',object(content))'
    .'&maxResults=' .$max_results. '&sortOrder=descending';

  $json = file_get_contents($request);
  if (!$json) return false;

  $result = json_decode($json, true);
  if (!$result) return false;

  return $result;
}

function gplus_api_activity_get($activity_id)
{
  $request = GPLUS_REQUEST_PREFIX. 'activities/' .$activity_id
    ._GPLUS_COMMENTS_REQUEST_DEFAULT. '&fields='
    ._GPLUS_COMMENTS_REQUEST_FIELDS. ',object(content,replies(totalItems))';

  $json = file_get_contents($request);
  if (!$json) return false;

  $result = json_decode($json, true);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */

function gplus_print_profilelink($actor)
{
  ?><a class="gplus_profilelink" target="_blank"<?
  ?> href="https://plus.google.com/<?
    echo $actor['id'];
  ?>" title="View this profile at plus.google.com"><img<?
  ?> class="gplus_profilelink" alt="(avatar)" src="<?
    echo common_url_amp($actor['image']['url']);
  ?>"><?
    _o($actor['displayName']);
  ?></a><?
}

/* ***************************************************************  */
