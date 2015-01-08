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

/* ***************************************************************  */

/* Convert all characters for HTML output and return/put to output
 * buffer.
 */
function _o_get($str, $newline=COMMON_USER_NEWLINE)
{
  $result = preg_replace('/\n/isu', $newline,
                         htmlentities($str, ENT_QUOTES, 'UTF-8'));

  /* Remove UTF-8 Byte Order Marks EF BB BF (sent by ex. Youtube API)  */
  $result = preg_replace("@\xef\xbb\xbf@", '', $result);

  return $result;
}
function _o($str)
{
  echo _o_get($str);
}

/* Convert all characters for HTML output, but leave HTML tags plain  */
function _o_html($str)
{
  echo htmlspecialchars_decode(_o_get($str), ENT_QUOTES);
}
function _o_cuthtml($str)
{
    echo _o_get($str, ' ');
}

function _e($function_name, $msg, $sensitive=false)
{
  $out = 'ERROR ' .$function_name. '(): ' .$msg;

  if (CONFIG_SECURITY_LOG_SENSITIVE && $sensitive)
    $out .= ' - [[ ' .$sensitive. ' ]]';

  error_log($out);
}

/* ***************************************************************  */
