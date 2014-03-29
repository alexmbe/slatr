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
//includes
require_once("code.init.php");
	/**
		user login
	**/
	if ($_POST['Submit'] == 'Log-In' && $sec->CheckCsrfToken($_POST['token']))
	{
		//taking data from the form
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		if (!$password) $error = "Wrong Password!";
		if (!$username) $error = "Wrong Username!";
		
		if (!$error)
{
                    $user = $userManager->Login($username, $password);

                        if ($user->id !='' && $user->id > 0){
								// Regenerate session ID and anti-CSRF token upon login
								$sec->NewSessionId();
								$sec->NewCsrfToken();

                                $_SESSION["userLoginId"] = $user->id;
                                $_SESSION['user'] = $user->username;
                                header("Location: page.php?where=app&what=reports");
                                exit();
                     }
                        else
                                $error = 'Access Denied!';
                } 
        }

	// Regenerate session ID before login for security
	$sec->NewSessionId();
?>


<html>

  <head>
    <title>slatr - SLA Tracking and Reporting System</title>
    <link rel="stylesheet" type="text/css" href="index.css">
  </head>
  
  <body>
    <table width='100%' height='100%'>
      <tr>
        <td align="center" valign="middle">
          <table class='border' width="250">
            <tr>
              <td>
                <form method="POST" action="index.php">
				<?php echo $sec->CsrfFormHtml(); ?>
                <table width="100%" align="center" class="txtGrey">
                  <tr>
                    <td colspan=2 align="center"><b>SLA Management Login</b>:<br><br></td>
                  </tr>
				<?php if ($error) { ?>
                  <tr>
                    <td colspan=2 class=error align="center"><?php et($error);?><br><br></td>
                  </tr>
				<?php  } ?>
                  <tr>
                    <td width="40%" align="center">Username:</td>
                    <td>
                      <input type="text" class="inText" name="username" value='<?php et($_POST['username'])?>'>
                    </td>
                  </tr>
                  <tr>
                    <td width="40%" align="center">Password:</td>
                    <td>
                      <input type="password" class="inText" name="password" value=''>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center">
                      <input type="submit" class="submit" name="Submit" value="Log-In">
                    </td>
                  </tr>
                </table>
                </form>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
