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

	//Delete loan product
	if($_GET[action]=='delete' && $_GET['id'] && $sec->CheckCsrfToken($_GET['token'])){
			//Delete images!!!
			$sel_user = $userManager->GetUserById($_GET[id]);
			$userManager->DeleteUser($sel_user);
	}
	

    /***setting up the type of the customers that must be shown**/
    if ($_POST['act'] == 'find')
        $_SESSION['sla']['users']['find'] = $_POST['find'];

    /**setting order**/
    if ($_GET['order'])
    {
        ($_GET['order'] == $_SESSION['sla']['users']['order'] && $_SESSION['sla']['users']['how'] == 'DESC')
            ? ($_SESSION['sla']['users']['how'] = 'ASC')
            : ($_SESSION['sla']['users']['how'] = 'DESC');

        $_SESSION['sla']['users']['order'] = $_GET['order'];
    }

    // Make sure this value is one of the two possible
    if ($_SESSION['sla']['users']['how'] != 'ASC') {
        $_SESSION['sla']['users']['how'] = 'DESC';
    }


    /***setting num*/
    if (!$_GET['num'] || ($_GET['num'] < 3))
        $num = 30;
    else
        $num = $_GET['num'];

     if (!$_REQUEST['pg'] || ($_REQUEST['pg'] < 1))
        $pg = 1;
    else
        $pg = $_REQUEST['pg'];

    $start = ($pg-1)*$num;


    /***building the order by array**/
    $_ORDER = array();
    $_ORDER['link'] = array();
    $_ORDER['img'] = array();
            
    $href = 'page.php?where=users&what=users';
    $_ORDER['link']['id'] = $href . "&order=id&num=$num&pg=$pg";
    $_ORDER['link']['firstname'] = $href . "&order=firstname&num=$num&pg=$pg";
    $_ORDER['link']['lastname'] = $href . "&order=lastname&num=$num&pg=$pg";
    $_ORDER['link']['type'] = $href . "&order=type&num=$num&pg=$pg";
	$_ORDER['link']['email'] = $href . "&order=email&num=$num&pg=$pg";
	$_ORDER['link']['username'] = $href . "&order=username&num=$num&pg=$pg";
		
    
    $_ORDER['img']['id'] = "<img src='images/spacer.gif' width=9 height=10>";
    $_ORDER['img']['firstname'] = "<img src='images/spacer.gif' width=9 height=10>";
    $_ORDER['img']['lastname'] = "<img src='images/spacer.gif' width=9 height=10>";
    $_ORDER['img']['type'] = "<img src='images/spacer.gif' width=9 height=10>";
    $_ORDER['img']['email'] = "<img src='images/spacer.gif' width=9 height=10>";
    $_ORDER['img']['username'] = "<img src='images/spacer.gif' width=9 height=10>";
    
    
    
    ($_SESSION['sla']['users']['order'])
        ? (true)
        : ($_SESSION['sla']['users']['order'] = 'id');
        
    if ($_SESSION['sla']['users']['how'] == 'ASC') {
         $img_name = 'order_asc.gif';
    } else {
         $img_name = 'order_desc.gif';
    }
    $_ORDER['img'][$_SESSION['sla']['users']['order']] = "<img src='images/$img_name'>";
    

?>



