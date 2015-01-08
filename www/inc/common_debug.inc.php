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

define('DEBUG',                         false);

/* ***************************************************************  */

define('DEBUG_DETAILS',                 true);
define('DEBUG_NO_REDIRECT',             true);
define('DEBUG_NO_JS_ONLOAD',            true);

/* ***************************************************************  */

if (!DEBUG) {

function debug_api_info_set($prop, $val) {}
function debug_api_info_incr($prop, $val=1) {}
function debug_api_info_print($prop=false) {}

} else { /* if (!DEBUG)  */
/* ***************************************************************  */

$_debug_api_info
  = array('cnt_http'                    => 0,
          'cnt_google_api'              => 0,
          'cnt_youtube_api_v2'          => 0,
          '---'                         => 0,
          'cnt_youtube_list'            => 0,
          'cnt_youtube_rate'            => 0,
          'cnt_google_plus'             => 0,
          '+++'                         => 0,
          'cnt_oauth2_auth'             => 0,
          'cnt_youtube_token'           => 0,
          'cnt_youtube_refresh'         => 0,
          );

$_debug_stack = array();

function debug_api_info_set($prop, $val, $details='<set>')
{
  global $_debug_api_info, $_debug_stack;

  $_debug_api_info[$prop] = $val;

  $_debug_stack[count($_debug_stack)]
    = $prop. '(' .$_debug_api_info[$prop]. '): ' .$details;
}

function debug_api_info_incr($prop, $val=1, $details='<incremented>')
{
  global $_debug_api_info, $_debug_stack;

  $_debug_api_info[$prop] += $val;

  $_debug_stack[count($_debug_stack)]
    = $prop. '(' .$_debug_api_info[$prop]. '): ' .$details;
}

function debug_api_info_print($details=false)
{
  global $_debug_api_info, $_debug_stack;

?>

  <table cellpadding="3" style="text-align: left;">
    <tr><th>Debug Property</th><th>Value</th></tr>
<?

  foreach ($_debug_api_info as $k => $v) {
?>
    <tr><td><? echo $k; ?></td><td><? _o('`' .$v. '\''); ?></td></tr>
<?
  } /* foreach ($tmp_array as $k => $v)  */

?>
  </table>

<?

  if (!DEBUG_DETAILS) return;

?>
  <table cellpadding="3" style="text-align: left;">
    <tr><th>No.</th><th>Details</th></tr>
<?

  foreach ($_debug_stack as $k => $v) {
?>
    <tr><td><? echo $k; ?></td><td><? _o($v); ?></td></tr>
<?
  } /* foreach ($tmp_array as $k => $v)  */

?>
  </table>

<?

}

} /* else if (!DEBUG)  */
/* ***************************************************************  */
