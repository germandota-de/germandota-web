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

/* Error files may be included by other files.  So we need to prefix
 * DIRNAME(__FILE__).
 */
include_once dirname(__FILE__). '/../inc/common.inc.php';

$glob_description
  = 'WTF O_O ...';
include_once dirname(__FILE__)
  .'/../themes/' .CONFIG_THEME. '/begin-head.inc.php';
common_print_htmltitle('404 Not Found', $glob_description);
include_once dirname(__FILE__)
  .'/../themes/' .CONFIG_THEME. '/head-title.inc.php';
common_print_title('404 Not Found');
include_once dirname(__FILE__)
  .'/../themes/' .CONFIG_THEME. '/title-content.inc.php';
?>

  <div class="warning"><p>
    What are you doing O.O ...  Do not enter random strings into the
    address bar of your web browser!
  </p></div>

<?
include_once dirname(__FILE__)
  .'/../themes/' .CONFIG_THEME. '/content-end.inc.php';
