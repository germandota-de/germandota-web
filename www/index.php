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

include_once 'inc/youtube_api.inc.php';

$glob_act_result = yt_recv_chan_activity('');
$glob_activities = $glob_act_result['items'];

include_once 'themes/' .CONFIG_THEME. '/begin-head.inc.php';
common_print_htmltitle(CONFIG_PROJECT_NAME_POST);
include_once 'themes/' .CONFIG_THEME. '/head-title.inc.php';
echo '  News - ';
common_print_title(CONFIG_PROJECT_NAME_POST);
include_once 'themes/' .CONFIG_THEME. '/title-content.inc.php';
?>

  <table class="activity_table">
<?

  for ($i=0; $i<count($glob_activities); $i++) {
    $cur_activ = $glob_activities[$i];
    $cur_published = $cur_activ['snippet']['publishedAt'];
?>
  <tr<? if ($i%2 == 0) echo ' class="activity_table_tr2"'; ?>>
    <td class="activity_table_thumb"><?
      ?><img class="activity_table_thumb" alt="(thumb)" src="<?
      echo $cur_activ['snippet']['thumbnails']['medium']['url'];
    ?>"></td>
    <td class="activity_table_date"><?
      echo yt_str2date($cur_published) .'<br>'
        .yt_str2time($cur_published);
    ?></td>
    <td class="activity_table_descr"><div class="activity_table_kind"><?
      // TODO
    ?>Hello</div><?
      _o($cur_activ['snippet']['title']);
    ?></td>
  </tr>
<?
  } // for ($i=0; $i<count($glob_activities); $i++)

?>
  </table>

<?
include_once 'themes/' .CONFIG_THEME. '/content-end.inc.php';
