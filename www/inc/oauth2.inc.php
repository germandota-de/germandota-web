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

function oauth2_login_url_get($url_pre, $client_id, $scope, $url_post,
                              $callback, $args)
{
  /* Must be application/x-www-form-urlencoded (RFC 6749 section
   * 3.1.2.)
   */
  $redirect_uri = urlencode(_OAUTH2_REDIRECT_URI);

  $id = session_oauth2login_set($callback, $args);

  return $url_pre. '?response_type=code'
    .'&client_id=' .CONFIG_GOOGLE_CLIENT_ID
    .'&redirect_uri=' .$redirect_uri .'&scope=' .$scope
    .'&state=' .$id. $url_post;
}

/* ***************************************************************  */
