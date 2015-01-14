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

function _common_meta_byname_print($name, $cont)
{
  echo '  <meta name="' .$name. '" content="';
  echo _o_cuthtml($cont);
  echo "\">\n";
}

function _common_meta_byprop_print($prop, $cont)
{
  echo '  <meta property="' .$prop. '" content="';
  _o_cuthtml($cont);
  echo "\">\n";
}

/* ***************************************************************  */

/* Opengraph reference: http://ogp.me/
 *
 * Facebook debugger:   https://developers.facebook.com/tools/debug/
 *
 */
function common_meta_printall($title, $description=false, $image=false,
                              $type_array=false, $url=false)
{
  if (!$description) {
    $description = 'Here is the social media stuff of '
      .COMMON_PROJECT_NAME_FULL. '.';
  }
  if (!$image) {
    $image = COMMON_SERVER_REQUEST_PROTSERVER. '/'
      .COMMON_DIR_INST_ABS.CONFIG_PROJECT_LOGO_200;
  }
  if (!$type_array) $type_array = array('type' => 'website');
  if (!$url) $url = COMMON_SERVER_REQUEST_URL;

  _common_meta_byname_print('robots', 'all');
  _common_meta_byprop_print('og:title', $title);
  _common_meta_byname_print('generator', 'GermanDota.de Webcode');
  _common_meta_byname_print('abstract', 'Website of '
                            .COMMON_PROJECT_NAME_FULL);
  _common_meta_byname_print('description', $description);

  _common_meta_byprop_print('og:site_name', COMMON_PROJECT_NAME_FULL);
  _common_meta_byprop_print('og:url', $url);
  _common_meta_byprop_print('og:type', $type_array['type']);
  _common_meta_byprop_print('og:image', $image);
  _common_meta_byprop_print('og:description', $description);

  if ($type_array && preg_match('/^video/i', $type_array['type'])) {
    _common_meta_byprop_print('og:video:type',
                              'application/x-shockwave-flash');
    _common_meta_byprop_print('og:video',
                              'http:' .$type_array['video_url']);
    _common_meta_byprop_print('og:video:secure_url',
                              'https:' .$type_array['video_url']);
    _common_meta_byprop_print('og:video:width',
                              $type_array['video_width']);
    _common_meta_byprop_print('og:video:height',
                              $type_array['video_height']);
  }
}

/* ***************************************************************  */
