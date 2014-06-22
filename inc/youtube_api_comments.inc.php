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

/* Youtube Data API !v2! comment reference:
 *
 * https://developers.google.com/youtube/2.0/developers_guide_protocol_comments
 */

define('YT_COMMENTS_PERPAGE',           10);
define('YT_COMMENTS_PXPERCOMMENT',      30);
define('YT_COMMENTS_OFFSET_PX',         200);

/* ***************************************************************  */

function yt_comments_iframeheight($comment_count)
{
  $cnt = $comment_count>YT_COMMENTS_PXPERCOMMENT
    ? YT_COMMENTS_PXPERCOMMENT: $comment_count;

  return YT_COMMENTS_OFFSET_PX + (YT_COMMENTS_PXPERCOMMENT*$cnt);
}
