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

function _init_error($state, $error, $ignore_state=false)
{
  /* Set following globals  */
  global $global_error_msg;

  if (!$ignore_state) {
    $tmp = oauth2_login_id2vars($state);
    if (!$tmp) return false;
  }

  $global_error_msg = oauth2_login_2errormsg($error);

  return 'error';
}

function _init_auth($state, $code,
                    $platform=false, $callback=false, $args=false)
{
  /* Set following globals  */
  global $global_title, $global_html_output, $global_js_onload;

  if (!$platform || !$callback || !$args) {
    $tmp = oauth2_login_id2vars($state);
    if (!$tmp) return false;
    list($platform, $callback, $args) = $tmp;

    /* Not really necessary, because session will not set in that
     * case.
     */
    if (!oauth2_callback_callable($platform, $callback, $args))
      return false;

    /* Do only send code request if $state is valid  */
    $session_result
      = oauth2_token_post_setsession($platform, $code);

    if (is_string($session_result))
      return _init_error(false, $session_result, true);
    else if (!$session_result)
      return _init_error(false, 'Could not check your identity :(', true);
  }

  $tmp = oauth2_callback_call($platform, $callback, $args);
  if (!$tmp) return false;
  list($global_title, $global_html_output, $global_js_onload) = $tmp;

  return 'auth';
}

function _init_redirect($platform, $callback)
{
  /* Set following globals  */
  global $global_args, $global_redirect_link, $global_redirect_href;

  $global_args = array();
  for ($i=0; isset($_POST['arg_' .$i]); $i++) {
    $global_args[$i] = trim($_POST['arg_' .$i]);
  }

  if (oauth2_logged_in($platform))
    return _init_auth(false, false, $platform, $callback, $global_args);

  /* ---  */

  if (!oauth2_callback_callable($platform, $callback, $global_args))
    return false;

  $global_redirect_link =
    oauth2_login_urlget_setsession($platform, $callback, $global_args);

  $global_redirect_href = common_url_amp($global_redirect_link);

  return 'redirect';
}

/* ***************************************************************  */

if ($global_pf !== false && $global_cb !== false)
  $global_state = _init_redirect($global_pf, $global_cb);
else if ($global_state_get !== false && $global_error !== false)
  $global_state = _init_error($global_state_get, $global_error);
else if ($global_state_get !== false && $global_code !== false)
  $global_state = _init_auth($global_state_get, $global_code);
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
else if ($global_state == 'auth') {
  common_print_htmltitle($global_title);
  common_html_js_onload($global_js_onload);
} else
  common_print_htmltitle('What ???');

include_once '../themes/' .CONFIG_THEME. '/head-title.frame.inc.php';

if ($global_state == 'redirect')
  common_print_title('Redirecting ...');
else if ($global_state == 'error')
  common_print_title('Error :( ...');
else if ($global_state == 'auth')
  common_print_title($global_title);
else
  common_print_title('What ???');

include_once '../themes/' .CONFIG_THEME. '/title-content.frame.inc.php';
?>

  <div id="oauth2_main">
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
    _o_html('    ' .$global_html_output. "\n");
  } else {
?>
    Do not know what you want :((
    <p><span class="oauth2_errmsg">Cookies enabled?</span></p>
<?
  }
?>
  </div>
  <div id="oauth2_bottom">
    <a id="oauth2_bottom_close" onclick="return popup_close();"<?
    ?> href="javascript:void(0);">Close Window</a>
  </div>

<?
include_once '../themes/' .CONFIG_THEME. '/content-end.frame.inc.php';
