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

include_once '../../inc/common.inc.php';

include_once '../../inc/youtube_api_comments.inc.php';

define('COMMENTS_LINES_COUNT',          4);

$video_id = isset($_GET['v'])? trim($_GET['v']): '';

$query_time = isset($_GET['q_time'])? urldecode(trim($_GET['q_time'])): '';
if (!preg_match('@^/[^/]@', $query_time)) $query_time = '';

$order = isset($_GET['order'])? trim($_GET['order']): '';
switch ($order) {
case 'newest': break;
default: $order = 'best';
}
$page = isset($_GET['p'])? intval(trim($_GET['p'])): 1;
$more_id = isset($_GET['more'])? trim($_GET['more']): '';

function _comments_link_self($video_id, $query_time, $order=false,
                             $page=false, $more_id=false)
{
  $result = $_SERVER['PHP_SELF'] .'?v='. $video_id;
  $result .= '&amp;q_time=' .urlencode($query_time);

  $result .= $order !== false? '&amp;order='. $order: '';
  $result .= $page !== false? '&amp;p='. $page: '';
  $result .= $more_id !== false? '&amp;more='. $more_id: '';

  /* Does not work in IFrames with Google Chrome  */
  //$result .= $more_id !== false? '#'. $more_id: '';
  return $result;
}

/* ***************************************************************  */

/* ---------------------------------------------------------------  */
/* Allow only if referred from our self  */

$glob_servername = $_SERVER['SERVER_NAME'];
if ($glob_servername == '127.0.0.1'  /* For development purposes  */
    || $glob_servername == 'localhost'
    || $glob_servername == common_url2hostname($_SERVER['HTTP_REFERER'])) {
  $glob_comments = yt_comments_recv($video_id, $order == 'newest',
                                    $page);
  $glob_results = $glob_comments['results'];
} else {
  $glob_comments = false;
  $glob_results = false;
}

/* ---------------------------------------------------------------  */

$_glob_comments_order_href_pre
  = _comments_link_self($video_id, $query_time, '');
$glob_comments_order = array(
  'best' => array(
    'title' => 'Top comments',
    'href' => $_glob_comments_order_href_pre. 'best',
  ),
  'newest' => array(
    'title' => 'Newest first',
    'href' => $_glob_comments_order_href_pre. 'newest',
  ),
);

/* ***************************************************************  */

include_once '../themes/' .CONFIG_THEME. '/begin-head.inc.php';
common_print_htmltitle('Comments (' .$glob_comments['totalResults']. ')');
include_once '../themes/' .CONFIG_THEME. '/head-title.comments.inc.php';
common_menu_print($glob_comments_order, 'comments_order', $order);
common_print_title('Comments (' .$glob_comments['totalResults']. ')', true);
include_once '../themes/' .CONFIG_THEME. '/title-content.comments.inc.php';
?>

  <table id="comments_table">
<?

  if ($page != 1) {
?>
  <tr>
    <th colspan="1"><?
      yt_comments_print_pageinfo($glob_comments, 'comments',
        _comments_link_self($video_id, $query_time, $order, ''));
    ?></th>
  </tr>
<?
  } // if ($page != 1)

  for ($i=0; $i<count($glob_results); $i++) {
    $cur_published = $glob_results[$i]['published']['$t'];
    $cur_updated = $glob_results[$i]['updated']['$t'];
    $cur_cid = yt_comments_2cid($glob_results[$i]['id']['$t']);
?>
  <tr<?
    if($i%2 == 0) echo ' class="comments_table_tr2"';
  ?>>
    <td><a name="<?
      echo $cur_cid;
    ?>"></a><span class="comments_author"><?
      yt_print_chanlink($glob_results[$i]['author'][0]['name']['$t'],
                        $glob_results[$i]['yt$channelId']['$t']);
    ?></span> <span class="comments_date"><?
      _o(yt_str2date($cur_published) .', '. yt_str2time($cur_published));

      if ($cur_published != $cur_updated) echo ' (updated)';

    ?></span><div class="comments_content"><?
      common_user_output($glob_results[$i]['content']['$t'],
        _comments_link_self($video_id, $query_time, $order, $page,
                            $cur_cid),
        $more_id == $cur_cid? 0: COMMENTS_LINES_COUNT,
        $query_time, '_parent');
    ?></div><?
      $cur_reply_cnt = intval($glob_results[$i]['yt$replyCount']['$t']);

      if ($cur_reply_cnt > 0) echo $cur_reply_cnt. ' replies';

    ?></td>
  </tr>
<?
  } // for ($i=0; $i<count($glob_comments); $i++)

?>
  <tr>
    <th colspan="1"><?
      yt_comments_print_pageinfo($glob_comments, 'comments',
        _comments_link_self($video_id, $query_time, $order, ''));
    ?></th>
  </tr>
  </table>

<?
include_once '../themes/' .CONFIG_THEME. '/content-end.comments.inc.php';
