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

if ($_POST['save']==" save " && $sec->CheckCsrfToken($_POST['token']))
{
	
	/*** data validation **/
	$event->type = $_POST['type'];
	$event->category = $_POST['category'];
	$event->bug_no = $_POST['bug_no'];
	$event->change_req_no = $_POST['change_req_no'];
	$event->start_date = $_POST['type'];
	$event->end_date = $_POST['type'];
	
	$event->sites = implode(",",$_POST['sites']);
	
	$event->description = $_POST['description'];
	
	//Calculate timestamp
	if ($_POST['start_day_time'] == 'pm' && $_POST['start_hour']!='12') $start_hour = (int)$_POST['start_hour'] + 12; 
	else $start_hour = (int)$_POST['start_hour'];
	
	if ($_POST['end_day_time'] == 'pm' && $_POST['end_hour']!='12') $end_hour = (int)$_POST['end_hour'] + 12; 
	else $end_hour = (int)$_POST['end_hour'];
	
	//Calculate timestamp
	if ($_POST['start_day_time'] == 'am' && $_POST['start_hour']=='12') $start_hour =0; 
	if ($_POST['end_day_time'] == 'am' && $_POST['end_hour']=='12') $end_hour = 0; 
	
	$event->start_date = strtotime($_POST['startdate']) + $start_hour*60*60 + (int)$_POST['start_minutes']*60;
	$event->end_date = strtotime($_POST['enddate']) + $end_hour*60*60 + (int)$_POST['end_minutes']*60;
	//print_r($event);
	
	if (!$event->type) $error[] = 'ERROR: select event type!';
	if (!$event->category) $error[] = 'ERROR: select event category!';
	if (!$event->start_date) $error[] = 'ERROR: insert event start date!';
	if (!$event->end_date) $error[] = 'ERROR: insert event end date!';
	if (!$event->sites) $error[] = 'ERROR: insert event affected sites!';
	if (!$event->description) $error[] = 'ERROR: insert event description!';
	if($event->start_date >= $event->end_date) $error[] = 'ERROR: event end date must be bigger then start date!';
	//Change password, only if password field is setup

	
	if (!$error)
	{
		
		if (!$cid)
		{
			$cid = $db->InsertObject("events", $event);
			$_GET['id'] = $cid;

			$sucess = 'New event is added!';
		}
		else
		{
			$event->id = $cid;
			$db->UpdateObject("events", $event);
			
			$sucess = 'Event is updated!';
		}
	}


	/**saving data**/
}

	/*** taking customers data **/
	//explode all event sites
	if (!$event)  $event = $db->SelectOne("SELECT * FROM events where id=%d", $_GET['id']);
	$allEventSites = explode(",",$event->sites);
	//explode events start and end date/times
	if($event->start_date){
		$start_date = date("m/d/Y",$event->start_date);
		$start_hour = date("h",$event->start_date);
		$start_minutes = date("i",$event->start_date);
		$start_daytime = date("a",$event->start_date);
	}else{
		$start_date = date("m/d/Y",time());
		$start_hour = date("h",time());
		$start_minutes = date("i",time());
		$start_daytime = date("a",time());
	}
	if($event->end_date){
		$end_date = date("m/d/Y",$event->end_date);
		$end_hour = date("h",$event->end_date);
		$end_minutes = date("i",$event->end_date);
		$end_daytime = date("a",$event->end_date);
	}
	
	
	$allSites = $db->SelectAll("SELECT * FROM sites order by site_order");
?>
<br>
<SCRIPT language=javascript src="./js/cal2.js"></SCRIPT>
<SCRIPT language=javascript src="./js/cal_conf2.js"></SCRIPT>

<script type="text/javascript" language="JavaScript">
function validateForm(theForm)
{ 	
  if (!validRequired(theForm.startdate,"Start Date")){return false;}
  if (!validRequired(theForm.enddate,"End Date")){return false;}
  if (!validRequired(theForm.description,"Description")){return false;}
  return true;
}
</script>

<br><br>
<table width=100% cellpadding=3 cellspacing=1>

    <tr style="height:20px">
        <td valign=bottom>
            <?php 
            if ($cid) echo "<b>Event Details</b>";
            else echo "<b>Add Event</b>";
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


