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

$list = isset($_GET['list'])? trim($_GET['list']): yt_get_recomm_plid();

/* ***************************************************************  */

$glob_yt_result = yt_recv_playlist_items($list);

/* On error we are trying the first page  */
if (!$glob_yt_result) {
  $list = yt_get_recomm_plid();
  $glob_yt_result = yt_recv_playlist_items($list);
}

$glob_yt_plitems = $glob_yt_result['items'];

/* ***************************************************************  */

include_once '../../template/begin-head.inc.php';
common_print_htmltitle('Video');
include_once '../../template/head-title.inc.php';
common_print_title('Video');
include_once '../../template/title-content.inc.php';
?>

  <table id="video_thumbs_table">
  <tr><th class="video_thumbs_table_top" colspan="<?
    echo count($glob_yt_plitems);
    ?>"><?
    echo 'Video TODO (select)' .' of '
      .$glob_yt_result['pageInfo']['totalResults'];
    ?></th></tr>
  <tr>
<?

  for ($i=0; $i<count($glob_yt_plitems); $i++) {
    if ($glob_yt_plitems[$i]['status']['privacyStatus'] != 'public')
      continue;
?>
    <td><a class="img_link" title="<?
      _o($glob_yt_plitems[$i]['snippet']['title']);
    ?>" href="./?list=<?
      echo $list .'&amp;v='. $glob_yt_plitems[$i]['contentDetails']['videoId'];
    ?>"><img class="videos_thumbs" alt="(thumb)" src="<?
      echo $glob_yt_plitems[$i]['snippet']['thumbnails']['default']['url'];
    ?>"></a></td>
<?
  } /* for ($i=0, $k=0; $i<count($glob_yt_plitems); $i++)  */
?>
  </tr>
<?

  for ($i=0; $i<count($glob_yt_plitems); $i++) {
    if ($glob_yt_plitems[$i]['status']['privacyStatus'] != 'public')
      continue;
?>
    <th class="video_thumbs_table_time"><?
       $cur_date = $glob_yt_plitems[$i]['snippet']['publishedAt'];
      echo yt_str2date($cur_date) .'<br>'. yt_str2time($cur_date);
    ?></th>
<?
  } /* for ($i=0, $k=0; $i<count($glob_yt_plitems); $i++)  */
?>
  <tr>
  </tr>
  </table>
  <pre><code><? var_dump($glob_yt_plitems); ?></code></pre>

<?
include_once '../../template/content-end.inc.php';
