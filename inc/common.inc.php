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

/* ***************************************************************  */

/* Convert all characters for HTML output and put to output buffer.  */
function _o($str)
{
  echo preg_replace('/\n/si', '<br>',
                    htmlentities($str, ENT_QUOTES, 'UTF-8'));
}

/* Convert all characters for HTML output, but leave HTML tags plain  */
function _o_html($str)
{
  echo htmlspecialchars_decode(
    preg_replace('/\n/si', '<br>', htmlentities($str, ENT_QUOTES, 'UTF-8'))
    , ENT_QUOTES);
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
