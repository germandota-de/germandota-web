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

?>  <!-- begin of head-title -->
</head>
<body onload="init()">
  <div class="topnav">
    <img id="topnav_logo" src="<? echo CONFIG_PROJECT_LOGO_ABS; ?>" alt="(logo)">
    <span class="topnav">
      <a class="topnav_link" href="/<?
        echo COMMON_DIR_INST_ABS; ?>">Home</a>
      | <a class="topnav_link" href="/<?
        echo COMMON_DIR_INST_ABS; ?>live/">Live Stream</a>
      | <a class="topnav_link" href="/<?
        echo COMMON_DIR_INST_ABS; ?>playlists/">Playlists</a>
    </span>
  </div>

  <div id="main">
    <div id="title">
  <!-- end of head-title -->