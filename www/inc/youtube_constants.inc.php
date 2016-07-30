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

if (!defined('YT_INCLUDED')) die('Include youtube.inc.php!');

/* ***************************************************************  */

define('_YT_REQUEST_METHOD_PREFIX',     'youtube/v3/');

define('YT_PLAYLISTS_MAXRESULTS',       3);
define('YT_PLAYLISTS_MAXRESULTS_NEXT',  10);
define('YT_CHAN_ACTIV_MAXRESULTS',      4);
define('YT_CHAN_ACTIV_MAXRESULTS_NEXT', 10);

/* Must be odd (3, 5, 7, ...) */
define('YT_PLVIDEOS_MAXRESULTS',        7);
define('YT_PLVIDEOS_MAXRESULTS_HALF',   YT_PLVIDEOS_MAXRESULTS >> 1);

define('_YT_RECV_PLIST_50PAGES_LIMIT',  8);

define('_YT_REQUEST_FIELDS_PAGING',
       'pageInfo,nextPageToken,prevPageToken');

define('YT_URL_WATCH',        'https://www.youtube.com/watch');
define('YT_URL_CHANNEL',      'https://www.youtube.com/channel/');

define('YT_URL_FLASH_EMBED_FMT_S',
       '//www.youtube.com/embed/%s?rel=0&vq=hd720&autoplay=1');
define('YT_URL_FLASH_SHARE_FMT_S',
       '//www.youtube.com/v/%s?version=3&autohide=1');

/* ***************************************************************  */

define('YT_COMMENTS_PERPAGE',           8);
define('YT_COMMENTS_PERPAGE_NEXT',      10);
define('YT_COMMENTS_PXPERCOMMENT',      100);
define('YT_COMMENTS_OFFSET_PX',         200);

define('YT_COMMENTS_REQUEST_PREFIX',
                              'https://gdata.youtube.com/feeds/api/');

/* ***************************************************************  */

define('_YT_AUTH_OAUTH2_SCOPE',
                           'https://www.googleapis.com/auth/youtube');

/* ***************************************************************  */
