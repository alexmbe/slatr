<?

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
<script type="text/javascript" src="js/cookies.js"></script>
	
<table cellpadding=10 cellspacing=0 style="border: 1px solid #dadada" width="100%" height="100%">
	<tr height=10>
		<td>You are: <?php et($_SESSION['user']);?></td>
		<td align=right>
			<a href='logout.php'>
				<img src='images/exit.gif' title='exit' border=0>
			</a>
		</td>
	</tr>
	<tr>
		<td colspan=2 valign=top>
            <img src='images/spacer.gif' width=180 height=1>
		
            	
		  <table width="100%" cellpadding="0" cellspacing="0">
			<tr>
			  <td rowspan="9" bgcolor="#e4e4e4" width="5" style="padding:3px"></td>
			  <td bgcolor="#a0a0a0" colspan=2></td>
			</tr>
			<tr bgcolor="#f4f4f4">
			  <td width="10"><img src="images/arrow_down.gif"></td>
			  <td style="padding:3px"><b>SLA Tools</b></td>
			</tr>	
			<tr>
			  <td bgcolor="#a0a0a0" colspan=2></td>
			</tr>
<?php if(($user->user_type=="admin")||($user->user_type=="advanced") ){ ?>				
			<tr>
			  <td width="10"></td>
			  <td style="padding:3px">- <a href="page.php?where=app&what=sites">Sites</a></td>
			</tr>
			<tr>
			  <td width="10"></td>
			  <td style="padding:3px">- <a href="page.php?where=app&what=events">Events</a></td>
			</tr>
<?php }; ?>				
			<tr>
			  <td width="10"></td>
			  <td style="padding:3px">- <a href="page.php?where=app&what=reports">Reports</a></td>
			</tr>
        </table> 
                         <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                          <td rowspan="9" bgcolor="#e4e4e4" width="5" style="padding:3px"></td>
                          <td bgcolor="#a0a0a0" colspan=2></td>
                        </tr>
                        <tr bgcolor="#f4f4f4">
                          <td width="10"><img src="images/arrow_down.gif"></td>
                          <td style="padding:3px"><b>Admin Tools</b></td>
                        </tr>
                        <tr>
                          <td bgcolor="#a0a0a0" colspan=2></td>
                        </tr>
<?php if($user->user_type=="admin"){ ?>
                        <tr>
                          <td width="10"></td>
                          <td style="padding:3px">- <a href="page.php?where=users&what=users">Users</a></td>
                        </tr>
<tr>
                          <td width="10"></td>
                          <td style="padding:3px">- <a href="page.php?where=admin&what=settings">System settings</a></td>
                        </tr>
<?php }; ?>
                        <tr>
                          <td width="10"></td>
                          <td style="padding:3px">- <a href="page.php?where=admin&what=password">Change password</a></td>
                        </tr>
        </table> 
		  <table width="100%" cellpadding="0" cellspacing="0">
			<tr>
			  <td rowspan="9" bgcolor="#e4e4e4" width="5" style="padding:3px"></td>
			  <td bgcolor="#a0a0a0" colspan=2></td>
			</tr>
			<tr bgcolor="#f4f4f4">
			  <td width="10"><img src="images/arrow_down.gif"></td>
			  <td style="padding:3px"><b>Miscellaneous</b></td>
			</tr>	
			<tr>
			  <td bgcolor="#a0a0a0" colspan=2></td>
			</tr>
			<tr>
			  <td width="10"></td>
			  <td style="padding:3px">- <a href="logout.php">Log Out</a></td>
			</tr>
			<tr>
			  <td bgcolor="#a0a0a0" colspan=2></td>
			</tr>
        </table> 

		</td>
	</tr>
</table>
