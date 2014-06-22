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

/* ***************************************************************  */

include_once '../../template/begin-head.inc.php';
common_print_htmltitle('Comments');
include_once '../../template/head-title.comments.inc.php';
common_print_title('Comments', true);
include_once '../../template/title-content.comments.inc.php';
for ($i=0; $i<100; $i++) {
?>

  Hello World!
  <a href="./">Test</a><br>

<?
    }
include_once '../../template/content-end.comments.inc.php';
