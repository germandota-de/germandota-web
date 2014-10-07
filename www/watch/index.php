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

$list = isset($_GET['list'])? trim($_GET['list']): '';
$video_id = isset($_GET['v'])? trim($_GET['v']): '';
$video_start = isset($_GET['t'])? trim($_GET['t']): '';

/* ***************************************************************  */
/* Try:
 *
 * 1. Video in playlist
 *
 * 2. Video without playlist
 *
 * 3. First video in playlist
 *
 * 4. check permissions
 *
 * 5. First in Liked playlist
 */

$temp_plist = false;
$temp_video = false;

/* If video_id was given  */
if ($video_id) {
  if ($list)
    $temp_plist = yt_recv_playlist_items_video($list, $video_id);

  /* If video not in playlist, load without playlist  */
  if (!$temp_plist) $temp_video = yt_recv_video($video_id);
}

/* If video_id not given or failed to find in playlist  */
if (!$temp_plist && !$temp_video) {
  if (!$list) $list = yt_get_likedlist_plid();
  $temp_plist = yt_recv_playlist_items($list);
  $temp_video = false;
}

/* ---------------------------------------------------------------  */
/* Only videos in playlists of our own channel || or our own videos  */

if (($temp_plist && $temp_plist['result']['items']
     [YT_PLVIDEOS_MAXRESULTS_HALF+$temp_plist['correction']]
     ['snippet']['channelId'] != CONFIG_YT_CHANNELID)
    || ($temp_video && $temp_video['items'][0]['snippet']['channelId']
        != CONFIG_YT_CHANNELID)
    ) {

  if (!common_server_is_localhost()) {  /* For development purposes  */
    $temp_plist = false;
    $temp_video = false;
  }
}

/* ---------------------------------------------------------------  */
/* If either no playlist or no video then we are falling back to liked
 * list
 */

if (!$temp_plist && !$temp_video) {
  $list = yt_get_likedlist_plid();
  $temp_plist = yt_recv_playlist_items($list);
  $temp_video = false;
}

/* $temp_plist (logical)-OR $temp_video is set now  */
/* ***************************************************************  */

$glob_yt_result = $temp_plist['result'];
$glob_correction = $temp_plist['correction'];

$glob_yt_plitems = $glob_yt_result['items'];
$glob_yt_videoitem
  = $glob_yt_plitems[YT_PLVIDEOS_MAXRESULTS_HALF+$glob_correction];

if (!$temp_video) {
  /* $temp_plist is set  */
  $video_id = $glob_yt_videoitem['contentDetails']['videoId'];
}
$glob_video_plposition = $glob_yt_videoitem['snippet']['position'];

/* ***************************************************************  */

if ($temp_plist) {
  $temp = yt_recv_playlist_short($list); $glob_yt_list = $temp['items'][0];
} else {
  $glob_yt_list = false;
}

if ($temp_video) {
  $glob_yt_video = $temp_video['items'][0];
} else {
  $temp = yt_recv_video($video_id); $glob_yt_video = $temp['items'][0];
}

/* ***************************************************************  */

function _page_td($token_name, $dir_str, $i_playlist, $text)
{
  global $list, $glob_yt_plitems, $glob_yt_result,
    $glob_video_plposition, $glob_correction;

  if (isset($glob_yt_result[$token_name])) {
?>
    <td class="video_thumbs_table_pagelink"><a<?
    ?> class="video_thumbs_table_pagelink" title="<?
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

include_once '../themes/' .CONFIG_THEME. '/begin-head.inc.php';
?>

  <script type="text/javascript" src="https://apis.google.com/js/platform.js"></script><?
common_print_htmltitle(
  ($glob_yt_list? '[' .$glob_yt_list['snippet']['title']. '] '
   .($glob_video_plposition+1). '. ': '')
  .$glob_yt_video['snippet']['title']);
include_once '../themes/' .CONFIG_THEME. '/head-title.inc.php';
common_print_title(($glob_video_plposition+1)
                   .'. '. $glob_yt_video['snippet']['title'], true);
include_once '../themes/' .CONFIG_THEME. '/title-content.inc.php';
?>

  <div id="video_videoframe">
    <iframe width="853" height="480" src="//www.youtube.com/embed/<?
      echo $video_id;
    ?>?rel=0&amp;vq=hd720&amp;autoplay=1<?
      if ($video_start) {
        echo '&amp;start='
        .yt_timeat2sec($video_start);
      } else if (isset($glob_yt_videoitem['contentDetails']['startAt'])) {
        echo '&amp;start='
        .yt_timeat2sec($glob_yt_videoitem['contentDetails']['startAt']);
      }

      if (isset($glob_yt_videoitem['contentDetails']['endAt']))
        echo '&amp;end='
        .yt_timeat2sec($glob_yt_videoitem['contentDetails']['endAt']);
    ?>" frameborder="0" allowfullscreen></iframe>

    <div id="video_videoframe_bottom">
      <a name="description"></a>
      <table id="video_videoframe_table">
      <tr>
        <td class="video_videoframe_table_small">&nbsp;&nbsp;&nbsp;<?
          echo yt_timeat2readable($glob_yt_video['contentDetails']
                                  ['duration']);
          ?></td>
        <td class="video_videoframe_table_small">&nbsp;&nbsp;&nbsp;<?
          ?><span class="video_videoframe_table_stats"><?
          echo number_format($glob_yt_video['statistics']['viewCount'],
                             0, ',', '.');
          ?> Views</span></td>
        <td class="video_videoframe_table_small">&nbsp;&nbsp;&nbsp;<?
        yt_auth_print_form('video_videoframe_table_icon', 'like',
            'Like it!', 'yt_video_like', array($video_id),
            '/' .COMMON_DIR_THEMECUR_IMG_ABS. 'icon_like.32.png'); ?></td>
        <td class="video_videoframe_table_small"><?
          echo number_format($glob_yt_video['statistics']['likeCount'],
                             0, ',', '.');
        ?></td>
        <td class="video_videoframe_table_small">&nbsp;&nbsp;&nbsp;<?
          ?><img class="video_videoframe_table_icon" alt="(comments)"<?
          ?> src="/<?
            echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_comment.32.png"></td>
        <td class="video_videoframe_table_small"><?
          echo number_format($glob_yt_video['statistics']['commentCount'],
                             0, ',', '.');
        ?></td>
        <td class="video_videoframe_table_small">&nbsp;&nbsp;&nbsp;<?
          ?><img class="video_videoframe_table_icon" alt="(dislikes)"<?
          ?> src="/<?
            echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_dislike.32.png"></td>
        <td class="video_videoframe_table_small"><?
          echo number_format($glob_yt_video['statistics']['dislikeCount'],
                             0, ',', '.');
        ?></td>
        <td class="video_videoframe_table_small">&nbsp;&nbsp;&nbsp;<?
          ?><span class="video_videoframe_table_date"><?
          _o(yt_str2date($glob_yt_video['snippet']['publishedAt']) .', '
             .yt_str2time($glob_yt_video['snippet']['publishedAt']));
        ?></span></td>
        <td></td>
        <td class="video_videoframe_table_small">Subscribe <?
          yt_print_chanlink($glob_yt_video['snippet']['channelTitle'],
                            $glob_yt_video['snippet']['channelId']);
          ?> on
        </td><td class="video_videoframe_table_small"><?
          yt_print_subscribe($glob_yt_video['snippet']['channelId']);
        ?></td>
      </tr>
      </table>
    </div>
  </div>

<?

  /* *************************************************************  */

  if ($glob_yt_list) {
?>
  <table id="video_thumbs_table">
  <tr><th class="video_thumbs_table_top" colspan="<?
    echo YT_PLVIDEOS_MAXRESULTS+2;
  ?>">Video <?
    echo ($glob_video_plposition+1).' of '
      .$glob_yt_result['pageInfo']['totalResults'];
    ?> - <span class="video_thumbs_table_top_time"><?
    _o($glob_yt_list['snippet']['title']
       .' ('.yt_str2date($glob_yt_list['snippet']['publishedAt'])
       .', ' .yt_str2time($glob_yt_list['snippet']['publishedAt'])
       .')');
    ?></span></th></tr>
  <tr>
<?
    _page_td('prevPageToken', 'Previous', 0, '&laquo;');

  for ($i=0; $i<count($glob_yt_plitems); $i++) {
    /* Show noise thumbnail (comment #1) ...
     *
     * if ($glob_yt_plitems[$i]['status']['privacyStatus'] != 'public')
     *   continue;
     */

    $cur_snippet = $glob_yt_plitems[$i]['snippet'];
?>
    <td class="<?
      if ($i - YT_PLVIDEOS_MAXRESULTS_HALF == $glob_correction)
        echo 'video_thumbs_table_select';
      else
        echo 'video_thumbs_table';
    ?>"><a class="img_link" title="<?
      _o($cur_snippet['title']);
    ?>" href="./?list=<?
      echo $list .'&amp;v='. $glob_yt_plitems[$i]['contentDetails']['videoId'];
    ?>"><img class="videos_thumbs" alt="(thumb)" src="<?
      if (!isset($cur_snippet['thumbnails']['default']['url']))
        echo '/' .COMMON_DIR_THEMECUR_IMG_ABS. 'thumb_noise.120.90.png';
      else
        echo $cur_snippet['thumbnails']['default']['url'];
    ?>"></a></td>
<?
  } /* for ($i=0, $k=0; $i<count($glob_yt_plitems); $i++)  */

  for (; $i<YT_PLVIDEOS_MAXRESULTS; $i++) {
?>
    <td class="video_thumbs_table"><img class="videos_thumbs" <?
      ?>alt="(thumb)" src="/<?
        echo COMMON_DIR_THEMECUR_IMG_ABS; ?>thumb_blackscreen.120.90.png"></td>
<?
  } // for (; $i<YT_PLVIDEOS_MAXRESULTS; $i++)

    _page_td('nextPageToken', 'Next', YT_PLVIDEOS_MAXRESULTS-1, '&raquo;');
?>
  </tr>
  <tr>
    <td></td>
<?

  for ($i=0; $i<count($glob_yt_plitems); $i++) {
    /* Show noise thumbnail (comment #2) ...
     *
     * if ($glob_yt_plitems[$i]['status']['privacyStatus'] != 'public')
     *   continue;
     */
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
  } // if ($glob_yt_list)

  /* *************************************************************  */

?>

  <div id="video_description" class="description">
    <span id="video_description_title">Published from <?
      yt_print_chanlink($glob_yt_video['snippet']['channelTitle'],
                        $glob_yt_video['snippet']['channelId']);
    ?></span><br>
    <?
      common_user_output($glob_yt_video['snippet']['description'], '',
                         '_self', 0, $_SERVER['REQUEST_URI'], '_self');
    ?>
  </div>

  <a name="iframe_top"></a>
  <iframe class="comments_iframe" src="../common/comments_iframe.php?v=<?
    echo $video_id;
  ?>&amp;q_time=<? echo urlencode($_SERVER['REQUEST_URI']); ?>" height="<?
    echo yt_comments_iframeheight($glob_yt_video['statistics']['commentCount']);
  ?>" onload="iframe_resize(this)"></iframe>

<?
include_once '../themes/' .CONFIG_THEME. '/content-end.inc.php';
