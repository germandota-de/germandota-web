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

define('OAUTH2_CALLBACKS_PREFIX',            'oauth2_cb_');

/* *******************************************************************
 *
 * Callback must have the form:
 *
 *   oauth2_cb_<platform>_<name>_<arg_count>()
 *
 * i.e.
 *
 *   oauth2_cb_youtube_video_like_1($video_id)
 */

function oauth2_cb_youtube_video_like_1($video_id)
{
  echo 'Hello World!';
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

function oauth2_callback_call($platform, $callback, $args)
{
  return call_user_func_array(_oauth2_callback_2str($platform, $callback,
                                                    $args), $args);
}

/* ***************************************************************  */
