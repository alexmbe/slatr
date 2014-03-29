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

/**
	* class Database
	*
	* Database-related functions
	*
	*/
class Database  
{  
  var $hDb;
  var $dbName;

  // Regex containing valid format specifiers (strings that are to be replaced in SQL queries)
  var $fs_regex = '/(%d|%s|%f|%%)/';

  
	function Database($dbServer,$dbPort,$dbUser,$dbPassword,$dbName) 
	{
	$this->dbName=$dbName;
	$this->hDb = mysql_connect($dbServer . ":" . $dbPort, $dbUser, $dbPassword)
	  or die("Failed to connect MySQL server $dbServer:dbPort.");
	mysql_select_db($dbName, $this->hDb) or die("Failed to select $dbName database");
	}
	
	function _SqlQueryEscape(&$args, $type)
	{
	  if (empty($args)) {
		return FALSE;
	  }

	  switch ($type) {
		case '%d':  // int
		  return (int) array_shift($args);
		case '%s':  // string
		  $str = array_shift($args);
		  $str = strip_tags(utf8_decode($str));
		  return mysql_real_escape_string($str);
		case '%f':  // float
		  return (float) array_shift($args);
		case '%%':  // literal '%'
		  return '%';
		default:    // invalid type; do nothing
		  return '';
	  }
	}

	function SqlQuery($sql)
	{
	  $orig_sql = $sql;

	  $args = func_get_args();
	  // Skip the first argument
	  array_shift($args);
	  if (isset($args[0]) and is_array($args[0])) {
	    // Arguments passed as an array
	    $args = $args[0];
	  }

	  $matches = array();
	  $offset = 0;
	  // Match one format specifier at a time
	  while (preg_match($this->fs_regex, $sql, $matches, PREG_OFFSET_CAPTURE, $offset)) {
		$fspec = $matches[1][0];
		$offset = $matches[1][1];

	    // Escape the value according to the type the format specifier corresponds to
	    $escaped_value = $this->_SqlQueryEscape($args, $fspec);

	    // Replace the format specifier with the actual escaped value
	    $sql = substr_replace($sql, $escaped_value, $offset, strlen($fspec));

		// Move the pointer past the substituted string
		$offset += strlen($escaped_value);
	  }

	  $res = mysql_query($sql, $this->hDb);

	  if (!$res) {
		// This indicated an error with a SQL query and should be useful for debugging; codenamed to avoid information leakage
		echo "Error 3045 occured.";
		exit;
	  }

	  return $res;
	}
	
	function InsertQuery($sql)
	{
	  // Pass the arguments further
	  $args = func_get_args();
	  array_shift($args);
	  if (isset($args[0]) and is_array($args[0])) {
	    $args = $args[0];
	  }

	  $res=$this->SqlQuery($sql, $args);
	  if (!$res)
	    return $res;
	  return mysql_insert_id($this->hDb);		  
	}	
	
	function LastError()
	{
	  return mysql_error($this->hDb);
	}
	
	function AffectedRows()
	{
	  return mysql_affected_rows($this->hDb);
	}
	
	
	//return number of rows in result
	//Ivica
	function NumSelRows($sql)
	{
	  // Pass the arguments further
	  $args = func_get_args();
	  array_shift($args);
	  if (isset($args[0]) and is_array($args[0])) {
	    $args = $args[0];
	  }

	  $res=$this->SqlQuery($sql, $args);	
	  return mysql_num_rows($res);
	}

	function SelectOne($sql)
	{
	  // Pass the arguments further
	  $args = func_get_args();
	  array_shift($args);
	  if (isset($args[0]) and is_array($args[0])) {
	    $args = $args[0];
	  }

	  $res=$this->SqlQuery($sql, $args);
	  if (!$res)
	    return false;
	  $obj = mysql_fetch_object($res);
	  return $obj;	  
	}
	
	function SelectAll($sql)
	{
	  // Pass the arguments further
	  $args = func_get_args();
	  array_shift($args);
	  if (isset($args[0]) and is_array($args[0])) {
	    $args = $args[0];
	  }

	  $res=$this->SqlQuery($sql, $args);
	  if (!$res)
	    return false;
	  $arr=array();
	  while($obj = mysql_fetch_object($res))
	  {
      $arr[$obj->id]=$obj;
	  }
	  return $arr;	  	  
	}
	
	function SelectRaw($sql)
	{
	  // Pass the arguments further
	  $args = func_get_args();
	  array_shift($args);
	  if (isset($args[0]) and is_array($args[0])) {
	    $args = $args[0];
	  }

	  $res=$this->SqlQuery($sql, $args);
	  if (!$res)
	    return false;
	  return $res;
	}	
	
	function GetFields($table)
	{
    $hFields = mysql_list_fields($this->dbName, $table, $this->hDb);
    $nFields = mysql_num_fields($hFields);
    $fields  = array();
    for ($i = 0; $i < $nFields; $i++)
    {
      $fields[mysql_field_name($hFields, $i)]=mysql_field_type($hFields,$i);	  
	  }
	  return $fields;
	}
	
	function UpdateObject($table, &$obj)
	{
	  $fields=$this->GetFields($table);

	  $args = array($table);
	  
	  $query="update `%s` set";
	  foreach ($obj as $field => $val) if (($field!="id")&&(isset($fields[$field])))
	  {
    	$query.=" `%s`='%s',";
		array_push($args, $field, $val);
	  }
	  $query=rtrim($query,",");
	  $query.=" where id=%d";
	  array_push($args, $obj->id);
	  
	  $res=$this->SqlQuery($query, $args); //true or false
	  return $res;	  
	}
	
	function InsertObject($table, &$obj)
	{
	  $fields=$this->GetFields($table);

	  $args = array();
	  $args2 = array();
	  
	  foreach ($obj as $field => $val) if (($field!="id")&&(isset($fields[$field]))&&(isset($val)))
	  {
	    $cols.="`%s`, ";
		array_push($args, $field);

	    $vals.="'%s', ";
		array_push($args2, $val);
	  }

	  $cols=rtrim($cols,", ");
	  $vals=rtrim($vals,", ");

	  $query.="insert into `%s` ($cols) values ($vals)";
	  $args = array_merge(array($table), $args, $args2);
	  
	  $res=$this->SqlQuery($query, $args); //true or false 	 
	  if (!$res)
	    return $res;
	  return mysql_insert_id($this->hDb);	  
	}
	
	function DeleteObject($table, &$obj)
	{
	  $query="delete from `%s` where id=%d";
	  $res=$this->SqlQuery($query, $table, $obj->id); //true or false
	  return $res;	  
	}
	
	function OptimizeTable($table)
	{
		$sql = "repair table `%s`";
		$res=$this->SqlQuery($sql, $table);
		
		$sql = "optimize table `%s`";
		$res=$this->SqlQuery($sql, $table);
    
	  	return $res;
	}
}

?>
