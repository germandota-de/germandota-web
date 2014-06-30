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

include_once '../../inc/common.inc.php';

include_once '../themes/' .CONFIG_THEME. '/begin-head.inc.php';
common_print_htmltitle('Live Stream');
include_once '../themes/' .CONFIG_THEME. '/head-title.inc.php';
common_print_title('Live Stream');
include_once '../themes/' .CONFIG_THEME. '/title-content.inc.php';
?>

  <table id="live_videoframe">
    <tr><td>

      <!-- height="378" width="620"  -->
      <!-- Do also change www/default.css  -->

      <object height="480" width="787">
        <param name="movie"
          value="http://www.twitch.tv/widgets/live_embed_player.swf?hostname=www.twitch.tv&amp;channel=germandota&amp;auto_play=true&amp;start_volume=50">
        <param name="allowFullScreen" value="true">
        <param name="allowScriptAccess" value="always">
        <param name="allowNetworking" value="all">
        <param name="bgcolor" value="#000000">
        <embed
          src="http://www.twitch.tv/widgets/live_embed_player.swf?hostname=www.twitch.tv&amp;channel=germandota&amp;auto_play=true&amp;start_volume=50"
          type="application/x-shockwave-flash"
          height="480" width="787"
          allowfullscreen="true" allowscriptaccess="always"
          allownetworking="all" bgcolor="#000000">
      </object>
      <div id="live_videoframe_bottom">
        <a href="http://www.twitch.tv/germandota">GermanDota live auf
          www.twitch.tv</a>
      </div>
    </td><td id="live_videoframe_chat">
      <iframe frameborder="0" scrolling="no"
        src="http://twitch.tv/germandota/chat?popout="

         height="502" width="350"></iframe>
         <!-- Do also change www/default.css -->
    </td></tr>
  </table>

<?
include_once '../themes/' .CONFIG_THEME. '/content-end.inc.php';
