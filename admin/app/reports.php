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

set_time_limit(600);

$allSites = $db->SelectAll("SELECT * FROM sites order by site_order");


?>

<SCRIPT language=javascript src="./js/cal2.js"></SCRIPT>
<SCRIPT language=javascript src="./js/cal_conf2.js"></SCRIPT>
<script type="text/JavaScript">
<!--
function show(id)
{
     if (document.getElementById(id).style.display == 'none'){
          document.getElementById(id).style.display = 'inline';
     }
}
//-->
<!--
function hide(id)
{
     if (document.getElementById(id).style.display == 'none'){
          document.getElementById(id).style.display = 'none';
     }
     else{
          document.getElementById(id).style.display = 'none';
     }
}
//-->
</script>
<b>Reports</b>
<br><br>
<table width="100%" cellpadding=3 cellspacing=1>
    <tr style="height:3px" bgcolor="#a0a0a0">
        <td colspan=4>
        </td>
    </tr>
    <form name="edit" method="post" style="display:inline" enctype="multipart/form-data">
    <tr>
        <td>
        <b>Select report range:&nbsp;&nbsp;</b> 
                <input type='text' class=inText readonly name="startdate" id="startdate" value="<?=($_POST['startdate'])?($_POST['startdate']):(date("m/01/Y",time()))?>" style="width:100px">
                <A href="javascript:showCal('startdate')"><IMG src="images/kronolith.gif" border=0></A>&nbsp;&nbsp;
                <input type='text' class=inText readonly name="enddate" value="<?=($_POST['enddate'])?($_POST['enddate']):(date("m/d/Y",time()));?>" style="width:100px">
            	<A href="javascript:showCal('enddate')"><IMG src="images/kronolith.gif" border=0></A>&nbsp;
        </td>
        <td>
       		<b>Affected sites:</b> 
        </td>
        <td align=right>
            	 <select multiple size=5  name="sites[]" class=inText>
            	 <option value="all" <?php if((array_search('all', $_POST['sites'])!==false) || !$_POST['sites']) echo "selected";?>>All Sites</option>
          	<?php foreach($allSites as $site){ ?>
          		<option value="<?=$site->id?>" <?php if(array_search($site->id, $_POST['sites'])!==false ) echo "selected";?>><?=$site->sitename?></option>
          	<?php }; ?>	
		  </select>
        </td>
        <td align="left" width="30%">
       		&nbsp;&nbsp;
        </td>
    </tr>
    <tr>
        <td colspan=3 align="center">
        	<input name="report" type="submit" class=submit value="Generate Report"><br><br>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr style="height:3px" bgcolor="#a0a0a0">
        <td colspan=4>
        </td>
    </tr>
     </form>
</table>


<?php 
		if (!$_POST['report']){
			$_POST['startdate'] = date("m/01/Y",time());
			$_POST['enddate'] = date("m/d/Y",time());
			$_POST['sites'][] = "all";
		}
		$startdate = strtotime($_POST['startdate']);
		$enddate = strtotime($_POST['enddate']) + 24*60*60;
		//check if some of data is missing or not correct
		if ($startdate <= 0 || $enddate <= 0 || $startdate >= $enddate || !$_POST['sites']){
			echo "<br><b>No Results</b>";
			exit();
		}else{
			 $allEvents = $db->SelectAll("select * from events WHERE 
			 		(end_date > %d AND end_date <= %d) OR
			 		(start_date >= %d AND start_date < %d)",
				$startdate, $enddate, $startdate, $enddate
				);
	  		
			 $period_in_min = ($enddate - $startdate)/60;		
		}
		$_SESSION['startdate'] = $startdate;
		$_SESSION['enddate'] = $enddate;
		$_SESSION['sites'] = $_POST['sites'];
		                
?>		
<br>
<table width="100%" cellpadding=3 cellspacing=1>
    <tr>
        <td>Reported period: <b><?= date("F j, Y",strtotime($_POST['startdate']))." - ".date("F j, Y",strtotime($_POST['enddate']));?></b></td>
        <td align="center">
        	<a href="./excel.php?token=<?php echo $sec->GetCsrfToken(); ?>"><img src="./images/excel.gif" width="27" alt="Download this report" border="0"></a><br>
        	<a href="./excel.php?token=<?php echo $sec->GetCsrfToken(); ?>">Download this report</a>
        </td>
    </tr>
