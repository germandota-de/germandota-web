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

session_start();
define('SESSION_ID',                    session_id());

define('SESSION_PRE_OAUTH2LOGIN',       'oauth2_login_');
define('SESSION_PRE_OAUTH2TOKEN',       'oauth2_token_');

/* ***************************************************************  */

function session_oauth2login_set($platform, $callback, $args)
{
  $data = array(
    'platform' => $platform, 'callback' => $callback, 'args' => $args
  );

  $id = sha1(print_r($data, true));
  $_SESSION[SESSION_PRE_OAUTH2LOGIN .$id] = $data;

  return $id;
}

function session_oauth2login_delete($id)
{
  $i = SESSION_PRE_OAUTH2LOGIN .$id;

  if (!isset($_SESSION[$i])) return false;
  $data = $_SESSION[$i]; unset($_SESSION[$i]);

  return array($data['platform'], $data['callback'], $data['args']);
}

/* ***************************************************************  */

define('_SESSION_OAUTH2TOKEN_SET_EXPIRESDEF_S',   '900');
function session_oauth2token_set($platform, $time_stamp, $token_resp)
{
  if (!isset($token_resp['access_token'])
      || !isset($token_resp['token_type'])) return false;

  /* We are currently only support tokens of type 'Bearer'  */
  if ($token_resp['token_type'] != 'Bearer') return false;

  /* EXPIRES_IN is optional, see RFC 6749 section 4.2.2.  */
  $expires_in = isset($token_resp['expires_in'])
    ? $token_resp['expires_in']: _SESSION_OAUTH2TOKEN_SET_EXPIRESDEF_S;

  $data = array('access_token' => $token_resp['access_token'],
                'token_type' => $token_resp['token_type'],
                'expires_in' => $expires_in,
                'time_stamp' => $time_stamp,
                );

  $i = SESSION_PRE_OAUTH2TOKEN .$platform;
  $_SESSION[$i]['required'] = $data;

  if (isset($token_resp['refresh_token']))
    $_SESSION[$i]['refresh_token'] = $token_resp['refresh_token'];

  return true;
}

function session_oauth2token_get($platform)
{
  $i = SESSION_PRE_OAUTH2TOKEN .$platform;

  if (!isset($_SESSION[$i])) return false;
  $required = $_SESSION[$i]['required'];

  $refresh_token = isset($_SESSION[$i]['refresh_token'])
    ? $_SESSION[$i]['refresh_token']: false;

  return array($required, $refresh_token);
}

/* ***************************************************************  */
