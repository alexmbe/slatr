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

if (!defined('SLA')) {
	// Exit silently
	exit;
}
	
class UserManager 
{
	
  var $db;

	function UserManager($_db)
	{
	  $this->db=$_db;	    
	}
	
    
  function GetUserById(&$userId)
  {
	  $query="select * from users where users.id=%d";	
	  $user = $this->db->SelectOne($query, $userId);
	  return $user;    
  }
  
  function GetAllUsers()
  {
	  $query="select * from users";	  
	  $allUsers = $this->db->SelectAll($query);	  
	  return $allUsers;    
  }
  function GetUsersZone($start, $num, $find, $find_args, $order, $how)
  {
	  $args = array_merge($find_args, array($order, $how, $start, $num));
	  $query="select * from users 
	  			WHERE 1 $find
                ORDER BY %s %s 
                LIMIT %d, %d";  

	  $users = $this->db->SelectAll($query, $args);
	  return $users;    
  }
  
  function GetUserByEmail(&$userEmail)
  {
	  $query="select * from users where users.email='%s'";	  
	  $user = $this->db->SelectOne($query, $userEmail);	  
	  return $user;    
  }
	
  function GetUserByUsername(&$userName)
  {
	  $query="select * from users where users.username='%s'";	  
	  $user = $this->db->SelectOne($query, $userName); 
	  return $user;    
  }
  
  function HashPswd($pswd, $salt)
	{
	  return hash('sha256', $salt . $pswd);
	}
	
	function AddUser(&$user)
	{
	  	global $db;
	  
    	$res=$this->db->InsertObject("users",$user);	  
    	if (!$res)
      		return $res;
    	$user->id=$res;
    
    	return $res;
	} 

	function CheckPswd($user, $password)
	{
	  return $this->HashPswd($password, $user->salt) == $user->password;
	}
	
	function Login($username, $pswd)
	{ 
	  // Check if user exists
	  $user = $this->db->SelectOne("select *
	    from users where users.username='%s'
	    and users.status='1'", $username);

	  // Check if password matches
	  if ($user && $this->CheckPswd($user, $pswd)) {
	    //update login details
		$user->last_login = date("YmdHis");
		$this->UpdateUser($user);
	    return $user;
	  }
	  return FALSE;
	}
	
	
	function UpdateUser(&$user)
	{
	  return $this->db->UpdateObject("users",$user);
	}
	
	function DeleteUser(&$user)
	{	
	  	return $this->db->DeleteObject("users",$user);
	}

	
	
	
	
	
}

?>
