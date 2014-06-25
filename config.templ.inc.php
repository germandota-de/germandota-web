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

/* Create one at
 *
 * https://console.developers.google.com -> projects -> APIs & auth
 *
 *   1. APIs -> 'YouTube Data API v3' set to ON
 *
 *   2. APIs -> 'Google+ API' set to ON
 *
 *   3. Credentials -> Public API access -> Server key
 */
define('CONFIG_YT_APIKEY', '');
if (CONFIG_YT_APIKEY == '') die('CONFIG_YT_APIKEY not configured!');

/* Youtube API request:
 *
 * https://www.googleapis.com/youtube/v3/channels?key={API_KEY} \
 *   &forUsername=GermanDotaTV&part=id
 *
 * GermanDotaTV: UCeKFc-ydU9pWKa4tqK-vYSw
 * Gronkh:       UCYJ61XIK64sp6ZFFS8sctxw
 */
define('CONFIG_YT_CHANNELID',      'UCeKFc-ydU9pWKa4tqK-vYSw');

/* Date/Time format to use on the site.  Consider for details:
 *
 * http://www.php.net/date
 */
define('CONFIG_DATE_FORMAT',       'j. M Y');
define('CONFIG_TIME_FORMAT',       'G:i');

/* ***************************************************************  */

/* Only used for displaying on the site  */
define('CONFIG_PROJECT_SITENAME',       'GermanDota.de');
define('CONFIG_PROJECT_NAME_SHORT',     'GermanDota');

/* Extends CONFIG_PROJECT_NAME_SHORT.  Full name will be
 * CONFIG_PROJECT_NAME_SHORT.' '.CONFIG_PROJECT_NAME_POST
 *
 * e.g. 'GermanDota Community'
 */
define('CONFIG_PROJECT_NAME_POST',      'Community');

define('CONFIG_PROJECT_LOGO_ABS',       '/img/logo.32.png');

define('CONFIG_ABOUT_ADDRESS_HTML',
  'Martin Dota
   Ultra-Tower-Str. 123
   12345 Mixen');

define('CONFIG_ABOUT_CONTACT_HTML',
  'Email: <a href="mailto:ulti@germandota.de">ulti@germandota.de</a>');

/* ***************************************************************  */

/* We are searching for contributors.  It's nice if here is a link to
 * https://github.com/germandota-de :))
 */
define('CONFIG_TEMPL_FOOTERLEFT_HTML',
  '<a target="_blank" href="https://github.com/germandota-de">'
    .'github.com/germandota-de</a>');

define('CONFIG_TEMPL_FOOTERCENTER_HTML',
  'Laufen? Iiieh, Sport! Hier gehts zur <i>Iiieh Sports League</i>: '
  .'<a target="_blank" href="https://running.aitiba.com/">'
  .'running.aitiba.com</a>');


define('CONFIG_TEMPL_ANALYTICS_HTML',
'  <script type="text/javascript">var _paq = _paq || []; _paq.push(["trackPageView"]); _paq.push(["enableLinkTracking"]); (function() { var u="https://ssl-id.de/dj-l.de/dynamic/"; _paq.push(["setTrackerUrl", u+"piwik.php"]); _paq.push(["setSiteId", "1"]); var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript"; g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s); })();</script>
  <noscript><img src="https://ssl-id.de/dj-l.de/dynamic/piwik.php?idsite=1&amp;rec=1" style="border:0" alt=""></noscript>');

/* ***************************************************************  */
