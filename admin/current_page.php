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
	
    $_location = array();

    $_location[] = '<a href="page.php?where=app&what=reports">SLA Management</a>';

    switch ($_GET['where'])
    {
        
        /***general layout of page**/
        case 'index':
            $_location[] = '<a href="page.php?where=index&what=list">General Layout</a>';
            break;

 		case 'admin':
             $_location[] = '<a href="page.php?where=admin">Admin Settings</a>';
            
             if ($_GET['what'] == 'admin')
                $_location[] = '<a href="page.php?where=admin&what=admin">Change admin email</a>';
			 			 if ($_GET['what'] == 'settings')
                $_location[] = '<a href="page.php?where=admin&what=settings">System settings</a>';
             if ($_GET['what'] == 'password')
                $_location[] = '<a href="page.php?where=admin&what=password">Change admin password</a>';
            break;
        
        case 'users':
             $_location[] = '<a href="page.php?where=users&what=users">Users</a>';
                        
             if ($_GET['what'] == 'users')
                $_location[] = '<a href="page.php?where=users&what=users">Users List</a>';
             if ($_GET['what'] == 'user_edit')
                $_location[] = '<a href="page.php?where=users&what=user_edit">Edit User</a>';
            break;   
            
        case 'app':
             $_location[] = '<a href="page.php?where=app&what=reports">SLA Tools</a>';    
              
             if ($_GET['what'] == 'sites')
                $_location[] = '<a href="page.php?where=app&what=sites">Sites</a>';
             if ($_GET['what'] == 'sites_edit')
                $_location[] = '<a href="page.php?where=app&what=sites_edit">Edit Site</a>';
             if ($_GET['what'] == 'events')
                $_location[] = '<a href="page.php?where=app&what=events">Events</a>';
             if ($_GET['what'] == 'event_edit')
                $_location[] = '<a href="page.php?where=app&what=event_edit">Edit Event</a>';
             if ($_GET['what'] == 'reports')
                $_location[] = '<a href="page.php?where=app&what=reports">Reports</a>';        
            break;                 
    }


    echo implode(' > ', $_location);
?>
