<?php
	/**
	 * MySQL functions
	 *
	 * @copyright Copyright (c) 2004 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	# MySQL consts
	define('MI_MYSQL_DATE_FORMAT', 'Y-m-d');
	define('MI_MYSQL_DATETIME_FORMAT', 'Y-m-d H:i:s');
	
		function sql_list_fields($dbName, $table, $link)
		{
			return @mysql_list_fields($dbName, $table, $link);
		}
		
		function sql_escape_string($u)
		{
			return mysql_escape_string($u);
		}
		
		function sql_real_escape_string($u, $link)
		{
			return mysql_real_escape_string($u, $link);
		}
		
		function sql_query($query, $link)
		{
			return @mysql_query($query, $link);
		}
		
		//[string server [, string username [, string password [, bool new_link [, int client_flags]]]]])
		function sql_connect($dbHost, $dbUser, $dbPass, $new_link = false)
		{
			return @mysql_connect($dbHost, $dbUser, $dbPass, $new_link);
		}
		
		function sql_error($link)
		{
			if ($link === null)
				return @mysql_error();
			else
				return @mysql_error($link);
		}
		
		function sql_errno($link)
		{
			if ($link === null)
				return @mysql_errno();
			else
				return @mysql_errno($link);
		}
		
		function sql_select_db($dbName, $link)
		{
			return @mysql_select_db($dbName, $link);
		}
		
		function sql_ping($link)
		{
			return @mysql_ping($link);
		}
		
		function sql_insert_id($link)
		{
			return @mysql_insert_id($link);
		}
		
		function sql_fetch_assoc($result)
		{
			return mysql_fetch_assoc($result);
		}
		
		function sql_free_result($result)
		{
			return @mysql_free_result($result);
		}
		
		function sql_num_fields($fields)
		{
			return mysql_num_fields($fields);
		}
		
		function sql_field_name($result, $fieldIndex)
		{
			return @mysql_field_name($result, $fieldIndex);
		}
?>