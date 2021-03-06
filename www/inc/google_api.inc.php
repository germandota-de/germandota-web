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

define('GOOGLE_OAUTH2_LOGIN_PRE',
       'https://accounts.google.com/o/oauth2/auth');
define('GOOGLE_OAUTH2_LOGIN_POST',
       //'&approval_prompt=auto&access_type=offline&login_hint=email@addre.ss');
       '&approval_prompt=auto&access_type=offline');
define('GOOGLE_OAUTH2_TOKEN_PRE',
       'https://accounts.google.com/o/oauth2/token');

/* ***************************************************************  */

function _google_api_httpheader_auth(&$header, $access_array=false)
{
  if ($access_array) {
    $token_type = $access_array['token_type'];

    if ($token_type != 'Bearer') {
      _e('_google_api_httpheader_auth', 'Token type not supported: `'
         .$token_type. '\'');
      return false;
    }

    $header[count($header)]
      = 'Authorization: Bearer ' .$access_array['access_token'];
  }

  return true;
}

/* ***************************************************************  */

function google_api_recv($method, $params, $access_array=false)
{
  $request = _GOOGLE_REQUEST_PREFIX .'/'. $method
    ._GOOGLE_REQUEST_DEFAULT. '&' .$params;

  $header = array();
  if (!_google_api_httpheader_auth($header, $access_array))
    return false;

  /* Do not display $request because of the API key  */
  debug_api_info_incr('cnt_google_api', 1, 'method: ' .$method);

  list($status_ok, $status, $json)
    = http_receive($request, 'GET', $header);
  if (!$status_ok) return false;
  if ($json === '') return "\n";

  $result = json_decode($json, true);
  if (!$result) return false;

  return $result;
}

function google_api_post($method, $params, $content=false,
                         $content_type=false, $access_array=false)
{
  $request = _GOOGLE_REQUEST_PREFIX .'/'. $method
    ._GOOGLE_REQUEST_DEFAULT. '&' .$params;

  $header = array();
  if (!_google_api_httpheader_auth($header, $access_array))
    return false;

  /* Do not display $request because of the API key  */
  debug_api_info_incr('cnt_google_api', 1, 'method: ' .$method);

  list($status_ok, $status, $json)
    = http_receive($request, 'POST', $header, $content, $content_type);
  if (!$status_ok) return false;
  if ($json === '') return "\n";

  $result = json_decode($json, true);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */
