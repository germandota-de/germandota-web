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
$order = isset($_GET['order'])? trim($_GET['order']): '';

switch ($order) {
case 'newest': break;
default: $order = 'best';
}

/* ---------------------------------------------------------------  */
/* TODO: Check if $video_id is allowed to view  */

/* ---------------------------------------------------------------  */

$glob_comments = yt_comments_recv($video_id, 0, $order == 'newest');
$glob_results = $glob_comments['results'];

/* ***************************************************************  */

include_once '../../template/begin-head.inc.php';
common_print_htmltitle('Comments (' .$glob_comments['totalResults']. ')');
include_once '../../template/head-title.comments.inc.php';
common_print_title('Comments (' .$glob_comments['totalResults']. ')', true);
?>
  <form class="floatright" method="get" action="comments_iframe.php"><?
    ?><input type="hidden" name="v" value="<? echo $video_id; ?>"><?
    ?><select onchange="this.form.submit()" name="order" size="1"><?
    ?><option value="best"<?
      if ($order == 'best') echo ' selected';
    ?>>Top comments</option><?
    ?><option value="newest"<?
      if ($order == 'newest') echo ' selected';
    ?>>Newest first</option><?
  ?></select></form>
<?
include_once '../../template/title-content.comments.inc.php';
?>

  <table id="comments_author_table">
<?
  for ($i=0; $i<count($glob_results); $i++) {
?>
  <tr<?
    if($i%2 == 0) echo ' class="comments_author_table_tr2"';
  ?>>
    <td><span class="comments_author"><?
      yt_print_chanlink($glob_results[$i]['author'][0]['name']['$t'],
                        $glob_results[$i]['yt$channelId']['$t']);
    ?></span> <span class="comments_date"><?
      $cur_published = $glob_results[$i]['published']['$t'];
      $cur_updated = $glob_results[$i]['updated']['$t'];

      _o(yt_str2date($cur_published) .', '. yt_str2time($cur_published));

      if ($cur_published != $cur_updated) echo ' (updated)';

    ?></span><div class="comments_content"><?
      // TODO
      common_user_output($glob_results[$i]['content']['$t'], 4);
    ?></div><?
      $cur_reply_cnt = intval($glob_results[$i]['yt$replyCount']['$t']);

      if ($cur_reply_cnt > 0) echo $cur_reply_cnt. ' replies';

    ?></td>
  </tr>
<?
  } // for ($i=0; $i<count($glob_comments); $i++)
?>
  </table>

<?
include_once '../../template/content-end.comments.inc.php';
