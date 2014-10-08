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

/* ***************************************************************  */

$global_state = false;
if ($global_pf !== false && $global_cb !== false)
  $global_state = _init_redirect();

/* ***************************************************************  */

if ($global_state == 'redirect')
  header('Location: ' .$global_redirect_link);

/* ***************************************************************  */

include_once '../themes/' .CONFIG_THEME. '/begin-head.inc.php';

if ($global_state == 'redirect') {
  common_print_htmltitle('Redirecting ...');
  echo '  <meta http-equiv="refresh" content="0; url='
    .$global_redirect_href. '">';
  echo "\n";
} else if ($global_state == 'auth')
  common_print_htmltitle('Authenticating ...');
else
  common_print_htmltitle('What ???');

include_once '../themes/' .CONFIG_THEME. '/head-title.frame.inc.php';

if ($global_state == 'redirect')
  common_print_title('Redirecting ...');
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
    <p>Redirecting you to an Authentication Server.  Please wait ...</p>
    or <a href="<? echo $global_redirect_href; ?>">click here</a>
<?
  } else if ($global_state == 'auth') {
?>
    Checking your identity using Authentication Server.  Please wait ...
<?
  } else {
?>
    Do not know what you want :((
<?
  }
?>
  </div>

<?
include_once '../themes/' .CONFIG_THEME. '/content-end.frame.inc.php';
