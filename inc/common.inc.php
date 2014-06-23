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

define('COMMON_CONF_FILE', dirname(__FILE__). '/../config.inc.php');
if (!file_exists(COMMON_CONF_FILE)) {
  die('<font color="#ff0000">config.inc.php not found! Copy it from '
      .'config.template.inc.php and make necessary changes on the copy</font>');
}
include_once COMMON_CONF_FILE;

/* ***************************************************************  */

define('COMMON_FIX_YT_LIKELIST',        true);
define('COMMON_USER_NEWLINE',           "\n<br>");

/* ***************************************************************  */

/* Convert all characters for HTML output and return/put to output buffer.  */
function _o_get($str)
{
  return preg_replace('/\n/si', COMMON_USER_NEWLINE,
                      htmlentities($str, ENT_QUOTES, 'UTF-8'));
}
function _o($str)
{
  echo _o_get($str);
}

/* Convert all characters for HTML output, but leave HTML tags plain  */
function _o_html($str)
{
  echo htmlspecialchars_decode(_o_get($str), ENT_QUOTES);
}

function common_print_htmltitle($title)
{
  echo "\n  <title>";
  _o(CONFIG_PROJECT_SITENAME .' - '. $title);
  echo "</title>\n\n";
}

function common_print_title($title, $short=false)
{
  echo "\n  ";
  if (!$short) _o(CONFIG_PROJECT_NAME_SHORT .' ');
  _o($title);
  echo "\n\n";
}

function common_user_output($str, $lines=0)
{
  $str = _o_get($str);

  $str = preg_replace('@(https?://[\S]+)@isu',
                      '<a target="_blank" href="\1">\1</a>', $str);
  $str = preg_replace('@\s(www\.[\S]+)@isu',
                      '<a target="_blank" href="http://\1">\1</a>', $str);
  $str = preg_replace('@(^|\W)\*([^<]*?\**)\*@isu', '\1<b>\2</b>', $str);
  $str = preg_replace('@(^|\W)_([^<]*?_*)_@isu', '\1<i>\2</i>', $str);
  $str = preg_replace('@(^|\W)-([^<]*?-*)-@isu', '\1<del>\2</del>', $str);

  if ($lines <= 0) { echo $str; return; }

  for ($i=0, $cur=0; $i<$lines; $i++) {
    if (!preg_match('@^.*?'.COMMON_USER_NEWLINE.'@su', $str, $matches,
                    PREG_OFFSET_CAPTURE, $cur)) return;

    // TODO
    //$cur += $matches[0][0];

    //var_dump($matches[0][0]);

    echo $matches[0][0];
  }
}
