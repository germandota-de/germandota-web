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

function session_oauth2token_set($platform, $token_resp)
{
  if (!isset($token_resp['access_token'])
      || !isset($token_resp['token_type'])
      || !isset($token_resp['expires_in'])) return false;

  $data = array('access_token' => $token_resp['access_token'],
                'token_type' => $token_resp['token_type'],
                'expires_in' => $token_resp['expires_in'],
                );

  $i = SESSION_PRE_OAUTH2TOKEN .$platform;
  $_SESSION[$i]['required'] = $data;

  if (isset($token_resp['refresh_token']))
    $_SESSION[$i]['refresh_token'] = $token_resp['refresh_token'];

  return true;
}

/* ***************************************************************  */
