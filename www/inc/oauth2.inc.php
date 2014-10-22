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

define('OAUTH2_PLATFORM_YOUTUBE',       'youtube');

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

function oauth2_login_urlget_setsession(
  $url_pre, $client_id, $scope, $url_post, $platform, $callback, $args)
{
  /* Must be application/x-www-form-urlencoded (RFC 6749 section
   * 3.1.2.)
   */
  $redirect_uri = urlencode(_OAUTH2_REDIRECT_URI);

  $id = session_oauth2login_set($platform, $callback, $args);

  return $url_pre. '?response_type=code'
    .'&client_id=' .CONFIG_GOOGLE_CLIENT_ID
    .'&redirect_uri=' .$redirect_uri .'&scope=' .$scope
    .'&state=' .$id. $url_post;
}

function oauth2_login_id2vars($id)
{
  return session_oauth2login_delete($id);
}

/* ***************************************************************  */

function oauth2_login_2errormsg($error_resp)
{
  /* Possible error responses are described in RFC 6749 section
   * 4.1.2.1.
   */

  switch ($error_resp) {
  case 'invalid_request':
    return 'You has been sent garbage';
  case 'unauthorized_client':
    return 'Authorization request denied';
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
  }

  return $error_resp;
}

/* ***************************************************************  */

function __oauth2_token_post_setsession($auth_array, $code,
                                        $code_is_refreshtoken)
{
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

  $data = $auth_param. '&client_id=' .$auth_array['client_id']
    .'&client_secret=' .$auth_array['client_secret']. '&redirect_uri='
    .$redirect_uri. '&grant_type=' .$grant_type;

  /* Do not display $data because of the client secret  */
  debug_api_info_incr('cnt_oauth2_auth', 1,
    'Platform: ' .$auth_array['platform']. ' - Refresh-Token: '
    . ($code_is_refreshtoken? 'yes': 'no'));

  $time_stamp = time();
  list($status_ok, $status, $json)
    = http_receive($auth_array['url_token'], 'POST', array(), $data);
  if (!$status_ok) return false;

  $token_resp = json_decode($json, true);
  if (!$token_resp) return false;

  /* Error response described in RFC 6749 section 5.2.
   *
   * We can't really catch this because the Auth Server responses with
   * HTTP 400 (Bad Request).  But in this case file_get_contents() is
   * returning FALSE.
   */
  // TODO: Should now catchable ...
  if (isset($token_resp['error'])) return false;

  if (!session_oauth2token_set($auth_array['platform'], $time_stamp,
                               $token_resp)) return false;

  return true;
}

function oauth2_token_post_setsession($auth_array, $code)
{
  debug_api_info_incr('cnt_' .$auth_array['platform']. '_auth', 1);

  return __oauth2_token_post_setsession($auth_array, $code, false);
}

function _oauth2_refresh_post_setsession($auth_array, $refresh_token)
{
  debug_api_info_incr('cnt_' .$auth_array['platform']. '_refresh', 1);

  return __oauth2_token_post_setsession($auth_array, $refresh_token,
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

function oauth2_token_get($auth_array)
{
  $platform = $auth_array['platform'];

  $tmp = session_oauth2token_get($platform);
  if (!$tmp) {
    _e('oauth2_token_get', '1 Logging user out');
    oauth2_logout($platform);
    return false;
  }
  list($required, $refresh_token) = $tmp;

  if (_oauth2_accesstoken_expired($required)) {

    if (!$refresh_token
        || !_oauth2_refresh_post_setsession($auth_array, $refresh_token)) {
      _e('oauth2_token_get', '2 Logging user out');
      oauth2_logout($platform);
      return false;
    }

    $tmp = session_oauth2token_get($platform);
    if (!$tmp) {
      _e('oauth2_token_get', '3 Logging user out');
      oauth2_logout($platform);
      return false;
    }
    list($required, $refresh_token) = $tmp;
  }

  return array($required['token_type'], $required['access_token']);
}

/* ***************************************************************  */
