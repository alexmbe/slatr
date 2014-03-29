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

if (!defined('SLA') || !$user || $user->user_type!="admin") {
	// Exit silently
	exit;
}

$cid = intval($_GET['id']);

if ($_POST['save']==" save " && $sec->CheckCsrfToken($_POST['token']))
{
	/*** data validation **/
	$sel_user->firstname = $_POST['firstname'];
	$sel_user->lastname = $_POST['lastname'];
	$sel_user->username = $_POST['username'];
	$sel_user->email = $_POST['email'];
	$sel_user->user_type = $_POST['type'];
	
	if (!$sel_user->firstname) $error[] = 'ERROR: insert firstname!';
	if (!$sel_user->lastname) $error[] = 'ERROR: insert lastname!';
	if (!$sel_user->username) $error[] = 'ERROR: insert username!';
	if (!$sel_user->email) $error[] = 'ERROR: insert email!';
	
	//Change password, only if password field is setup
	
	//if new user is added, check for password
	if(!$cid || $_POST['password'] != ''){
		$sel_user->salt = $sec->NewSalt();
		$sel_user->password = $userManager->HashPswd($_POST['password'], $sel_user->salt);
		if ($_POST['password'] == '') $error[] = 'ERROR: insert password!';
		if ($_POST['password2'] == '') $error[] = 'ERROR: insert password 2!';
		if ($_POST['password2'] != $_POST['password']) $error[] = 'ERROR: password mismatch!';			
	}
	//check if username or email exist in db
	if($cid){ 
		if(($chkUser = $userManager->GetUserByUsername($sel_user->username)) && ($chkUser->id!= $cid))
			$error[] = 'ERROR: username already exist in system';
		if(($chkUser = $userManager->GetUserByEmail($sel_user->email)) && ($chkUser->id!= $cid))
			$error[] = 'ERROR: email already exist in system';		
	}else{
		if($userManager->GetUserByUsername($sel_user->username)) $error[] = 'ERROR: username already exist in system';
		if($userManager->GetUserByEmail($sel_user->email)) $error[] = 'ERROR: email already exist in system';
	};
	
	if (!$error)
	{
		
		if (!$cid)
		{ 
			$id = $userManager->AddUser($sel_user);
			
			if ($id)
			{
				$cid = $id;
				$_GET['id'] = $id;
			}

			$sucess = 'new user added!';
		}
		else
		{
			$sel_user->id = $cid;
			$userManager->UpdateUser($sel_user);
			$sucess = 'user is updated!';
		}
	}


	/**saving data**/
}

	/*** taking customers data **/
	$sel_user = $userManager->GetUserById($cid);	
?>
<script type="text/javascript" language="JavaScript">
function validateForm(theForm)
{ 	
  if (!validRequired(theForm.firstname,"First Name")){return false;}
  if (!validRequired(theForm.lastname,"Last Name")){return false;}
  if (!validRequired(theForm.username,"Username")){return false;}
  if (!validRequired(theForm.email,"Email")){return false;}
  if (!validEmail(theForm.email,"Email")){return false;}
  if (!validRequired(theForm.username,"Username")){return false;}
  if (!isequal(theForm.password,theForm.password2,"Passwords")){return false;} 
  return true;
}
</script>

<br><br>
<table width=100% cellpadding=3 cellspacing=1>

    <tr style="height:20px">
        <td valign=bottom>
            <?php 
            if ($cid) echo "<b>Details of ". t($sel_user->firstname . ' ' . $sel_user->lastname) . "</b>";
            else echo "<b>Add User</b>";
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


<form name = "edit" method=post action="page.php?where=users&what=user_edit&id=<?php echo $cid;?>" onsubmit="return validateForm(this)">
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
          <td>First Name *</td>
          <td colspan=4><input name=firstname type='text' class=inText id="firstname" value="<?php et($sel_user->firstname); ?>"></td>
        </tr>
		<tr bgcolor='#ffffff'>
          <td></td>
          <td>Last Name *</td>
          <td colspan=4><input name=lastname type='text' class=inText id="lastname" value="<?php et($sel_user->lastname); ?>"></td>
        </tr>        
		<tr bgcolor='#ffffff'>
          <td></td>
          <td>Username *</td>
          <td colspan=4><input name=username type='text' class=inText id="username" value="<?php et($sel_user->username); ?>"></td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>Email *</td>
          <td colspan=4><input name=email type='text' class=inText id="email" value="<?php et($sel_user->email); ?>"></td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>Password *</td>
          <td colspan=4><input name=password type='password' class=inText id="password" value=""></td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>Confirm Password *</td>
          <td colspan=4><input name=password2 type='password' class=inText id="password2" value=""></td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>Type *</td>
          <td colspan=4>
          <select name="type" class=inText>
          	<option value="normal" <?php if($sel_user->user_type == "normal") echo "selected";?>>Normal</option>	
          	<option value="advanced" <?php if($sel_user->user_type == "advanced") echo "selected";?>>Advanced</option>
          	<option value="admin" <?php if($sel_user->user_type == "admin") echo "selected";?>>Admin</option>
          </select>
          </td>
        </tr>
        
        
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
       
        <tr bgcolor='#ffffff'>
          <td colspan="6"><hr></td>
        </tr>
        

</form>        

        
        </table></td>
          <td>&nbsp;</td>
        </tr>
        
    </table>

