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
	
	//Delete loan product
	if($_GET[action]=='delete' && $_GET['id'] && $sec->CheckCsrfToken($_GET['token'])){
		$obj = (object) NULL;
		$obj->id = $_GET['id'];
		$db->DeleteObject("events", $obj);
	}
	

    /***setting up the type of the customers that must be shown**/
    if ($_POST['act'] == 'find')
        $_SESSION['sla']['events']['find'] = $_POST['find'];

    /**setting order**/
    if ($_GET['order'])
    {
        ($_GET['order'] == $_SESSION['sla']['events']['order'] && $_SESSION['sla']['events']['how'] == 'DESC')
            ? ($_SESSION['sla']['events']['how'] = 'ASC')
            : ($_SESSION['sla']['events']['how'] = 'DESC');

        $_SESSION['sla']['events']['order'] = $_GET['order'];
    }

    // Make sure this value is one of the two possible
    if ($_SESSION['sla']['events']['how'] != 'ASC') {
        $_SESSION['sla']['events']['how'] = 'DESC';
    }


    /***setting num*/
    if (!$_GET['num'] || ($_GET['num'] < 3)) {
        $num = $_CONFIG['no_of_res_per_page'];
        if (!$num) {
            $num = 10;
        }
	} else {
        $num = $_GET['num'];
	}

     if (!$_REQUEST['pg'] || ($_REQUEST['pg'] < 1))
        $pg = 1;
    else
        $pg = $_REQUEST['pg'];

    $start = ($pg-1)*$num;


    /***building the order by array**/
    $_ORDER = array();
    $_ORDER['link'] = array();
    $_ORDER['img'] = array();
            
    $href = 'page.php?where=app&what=events';
    $_ORDER['link']['id'] = $href . "&order=id&num=$num&pg=$pg";
    $_ORDER['link']['type'] = $href . "&order=type&num=$num&pg=$pg";
    $_ORDER['link']['category'] = $href . "&order=category&num=$num&pg=$pg";
    $_ORDER['link']['start_date'] = $href . "&order=start_date&num=$num&pg=$pg";
	$_ORDER['link']['end_date'] = $href . "&order=end_date&num=$num&pg=$pg";
		
    
    $_ORDER['img']['id'] = "<img src='images/spacer.gif' width=9 height=10>";
    $_ORDER['img']['type'] = "<img src='images/spacer.gif' width=9 height=10>";
    $_ORDER['img']['category'] = "<img src='images/spacer.gif' width=9 height=10>";
    $_ORDER['img']['start_date'] = "<img src='images/spacer.gif' width=9 height=10>";
    $_ORDER['img']['end_date'] = "<img src='images/spacer.gif' width=9 height=10>";
    
    
    
    ($_SESSION['sla']['events']['order'])
        ? (true)
        : ($_SESSION['sla']['events']['order'] = 'id');
        
    if ($_SESSION['sla']['events']['how'] == 'ASC') {
         $img_name = 'order_asc.gif';
    } else {
         $img_name = 'order_desc.gif';
    }
    $_ORDER['img'][$_SESSION['sla']['events']['order']] = "<img src='images/$img_name'>";
    

?>

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

<b>Events List</b>
<br><br>
<table width="100%" cellpadding=3 cellspacing=1>
    <tr style="height:3px" bgcolor="#a0a0a0">
        <td colspan=2>
        </td>
    </tr>
    <tr>
        <td>
        </td>
        <td align=right>
            <form name="find" method="post" style="display:inline">
                Find event: 
                <input type="text" class=inText name=find value="<?php et($_SESSION['sla']['events']['find']); ?>">
                <input type="submit" class=submit value=" go ">
                <input type="hidden" name="act" value="find">
            </form>
            <form name="reset" method="post" style="display:inline">
                <input type="submit" class=submit name='submit' value="view all">
            </form>
        <td>
    </tr>
</table>


<?php
		
	if ($_POST[submit]=='view all') unset($_SESSION['sla']['events']['find']);
	
    /***setting up the type for query**/
    if ($_SESSION['sla']['events']['find']) {
            $_FIND = " AND (events.description LIKE '%s')";
            $_FIND_arg = "%" . $_SESSION['sla']['events']['find'] . "%";
    } else {
            $_FIND = '%s';
            $_FIND_arg = '';
    }
                    
    $_how = $_SESSION['sla']['events']['how'];
    switch ($_SESSION['sla']['events']['order'])
    {
        case 'id':
            $_order = 'events.id';
            break;
        case 'type':
            $_order = 'events.type';
            break;
       case 'category':
            $_order = 'events.category';
            break;
       case 'start_date':
            $_order = 'events.start_date';
            break;        
        case 'end_date':
            $_order = 'events.end_date';
            break;  
                  
        default:
            $_order = 'events.id';
            $_how = 'ASC';
            break;
    }