<form name = "edit" method=post action="page.php?where=app&what=event_edit&id=<?php echo $cid;?>" enctype="multipart/form-data" onsubmit="return validateForm(this)">
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
          <td>Event Type *</td>
          <td colspan=4>
          	<select name="type" class=inText>
          	<?php foreach($allEventTypes as $eventTypeKey => $eventTypeValue){ ?>
          		<option value="<?=$eventTypeKey?>" <?php if($event->type == $eventTypeKey) echo "selected";?>><?=$eventTypeValue?></option>
          	<?php }; ?>	
          	</select>
          </td>
        </tr>
		<tr bgcolor='#ffffff'>
          <td></td>
          <td>Event Category *</td>
          <td colspan=4><select name="category" class=inText>
          	<?php foreach($allEventCategories as $eventCatKey => $eventCatValue){ ?>
          		<option value="<?=$eventCatKey?>" <?php if($event->category == $eventCatKey) echo "selected";?>><?=$eventCatValue?></option>
          	<?php }; ?>	
          	</select></td>
        </tr>        
		<tr bgcolor='#ffffff'>
          <td></td>
          <td>Bug# (optional)</td>
          <td colspan=4><input name=bug_no type='text' class=inText id="bug_no" value="<?php et($event->bug_no); ?>"></td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>Change Request# (optional)</td>
          <td colspan=4><input name=change_req_no type='text' class=inText id="change_req_no" value="<?php et($event->change_req_no); ?>"></td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>Start Date *</td>
          <td colspan=4>
          	<input type='text' class=inText readonly name="startdate" id="startdate" value="<?=$start_date?>" style="width:100px">
                <A href="javascript:showCal('startdate')"><IMG src="images/kronolith.gif" border=0></A>&nbsp;
            <select name="start_hour" class=inText>
          	<?php foreach($defHours as $hours){ ?>
          		<option value="<?=$hours?>" <?php if($start_hour == $hours) echo "selected";?>><?=$hours?></option>
          	<?php }; ?>	
          	</select> :
          	<select name="start_minutes" class=inText>
          	<?php foreach($defMinutes as $minutes){ ?>
          		<option value="<?=$minutes?>" <?php if($start_minutes == $minutes) echo "selected";?>><?=$minutes?></option>
          	<?php }; ?>	
          	</select>
          	<select name="start_day_time" class=inText>
          		<option value="am" <?php if($start_daytime == 'am') echo "selected";?>>AM</option>
          		<option value="pm" <?php if($start_daytime == 'pm') echo "selected";?>>PM</option>
          	</select>    
          </td>
        </tr>
		
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>End Date *</td>
          <td colspan=4>          	
          	<input type='text' class=inText readonly name="enddate" value="<?=$end_date?>" style="width:100px">
            <A href="javascript:showCal('enddate')"><IMG src="images/kronolith.gif" border=0></A>&nbsp;
				 <select name="end_hour" class=inText>
          	<?php foreach($defHours as $hours){ ?>
          		<option value="<?=$hours?>" <?php if($end_hour == $hours) echo "selected";?>><?=$hours?></option>
          	<?php }; ?>	
          	</select> :
          	<select name="end_minutes" class=inText>
          	<?php foreach($defMinutes as $minutes){ ?>
          		<option value="<?=$minutes?>" <?php if($end_minutes == $minutes) echo "selected";?>><?=$minutes?></option>
          	<?php }; ?>	
          	</select>
          	<select name="end_day_time" class=inText>
          		<option value="am" <?php if($end_daytime == 'am') echo "selected";?>>AM</option>
          		<option value="pm" <?php if($end_daytime == 'pm') echo "selected";?>>PM</option>
          	</select>    
			
			</td>
        </tr> 
        
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>Site(s) *</td>
          <td colspan=4>
          <select multiple size=5  name="sites[]" class=inText>
          	<?php foreach($allSites as $site){ ?>
          		<option value="<?=$site->id?>" <?php if(array_search($site->id, $allEventSites)!==false ) echo "selected";?>><?=$site->sitename?></option>
          	<?php }; ?>	
		  </select>
		




          </td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td></td>
          <td>Description *</td>
          <td colspan=4>
          	<textarea class="inText" cols="50" rows="6" name="description"><?=$event->description?></textarea>
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







    
