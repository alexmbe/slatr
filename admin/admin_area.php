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

if (!defined('SLA') || !$user) {
	// Exit silently
	exit;
}     
?>

<table width='100%' cellpadding=10 cellspacing=1 style='border: 1px solid #cccccc; height:100%'>
	<tr>
		<td valign=top height=15>
			<?php include_once('./admin/current_page.php');?>
		</td>
	</tr>
	<tr>
		<td valign=top>
<?php
	if ($_GET['where'] && $_GET['what']) {
		if (isset($pages[$_GET['where']], $pages[$_GET['where']][$_GET['what']])) {
			require_once($pages[$_GET['where']][$_GET['what']]);
		} else {
			// Someone tried to hack us? Just exit as per OWASP 2007 A6
			echo "There's no such page.";
			exit;
		}
	}
?>
		</td>
	</tr>
</table>
