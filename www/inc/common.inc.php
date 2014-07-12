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

define('COMMON_EXIST',                  true);

session_start();
define('COMMON_SESSION_ID',             session_id());

/* ***************************************************************  */
/* Formats:
 *
 *  * *ROOT: '/var/www/.../'
 *
 *  * *ABS: 'inc/xyz/abc/'
 */

define('COMMON_DIR_INC',      'inc');
define('COMMON_DIR_THEMES',   'themes');
define('COMMON_DIR_IMG',      'img');

/* Is depending on apache config directive DocumentRoot if there is a
 * tailing `/'
 */
define('COMMON_DIR_DOCROOT', $_SERVER['DOCUMENT_ROOT']
       .(preg_match('@/$@', $_SERVER['DOCUMENT_ROOT'])
	 ? '': '/')
       );
define('COMMON_DIR_INSTROOT', realpath(dirname(__FILE__) .'/..'). '/');

define('COMMON_DIR_INST_ABS',
  preg_replace('@^' .COMMON_DIR_DOCROOT. '(.*)$@', '\1',
	       COMMON_DIR_INSTROOT));

define('COMMON_CONF_FILE',     'config.inc.php');
define('COMMON_CONF_FILEROOT', COMMON_DIR_INSTROOT.COMMON_CONF_FILE);

/* ***************************************************************  */

if (!file_exists(COMMON_CONF_FILEROOT)) {
  die('<font color="#ff0000">config.inc.php not found! Copy it from'
      .' config.template.inc.php and make necessary changes on the'
      .' copy</font>');
}
$_common_files = scandir(dirname(COMMON_CONF_FILEROOT));
foreach ($_common_files as $v) {
  if (preg_match('@^' .COMMON_CONF_FILE. '.+@', $v)) {
    die('<font color="#ff0000">config.inc.php backup file `' .$v
	. '\' found!  Delete it, it could be a security issue.</font>');
  }
}

include_once COMMON_CONF_FILEROOT;

/* ***************************************************************  */

define('COMMON_DIR_THEMECUR_ABS',
  COMMON_DIR_INST_ABS.COMMON_DIR_THEMES .'/'. CONFIG_THEME .'/');
define('COMMON_DIR_THEMECUR_IMG_ABS',
  COMMON_DIR_THEMECUR_ABS.COMMON_DIR_IMG. '/');
define('COMMON_DIR_IMG_ABS',
  COMMON_DIR_INST_ABS.COMMON_DIR_IMG .'/');

define('COMMON_FIX_YT_LIKELIST',        true);
define('COMMON_USER_NEWLINE',           "\n<br>");

/* ***************************************************************  */

