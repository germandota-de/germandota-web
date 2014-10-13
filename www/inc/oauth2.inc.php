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

function oauth2_token_post_setsession($url, $client_id, $client_secret,
                                      $platform, $code)
{
  /* Must be application/x-www-form-urlencoded (RFC 6749 section
   * 4.1.3.)
   */
  $redirect_uri = urlencode(_OAUTH2_REDIRECT_URI);

  $data = 'code=' .$code. '&client_id=' .$client_id. '&client_secret='
    .$client_secret. '&redirect_uri=' .$redirect_uri
    .'&grant_type=authorization_code';

  $context  = stream_context_create(array(
    'http' => array(
      'method'  => 'POST',
      'header'  => 'Content-type: application/x-www-form-urlencoded',
      'content' => $data)
  ));

  /* Do not display $data because of the client secret  */
  //debug_api_info_incr('cnt_oauth2_auth', 1, $data);
  debug_api_info_incr('cnt_oauth2_auth', 1);

  $json = file_get_contents($url, false, $context);
  if (!$json) return false;

  $token_resp = json_decode($json, true);
  if (!$token_resp) return false;

  /* Error response described in RFC 6749 section 5.2.
   *
   * We can't really catch this because the Auth Server responses with
   * HTTP 400 (Bad Request).  But in this case file_get_contents() is
   * returning FALSE.
   */
  if (isset($token_resp['error'])) return false;

  if (!session_oauth2token_set($platform, $token_resp)) return false;

  return true;
}

/* ***************************************************************  */
