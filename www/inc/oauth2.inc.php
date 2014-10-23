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

include_once dirname(__FILE__). '/oauth2_callbacks.inc.php';

/* Specification:
 *
 * http://tools.ietf.org/html/rfc6749
 */

define('_OAUTH2_REDIRECT_URI',
       COMMON_SERVER_PROTOCOL. '://' .COMMON_SERVER_NAME. '/'
       .COMMON_DIR_OAUTH2_ABS);

$oauth2_platforms_data = array();

/* ***************************************************************  */

function oauth2_redirect_params_print($platform, $callback, $args)
{
  ?><input type="hidden" name="pf" value="<? echo $platform; ?>"><?
  ?><input type="hidden" name="cb" value="<? echo $callback; ?>"><?

  for ($i=0; $i<count($args); $i++) {
    ?><input type="hidden" name="arg_<?
      echo $i;
    ?>" value="<?
      echo $args[$i];
    ?>"><?
  }
}

function oauth2_login_urlget_setsession($platform, $callback, $args)
{
  global $oauth2_platforms_data;
  $pf_data = $oauth2_platforms_data[$platform];

  /* Must be application/x-www-form-urlencoded (RFC 6749 section
   * 3.1.2.)
   */
  $redirect_uri = urlencode(_OAUTH2_REDIRECT_URI);

  $id = session_oauth2login_set($platform, $callback, $args);

  return $pf_data['oauth2_url_login_pre']. '?response_type=code'
    .'&client_id=' .$pf_data['oauth2_client_id']
    .'&redirect_uri=' .$redirect_uri .'&scope=' .$pf_data['oauth2_scope']
    .'&state=' .$id. $pf_data['oauth2_url_login_post'];
}

function oauth2_login_id2vars($id)
{
  return session_oauth2login_delete($id);
}

/* ***************************************************************  */

function oauth2_2errormsg($error_resp)
{
  /* Possible error responses are described in RFC 6749 section
   * 4.1.2.1.
   */

  switch ($error_resp) {
  case 'invalid_request':
    return 'You has been sent garbage';
  case 'unauthorized_client':
    return 'Authorization request denied (GRANT_TYPE denied)';
  case 'access_denied':
    return 'Access denied';
  case 'unsupported_response_type':
    return 'Response type is not supported';
  case 'invalid_scope':
    return 'Scope is not known/supported';
  case 'server_error':
    return 'Authorization Server encountered an enexpected error'
      .' (500 Internal Server Error)';
  case 'temporarily_unavailable':
    return 'Authorization Server seems to be overloaded'
      .' (503 Service Unavailable)';

  /* Possible error responses for token request are described in RFC
   * 6749 section 5.2.
   */
  //case 'invalid_request': implemented above
  case 'invalid_client':
    return 'Invalid CLIENT_ID or CLIENT_SECRET configured';
  case 'invalid_grant':
    return 'Invalid CODE or REFRESH_TOKEN';
  //case 'unauthorized_client': implemented above
  case 'unsupported_grant_type':
    return 'GRANT_TYPE not supported';
  //case 'invalid_scope': implemented above
  }

  return $error_resp;
}

/* ***************************************************************  */

/* Returns: TRUE on success, FALSE on error, String(error_response) on
 * remote error.
 */
function __oauth2_token_post_setsession($platform, $code,
                                        $code_is_refreshtoken)
{
  global $oauth2_platforms_data;
  $pf_data = $oauth2_platforms_data[$platform];

  /* Must be application/x-www-form-urlencoded (RFC 6749 section
   * 4.1.3.)
   */
  $redirect_uri = urlencode(_OAUTH2_REDIRECT_URI);

  if ($code_is_refreshtoken) {
    $grant_type = 'refresh_token';
    $auth_param = 'refresh_token=' .$code;
  } else {
    $grant_type = 'authorization_code';
    $auth_param = 'code=' .$code;
  }

  $data = $auth_param. '&client_id=' .$pf_data['oauth2_client_id']
    .'&client_secret=' .$pf_data['oauth2_client_secret']. '&redirect_uri='
    .$redirect_uri. '&grant_type=' .$grant_type;

  /* Do not display $data because of the client secret  */
  debug_api_info_incr('cnt_oauth2_auth', 1,
                      'Platform: ' .$platform. ' - Refresh-Token: '
                      . ($code_is_refreshtoken? 'yes': 'no'));

  $time_stamp = time();
  list($status_ok, $status, $json)
    = http_receive($pf_data['oauth2_url_token'], 'POST', array(), $data);

  /* Error response will be returned with HTTP CODE 400 (Bad Request).
   * See RFC 6749 section 5.2.
   */
  if ($status != 400 && !$status_ok) return false;

  $token_resp = json_decode($json, true);
  if (!$token_resp) return false;

  /* Error responses described in RFC 6749 section 5.2.  */
  if (isset($token_resp['error'])) return $token_resp['error'];

  if (!session_oauth2token_set($platform, $time_stamp, $token_resp))
    return false;

  return true;
}

/* Returns: TRUE on success, FALSE on error, String(error_response) on
 * remote error.
 */
function oauth2_token_post_setsession($platform, $code)
{
  debug_api_info_incr('cnt_' .$platform. '_token', 1);

  return __oauth2_token_post_setsession($platform, $code, false);
}

/* Returns: TRUE on success, FALSE on error, String(error_response) on
 * remote error.
 */
function _oauth2_refresh_post_setsession($platform, $refresh_token)
{
  debug_api_info_incr('cnt_' .$platform. '_refresh', 1);

  return __oauth2_token_post_setsession($platform, $refresh_token,
                                        true);
}

/* ***************************************************************  */

define('_OAUTH2_ACCESSTOKEN_EXPIRED_DELTA_S',     10);
function _oauth2_accesstoken_expired($token_required)
{
  return $token_required['time_stamp'] + $token_required['expires_in']
    - _OAUTH2_ACCESSTOKEN_EXPIRED_DELTA_S < time();
}

function oauth2_logged_in($platform)
{
  $tmp = session_oauth2token_get($platform);
  if (!$tmp) return false;
  list($required, $refresh_token) = $tmp;

  return !_oauth2_accesstoken_expired($required)
    || $refresh_token !== false;
}

function oauth2_logout($platform)
{
  session_oauth2token_delete($platform);
}

/* Returns: array() on success, FALSE on error, String(error_response)
 * on remote error.
 */
function oauth2_token_get($platform)
{
  $tmp = session_oauth2token_get($platform);
  if (!$tmp) {
    _e('oauth2_token_get',
       'No session information available.  Logging user out');
    oauth2_logout($platform);
    return false;
  }
  list($required, $refresh_token) = $tmp;

  if (_oauth2_accesstoken_expired($required)) {

    if (!$refresh_token) {
      _e('oauth2_token_get',
         'Refreshing triggered without Refresh Token.  Logging user'
         .' out');
      oauth2_logout($platform);
      return false;
    }

    $tmp = _oauth2_refresh_post_setsession($platform, $refresh_token);
    if ($tmp !== true) {
      _e('oauth2_token_get',
         'Authorization Server responses `' .$tmp. '\'.  Logging user'
         .' out');
      oauth2_logout($platform);
      return $tmp;
    }

    $tmp = session_oauth2token_get($platform);
    if (!$tmp) {
      _e('oauth2_token_get',
         'No session information available after Refrshing.  Logging'
         .' user out');
      oauth2_logout($platform);
      return false;
    }
    list($required, $refresh_token) = $tmp;
  }

  return array('platform' => $platform,
               'token_type' => $required['token_type'],
               'access_token' => $required['access_token']);
}

/* ***************************************************************  */
