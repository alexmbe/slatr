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

if (!defined('SLA') || !$user || !($user->user_type=="admin" || $user->user_type=="advanced")) {
	// Exit silently
	exit;
}

$cid = intval($_GET['id']);

/*****/
if (isset($_POST) && $sec->CheckCsrfToken($_POST['token']))
{
	/*** data validation **/
		
	if ($_POST['sitename']=='') $error[] = 'ERROR: insert sitename!';
	if ($_POST['sla']=='') $error[] = 'ERROR: insert sla!';
	if ($_POST['location']=='') $error[] = 'ERROR: insert location!';
	
	$site->sitename = $_POST['sitename'];
	$site->sla = $_POST['sla'];
	$site->location = $_POST['location'];

	//check if sitename already exist
	// commented out: erroneous and not used
	//if (!$db->SelectOne("select * from users where sitename='{$site->sitename}'")){
	//			}

	if($cid){ 
		if(($chkSite = $db->SelectOne("select * from sites where sitename='%s'", $site->sitename)) && ($chkSite->id!= $cid))
			$error[] = 'ERROR: site name already exist';		
	}else{
		if($db->SelectOne("select * from sites where sitename='%s'", $site->sitename)) $error[] = 'ERROR: site already exist';
	};

	
	if (!$error)
	{
		
		if (!$cid)
		{
			
			$cid = $db->InsertObject("sites", $site);
			$_GET['id'] = $cid;
			
			$site = $db->SelectOne("SELECT * FROM sites where id=%d", $cid);
			$site->site_order = $cid;
			$db->UpdateObject("sites", $site);
			
			$sucess = 'New site is added!';
			
		}
		else
		{	
			$site->id = $cid;
			$db->UpdateObject("sites", $site);
			
			$sucess = 'Site is updated!';
		}
	}


	/**saving data**/
}

$site = $db->SelectOne("SELECT * FROM sites where id=%d", $_GET['id']);

	
?>

<script type="text/javascript" language="JavaScript">
function validateForm(theForm)
{ 	
  if (!validRequired(theForm.sitename,"Site Name")){return false;}
  if (!validRequired(theForm.sla,"SLA")){return false;}
  if (!isNum(theForm.sla,"SLA")){return false;}
  if (!validRequired(theForm.location,"Location")){return false;}
  return true;
}
</script>

<br><br>
<table width=100% cellpadding=3 cellspacing=1>

    <tr style="height:20px">
        <td valign=bottom>
            <?php 
            if ($cid) echo "<b>Details of " . t($site->sitename) . "</b>";
            else echo "<b>Add new site</b>";
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


<form name = "edit" method=post action="page.php?where=app&what=sites_edit&id=<?php echo $cid;?>" onsubmit="return validateForm(this)">
	<?php echo $sec->CsrfFormHtml(); ?>
    <table width=550 cellpadding=4 cellspacing=0 bgcolor='#e4e4e4' style="border:1px solid #e4e4e4">
        <tr>
            <td colspan=3 align=left>&nbsp;
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
          <td>Site name *</td>
          <td colspan=4><input name="sitename" type='text' class=inText id="sitename" value="<?php et($site->sitename); ?>"></td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>SLA * </td>
          <td colspan=4><input name="sla" type='text' class=inText id="sla" value="<?php et($site->sla); ?>"> %</td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>Location * </td>
          <td colspan=4><input name="location" type='text' class=inText id="location" value="<?php et($site->location); ?>"></td>
        </tr>
        
    </table>
</form>




    
    


