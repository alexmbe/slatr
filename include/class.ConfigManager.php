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
	
class ConfigManager 
{
  var $db; 

	function ConfigManager($_db)
	{
	  $this->db=$_db;	  
	}
	  
  function GetAllConfigs()
  {
	  $query="select * from config";	  
	  $allConfigs = $this->db->SelectAll($query);	  
	  return $allConfigs;    
  }
  
  function GetConfigByName($name)
  {
	  $query="select * from config where name='%s'";	
	  $config = $this->db->SelectOne($query, $name);
	  return $config;    
  }
  
  function GetConfigById($id)
  {
	  $query="select * from config where id=%d";	  
	  $config = $this->db->SelectOne($query, $id);	  
	  return $config; 
  }
  
  function UpdateConfig(&$config)
	{
		return $this->db->UpdateObject("config",$config);
	}
	
}

?>
