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

include_once '../../template/begin-head.inc.php';
?>

  <link rel="stylesheet" type="text/css" href="fix.css">
  <title>GermanDota.de - Live Stream</title>

<?
include_once '../../template/head-title.inc.php';
?>

  Live Stream

<?
include_once '../../template/title-content.inc.php';
?>
  <div id="live_videoframe">

    <!-- height="378" width="620" -->
    <!-- Do also change www/default.css -->

    <object type="application/x-shockwave-flash"
      height="480" width="787"
      id="live_embed_player_flash"
      data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=germandota"
      bgcolor="#000000">
        <param name="allowFullScreen" value="true">
        <param name="allowScriptAccess" value="always">
        <param name="allowNetworking" value="all">
        <param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf">
        <param name="flashvars" value="hostname=www.twitch.tv&channel=germandota&auto_play=false&start_volume=70">
    </object>
    <div id="live_videoframe_bottom">
      <a href="http://www.twitch.tv/germandota">GermanDota live auf
        www.twitch.tv</a>
    </div>
  </div>
  <div id="live_chatframe">
    <iframe frameborder="0" scrolling="no"
      src="http://twitch.tv/germandota/chat?popout="

       height="501" width="350"></iframe>
       <!-- Do also change www/default.css -->

  </div>

<?
include_once '../../template/content-end.inc.php';
