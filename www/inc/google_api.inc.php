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

/* Google References:
 *
 * https://developers.google.com/products/
 */

define('_GOOGLE_REQUEST_PREFIX',   'https://www.googleapis.com');
define('_GOOGLE_REQUEST_DEFAULT',
       '?key=' .CONFIG_YT_APIKEY. '&userIp='.COMMON_USER_IP
       .'&quotaUser=' .COMMON_SESSION_ID
       );

/* ***************************************************************  */

function google_api_recv($method, $params)
{
  $request = _GOOGLE_REQUEST_PREFIX .'/'. $method
    ._GOOGLE_REQUEST_DEFAULT. '&' .$params;

  $json = file_get_contents($request);
  if (!$json) return false;

  $result = json_decode($json, true);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */
