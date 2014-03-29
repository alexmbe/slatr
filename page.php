<?php

/*
 * This file is part of the slatr project
 * Copyleft Alex Bello
 *
 * For more information visit: http://github.com/alexmbe/slatr 
 *
 * slatr is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * slatr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with slatr.  If not, see http://www.gnu.org/licenses/
 */

	//Define constants, usufull for security
	define('SLA', true);
 	require_once("code.init.php");

	if (!$user) {
		exit;
	}

    function getmicrotime()
    { 
        list($usec, $sec) = explode(" ",microtime()); 
        return ((float)$usec + (float)$sec); 
    }     
    $_START = getmicrotime();
?>
<html>

  <head>
    <title>SLA Management</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="stylesheet" type="text/css" href="index.css">
    <script src="./js/check.js" type="text/javascript"></script>
  </head>
  
  <body>
    <table cellpadding=0 cellspacing=20 width='100%' height='100%'>
      <tr>
        <td width=200 valign=top>
			<!-- meniu -->
			<?php require_once('./admin/navigation.php');?>
        </td>
		<td valign=top valign=top>
			<!-- admin area -->
			<?php require_once('./admin/admin_area.php');?>
		</td>
      </tr>
    </table>
  </body>

</html>
