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

include_once '../inc/youtube_auth.inc.php';

include_once '../themes/' .CONFIG_THEME. '/begin-head.inc.php';
common_print_htmltitle('Authenticating ...');
include_once '../themes/' .CONFIG_THEME. '/head-title.frame.inc.php';
common_print_title('Authenticating ...');
include_once '../themes/' .CONFIG_THEME. '/title-content.frame.inc.php';
?>

  <div class="warning">
    Checking your identity using Authentication Server.  Please wait ...
  </div>

<?
include_once '../themes/' .CONFIG_THEME. '/content-end.frame.inc.php';