</table>	
<table width="100%" cellpadding=3 cellspacing=1>
    <tr>
        <td>&nbsp;</td>
    </tr>
	<tr>
        <td><b>All sites</b></td>
    </tr>
    <tr>
        <td>
            <table width=100% cellpadding=3 cellspacing=1 bgcolor="#e4e4e4">
                <tr bgcolor="#f4f4f4">
                    <td width=6% align=center>
                         <b>Site Name</b>
                    </td>
                    <td align=center>
                        <b>Uptime <br>(%)</b>
                    </td>
                    <td align=center>
                         <b>SLA <br>(%)</b>
                    </td>
                    <td align=center>
                         <b>Full outage <br>(min)</b>
                    </td>
                    <td align=center>
                        <b>Partial outage <br>(min)</b>
                    </td>
                    <td align=center>
                       <b>Maintenance <br>(min)</b>
                    </td>
                </tr>
            <?php
  				$total_full_outgage = 0;
 				$total_partial_outgage = 0;
 				$total_maintanance = 0;
 				$total_uptime = 0;
 				$allSitesId = array();
 				if((array_search('all', $_POST['sites'])!==false)){          	 
 					$allSites = $db->SelectAll("select * from sites order by site_order");
 					foreach($allSites  as $site) $allSitesId[] = $site->id;
 				}else{
 					$allSitesId = $_POST['sites'];
 				}
 				$no_of_sites = 0;
 				foreach($allSitesId as $siteId){
 					$no_of_sites++;
 					$site = $db->SelectOne("select * from sites where id=%d order by site_order", $siteId);
 					$full_outgage = 0;
 					$partial_outgage = 0;
 					$maintanance = 0;
 					foreach($allEvents as $event){
 						//check if site is affected by event
 						$allAffectedSites = explode(",",$event->sites); 
 						
 						if(array_search($site->id, $allAffectedSites)!==false){ 
 							if($event->start_date < $startdate) $event->start_date = $startdate;
 							if($event->end_date > $enddate) $event->end_date = $enddate;
 								
 							if($event->type == '1') $full_outgage += ($event->end_date - $event->start_date)/60;
 							if($event->type == '2') $partial_outgage += ($event->end_date - $event->start_date)/60;
 							if($event->type == '3') $maintanance += ($event->end_date - $event->start_date)/60; 	
 						}
 					}
 					$uptime = number_format((($period_in_min - $full_outgage)/$period_in_min)*100,2);
 					$total_uptime += $uptime;
 					$total_full_outgage += $full_outgage;
 					$total_partial_outgage += $partial_outgage;
 					$total_maintanance += $maintanance;
 				

if ((float)$uptime < (float)$site->sla)
{
	$column_color = "#ff4d4d";
}
elseif ((float)$uptime > (float)$site->sla && (float)$uptime < 100)
{
	$column_color = "#ffff00";
}
else
{
	$column_color = "#ffffff";
}	
            ?>
                <tr bgcolor="<?php et($column_color);?>">
                    <td align=right><?php et($site->sitename);?>&nbsp;</td>
                    <td align='center'>&nbsp;<?php et($uptime);?></td>
                    <td align='center'>&nbsp;<?php et($site->sla);?></td>
                    <td align='center'>&nbsp;<?php et($full_outgage);?></td>
                    <td align='center'>&nbsp;<?php et($partial_outgage);?></td>
					<td align='center'>&nbsp;<?php et($maintanance);?></td>
                </tr>
            <?php 
                }
                //$total_uptime = number_format((($period_in_min - $total_full_outgage)/$period_in_min)*100,2);; 
                 
            ?>
                <tr>
                    <td></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                </tr>
                <tr bgcolor="#ffffff">
                    <td align=right>Total:</td>
                    <td align='center'>&nbsp;<?php et(number_format($total_uptime/$no_of_sites,2));?></td>
                    <td align='center'>/</td>
                    <td align='center'>&nbsp;<?php et($total_full_outgage);?></td>
                    <td align='center'>&nbsp;<?php et($total_partial_outgage);?></td>
					<td align='center'>&nbsp;<?php et($total_maintanance);?></td>
                </tr>
            </table>
        </td>
    </tr>
    
     <tr>
        <td>&nbsp;</td>
    </tr>
     <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><b>Events summary</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <?php
        	//JUST FOR EXPAND/COLLAPSE
        	$code_expand = "";
        	$code_collapse = "";
        	
        	                $allSitesId = array();
                if((array_search('all', $_POST['sites'])!==false)){          	 
 					$allSites = $db->SelectAll("select * from sites order by site_order");
 					foreach($allSites  as $site) $allSitesId[] = $site->id;
 				}else{
 					$allSitesId = $_POST['sites'];
 				}
 				
                foreach($allEvents as $event)
                {
                	if($event->start_date < $startdate) $event->start_date = $startdate;
 					if($event->end_date > $enddate) $event->end_date = $enddate;
 							
                	$allSites = array();
                    $allSitesIds = explode(",", $event->sites);
                    $event_ok = '0';
                    foreach($allSitesIds as $siteId){
                    	//foreach($_POST['sites'] as $siteReportId){
 							if(array_search($siteId, $allSitesId) !== false){
 								$event_ok='1';
 							}
                	//}
						$allSites[] = $db->SelectOne("select * from sites where id=%d order by site_order", $siteId);
                    }
                    if ($event_ok == '0') continue; 
                	if(strlen($event->description)>50){    
            			$code_expand .= "show('desc_{$event->id}');hide('more_{$event->id}');";
            			$code_collapse .= "hide('desc_{$event->id}');show('more_{$event->id}');";
                	}    	
            }
