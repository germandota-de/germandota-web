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

include_once '../themes/' .CONFIG_THEME. '/begin-head.inc.php';
common_print_htmltitle('About');
include_once '../themes/' .CONFIG_THEME. '/head-title.inc.php';
common_print_title('About');
include_once '../themes/' .CONFIG_THEME. '/title-content.inc.php';
?>

  <div class="textblock">
    <p>Diese Site ist eine Zusammenarbeit der Community.  Die Inhalte
       werden größten Teils von dieser erstellt.</p>
    <p>Und so kannst du helfen:</p>
    <table class="default_table">
       <tr><th>Videos gucken :D</th><td>Ist das aller wichtigste!
         Denn nur so wirst<br>du ein ruhiges und sorgenfreies Leben führen
         ;P</td></tr>
       <tr><th>Bugs, Verbesserungen, Ideen</th><td><a target="_blank"
        href="https://github.com/germandota-de/germandota-web/issues">Kannst
        du hier rein schreiben</a></td></tr>
      <tr><th>Selber coden</th><td><a target="_blank"
        href="https://github.com/germandota-de/germandota-web">Im Code
          herum stöbern und herum probieren</a></td></tr>
    </table>
    <br><br>
    <hr>
    <table id="about_person">
    <tr>
      <td><b>Contact</b><br><?
        _o_html(CONFIG_ABOUT_CONTACT_HTML);
      ?></td>
      <td><b>Address</b><br><?
        _o_html(CONFIG_ABOUT_ADDRESS_HTML);
      ?></td>
    </tr>
    </table>
  </div>

<?
include_once '../themes/' .CONFIG_THEME. '/content-end.inc.php';