/* Convert all characters for HTML output and return/put to output buffer.  */
function _o_get($str)
{
  $result = preg_replace('/\n/isu', COMMON_USER_NEWLINE,
                         htmlentities($str, ENT_QUOTES, 'UTF-8'));

  /* Remove UTF-8 Byte Order Marks EF BB BF (sent by ex. Youtube API)  */
  $result = preg_replace("@\xef\xbb\xbf@", '', $result);

  return $result;
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

function common_url_amp($url)
{
  return preg_replace('@&@', '&amp;', $url);
}

/* ***************************************************************  */

/* Format: PThhHmmMssS (example: PT25M2S)  */
function common_time2url($s=0, $min=0, $h=0)
{
  $result = 'PT';

  if ($h > 0) {
    $result .= $h. 'H' .sprintf('%02u', $min). 'M'
      .sprintf('%02u', $s). 'S';
  } else {
    $result .= $min. 'M' .sprintf('%02u', $s). 'S';
  }

  return $result;
}

/* ***************************************************************  */

function common_user_output_htmlin($str, $more_link='', $lines=0,
                                   $time_link='', $time_target='_self')
{
  $str = preg_replace('@([^"])(https?://[\S]+)@isu',
                      '\1<a target="_blank" href="\2">\2</a>', $str);
  $str = preg_replace('@(\s)(www\.[\S]+)@isu',
                      '\1<a target="_blank" href="http://\2">\2</a>', $str);
  $str = preg_replace('@(^|[\s,.:;?!\n])\*(\w[^<>]*?)\*([\s,.:;?!]|$)@isu',
                      '\1<b>\2</b>\3', $str);
  $str = preg_replace('@(^|[\s,.:;?!])_(\w[^<>]*?)_([\s,.:;?!]|$)@isu',
                      '\1<i>\2</i>\3', $str);
  $str = preg_replace('@(^|[\s,.:;?!])-(\w[^<>]*?)-([\s,.:;?!]|$)@isu',
                      '\1<del>\2</del>\3', $str);

  if ($time_link) {
    $time_link_strip = preg_replace('@&t=PT[HMS0-9:]+@', '',
				    $time_link);

    $str = preg_replace('@(^|[\s,.;?!])'
      .'([0-9]{1,2}):([0-9]{2,2})([\s,.;?!]|$)@isu',
      '\1<a target="' .$time_target. '" href="'
      .common_url_amp($time_link_strip). '&amp;t=PT\2M\3S">\2:\3</a>\4',
      $str);
    $str = preg_replace('@(^|[\s,.;?!])'
      .'([0-9]{1,2}):([0-9]{2,2}):([0-9]{2,2})([\s,.;?!]|$)@isu',
      '\1<a target="' .$time_target. '" href="'
      .common_url_amp($time_link_strip). '&amp;t=PT\2H\3M\4S">\2:\3:\4</a>\5',
      $str);
  } // if ($time_link)

  if ($lines <= 0) { echo $str; return; }

  for ($i=0, $cur=0; $i<$lines+1; $i++) {
    if (!preg_match('@^.*?($|<br[^>]*>)@su', substr($str, $cur),
                    $matches)) return;
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
function common_user_output($str, $more_link='', $lines=0,
                            $time_link='', $time_target='_self')
{
  common_user_output_htmlin(_o_get($str), $more_link, $lines,
                            $time_link, $time_target);
}

/* ***************************************************************  */

function common_newline_html($html_str, $chars_per_line)
{
  $result = '';

  $cur_char = 0; $nest_count = 0;
  for ($i=0; $i<strlen($html_str); $i++) {
    $result .= $html_str[$i];

    if ($html_str[$i] == '<') {
      if (substr($html_str, $i, 3) == '<br') $cur_char = 0;
      $nest_count++; continue;
    }
    if ($html_str[$i] == '>') { $nest_count--; continue; }
    if ($nest_count > 0) continue;

    if ($cur_char >= $chars_per_line
        && $html_str[$i] == ' ') {
      $result .= COMMON_USER_NEWLINE;
      $cur_char = 0;
    }

    $cur_char++;
  }

  return $result;
}

/* ***************************************************************  */

function common_url2hostname($url)
{
  return preg_replace('@^http[s]?://(.*?)(/.*)?$@i', '\1', $url);
}

/* $menu_array = array(
 *   'entry1' => array(
 *     'title' => 'Menu entry',
 *     'href' => 'xyz.php?...abc=entry1...',
 *   ),
 *   ...
 * );
 */
function common_menu_print($menu_array, $id, $entry_selected)
{
?>
  <div id="<? echo $id; ?>_position" class="menu_position"><div tabindex="0"<?
    ?> onmousedown="return menu_toggle_check('<? echo $id; ?>');"<?
    ?> onclick="return menu_toggle_do('<? echo $id; ?>');"<?
    ?> id="<? echo $id; ?>" class="menu">
    <?
      if (!isset($menu_array[$entry_selected])) _o($menu_array[0]['title']);
      else _o($menu_array[$entry_selected]['title']);
    ?> <img id="<? echo $id; ?>_dropdown" class="menu_dropdown"<?
    ?> alt="(dropdown)" src="/<? echo COMMON_DIR_THEMECUR_IMG_ABS;
    ?>icon_dropdown.22.png">

    <ul><?

      foreach ($menu_array as $k => $v) {
      ?><li<?
        if ($entry_selected == $k)
          echo ' id="' .$id. '_selected" class="menu_selected"';
      ?>><a href="<? echo $v['href']; ?>"><? _o($v['title']); ?></a></li><?
      } /* foreach ($menu_array as $k => $v)  */

    ?></ul>
  </div></div>

<?
}

/* ***************************************************************  */
