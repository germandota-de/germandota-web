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

function common_user_output($str, $more_link='', $lines=0)
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

  for ($i=0, $cur=0; $i<$lines+1; $i++) {
    if (!preg_match('@^.*?($|' .COMMON_USER_NEWLINE. ')@su',
                    substr($str, $cur), $matches)) return;
    if (strlen($matches[0]) == 0) return;

    if ($i == $lines) {
      echo '<a class="useroutput_more" title="Show full text" href="'
        .$more_link. '"> ... (more)</a>';
      return;
    }

    $cur += strlen($matches[0]);

    echo $matches[0];
  }
}

function common_url2hostname($url)
{
  return preg_replace('@^http[s]?://(.*?)(/.*)?$@', '\1', $url);
}

/* $menu_array = array(
 *   'entry1' => array(
 *     'title' => 'Menu entry',
 *     'href' => 'xyz.php?...abc=entry1...',
 *   ),
 *   ...
 * );
 */
function common_menu_print($menu_array, $class, $entry_selected)
{
?>
  <div class="menu <? echo $class; ?>">
    <?
  if (!isset($menu_array[$entry_selected])) _o($menu_array[0]['title']);
  else _o($menu_array[$entry_selected]['title']);
    ?>

    <ul><?

  foreach ($menu_array as $k => $v) {
    ?><li><a href="<? echo $v['href']; ?>"><? _o($v['title']); ?></a></li><?
  } /* foreach ($menu_array as $k => $v)  */

    ?></ul>
  </div>

<?
}
