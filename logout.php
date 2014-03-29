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

	define('SLA', true);

	// Delete old session data, cookies and destroy the session
	session_start();
	setcookie(session_name(), '', time() - 3600);
	setcookie('token', '', time() - 3600);
	$_SESSION = array();
	unset($_SESSION);
	session_destroy();

	// Restart session and regenerate session ID
	require_once("./include/class.Security.php");
	$sec = new Security();
	$sec->NewSessionId();

	// Redirect to the login page
	echo "<script> window.location='./'; </script>";
?>
