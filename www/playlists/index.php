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

$glob_yt_result = yt_get_playlists('');
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

  <table id="lists_table">
<?

    for ($i=0, $k=0; $i<count($glob_yt_playlists); $i++) {
    if ($glob_yt_playlists[$i]['status']['privacyStatus'] != 'public')
      continue;
    $k++;

    $cur_id = $glob_yt_playlists[$i]['id'];
    $cur_title = $glob_yt_playlists[$i]['snippet']['title'];
    $cur_published = $glob_yt_playlists[$i]['snippet']['publishedAt'];
?>
  <tr<? if ($k%2 == 0) echo ' class="lists_table_tr2"'; ?>>
    <td class="lists_table_thumb"><a class="img_link"<?
    ?> title="Watch playlist" href="./?list=<?
      echo $cur_id;
    ?>"><img class="lists_table_thumb" alt="(icon)" src="<?
      echo $glob_yt_playlists[$i]['snippet']['thumbnails']['medium']['url'];
    ?>"></a></td>
    <td class="lists_table_videocount"><?
      _o($glob_yt_playlists[$i]['contentDetails']['itemCount']);
    ?> Videos<p class="lists_table_videocount"><?
      echo yt_str2date($cur_published) .'<br>'. yt_str2time($cur_published);
    ?></p></td>
    <td class="lists_table_text"><a class="playlist_link"<?
    ?> title="Watch playlist" href="./?list=<?
      echo $cur_id;
    ?>"><?
      _o($cur_title);
    ?></a><div class="lists_table_text_descr"><?
      _o($glob_yt_playlists[$i]['snippet']['description']);
    ?></div></td>
  </tr>
<?
  } /* for ($i=0; $i<count($glob_yt_playlists); $i++)  */

?>
  </table>

<?
include_once '../../template/content-end.inc.php';
