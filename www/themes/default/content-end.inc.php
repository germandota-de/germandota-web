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

if (!defined('COMMON_EXIST')) exit();

?>  <!-- begin of content-end -->
    </div>
  </div>
  <div id="footer">
    <span class="floatleft"><? _o_html(CONFIG_TEMPL_FOOTERLEFT_HTML); ?></span>
    <? _o_html(CONFIG_TEMPL_FOOTERCENTER_HTML); ?>

    <span class="floatright"><a id="impressum" href="/about/">About us</a></span>
  </div>
<?
  echo CONFIG_TEMPL_ANALYTICS_HTML;
?>

</body>
</html>
