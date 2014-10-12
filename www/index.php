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

include_once 'inc/common.inc.php';

include_once 'inc/youtube.inc.php';

$page_token = isset($_GET['p'])? trim($_GET['p']): '';

/* ***************************************************************  */

$glob_act_result = yt_recv_chan_activity($page_token);

/* On error we are trying the first page  */
if (!$glob_act_result) {
  $page_token = '';
  $glob_act_result = yt_recv_chan_activity($page_token);
}

$state_first_page = !isset($glob_act_result['prevPageToken']);
$glob_activities = $glob_act_result['items'];

/* ***************************************************************  */

include_once 'themes/' .CONFIG_THEME. '/begin-head.inc.php';
common_print_htmltitle(CONFIG_PROJECT_NAME_POST);
include_once 'themes/' .CONFIG_THEME. '/head-title.inc.php';
echo "\n  News - ";
common_print_title(CONFIG_PROJECT_NAME_POST);
include_once 'themes/' .CONFIG_THEME. '/title-content.inc.php';
?>

  <table class="activity_table">
<?
  if (!$state_first_page) {
?>
  <tr><th colspan="3"><?
    yt_print_pageinfo($glob_act_result, 'activities', './');
  ?></th></tr>
<?
  }

  for ($i=0; $i<count($glob_activities); $i++) {
    list($glob_activities, $cur_selected)
      = yt_activity_group($glob_activities, $i);
    $cur_activ = $cur_selected[count($cur_selected)-1];

    list($cur_blank, $cur_url) = yt_activity_url($cur_activ);
    $cur_channel = yt_activity_recv_channel($cur_activ);

    $cur_published = $cur_activ['snippet']['publishedAt'];

?>
  <tr<? if ($i%2 == 0) echo ' class="activity_table_tr2"'; ?>>
    <td class="activity_table_thumb"><?
      yt_print_activity_thumblink($cur_activ, $cur_channel, $cur_blank,
                                  $cur_url);
    ?></td>
    <td class="activity_table_date"><?
      echo yt_str2date_html($cur_published) .'<br>'
        .yt_str2time_html($cur_published);
    ?><div class="activity_table_kind"><?
      yt_printshort_activity_type($cur_selected);
    ?></div></td>
    <td class="activity_table_descr"><?
      yt_print_activity_link($cur_activ, $cur_channel, $cur_blank,
                             $cur_url);

      yt_print_activity_desc($cur_selected, $cur_channel, $cur_blank,
                             $cur_url);
    ?></td>
  </tr>
<?
  } // for ($i=0; $i<count($glob_activities); $i++)
?>
  <tr><th colspan="3"><?
    yt_print_pageinfo($glob_act_result, 'activities', './');
  ?></th></tr>
  </table>

<?
include_once 'themes/' .CONFIG_THEME. '/content-end.inc.php';
