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

/* ***************************************************************  */

function session_oauth2login_set($data)
{
  $state = sha1(print_r($data, true));

  $_SESSION[SESSION_PRE_OAUTH2LOGIN .$state] = $data;

  return $state;
}

function session_oauth2login_delete($state)
{
  $i = SESSION_PRE_OAUTH2LOGIN .$state;

  $data = $_SESSION[$i]; unset($_SESSION[$i]);

  return $data;
}

/* ***************************************************************  */
