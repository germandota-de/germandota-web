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

include_once '../../inc/youtube_api_comments.inc.php';

$video_id = isset($_GET['v'])? trim($_GET['v']): '';

$glob_comments = yt_comments_recv($video_id, 0);
$glob_results = $glob_comments['results'];

/* ***************************************************************  */

include_once '../../template/begin-head.inc.php';
common_print_htmltitle('Comments (' .$glob_comments['totalResults']. ')');
include_once '../../template/head-title.comments.inc.php';
common_print_title('Comments (' .$glob_comments['totalResults']. ')', true);
include_once '../../template/title-content.comments.inc.php';
?>

  <table>
<?
  for ($i=0; $i<count($glob_results); $i++) {
?>
  <tr>
    <td><span class="comments_nick"><?
      _o($glob_results[$i]['author'][0]['name']['$t']);
    ?></span></td>
  </tr>
<?
  } // for ($i=0; $i<count($glob_comments); $i++)
?>
  </table>

<?
include_once '../../template/content-end.comments.inc.php';
