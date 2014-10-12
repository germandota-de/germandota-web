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

include_once dirname(__FILE__). '/common.inc.php';
include_once dirname(__FILE__). '/youtube_constants.inc.php';

include_once dirname(__FILE__). '/google_plus_api.inc.php';

/* Youtube Data API !v2! comment reference:
 *
 * https://developers.google.com/youtube/2.0/developers_guide_protocol_comments
 * https://developers.google.com/youtube/articles/changes_to_comments
 *
 * Youtube replies:
 *
 * https://developers.google.com/+/api/latest/comments/list
 */

/* ***************************************************************  */

function yt_comments_2commentid($rcv_str)
{
  return preg_replace('@^.*/([^/]+)$@', '\1', $rcv_str);
}

function _yt_comments_2activityid($rcv_str)
{
  return preg_replace('@^.*/([^/]+)$@', '\1', $rcv_str);
}

/* ***************************************************************  */

function _yt_comments_apiv2_list($method, $start_index, $max_results,
                                 $params='')
{
  /* ?prettyprint=true for debugging ...  */
  $request = YT_COMMENTS_REQUEST_PREFIX .$method. '?alt=json&start-index='
    .$start_index. '&max-results=' .$max_results
    . ($params == ''? '': '&' .$params);

  debug_api_info_incr('cnt_youtube_api_v2', 1,
                      $method .' - index ' .$start_index. '..'
                      .($start_index+$max_results). ' - ' .$params);

  $json = file_get_contents($request, false, stream_context_create(
    array('ssl' => array('CN_match' => YT_COMMENTS_SSL_CNMATCH))
  ));
  if (!$json) return false;

  $result = json_decode($json, true);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */

function yt_comments_recv($vid, $order_newest, $page)
{
  /* relevant-to-me=true only with OAuth ...  */
  $result = _yt_comments_apiv2_list(
    sprintf('videos/%s/comments', $vid), 1 + ($page-1)*YT_COMMENTS_PERPAGE,
    $page == 1? YT_COMMENTS_PERPAGE: (YT_COMMENTS_PERPAGE_NEXT+1),
    $order_newest? 'orderby=published': '');
  if (!$result || !isset($result['feed']['entry'])) return false;

  $next_exist = false; $prev_exist = false;
  foreach ($result['feed']['link'] as $link) {
    if ($link['rel'] == 'next') $next_exist = true;
    if ($link['rel'] == 'previous') $prev_exist = true;
  }

  $activity_id = array();
  foreach ($result['feed']['entry'] as $entry) {
    $activity_id[count($activity_id)]
      = _yt_comments_2activityid($entry['id']['$t']);
  }

  return array(
    'totalResults' => $result['feed']['openSearch$totalResults']['$t'],
    'nextExist' => $next_exist,
    'prevExist' => $prev_exist,
    'page' => $page,
    'activityId' => $activity_id
  );
}

/* ***************************************************************  */

function yt_comments_recv_comment($activity_id)
{
  $result = gplus_api_activity_get($activity_id);
  if (!$result) return false;

  return $result;
}

function yt_comments_recv_replies($activity_id, $max_results)
{
  $result = gplus_api_comments_list($activity_id, $max_results);
  if (!$result) return false;

  return $result;
}

/* ***************************************************************  */

function yt_comments_iframeheight($comment_count)
{
  $cnt = $comment_count>YT_COMMENTS_PERPAGE
    ? YT_COMMENTS_PERPAGE: $comment_count;

  return YT_COMMENTS_OFFSET_PX + (YT_COMMENTS_PXPERCOMMENT*$cnt);
}

/* ***************************************************************  */

function yt_comments_print_pageinfo($yt_response, $items_str, $url_pre,
                                    $url_post='')
{
  if ($yt_response['prevExist']) {
    echo '<a title="Previous ' .YT_COMMENTS_PERPAGE_NEXT. ' '
      .$items_str. '" class="page_nextlink" onclick="return'
      .' iframe_scroll_top();" href="' .$url_pre .($yt_response['page']-1)
      .($url_post===''? '': '&amp;' .$url_post) .'">&laquo;-'
      .YT_COMMENTS_PERPAGE_NEXT.'</a> ';
  } else {
    echo 'First ';
  }

  echo count($yt_response['activityId']) .' of '
    .$yt_response['totalResults'] .' '. $items_str;

  if ($yt_response['nextExist']) {
    echo ' <a title="Next ' .YT_COMMENTS_PERPAGE_NEXT. ' '
      .$items_str. '" class="page_nextlink" onclick="return'
      .' iframe_scroll_top();" href="' .$url_pre
      .($yt_response['page']+1)
      .($url_post===''? '': '&amp;' .$url_post) .'">'
      .YT_COMMENTS_PERPAGE_NEXT.'+&raquo;</a>';
  }
}

/* ***************************************************************  */

function yt_comments_strip_html($str)
{
  /* Remove UTF-8 Byte Order Marks EF BB BF (sent by ex. Youtube API)  */
  $str = preg_replace("@\xef\xbb\xbf@", '', $str);

  /* Start times of videos  */
  $str = preg_replace('@<a[^>]+href="[^"]+youtube\.com.*?>'
    .'([0-9]+:[0-9]{2,2}(:[0-9]{2,2})?)</a>@isu', '\1', $str);

  /* Hashtags  */
  $str = preg_replace('@<a[^>]+class="[^"]+hashtag.*?>'
    .'#([^<]+)</a>@isu', '#\1', $str);

  /* Google+ profile  */
  $str = preg_replace(
    '@(<a[^>]class="proflink")(.*?>)@isu', '\1 target="_blank"\2',
    $str);

  return $str;
}

/* ***************************************************************  */

function yt_comments_print_comment($comment, $more_link, $more_target,
  $lines, $time_link, $time_target)
{
  $published = $comment['published'];
  $updated = $comment['updated'];
  $cid = $comment['id'];
  $like_count = $comment['object']['objectType'] == 'comment'
    ? $comment['plusoners']['totalItems']
    : $comment['object']['plusoners']['totalItems'];

  ?><a name="<?
    echo $cid;
  ?>"></a><span class="comments_author"><?
    gplus_print_profilelink($comment['actor']);
  ?></span><span class="comments_date"><?
    echo yt_str2date_html($published) .', '
      . yt_str2time_html($published);

    if ($published != $updated) echo ' (updated)';

  ?></span><div class="comments_content"><?
    $content = yt_comments_strip_html($comment['object']['content']);
    $content = common_newline_html($content, COMMENTS_CHARS_PER_LINE);
    common_user_output_htmlin($content, $more_link, $more_target,
                              $lines, $time_link, $time_target);
  ?><table class="comments_likes"><tr><td class="comments_likes_no"><?
    if ($like_count > 0) echo $like_count;
  ?></td><td><img class="comments_likes_icon" alt="(likes)" src="/<?
    echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_comments_like.32.png"><?
  ?></td><td><img class="comments_likes_icon" alt="(dislikes)" src="/<?
    echo COMMON_DIR_THEMECUR_IMG_ABS; ?>icon_comments_dislike.32.png"><?
  ?></td><?
  ?></tr></table></div><?
}

/* ***************************************************************  */
