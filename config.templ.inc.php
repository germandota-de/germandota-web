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
 *   2. Credentials -> Public API access -> Server key
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

define('CONFIG_YT_RECOMM_PLID',    'LLeKFc-ydU9pWKa4tqK-vYSw');

/* ***************************************************************  */

define('CONFIG_IMPRESSUM_ADDRESS_HTML',
'  <b>Angaben gemäß § 5 TMG</b>
    Dirk Lehmann
    Sickingenstr. 58
    10553 Berlin');

define('CONFIG_IMPRESSUM_CONTACT_HTML',
'  <b>Kontakt</b>
    Email: <a href="mailto:dotadirk@dj-l.de">dotadirk@dj-l.de</a>');

/* ***************************************************************  */

/* We are searching for contributors.  It's nice if here is a link to
 * https://github.com/germandota-de :))
 */

define('CONFIG_TEMPL_FOOTERLEFT_HTML',
  '<a target="_blank" href="https://github.com/germandota-de">github.com/germandota-de</a>');

define('CONFIG_TEMPL_FOOTERCENTER_HTML',
  'Laufen? Iiieh, Sport! Hier gehts zur <i>Iiieh Sports League</i>: '
  .'<a target="_blank" href="https://running.aitiba.com/">running.aitiba.com</a>');
