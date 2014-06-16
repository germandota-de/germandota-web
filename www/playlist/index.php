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

include_once '../../inc/youtube_api.inc.php';

/* ***************************************************************  */

$page = 1;
$glob_yt_result = yt_get_playlists($page);
if (!$glob_yt_result) die('Error communicating with Youtube :((');

$glob_yt_pageinfo = $glob_yt_result['pageInfo'];
$glob_yt_playlists = $glob_yt_result['items'];

/* ***************************************************************  */

include_once '../../template/begin-head.inc.php';
?>

  <title>GermanDota.de - Refresh</title>

<?
include_once '../../template/head-title.inc.php';
?>

  GermanDota Playlists

<?
include_once '../../template/title-content.inc.php';
?>

  <table>
<?

  for ($i=0; $i<count($glob_yt_playlists); $i++) {
    if ($glob_yt_playlists[$i]['status']['privacyStatus'] != 'public')
      continue;
?>
  <tr>
    <td><img alt="(icon)" src="<?
      echo $glob_yt_playlists[$i]['snippet']['thumbnails']['medium']['url'];
    ?>"></td>
  </tr>
<?
  } /* for ($i=0; $i<count($glob_yt_playlists); $i++)  */

?>
  </table>

<?
include_once '../../template/content-end.inc.php';