?>
<table width="100%" cellpadding=3 cellspacing=1>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>
             <b>
                <?php 
                    if ($_SESSION['sla']['events']['find'])
                        et("Events where description contains '{$_SESSION['sla']['events']['find']}'");
                ?>
             </b>
             
        </td>
    </tr>
    <tr>
        <td>
            <?php 
                $allEvents = $db->SelectAll(
					"select * from events 
		  				WHERE 1 $_FIND
    	            	ORDER BY %s %s
        	        	LIMIT %d, %d",
					$_FIND_arg, $_order, $_how, $start, $num
				);

                $total = count($allEvents);
            ?>
            <a href="page.php?where=app&what=event_edit"><img src="images/add.gif" border=0 title="Add new event" vspace="10"></a>
            <table width=100% cellpadding=3 cellspacing=1 bgcolor="#e4e4e4">
                <tr bgcolor="#ffffff">
                    <td colspan=6>
                        Results: <?php et(($start+1) . ' - ' . (($start+$num < $total)?($start+$num):($total) ));?> from <?php et($total); ?>
                    </td>
                    <td colspan=4 align=right>
                        <a href="<?php et($href . "&num=$num&pg=1"); ?>"><img src="images/first.gif" alt="first page" border=0></a>
                        <a href="<?php et($href . "&num=$num&pg=".($pg-1)); ?>"><img src="images/prior.gif" alt="prior page" border=0></a>

                        &nbsp;&nbsp;
                        <a href="<?php et($href . "&num=$num&pg=".($pg+1)); ?>"><img src="images/next.gif" alt="next page" border=0></a>
                        <a href="<?php et($href . "&num=$num&pg=".ceil($total/$num)); ?>"><img src="images/last.gif" alt="last page" border=0></a>

                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <form name="go" method="post" style="display:inline">
                        	<input type='text' name="pg" value="<?php et($pg); ?>" class=inText style="text-align:right; width:25px">
                        	<input type="submit" class=submit value=" go ">
                        </form>
                    </td>
                </tr>
                <tr bgcolor="#f4f4f4">
                    <td width=6% align=center>
                        <a href="<?php echo $_ORDER['link']['id'];?>"><b>id</b></a>
                        <?php echo $_ORDER['img']['id'];?>
                    </td>
                    <td align=center>
                       <a href="<?php echo $_ORDER['link']['type'];?>"><b>Event Type</b></a>
                        <?php echo $_ORDER['img']['type'];?>
                    </td>
                    <td align=center>
                        <a href="<?php echo $_ORDER['link']['category'];?>"><b>Event Category</b></a>
                        <?php echo $_ORDER['img']['category'];?>
                    </td>
                    <td align=center>
                        <a href="<?php echo $_ORDER['link']['start_date'];?>"><b>Start Date</b></a>
                        <?php echo $_ORDER['img']['start_date'];?>
                    </td>
                    <td align=center>
                        <a href="<?php echo $_ORDER['link']['end_date'];?>"><b>End Date</b></a>
                        <?php echo $_ORDER['img']['end_date'];?>
                    </td>
                    <td align=center>
                       <b>Duration (min)</b>
                    </td>
                    <td align=center>
                       <b>Affected Site(s)</b>
                    </td>
                    <td align=center>
   					<?php
   					$code_expand = "";
        			$code_collapse = "";
   					foreach($allEvents as $event)
                	{                 
                		if(strlen($event->description)>50){    
            				$code_expand .= "show('desc_{$event->id}');hide('more_{$event->id}');";
            				$code_collapse .= "hide('desc_{$event->id}');show('more_{$event->id}');";
                		}  
                	}
              		?>
                       <b>Description</b>&nbsp;&nbsp;
                       <?
                       	echo "<a href='#event1' onclick=\"{$code_expand};return true;\"><img src='images/plus.gif' border=0></a>&nbsp;&nbsp;";
        				echo "<a href='#event1' onclick=\"{$code_collapse};return true;\"><img src='images/minus.gif' border=0></a>";
        				?>
        				
                    </td>
                    <td align=center width=12%><b>Action</b></td>
                </tr>
            <?php 
                $i=0;
                foreach($allEvents as $event)
                {
                    $i++;
                    $allSites = array();
                    $allSitesIds = explode(",", $event->sites);
                    foreach($allSitesIds as $siteId){
						$allSites[] = $db->SelectOne("select * from sites where id=%d order by site_order", $siteId);
                    }
            ?>
                <tr bgcolor="#ffffff">
                    <td align=right><?php echo $event->id;?>&nbsp;</td>
                    <td align='center'>&nbsp;<?php et($allEventTypes[$event->type]); ?></td>
                    <td align='center'>&nbsp;<?php et($allEventCategories[$event->category]); ?></td>
                    <td align='center'>&nbsp;<?php echo date("m/d/Y",$event->start_date)."<br>".date("h:i a",$event->start_date);?></td>
                    <td align='center'>&nbsp;<?php echo date("m/d/Y",$event->end_date)."<br>".date("h:i a",$event->end_date);?></td>
					<td align='center'>&nbsp;<?php echo ($event->end_date-$event->start_date)/60;?> min</td>
					<td align='center'>&nbsp;
					<?php	foreach($allSites as $site){
							echo t($site->sitename)."<br>";
                    }
                    ?>
					</td>
                    <td align='left' width="28%">
                    	<?php et(substr($event->description,0,50));
	                    	if(strlen($event->description)>50){ 
	                    	echo "<span id='more_{$event->id}'><a href='#more_{$event->id}' onclick=\"show('desc_{$event->id}');hide('more_{$event->id}');return true;\"> more...</a></span>";
	                    	echo "<span id='desc_{$event->id}' style='display:none'>".t(substr($event->description,50))."</span>";	
	                    }
	                    ?>
                    </td>
                    
                    </td>
                    <td align=center valign="middle">
                       <a href="page.php?where=app&what=event_edit&id=<?php echo $event->id;?>"><img src='images/edit.gif' border=0></a>
                       <a href="page.php?where=app&what=events&action=delete&id=<?php echo $event->id;?>&token=<?php echo $sec->GetCsrfToken(); ?>" onClick="return confirm('Are you sure that you want to delete specified event???')"><img src='images/del.gif' border=0></a>
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
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                    <td><img src='images/spacer.gif' width=40 height=1></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
