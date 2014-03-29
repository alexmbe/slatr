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

/*****/
if (isset($_POST) && $sec->CheckCsrfToken($_POST['token']))
{
	/*** data validation **/
	$oldPassword = $_POST['old_password'];
	$szPassword = $_POST['password'];
	$szPassword2 = $_POST['password2'];
	
		
	if ($szPassword=='') $error[] = 'insert password!';
	if ($szPassword!=$szPassword2) $error[] = 'password mismatch!';
	
	if (!$error)
	{		
		if (!$userManager->CheckPswd($user, $oldPassword)) {
			$error[] = 'old password is incorrect!';
		} else {
			$salt = $sec->NewSalt();
			$hashedPassword = $userManager->HashPswd($szPassword, $salt);
			$sql = "UPDATE users
	           			SET salt = '%s', password = '%s'
                        WHERE id = %d";

			$res = $db->SqlQuery($sql, $salt, $hashedPassword, $_SESSION['userLoginId']);
			if ($res) $sucess = 'Password changed!';
		}
	}
	/**saving data**/
}

?>


<br><br>
<table width=100% cellpadding=3 cellspacing=1>

    <tr style="height:20px">
        <td valign=bottom>
            <?php 
            echo "<b>Change Your password</b>";
              ?>
        </td>
    </tr>
    <tr style="height:3px" bgcolor="#a0a0a0">
        <td></td>
    </tr>
    <tr style="height:20px">
        <td></td>
    </tr>
</table>


<form name = "edit" method=post action="page.php?where=admin&what=password">
	<?php echo $sec->CsrfFormHtml(); ?>
    <table width=550 cellpadding=4 cellspacing=0 bgcolor='#e4e4e4' style="border:1px solid #e4e4e4">
        <tr>
            <td colspan=3 align=left>
            </td>
            <td colspan=3 align=right>
                <input type="submit" class=submit name="save" value=" save ">
            </td>
        </tr>
        <tr bgcolor='#ffffff' class=error>
            <td colspan=6>
                <?php 
                	if ($sucess) et($sucess);
                	else{
                		foreach($error as $error_value){
                			et($error_value).'<br>';
                		};
                	};
                	
                ?>
            </td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>Old Password  *</td>
          <td colspan=4><input name=old_password type='password' class=inText id="old_password" size="20"></td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>New Password  *</td>
          <td colspan=4><input name=password type='password' class=inText id="password" size="20"></td>
        </tr>
		<tr bgcolor='#ffffff'>
          <td></td>
          <td>Confirm Password *</td>
          <td colspan=4><input name=password2 type='password' class=inText id="password2" size="20"></td>
        </tr>
    </table>
</form>




    
    


