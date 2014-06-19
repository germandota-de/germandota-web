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
$video_id = isset($_GET['v'])? trim($_GET['v']): '';

/* ***************************************************************  */

/* If video_id was given  */
if ($video_id) {
  $temp = yt_recv_playlist_items_video($list, $video_id);

  $glob_yt_result = $temp['result'];
  $glob_correction = $temp['correction'];
}

/* If video_id not given or fail to find  */
if (!$video_id || !$temp) {
  $glob_correction = -YT_PLVIDEOS_MAXRESULTS_HALF;
  $glob_yt_result = yt_recv_playlist_items($list);
}

/* **-------------------------------------------------------------  */
/* Only videos in playlists of our own channel  */

if ($glob_yt_result && $glob_yt_result['items']
    [YT_PLVIDEOS_MAXRESULTS_HALF+$glob_correction]['snippet']['channelId']
    != CONFIG_YT_CHANNELID) {
  $glob_correction = -YT_PLVIDEOS_MAXRESULTS_HALF;
  $glob_yt_result = false;
}

/* ---------------------------------------------------------------  */

/* Otherwise we are trying the recommend playlist  */
if (!$glob_yt_result) {
  $list = yt_get_recomm_plid();
  $glob_yt_result = yt_recv_playlist_items($list);
}

$glob_yt_plitems = $glob_yt_result['items'];
$glob_yt_videoitem
  = $glob_yt_plitems[YT_PLVIDEOS_MAXRESULTS_HALF+$glob_correction];

/* Override if exist ...  */
$video_id = $glob_yt_videoitem['contentDetails']['videoId'];
$glob_video_plposition = $glob_yt_videoitem['snippet']['position'];

/* ***************************************************************  */

function _page_td($token_name, $dir_str, $i_playlist, $text)
{
  global $list, $glob_yt_plitems, $glob_yt_result;

  if (isset($glob_yt_result[$token_name])) {
?>
    <td class="video_thumbs_table_pagelink"><a <?
    ?>class="video_thumbs_table_pagelink" title="<?
      echo $dir_str .' '. YT_PLVIDEOS_MAXRESULTS_HALF;
    ?> videos" href="./?list=<?
      echo $list .'&amp;v='. $glob_yt_plitems[$i_playlist]
        ['contentDetails']['videoId'];
    ?>"><? echo $text; ?></a></td>
<?
  } else { // if (isset($glob_yt_result['nextPageToken']))
?>
    <td class="video_thumbs_table_pagelink"><span<?
      ?> title="No more videos :("><? echo $text; ?></span></td>
<?
  } // if (isset($glob_yt_result[$token_name]))
}

/* ***************************************************************  */

include_once '../../template/begin-head.inc.php';
?>

  <script type="text/javascript" src="https://apis.google.com/js/platform.js"></script><?
common_print_htmltitle($glob_yt_videoitem['snippet']['title']);
include_once '../../template/head-title.inc.php';
common_print_title($glob_yt_videoitem['snippet']['title']);
include_once '../../template/title-content.inc.php';
?>

  <div id="video_videoframe">
    <iframe width="853" height="480" src="//www.youtube.com/embed/<?
      echo $video_id;
    ?>?rel=0&amp;vq=hd720&amp;autoplay=1<?
      if (isset($glob_yt_videoitem['contentDetails']['startAt']))
        echo '&amp;start='
        .yt_timeat2sec($glob_yt_videoitem['contentDetails']['startAt']);
      if (isset($glob_yt_videoitem['contentDetails']['endAt']))
        echo '&amp;end='
        .yt_timeat2sec($glob_yt_videoitem['contentDetails']['endAt']);
    ?>" frameborder="0" allowfullscreen></iframe>

    <div id="video_videoframe_bottom">
      <table id="video_videoframe_table">
        <tr><td><a target="_blank"
          href="http://www.youtube.com/user/GermanDotaTV">GermanDota</a>
            auf Youtube abonnieren
        </td><td>
          <div class="g-ytsubscribe" data-channel="GermanDotaTV">Abonnieren</div>
        </td></tr>
      </table>
    </div>
  </div>

  <table id="video_thumbs_table">
  <tr><th class="video_thumbs_table_top" colspan="<?
    echo YT_PLVIDEOS_MAXRESULTS+2;
    ?>"><?
    echo 'Video ' .($glob_video_plposition+1).' of '
      .$glob_yt_result['pageInfo']['totalResults'];
    ?></th></tr>
  <tr>
<?
    _page_td('prevPageToken', 'Previous', 0, '&laquo;');

  for ($i=0; $i<count($glob_yt_plitems); $i++) {
    if ($glob_yt_plitems[$i]['status']['privacyStatus'] != 'public')
      continue;
?>
    <td class="<?
      if ($i - YT_PLVIDEOS_MAXRESULTS_HALF == $glob_correction)
        echo 'video_thumbs_table_select';
      else
        echo 'video_thumbs_table';
    ?>"><a class="img_link" title="<?
      _o($glob_yt_plitems[$i]['snippet']['title']);
    ?>" href="./?list=<?
      echo $list .'&amp;v='. $glob_yt_plitems[$i]['contentDetails']['videoId'];
    ?>"><img class="videos_thumbs" alt="(thumb)" src="<?
      echo $glob_yt_plitems[$i]['snippet']['thumbnails']['default']['url'];
    ?>"></a></td>
<?
  } /* for ($i=0, $k=0; $i<count($glob_yt_plitems); $i++)  */

  for (; $i<YT_PLVIDEOS_MAXRESULTS; $i++) {
?>
    <td class="video_thumbs_table"><img class="videos_thumbs" <?
      ?>alt="(thumb)" src="../img/thumb_blackscreen.png"></td>
<?
  } // for (; $i<YT_PLVIDEOS_MAXRESULTS; $i++)

    _page_td('nextPageToken', 'Next', YT_PLVIDEOS_MAXRESULTS-1, '&raquo;');
?>
  </tr>
  <tr>
    <td></td>
<?

  for ($i=0; $i<count($glob_yt_plitems); $i++) {
    if ($glob_yt_plitems[$i]['status']['privacyStatus'] != 'public')
      continue;
?>
    <th class="<?
      if ($i - YT_PLVIDEOS_MAXRESULTS_HALF == $glob_correction)
        echo 'video_thumbs_table_time_selct';
      else
        echo 'video_thumbs_table_time';
    ?>"><span<?
    ?> class="video_thumbs_table_time_no"><?
      echo ($glob_yt_plitems[$i]['snippet']['position']+1) .'.<br>';
    ?></span><?
      $cur_date = $glob_yt_plitems[$i]['snippet']['publishedAt'];
      echo yt_str2date($cur_date) .'<br>'. yt_str2time($cur_date);
    ?></th>
<?
  } /* for ($i=0, $k=0; $i<count($glob_yt_plitems); $i++)  */
?>
    <td></td>
  </tr>
  </table>

<?
include_once '../../template/content-end.inc.php';
