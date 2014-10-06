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

function yt_auth_print_link($descr, $html)
{
  $url_out
    = common_url_amp('https://accounts.google.com/o/oauth2/auth?TODO=1&uswusf=blahh');
  // TODO ...

  ?><a class="auth_link" target="auth" title="<? _o($descr); ?>"<?
  ?> onclick="return auth_popup('<? echo $url_out; ?>');"<?
  ?> href="javascript:void()"><?
    echo $html;
  ?></a><?
}
