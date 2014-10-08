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

include_once '../inc/common.inc.php';

include_once '../inc/youtube.inc.php';

$global_pf = isset($_POST['pf'])? trim($_POST['pf']): false;
$global_cb = isset($_POST['cb'])? trim($_POST['cb']): false;

$global_state_get = isset($_GET['state'])? trim($_GET['state']): false;
$global_error = isset($_GET['error'])? trim($_GET['error']): false;
$global_code = isset($_GET['code'])? trim($_GET['code']): false;

/* ***************************************************************  */

function _init_redirect()
{
  global $global_pf, $global_cb;

  /* Set following globals  */
  global $global_args, $global_redirect_link, $global_redirect_href;

  $global_args = array();
  for ($i=0; isset($_POST['arg_' .$i]); $i++) {
    $global_args[$i] = trim($_POST['arg_' .$i]);
  }

  if (!oauth2_callback_callable($global_pf, $global_cb, $global_args))
    return false;

  if ($global_pf == OAUTH2_PLATFORM_YOUTUBE)
    $global_redirect_link = yt_auth_link_get($global_cb, $global_args);
  else
    return false;

  $global_redirect_href = common_url_amp($global_redirect_link);

  return 'redirect';
}

function _init_error()
{
  global $global_state_get, $global_error;

  /* Set following globals  */
  global $global_pf, $global_cb, $global_args, $global_error_msg;

  $tmp = oauth2_login_id2vars($global_state_get);
  if (!$tmp) return false;
  list($global_pf, $global_cb, $global_args) = $tmp;

  $global_error_msg = oauth2_login_2errormsg($global_error);

  return 'error';
}

function _init_auth()
{
  global $global_state_get, $global_code;

  /* Set following globals  */
  global $global_pf, $global_cb, $global_args;

  $tmp = oauth2_login_id2vars($global_state_get);
  if (!$tmp) return false;
  list($global_pf, $global_cb, $global_args) = $tmp;

  // TODO: Request Access & Refresh token && set these into $_SESSION

  return 'auth';
}

/* ***************************************************************  */

if ($global_pf !== false && $global_cb !== false)
  $global_state = _init_redirect();
else if ($global_state_get !== false && $global_error !== false)
  $global_state = _init_error();
else if ($global_state_get !== false && $global_code !== false)
  $global_state = _init_auth();
else
  $global_state = false;

/* ***************************************************************  */

if ($global_state == 'redirect')
  common_http_location_write($global_redirect_link);

/* ***************************************************************  */

include_once '../themes/' .CONFIG_THEME. '/begin-head.inc.php';

if ($global_state == 'redirect') {
  common_print_htmltitle('Redirecting ...');
  common_html_meta_refresh($global_redirect_href);
} else if ($global_state == 'error')
  common_print_htmltitle('Error :( ...');
else if ($global_state == 'auth')
  common_print_htmltitle('Authenticating ...');
else
  common_print_htmltitle('What ???');

include_once '../themes/' .CONFIG_THEME. '/head-title.frame.inc.php';

if ($global_state == 'redirect')
  common_print_title('Redirecting ...');
else if ($global_state == 'error')
  common_print_title('Error :( ...');
else if ($global_state == 'auth')
  common_print_title('Authenticating ...');
else
  common_print_title('What ???');

include_once '../themes/' .CONFIG_THEME. '/title-content.frame.inc.php';
?>

  <div class="warning">
<?

  if ($global_state == 'redirect') {
?>
    Redirecting you to an Authorization Server.  Please wait ...
    <p><span class="oauth2_errmsg">or <a href="<?
      echo $global_redirect_href; ?>">click here</a></span></p>
<?
  } else if ($global_state == 'error') {
?>
    Authorization Server gives an error:
    <p><span class="oauth2_errmsg"><? _o($global_error_msg); ?></span></p>
<?
  } else if ($global_state == 'auth') {
?>
    Checking your identity using Authorization Server.  Please wait ...
    <p><span class="oauth2_errmsg">Not implemented</span></p>
<?
  } else {
?>
    Do not know what you want :((
<?
  }
?>
  </div>
  <div class="oauth2_bottom">
    <a class="oauth2_bottom_close" onclick="return popup_close();"<?
    ?> href="javascript:void(0);">Close Window</a>
  </div>

<?
include_once '../themes/' .CONFIG_THEME. '/content-end.frame.inc.php';
