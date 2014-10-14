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

include_once dirname(__FILE__). '/youtube.inc.php';

define('OAUTH2_CALLBACKS_PREFIX',            'oauth2_cb_');

/* *******************************************************************
 *
 * Callback must have the form:
 *
 *   oauth2_cb_<platform>_<name>_<arg_count>()
 * i.e.
 *   oauth2_cb_youtube_video_like_1($video_id)
 *
 * Returning must be FALSE on error or on success
 *
 *   array($title, $html_output, $js_onload);
 */

function oauth2_cb_youtube_video_like_1($video_id)
{
  $thousands = CONFIG_NUMBERS_THOUSANDS;
  $locale = CONFIG_NUMBERS_LOCALE;

  if (yt_recv_video_rate($video_id, 'like')) {
    $title = 'Liked :D';
    $html_output = 'Yeeah you like it xD ...';
    $js_onload = <<<EOS

    var html_element = self.opener.document
      .getElementById('js_yt_video_like_{$video_id}');
    var val = html_element.firstChild.nodeValue;

    val = parseInt(val.replace(/\\{$thousands}/g, ''));
    html_element.firstChild.nodeValue
      = (val+1).toLocaleString('{$locale}', {useGrouping: true});

    popup_close();

EOS;
  } else {
    $title = 'Error liking';
    $html_output = 'You can\'t like this video :( ...  What a message'
      .' ;P ...';
    $js_onload = <<<EOS

EOS;
  }

  return array($title, $html_output, $js_onload);
}

/* ***************************************************************  */

function _oauth2_callback_2str($platform, $callback, $args)
{
  return OAUTH2_CALLBACKS_PREFIX .$platform. '_' .$callback. '_'
    .count($args);
}

function oauth2_callback_callable($platform, $callback, $args)
{
  return function_exists(_oauth2_callback_2str($platform, $callback,
                                               $args));
}

/* Returns FALSE on any error  */
function oauth2_callback_call($platform, $callback, $args)
{
  return call_user_func_array(_oauth2_callback_2str($platform, $callback,
                                                    $args), $args);
}

/* ***************************************************************  */
