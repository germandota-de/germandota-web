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

define('YT_INCLUDED',                   true);

include_once dirname(__FILE__). '/google_api.inc.php';
include_once dirname(__FILE__). '/youtube_constants.inc.php';

define('OAUTH2_PLATFORM_YOUTUBE',       'youtube');
$oauth2_platforms_data[OAUTH2_PLATFORM_YOUTUBE] = array(
  'oauth2_url_login_pre'           => GOOGLE_OAUTH2_LOGIN_PRE,
  'oauth2_url_login_post'          => GOOGLE_OAUTH2_LOGIN_POST,
  'oauth2_scope'                   => _YT_AUTH_OAUTH2_SCOPE,

  'oauth2_url_token'               => GOOGLE_OAUTH2_TOKEN_PRE,
  'oauth2_client_id'               => CONFIG_GOOGLE_CLIENT_ID,
  'oauth2_client_secret'           => CONFIG_GOOGLE_CLIENT_SECRET,
);

include_once dirname(__FILE__). '/youtube_receive.inc.php';
include_once dirname(__FILE__). '/youtube_common.inc.php';
include_once dirname(__FILE__). '/youtube_comments.inc.php';
include_once dirname(__FILE__). '/youtube_auth.inc.php';
