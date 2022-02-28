<?php
	/**
	 * Proxy Database Util Class
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 */
	require_once('DBUtilImpl.php');
	
	
	/**
	 * A proxy class that has static methods to access the preconfigured database connection
	 * The database connection is configured using the following settings:
	 * MI_DEFAULT_DB_HOST, MI_DEFAULT_DB_USER, MI_DEFAULT_DB_PASS and MI_DEFAULT_DB_NAME
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miStaticDBUtil {
		
		
		/**
		 * Returns an object of class miDBUtilImpl
		 *
		 * Object of class miDBUtilImpl or its subclasses is needed to handle
		 * the connection between database and miStaticDBUtil class
		 * 
		 * @access public
		 * @return object object of class miDBUtilImpl or its subclasses
		 */
		public static function getDBUtil()
		{
			static $dBUtil = null;
			
			if ($dBUtil != null)
				return $dBUtil;
			
			$settings = miSettings::singleton();
			$dBUtil = new miDBUtilImpl($settings->get('MI_DEFAULT_DB_HOST'), $settings->get('MI_DEFAULT_DB_USER'),
				$settings->get('MI_DEFAULT_DB_PASS'), $settings->get('MI_DEFAULT_DB_NAME'));
			return $dBUtil;
		}
		
		/**
		 * Ping the database and attempt to reconnect if necessary
		 * 
		 * @access public
		 * @return false on failure, true on success
		 * @throws miDBException
		 */
		public static function ping()
		{
			return miStaticDBUtil::getDBUtil()->ping();
		}
		
		/**
		 * Executes a SQL query
		 * 
		 * @access public
		 * @param string $query query string
		 * @return resource Resource identifier or true depending on the query
		 * @throws miDBException
		 */
		public static function &execSQL($query)
		{
			return miStaticDBUtil::getDBUtil()->execSQL($query);
		}
		
		
		/**
		 * Executes a SQL insert query
		 * 
		 * @access public
		 * @param string $query query string
		 * @return int the ID generated for an AUTO_INCREMENT column by the previous INSERT query
		 * or 0 if the previous query does not generate an AUTO_INCREMENT value
		 * @throws miDBException
		 */
		public static function execSQLInsert($query)
		{
			return miStaticDBUtil::getDBUtil()->execSQLInsert($query);
		}
		
		
		/**
		 * Returns an array with associative arrays from the result
		 * 
		 * @access public
		 * @param string $query query string
		 * @param array $params positional query params (optional)
		 * @return array array with associative arrays from the result
		 * @throws miDBException
		 */
		public static function execSelect($query, $params = array())
		{
			return miStaticDBUtil::getDBUtil()->execSelect($query, $params);
		}
		
		
		/**
		 * Executes the select query and also returns the selected column names in the $fieldsArray
		 * 
		 * @access public
		 * @param string $query query string
		 * @param array $fieldsArray array for names of the fields
		 * @return array array with associative arrays from the result
		 * @throws miDBException
		 */
		public static function execSelectAndGetFields($query, &$fieldsArray)
		{
			return miStaticDBUtil::getDBUtil()->execSelectAndGetFields($query, $fieldsArray);
		}
		
		
		/**
		 * Executes a SQL insert query from an associative array.
		 * 
		 * Associative array is representing one record where keys 
		 * are the table fields names.
		 * 
		 * @access public
		 * @param string $table table name
		 * @param array $values associative arrays with data for inserting
		 * @return int the ID generated for an AUTO_INCREMENT column by the previous INSERT query
		 * or 0 if the previous query does not generate an AUTO_INCREMENT value
		 * @throws miDBException
		 */
		public static function execInsert($table, $values)
		{
			return miStaticDBUtil::getDBUtil()->execInsert($table, $values);
		}
		
		
		/**
		 * Executes a SQL update query from an array for a specify record key
		 * 
		 * @access public
		 * @param string $table database table name
		 * @param array $values associative arrays with data for updating
		 * @param string $key name of the key field
		 * @param int $keyval value of the key field
		 * @return void
		 * @throws miDBException
		 */
		public static function execUpdate($table, $values, $key, $keyval)
		{
			miStaticDBUtil::getDBUtil()->execUpdate($table, $values, $key, $keyval);
		}
		
		
		/**
		 * Executes a SQL delete query for a specify record key
		 * 
		 * @access public
		 * @param string $table database table name
		 * @param string $key name of the key field
		 * @param int $keyval value of the key field
		 * @return void
		 * @throws miDBException
		 */
		public static function execDelete($table, $key, $keyval)
		{
			miStaticDBUtil::getDBUtil()->execDelete($table, $key, $keyval);
		}
		
		
		/**
		 * Returns an array with names of the fields in the specified table.
		 * 
		 * @access public
		 * @param string $table database table name
		 * @return array array with names of the fields
		 * @throws miDBException
		 */
		public static function &getTableFields($table)
		{
			return miStaticDBUtil::getDBUtil()->getTableFields($table);
		}
	}
?>