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

	//Delete site
	if($_GET[action]=='delete' && $_GET['id'] && $sec->CheckCsrfToken($_GET['token'])){
		$obj = (object) NULL;
		$obj->id = $_GET['id'];
		$db->DeleteObject("sites", $obj);
	}
	
	//move selected portfolio up
	if (($_GET['action']=='site_up')&&($_GET['site_id']) && $sec->CheckCsrfToken($_GET['token'])){
		$site = $db->SelectOne("select * from sites where id = %d", $_GET['site_id']);
		$query = "select * from sites where site_order < %d order by site_order desc";
		
		if($site_change = $db->SelectOne($query, $site->site_order)){
		  	$temp_order = $site->site_order;
			$site->site_order = $site_change->site_order;
			$site_change->site_order = $temp_order;	
		  	$db->UpdateObject("sites", $site);
		  	$db->UpdateObject("sites", $site_change);
	  }		
	}
	
	//move selected portfolio down
	if (($_GET['action']=='site_down')&&($_GET['site_id']) && $sec->CheckCsrfToken($_GET['token'])){
		$site = $db->SelectOne("select * from sites where id = %d", $_GET['site_id']);
		$query = "select * from sites where site_order > %d order by site_order asc";
		
		if($site_change = $db->SelectOne($query, $site->site_order)){
		  	$temp_order = $site->site_order;
			$site->site_order = $site_change->site_order;
			$site_change->site_order = $temp_order;	
		  	$db->UpdateObject("sites", $site);
		  	$db->UpdateObject("sites", $site_change);
	  }	
	}
	
	//Get all sites
	$allSites = $db->SelectAll("SELECT * FROM sites order by site_order");
?>

<b>Sites List</b>
<br><br>
<table width="100%" cellpadding=3 cellspacing=1>
    <tr style="height:3px" bgcolor="#a0a0a0">
        <td colspan=2>
        </td>
    </tr>

</table>


<table width="100%" cellpadding=3 cellspacing=1>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>

        <a href="page.php?where=app&what=sites_edit"><img src="images/add.gif" border=0 title="Add new site" vspace="10"></a>
            <table width=50% cellpadding=3 cellspacing=1 bgcolor="#e4e4e4">
				
            	<tr bgcolor="#f4f4f4">
                   
                    <td align=center>
                        <b>Site Name</b>
                    </td>
                    <td align=center>
                        <b>SLA</b>
                    </td>
                    <td align=center>
                        <b>Location</b>
                    </td>
					<td align=center>
                        <b>Position</b>
                    </td>
                    <td align=center><b>Action</b></td>
                </tr>
            <?php 
                foreach($allSites as $site){
            ?>
                <tr bgcolor="#ffffff">
					<td align=left><?php et($site->sitename); ?>&nbsp;</td>
					<td align=left><?php et($site->sla); ?>&nbsp;</td>
					<td align=left><?php et($site->location);?>&nbsp;</td>
					<td align=center>
						<a href="page.php?where=app&what=sites&action=site_up&site_id=<?=$site->id?>&token=<?php echo $sec->GetCsrfToken(); ?>"><img src="./images/up.gif" border="0" alt="One position up" /></a>&nbsp;
			    		<a href="page.php?where=app&what=sites&action=site_down&site_id=<?=$site->id?>&token=<?php echo $sec->GetCsrfToken(); ?>"><img src="./images/down.gif" border="0" alt="One position down" /></a>&nbsp;					
					</td>
					
                    <td align=center>
                       <a href="page.php?where=app&what=sites_edit&id=<?php echo $site->id;?>"><img src='images/edit.gif' border=0></a>
                       <a href="page.php?where=app&what=sites&action=delete&id=<?php echo $site->id;?>&token=<?php echo $sec->GetCsrfToken(); ?>" onClick="return confirm('Are you sure?')"><img src='images/del.gif' border=0></a> 
                    </td>
                </tr>
            <?php 
                }   
            ?>
                <tr>
                    <td></td>
                    <td><img src='images/spacer.gif' height=1></td>
                    <td><img src='images/spacer.gif' height=1></td>
                    <td><img src='images/spacer.gif' height=1></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
