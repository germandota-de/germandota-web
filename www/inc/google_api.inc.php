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

include_once dirname(__FILE__). '/oauth2.inc.php';

/* Google References:
 *
 * https://developers.google.com/products/
 * https://developers.google.com/youtube/v3/guides/authentication#server-side-apps
 */

define('_GOOGLE_REQUEST_PREFIX',   'https://www.googleapis.com');
define('_GOOGLE_REQUEST_DEFAULT',
       '?key=' .CONFIG_GOOGLE_APIKEY. '&userIp=' .COMMON_USER_IP
       /* Do not use session_id (or other fakeable ID) as `quotaUser'!
        */
       );

define('_GOOGLE_OAUTH2_PRE',
       'https://accounts.google.com/o/oauth2/auth');
define('_GOOGLE_OAUTH2_TOKEN_PRE',
       'https://accounts.google.com/o/oauth2/token');

/* ***************************************************************  */

function google_api_recv($method, $params)
{
  $request = _GOOGLE_REQUEST_PREFIX .'/'. $method
    ._GOOGLE_REQUEST_DEFAULT. '&' .$params;

  /* Do not display $request because of the API key  */
  //debug_api_info_incr('cnt_google_api', 1, $request);
  debug_api_info_incr('cnt_google_api', 1);

  $json = file_get_contents($request);
  if (!$json) return false;

  $result = json_decode($json, true);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */

function google_oauth2_login_url_get($scope, $platform, $callback,
                                     $args)
{
  return oauth2_login_url_get(
    _GOOGLE_OAUTH2_PRE, CONFIG_GOOGLE_CLIENT_ID, $scope,
    '&approval_prompt=auto&access_type=offline',
  //'&approval_prompt=auto&access_type=offline&login_hint=email@addre.ss',
    $platform, $callback, $args
  );
}

/* ***************************************************************  */

function google_oauth2_setsession($platform, $code)
{
  return oauth2_token_post_setsession(_GOOGLE_OAUTH2_TOKEN_PRE,
    CONFIG_GOOGLE_CLIENT_ID, CONFIG_GOOGLE_CLIENT_SECRET, $platform,
    $code);
}

/* ***************************************************************  */
