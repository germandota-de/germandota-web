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

include_once '../../../template/begin-head.inc.php';
?>

  <script type="text/javascript" src="https://apis.google.com/js/platform.js"></script>
  <title>GermanDota.de - Dota 2 Playlist</title>

<?
include_once '../../../template/head-title.inc.php';
?>

  Dota 2 Playlist

<?
include_once '../../../template/title-content.inc.php';
?>

  <div id="playlist_videoframe">
    <iframe width="853" height="480"
      src="//www.youtube.com/embed/videoseries?list=PLNn2VtDvrJFjyeE1NiSv3uYguvzEnQ_19&amp;vq=hd720"
      frameborder="0" allowfullscreen></iframe>

    <div id="playlist_videoframe_bottom">
      <table id="playlist_videoframe_table">
        <tr><td><a target="_blank"
          href="http://www.youtube.com/user/GermanDotaTV">GermanDota</a>
            auf Youtube abonnieren
        </td><td>
          <div class="g-ytsubscribe" data-channel="GermanDotaTV">Abonnieren</div>
        </td></tr>
      </table>
    </div>
  </div>

<?
include_once '../../../template/content-end.inc.php';
