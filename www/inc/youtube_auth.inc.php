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

include_once dirname(__FILE__). '/google_api.inc.php';

/* Progamming Guide:
 *
 * https://developers.google.com/youtube/v3/guides/authentication#server-side-apps
 */

/* ***************************************************************  */

function yt_auth_print_form($submit_class, $alt, $descr, $callback,
                            $args, $pic_url)
{
  ?><form class="yt_auth_form" method="post" target="auth" action="/<?
    echo COMMON_DIR_OAUTH2_ABS;
  ?>" onsubmit="return popup_auth();"><?

    oauth2_redirect_params_print(OAUTH2_PLATFORM_YOUTUBE, $callback,
                                 $args);
    ?><input class="<? echo $submit_class; ?> yt_auth_submit"<?
    ?> type="image" src="<? echo $pic_url; ?>" alt="<? echo $alt; ?>"<?
    ?> title="<? _o($descr); ?>"><?

  ?></form><?
}

/* ***************************************************************  */

function yt_auth_urlget_setsession($callback, $args)
{
  return google_oauth2_urlget_setsession(
    _YT_AUTH_OAUTH2_SCOPE, OAUTH2_PLATFORM_YOUTUBE, $callback, $args);
}

/* ***************************************************************  */

function yt_auth_setsession($code)
{
  debug_api_info_incr('cnt_youtube_auth', 1, 'code '. $code);

  return google_oauth2_setsession(OAUTH2_PLATFORM_YOUTUBE, $code);
}

/* ***************************************************************  */
