<?

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

if (!defined('SLA')) {
	// Exit silently
	exit;
}
	
class DbConfig
{
    var $server   = "localhost";
    var $port     = "3306";
    var $user     = "root";
    var $password = "";
    var $name     = "slatr";
}

text: ghu_uTzsHn7ntsbrT3RUE7dsGx3Qq4689V2Jzoq0
apikey: ghu_uTzsHn7ntsbrT3RUE7dsGx3Qq4689V2Jzoq0


// Page tree: the first level is $_GET["where"], the second level is $_GET["what"]
$pages = array(
	'admin'	=> array(
			'settings'	=> 'admin/settings.php',
			'password'	=> 'admin/password.php'
		),
	'users'	=> array(
			'user_edit'	=> 'users/user_edit.php',
			'users'		=> 'users/users.php'
		),
	'app'	=> array(
			'events'	=> 'app/events.php',
			'event_edit'=> 'app/event_edit.php',
			'reports'	=> 'app/reports.php',
			'sites'		=> 'app/sites.php',
			'sites_edit'=> 'app/sites_edit.php'
		)
	);


$allEventTypes = array(
	1 => "Full outage", 
	2 => "Partial outage",
	3 => "Maintenance"
	);

$allEventCategories = array(
	1 => "Application", 
	2 => "Database",
	3 => "Network",
	4 => "Security", 
	5 => "System",
	6 => "Hosting  provider"
	);

$defYears = array(2007, 2008, 2009, 2010, 2011, 2012);

$defMonths = array(1, 2, 3, 4, 5, 6, 7, 8,9 ,10, 11, 12);

$defDays = array(1=>1, 2, 3, 4, 5, 6, 7, 8,9 ,10, 11, 12, 13, 14, 15, 16, 17, 18, 19,20, 21, 22, 23,24,25,26,27,28,29,30,31);	

$defHours = array(1=>'01', '02', '03', '04', '05', '06', '07', '08','09' ,'10', '11', '12');
$defMinutes = array(0=>'00','01',' 02',' 03',' 04',' 05',' 06',' 07',' 08','09 ','10',' 11',' 12',' 13',' 14',' 15',' 16',' 17',' 18',' 19','20',' 21',' 22',' 23','24','25','26','27','28','29','30','
				31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50','51','52','53','54','55','56','57','58','59');

?>
