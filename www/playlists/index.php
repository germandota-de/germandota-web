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

include_once '../inc/youtube.inc.php';

$page_token = isset($_GET['p'])? trim($_GET['p']): '';

/* ***************************************************************  */

$glob_yt_result = yt_recv_playlists($page_token);

/* On error we are trying the first page  */
if (!$glob_yt_result) {
  $page_token = '';
  $glob_yt_result = yt_recv_playlists($page_token);
}

$state_first_page = !isset($glob_yt_result['prevPageToken']);
$glob_yt_playlists = $glob_yt_result['items'];

if ($state_first_page) {
  $temp = yt_recv_playlists('', yt_get_likedlist_plid());
  $glob_liked_list = $temp['items'][0];
}

/* ***************************************************************  */

include_once '../themes/' .CONFIG_THEME. '/begin-head.inc.php';
common_print_htmltitle('Playlists');
include_once '../themes/' .CONFIG_THEME. '/head-title.inc.php';
common_print_title('Playlists');
include_once '../themes/' .CONFIG_THEME. '/title-content.inc.php';
?>

  <table id="lists_table">
<?
  if (!$state_first_page) {
?>
  <tr><th colspan="3"><?
    yt_print_pageinfo($glob_yt_result, 'playlists', './');
  ?></th></tr>
<?
  }

  for ($i=-1, $k=0; $i<count($glob_yt_playlists); $i++) {
    if ($i<0) {
      if (!$state_first_page) continue;

      $cur_privacy = $glob_liked_list['status']['privacyStatus'];
      $cur_id = $glob_liked_list['id'];
      $cur_thumb_url = $glob_liked_list['snippet']['thumbnails']['medium']['url'];
      $cur_videos_count = $glob_liked_list['contentDetails']['itemCount'];
      $cur_title = $glob_liked_list['snippet']['title'];
      $cur_published = $glob_liked_list['snippet']['publishedAt'];
      $cur_description = $glob_liked_list['snippet']['description'];
    } else {
      $cur_privacy = $glob_yt_playlists[$i]['status']['privacyStatus'];
      $cur_id = $glob_yt_playlists[$i]['id'];
      $cur_thumb_url = $glob_yt_playlists[$i]['snippet']['thumbnails']['medium']['url'];
      $cur_videos_count = $glob_yt_playlists[$i]['contentDetails']['itemCount'];
      $cur_title = $glob_yt_playlists[$i]['snippet']['title'];
      $cur_published = $glob_yt_playlists[$i]['snippet']['publishedAt'];
      $cur_description = $glob_yt_playlists[$i]['snippet']['description'];
    }

    if ($cur_privacy != 'public') continue;
    $k++;
?>
  <tr<? if ($k%2 == 0) echo ' class="lists_table_tr2"'; ?>>
    <td class="lists_table_thumb"><a class="img_link"<?
    ?> title="Watch playlist" href="../watch/?list=<?
      echo $cur_id;
    ?>"><img class="lists_table_thumb" alt="(thumb)" src="<?
      echo $cur_thumb_url;
    ?>"></a></td>
    <td class="lists_table_videocount"><?
      _o($cur_videos_count);
    ?> Videos<p class="lists_table_videocount"><?
      echo yt_str2date_html($cur_published) .'<br>'
        . yt_str2time_html($cur_published);
    ?></p></td>
    <td class="lists_table_text"><a class="playlist_link"<?
    ?> title="Watch playlist" href="../watch/?list=<?
      echo $cur_id;
    ?>"><img class="icon_large" alt="(video)" src="/<?
      echo COMMON_DIR_THEMECUR_IMG_ABS;
    ?>icon_video.32.png"><span class="icon_text"><?
      _o($cur_title);
    ?></span></a><div class="description lists_table_text_descr"><?
      if (!$cur_description)
        _o('The playlist of '.$cur_title. '.');
      else
        _o($cur_description);
    ?></div></td>
  </tr>
<?
  } /* for ($i=0; $i<count($glob_yt_playlists); $i++)  */
?>
  <tr><th colspan="3"><?
    yt_print_pageinfo($glob_yt_result, 'playlists', './');
  ?></th></tr>
  </table>

<?
include_once '../themes/' .CONFIG_THEME. '/content-end.inc.php';
