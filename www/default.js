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

function init_comments()
{
}

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
