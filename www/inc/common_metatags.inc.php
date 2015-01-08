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

function common_meta_printall($description, $url=false)
{
  if (!$url) $url = COMMON_SERVER_REQUEST_URL;

  _common_meta_byname_print('robots', 'all');

  _common_meta_byname_print('generator', 'GermanDota.de Webcode');
  _common_meta_byname_print('abstract', 'Website of '
    .CONFIG_PROJECT_NAME_SHORT .' '. CONFIG_PROJECT_NAME_POST);
  _common_meta_byname_print('description', $description);

  _common_meta_byprop_print('og:site_name',
    CONFIG_PROJECT_NAME_SHORT .' '. CONFIG_PROJECT_NAME_POST);
  _common_meta_byprop_print('og:url', $url);

  // TODO Meta tags
}

/* ***************************************************************  */
