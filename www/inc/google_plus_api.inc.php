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

function gplus_api_comments_list($activity_id)
{
  $request = GPLUS_REQUEST_PREFIX. 'activities/' .$activity_id
    . '/comments?key=' .CONFIG_YT_APIKEY;

  $json = file_get_contents($request);
  if (!$json) return false;

  $result = json_decode($json, true);
  if (!$result) return false;

  return $result;
}

function gplus_api_activity_get($activity_id)
{
  $request = GPLUS_REQUEST_PREFIX. 'activities/' .$activity_id
    . '?key=' .CONFIG_YT_APIKEY;

  $json = file_get_contents($request);
  if (!$json) return false;

  $result = json_decode($json, true);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */

function gplus_print_profilelink($name, $url)
{
  ?><a class="gplus_profilelink" target="_blank"<?
  ?> href="<?
    echo common_url_amp($url);
  ?>" title="View this profile at plus.google.com"><?
    _o($name);
  ?></a><?
}

/* ***************************************************************  */
