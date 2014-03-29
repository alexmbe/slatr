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

// Should be near the top; initializes session among other things
require_once("./include/class.Security.php");
$sec = new Security();

//Include common files, create main classes, make db connections etc
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

//includes
require_once("./include/config.php");
require_once("./include/class.Database.php");
require_once("./include/class.UserManager.php");
require_once("./include/class.ConfigManager.php");


//useful variables
$urlSelf = $_SERVER['PHP_SELF'];

$message = "";

//global objects
$dbConfig      = new DbConfig();


//database connection
$db            = new Database($dbConfig->server,$dbConfig->port,$dbConfig->user,
                               $dbConfig->password,$dbConfig->name);


//create config manager
$configManager = new ConfigManager($db);
$allConfigs = $configManager->GetAllConfigs();
foreach($allConfigs as $temp_config)
	$_CONFIG[$temp_config->name] = $temp_config->value; 	

//login
$user=null;
$userManager   = new UserManager($db);


if (isset($_SESSION["userLoginId"]))
{
    $user=$userManager->GetUserById($_SESSION["userLoginId"]);
}



//propagate message through redirects
if (isset($_SESSION["message"]))
{
    $message=$_SESSION["message"];
    unset($_SESSION["message"]);
}

?>
