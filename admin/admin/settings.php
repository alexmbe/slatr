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

/*****/
if ($_POST['save'] && $sec->CheckCsrfToken($_POST['token']))
{

	$error = '';
	foreach ($_POST as $key => $value) {
   		if ($key = strstr($key,"sett_id_")) {
   			$key = str_replace('sett_id_','',$key);
   			if ($key!='' && $value!=''){
   				$settings = $configManager->GetConfigById($key);
   				$settings->value = $value;
   				$configManager->UpdateConfig($settings);
   			}
   		}
   		
    }
}
	$settings = $configManager->GetAllConfigs();
?>
<br><br>
<table width=100% cellpadding=3 cellspacing=1>

    <tr style="height:20px">
        <td valign=bottom>
            <?php 
            echo "<b>System Settings</b>";
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


<form name = "edit" method=post action="page.php?where=admin&what=settings">
	<?php echo $sec->CsrfFormHtml(); ?>
    <table width=550 cellpadding=4 cellspacing=0 bgcolor='#e4e4e4' style="border:1px solid #e4e4e4">
        <tr>
            <td colspan=2 align=left>
           	&nbsp;
            </td>
            <td colspan=4 align=right>
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
<?php foreach ($settings as $config): ?>
		<tr bgcolor='#ffffff'>
          <td></td>
          <td><?php et($config->title); ?> <i>(<?php et($config->default_value); ?>)</i>:*</td>
          <td colspan=4><input name='sett_id_<?php echo $config->id; ?>' type='text' class=inText id='sett_id_<?php echo $config->id; ?>' value='<?php et($config->value); ?>' size=40>&nbsp;&nbsp;
          							<img src='./images/alert.gif' alt='<?php et($config->description); ?>' /></td>
        </tr>
<?php endforeach; ?>
        
        <tr bgcolor='#ffffff'>
          <td></td>
          <td colspan=6 align=left>
          	
          <br>
          &nbsp;</td>
        </tr>
       
        

    </table>
</form>







    
    


