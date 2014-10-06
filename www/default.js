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

function init()
{
  for (var i=0; i<parent.frames.length; i++) {
    if (parent.frames[i].location.href == window.location.href)
      parent.location.href = window.location.href;
  }
}

function init_frame()
{
}

/* ***************************************************************  */

var menu_toggle_doblur = false;
function menu_toggle_check(id)
{
  var menu_dev = document.getElementById(id);

  menu_toggle_doblur = document.activeElement == menu_dev;

  return true;
}
function menu_toggle_do(id)
{
  var menu_dev = document.getElementById(id);

  if (menu_toggle_doblur) menu_dev.blur();

  return true;
}

/* ***************************************************************  */

function iframe_resize(iframe)
{
  iframe.style.height
    = iframe.contentWindow.document.body.scrollHeight + 'px';
}

var _iframe_scroll_top_timeout_ms = 16; /* 40 => 25 FPS, 16 => 60 FPS  */
var _iframe_scroll_top_delta      = 50; /* px/frame  */
var _iframe_scroll_top_parent     = parent;
function _iframe_scroll_top_exec()
{
  var diff = _iframe_scroll_top_parent.timer_anchor.offsetTop
    - _iframe_scroll_top_parent.pageYOffset
    - 2*_iframe_scroll_top_delta;

  if (Math.abs(diff) < 2*_iframe_scroll_top_delta) {
    _iframe_scroll_top_parent
      .clearInterval(_iframe_scroll_top_parent.timer_id);
    return;
  }

  _iframe_scroll_top_parent.scrollBy(0,
    diff>0? _iframe_scroll_top_delta: -_iframe_scroll_top_delta);
}
function iframe_scroll_top()
{
  _iframe_scroll_top_parent.timer_anchor
    = parent.document.anchors['iframe_top'];

  _iframe_scroll_top_parent.timer_id
    = _iframe_scroll_top_parent.setInterval('_iframe_scroll_top_exec()',
                                            _iframe_scroll_top_timeout_ms);

  return true;
}

/* ***************************************************************  */

function auth_popup(link)
{
  var w = screen.availWidth * 1/2;
  var h = screen.availHeight * 3/4;
  var l = (screen.width-w)/2;
  var t = (screen.height-h)/2;

  var width_height =
    "width=" + w
    + ",height=" + h
    + ",left=" + l
    + ",top=" + t
    + ",menubar=no,resizable=yes,scrollbars=no,status=no"
    + ",toolbar=no,location=no";

  var pwindow = window.open(link, 'auth', width_height);

  if (!pwindow) return true;
  pwindow.focus();

  return false;
}

/* ***************************************************************  */