//        	echo "<a href='#' onclick=\"{$code_expand};return true;\">Expand All</a>";
//        	echo "<a href='#' onclick=\"{$code_collapse};return true;\">Collapse All</a>";
        ?>	
        	 </td>
    </tr>
    <tr>
        <td>
            <table width=100% cellpadding=3 cellspacing=1 bgcolor="#e4e4e4">
                <tr bgcolor="#f4f4f4">
                    <td align=center>
                    	<b>Event Type</b>
                    </td>
                    <td align=center>
                    	<b>Event Category</b>
                    </td>
                    <td align=center>
                    	<b>Start Date</b>
                    </td>
                    <td align=center>
                    	<b>End Date</b>
                    </td>
                    <td align=center>
                       <b>Duration</b>
                    </td>
                    <td align=center>
                       <b>Affected Site(s)</b>
                    </td>
                    <td align=center>
                       <b>Description</b>&nbsp;&nbsp;
                       <?
                       	echo "<a href='#event1' onclick=\"{$code_expand};return true;\"><img src='images/plus.gif' border=0></a>&nbsp;&nbsp;";
        				echo "<a href='#event1' onclick=\"{$code_collapse};return true;\"><img src='images/minus.gif' border=0></a>";
        				?>
                    </td>
                </tr>
            <?php 
                $allSitesId = array();
                if((array_search('all', $_POST['sites'])!==false)){          	 
 					$allSites = $db->SelectAll("select * from sites order by site_order");
 					foreach($allSites  as $site) $allSitesId[] = $site->id;
 				}else{
 					$allSitesId = $_POST['sites'];
 				}
 				
                foreach($allEvents as $event)
                {
                	if($event->start_date < $startdate) $event->start_date = $startdate;
 					if($event->end_date > $enddate) $event->end_date = $enddate;
 							
                	$allSites = array();
                    $allSitesIds = explode(",", $event->sites);
                    $event_ok = '0';
                    foreach($allSitesIds as $siteId){
                    	//foreach($_POST['sites'] as $siteReportId){
 							if(array_search($siteId, $allSitesId) !== false){
 								$event_ok='1';
 							}
                	//}
						$allSites[] = $db->SelectOne("select * from sites where id=%d order by site_order", $siteId);
                    }
                    if ($event_ok == '0') continue; 
                    
                	
 					
            ?>
                <tr bgcolor="#ffffff" id='event1'>
                    <td align='center'>&nbsp;<?php et($allEventTypes[$event->type]);?></td>
                    <td align='center'>&nbsp;<?php et($allEventCategories[$event->category]);?></td>
                    <td align='center'>&nbsp;<?php echo date("m/d/Y",$event->start_date)."<br>".date("h:i a",$event->start_date);?></td>
                    <td align='center' >&nbsp;<?php echo date("m/d/Y",$event->end_date)."<br>".date("h:i a",$event->end_date);?></td>
					<td align='center'>&nbsp;<?php echo ($event->end_date-$event->start_date)/60;?> min</td>
					<td align='center'>&nbsp;
					<?php	foreach($allSites as $site){
							et($site->sitename)."<br>";      				
                    }
                    ?>
					</td>
                    <td align='left' width="28%">
                    <?php 
                    	et(substr($event->description,0,50));
                    	if(strlen($event->description)>50){ 
                    	echo "<span id='more_{$event->id}'><a href='#more_{$event->id}' onclick=\"show('desc_{$event->id}');hide('more_{$event->id}');return true;\"> more...</a></span>";
                    	echo "<span id='desc_{$event->id}' style='display:none'>".t(substr($event->description,50))."</span>";	
                    }
                    ?>
                    </td>
                </tr>
            <?php 
                }   
            ?>
                <tr>
                    <td></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
