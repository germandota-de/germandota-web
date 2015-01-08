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

define('HTTP_TIMEOUT_S',                5);
define('HTTP_STATUS_ERROR',             700);

/* ***************************************************************  */

function _http_status_ok($status)
{
  return $status >= 200 && $status < 300;
}

function _http_receive_error()
{
  return array(_http_status_ok(HTTP_STATUS_ERROR), HTTP_STATUS_ERROR,
               '- Connection Error -');
}

/* ***************************************************************  */

function http_receive($url, $method='GET', $header=array(), $content='',
                      $content_type=false)
{
  $content_length = strlen($content);
  $content_type = $content_type? $content_type
    : 'application/x-www-form-urlencoded';

  debug_api_info_incr('cnt_http', 1,
                      $method .' Content-Length: ' .$content_length
                      .' Content-Type: '. $content_type);

  $curl_method_val = true;
  switch ($method) {
  case 'GET': $curl_method = CURLOPT_HTTPGET;
    break;
  case 'POST': $curl_method = CURLOPT_POST;
    $header[count($header)] = 'Content-Type: ' .$content_type;
    $header[count($header)] = 'Content-Length: ' .$content_length;
    break;
  case 'PUT': $curl_method = CURLOPT_PUT;
    $header[count($header)] = 'Content-Type: ' .$content_type;
    $header[count($header)] = 'Content-Length: ' .$content_length;
    break;
  case 'HEAD': $curl_method = CURLOPT_NOBODY;
    break;
  case 'DELETE': $curl_method = CURLOPT_CUSTOMREQUEST;
    $curl_method_val = 'DELETE';
    break;
  default:
    return _http_receive_error();
  }

  $curl_options = array(CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => HTTP_TIMEOUT_S,
                        CURLOPT_SSL_VERIFYPEER => true,
                        CURLOPT_SSL_VERIFYHOST => 2,
                        $curl_method => $curl_method_val,
                        );
  if (count($header) != 0) $curl_options[CURLOPT_HTTPHEADER] = $header;
  if ($content != '') $curl_options[CURLOPT_POSTFIELDS] = $content;

  if (!($ch = curl_init())) return _http_receive_error();
  if (!curl_setopt_array($ch, $curl_options)) {
    curl_close($ch); return _http_receive_error();
  }
  $result_data = curl_exec($ch);
  $result_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $result_status_ok = _http_status_ok($result_status);
  $result_error = curl_error($ch);
  curl_close($ch);

  if ($result_data === false) {
    _e('http_receive', 'HTTP CONNECTION "' .$result_error. '"', $url);
    return _http_receive_error();
  }

  if (!$result_status_ok) {
    _e('http_receive', 'HTTP STATUS ' .$result_status,
       $url. ' - "' .$result_data. '"');
  }

  return array($result_status_ok, $result_status, $result_data);
}