<b>Users List</b>
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
                Find user: 
                <input type="text" class=inText name=find value="<?php et($_SESSION['sla']['users']['find']); ?>">
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
		
	if ($_POST[submit]=='view all') unset($_SESSION['sla']['users']['find']);
	
    /***setting up the type for query**/
    if ($_SESSION['sla']['users']['find']) {
            $_FIND = " AND (users.firstname LIKE '%s'
            			  OR users.lastname LIKE '%s'	
            			  OR users.email LIKE '%s')";
			$_FIND_args = array(
                "%" . $_SESSION['sla']['users']['find'] . "%",
                "%" . $_SESSION['sla']['users']['find'] . "%",
                "%" . $_SESSION['sla']['users']['find'] . "%"
            );
    } else {
            $_FIND = '%s';
			$_FIND_args = array('');
    }

    $_how = $_SESSION['sla']['users']['how'];
    switch ($_SESSION['sla']['users']['order'])
    {
        case 'id':
            $_order = 'users.id';
            break;
        case 'firstname':
            $_order = 'users.firstname';
            break;
       case 'lastname':
            $_order = 'users.firstname';
            break;
       case 'type':
            $_order = 'users.user_type';
            break;        
        case 'email':
            $_order = 'users.email';
            break;  
        case 'username':
            $_order = 'users.username';
            break;
                  
        default:
            $_order = 'users.id';
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
                    if ($_SESSION['sla']['users']['find'])
                        et("where Fullname or Email contains '{$_SESSION['sla']['users']['find']}'");
                ?>
             </b>
             
        </td>
    </tr>
    <tr>
        <td>
            <?php 
                $allUsers = $userManager->GetUsersZone($start, $num, $_FIND, $_FIND_args, $_order, $_how);
                $total = count($allUsers);
            ?>
            <a href="page.php?where=users&what=user_edit"><img src="images/add.gif" border=0 title="Add new user" vspace="10"></a>
            <table width=100% cellpadding=3 cellspacing=1 bgcolor="#e4e4e4">
                <tr bgcolor="#ffffff">
                    <td colspan=5>
                        Results: <?php et(($start+1) . ' - ' . (($start+$num < $total)?($start+$num):($total) ));?> from <?php et($total); ?>
                    </td>
                    <td colspan=3 align=right>
                        <a href="<?php echo $href . "&num=$num&pg=1";?>"><img src="images/first.gif" alt="first page" border=0></a>
                        <a href="<?php echo $href . "&num=$num&pg=".($pg-1);?>"><img src="images/prior.gif" alt="prior page" border=0></a>

                        &nbsp;&nbsp;
                        <a href="<?php echo $href . "&num=$num&pg=".($pg+1);?>"><img src="images/next.gif" alt="next page" border=0></a>
                        <a href="<?php echo $href . "&num=$num&pg=".ceil($total/$num);?>"><img src="images/last.gif" alt="last page" border=0></a>

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
                       <a href="<?php echo $_ORDER['link']['username'];?>"><b>Username</b></a>
                        <?php echo $_ORDER['img']['username'];?>
                    </td>
                    <td align=center>
                        <a href="<?php echo $_ORDER['link']['firstname'];?>"><b>First Name</b></a>
                        <?php echo $_ORDER['img']['firstname'];?>
                    </td>
                    <td align=center>
                        <a href="<?php echo $_ORDER['link']['lastname'];?>"><b>Last Name</b></a>
                        <?php echo $_ORDER['img']['lastname'];?>
                    </td>
                    <td align=center>
                        <a href="<?php echo $_ORDER['link']['type'];?>"><b>Type</b></a>
                        <?php echo $_ORDER['img']['type'];?>
                    </td>
                    <td align=center>
                       <a href="<?php echo $_ORDER['link']['email'];?>"><b>Email</b></a>
                        <?php echo $_ORDER['img']['email'];?>
                    </td>
                    <td align=center width=12%><b>Action</b></td>
                </tr>
            <?php 
                $i=0;
                foreach($allUsers as $sel_user)
                {
                    $i++;
                    $id = $sel_user->id;
                    $user_type = $sel_user->user_type;
                    $firstname = $sel_user->firstname;
                    $lastname = $sel_user->lastname;
                    $username = $sel_user->username;
                    $email = $sel_user->email;
                                            
                    ($status) 
                        ? ($status = "<img src='images/on.gif' border='0'>") 
                        : ($status = "<img src='images/off.gif' border='0'>");

      				

            ?>
                <tr bgcolor="#ffffff">
                    <td align=right><?php echo $id;?>&nbsp;</td>
                    <td align='center'>&nbsp;<?php et($username);?></td>
                    <td align='center'>&nbsp;<?php et($firstname);?></td>
                    <td align='center'>&nbsp;<?php et($lastname);?></td>
                    <td align='center'>&nbsp;<?php et($user_type);?></td>

                    <td align='center'>&nbsp;<a HREF="mailto:<?php et($email);?>"><?php et($email);?></a>
                    </td>
                    <td align=center valign="middle">
                       <a href="page.php?where=users&what=user_edit&id=<?php echo $id;?>"><img src='images/edit.gif' border=0></a>
                       <a href="page.php?where=users&what=users&action=delete&id=<?php echo $id;?>&token=<?php echo $sec->GetCsrfToken(); ?>" onClick="return confirm('Are you sure that you want to delete selected user???')"><img src='images/del.gif' border=0></a>
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
