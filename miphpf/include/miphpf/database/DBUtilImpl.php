<?php
	/**
	 * Database Util Implementation Class
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 */	
	require_once(MI_DATABASE_ENGINE . '.php');
	

	/**
	 * Implements functions for database
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miDBUtilImpl {
		
		
		/**
		 * @access protected
		 */
		protected $_link = null;
		
		
		/**
		 * @access protected
		 */
		protected $_dbHost;
		
		
		/**
		 * @access protected
		 */ 
		protected $_dbUser;
		
		
		/**
		 * @access protected
		 */
		protected $_dbPass;
		
		
		/**
		 * @access protected
		 */
		protected $_dbName;
		
		/**
		 * miDBUtilImpl constructor.
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $DBUtilImpl = new miDBUtilImpl($dbHost, $dbUser, $dbPass, $dbName);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $dbHost host name
		 * @param string $dbUser username
		 * @param string $dbPass password
		 * @param string $dbName database name
		 */
		public function __construct($dbHost, $dbUser, $dbPass, $dbName)
		{
			$this->_dbHost = $dbHost;
			$this->_dbUser = $dbUser;
			$this->_dbPass = $dbPass;
			$this->_dbName = $dbName;
		}
		
		
		/**
		 * Connects to a SQL database
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $DBUtilImpl = new miDBUtilImpl($dbHost, $dbUser, $dbPass, $dbName);
		 * $DBUtilImpl->pconnect();
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @return resource Returns a SQL link identifier
		 * @throws miDBException
		 */
		public function pconnect()
		{
			if (!is_null($this->_link)) {
				return $this->_link;
			}
			
			$this->_link = sql_connect($this->_dbHost, $this->_dbUser, $this->_dbPass);
			if ($this->_link === false) {
				throw new miDBException(sql_error(null), miDBException::EXCEPTION_CONNECT, sql_errno(null));
			}
			if (sql_select_db($this->_dbName, $this->_link) === false) {
				throw new miDBException(sql_error($this->_link), miDBException::EXCEPTION_SELECTDB, sql_errno($this->_link));
			}
			
			sql_query('SET NAMES utf8', $this->_link);
			
			return $this->_link;
		}
		
		
		/**
		 * Ping the database and attempt to reconnect if necessary
		 * 
		 * @access public
		 * @return false on failure, true on success
		 */
		public function ping()
		{
			if (is_null($this->_link)) {
				$link = sql_connect($this->_dbHost, $this->_dbUser, $this->_dbPass);
			} else {
				$link = $this->_link;
			}
			if ($link === false)
				return false;
			
			return sql_ping($link);
		}
		
		
		/**
		 * Executes a SQL query
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $DBUtilImpl = new miDBUtilImpl($dbHost, $dbUser, $dbPass, $dbName);
		 * $DBUtilImpl->pconnect();
		 * $rezult = $DBUtilImpl->execSQL($query);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $query query string
		 * @param array $params positional params (optional)
		 * @return mixed Resource identifier or true depending on the query
		 * @throws miDBException
		 */
		public function &execSQL($query, $params = array())
		{
			$link = $this->pconnect();
			
			if (count($params) > 0) {
				$queryParts = explode('?', $query);
				if (count($queryParts) != (count($params)+1))
					throw new miDBException('Query contains wrong number of positional params.', miDBException::EXCEPTION_QUERY, 0, $query);
				$query = '';
				foreach ($params as $key => $param)
					$query .= array_shift($queryParts) . '"' . mysql_real_escape_string($param) . '"';
				$query .= array_shift($queryParts);
			}
			
			$result = sql_query($query, $link);
			if ($result === false) {
				throw new miDBException(sql_error($link), miDBException::EXCEPTION_QUERY, sql_errno($link), $query);
			}
			return $result;
		}
		
		
		/**
		 * Executes a SQL insert query
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $DBUtilImpl = new miDBUtilImpl($dbHost, $dbUser, $dbPass, $dbName);
		 * $DBUtilImpl->pconnect();
		 * $result = $DBUtilImpl->execSQLInsert($query);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $query query string
		 * @return int the ID generated for an AUTO_INCREMENT column by the previous INSERT query
		 * or 0 if the previous query does not generate an AUTO_INCREMENT value
		 * @throws miDBException
		 */
		public function execSQLInsert($query)
		{
			$link = $this->pconnect();
			$result = sql_query($query, $link);
			if ($result === false) {
				throw new miDBException(sql_error($link), miDBException::EXCEPTION_QUERY, sql_errno($link), $query);
			}
			
			$result = sql_insert_id($link);
			if ($result === false) {
				throw new miDBException(sql_error($link), miDBException::EXCEPTION_QUERY, sql_errno($link), $query);
			}
			return $result;
		}
		
		
		/**
		 * Returns an array with associative arrays from the result
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $DBUtilImpl = new miDBUtilImpl($dbHost, $dbUser, $dbPass, $dbName);
		 * $DBUtilImpl->pconnect();
		 * $rows = $DBUtilImpl->execSelect($query)
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $query query string
		 * @param array $params positional query params (optional)
		 * @return array array with associative arrays from the result
		 * @throws miDBException
		 */
		public function execSelect($query, $params = array())
		{
			$rows = array();
			$result = $this->execSQL($query, $params);
			
			while ($row = sql_fetch_assoc($result)) {
				$rows[] = $row;
			}
			if (sql_free_result($result) === false) {
				throw new miDBException(sql_error($this->_link), miDBException::EXCEPTION_QUERY, sql_errno($this->_link), $query);
			}
			
			return $rows;
		}
		
		
		/**
		 * Executes the select query and also returns the selected column names in the $fieldsArray
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $DBUtilImpl = new miDBUtilImpl($dbHost, $dbUser, $dbPass, $dbName);
		 * $DBUtilImpl->pconnect();
		 * $rows = $DBUtilImpl->execSelectAndGetFields($query, $fieldsArray);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $query query string
		 * @param array $fieldsArray array for names of the fields
		 * @return array array with associative arrays from the result
		 * @throws miDBException
		 */
		public function execSelectAndGetFields($query, &$fieldsArray)
		{
			$rows = array();
			$result = $this->execSQL($query);

			while ($row = sql_fetch_assoc($result)) {
				$rows[] = $row;
			}
			$num_fields = sql_num_fields($result);
			if ($num_fields === false)
				throw new miDBException(sql_error($this->_link), miDBException::EXCEPTION_QUERY, sql_errno($this->_link), $query);
			
			for ($i = 0; $i < $num_fields; $i++) {
				$fieldsArray[] = sql_field_name($result, $i);
			}
			
			if (sql_free_result($result) === false) {
				throw new miDBException(sql_error($this->_link), miDBException::EXCEPTION_QUERY, sql_errno($this->_link), $query);
			}
			return $rows;
		}
		
		
		/**
		 * Executes a SQL insert query from an associative array.
		 * 
		 * Associative array is representing one record where keys 
		 * are the table fields names.
		 * Example:
		 * <code>
		 * <?php
		 * $DBUtilImpl = new miDBUtilImpl($dbHost, $dbUser, $dbPass, $dbName);
		 * $DBUtilImpl->pconnect();
		 * //$values is an associative array with data for inserting
		 * $DBUtilImpl->execInsert($table, $values);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $table table name
		 * @param array $values associative arrays with data for inserting
		 * @return int the ID generated for an AUTO_INCREMENT column by the previous INSERT query
		 * or 0 if the previous query does not generate an AUTO_INCREMENT value
		 * @throws miDBException
		 */
		public function execInsert($table, $values)
		{
			foreach ($values as $key => $value) {
				if ($value === null)
					$values[$key] = 'NULL';
				else
					$values[$key] = '"' . sql_escape_string($value) . '"';
			}
			
			$query = 'INSERT INTO ' . $table . '(';
			$query .= implode(',', array_keys($values));
			$query .= ') VALUES(';
			$query .= implode(',', array_values($values));
			$query .= ')';
			
			return $this->execSQLInsert($query);
		}
		
		
		/**
		 * Executes a SQL update query from an array for a specify record key
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $DBUtilImpl = new miDBUtilImpl($dbHost, $dbUser, $dbPass, $dbName);
		 * $DBUtilImpl->pconnect();
		 * $DBUtilImpl->execUpdate($table, $values, $key, $keyval);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $table database table name
		 * @param array $values associative arrays with data for updating
		 * @param string $key name of the key field
		 * @param int $keyval value of the key field
		 * @return void
		 * @throws miDBException
		 */
		public function execUpdate($table, $values, $key, $keyval)
		{
			$data = array();
			foreach ($values as $field => $fieldValue) {
				if ($fieldValue === null)
					$data[] = $field . '=NULL';
				else
					$data[] = $field . '="' . sql_escape_string($fieldValue) . '"';
			}

			$query = 'UPDATE ' . $table . ' SET ';
			$query .= implode(',', $data);
			if (is_array($key)) {
				$keysArray = array();
				foreach ($key as $keyId => $keyName)
					$keysArray[] = $keyName . '="' . sql_escape_string($keyval[$keyId]) . '"';
				$query .= ' WHERE ' . implode(' AND ', $keysArray);
			} else
				$query .= ' WHERE ' . $key . '="' . sql_escape_string($keyval) . '"';
			
			$this->execSQL($query);
		}
		
		
		/**
		 * Executes a SQL delete query for a specify record key
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $DBUtilImpl = new miDBUtilImpl($dbHost, $dbUser, $dbPass, $dbName);
		 * $DBUtilImpl->pconnect();
		 * $DBUtilImpl->execDelete($table, $key, $keyval);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $table database table name
		 * @param string $key name of the key field
		 * @param int $keyval value of the key field
		 * @return void
		 * @throws miDBException
		 */
		public function execDelete($table, $key, $keyval)
		{
			$query = 'DELETE FROM ' . $table;
			if (is_array($key)) {
				$keysArray = array();
				foreach ($key as $keyId => $keyName)
					$keysArray[] = $keyName . '="' . sql_escape_string($keyval[$keyId]) . '"';
				$query .= ' WHERE ' . implode(' AND ', $keysArray);
			} else
				$query .= ' WHERE ' . $key . '="' . sql_escape_string($keyval) . '"';
			$this->execSQL($query);
		}
		
		
		/**
		 * Returns an array with names of the fields in the specified table.
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $DBUtilImpl = new miDBUtilImpl($dbHost, $dbUser, $dbPass, $dbName);
		 * $DBUtilImpl->pconnect();
		 * $fieldsArray = $DBUtilImpl->getTableFields($tableName);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $table database table name
		 * @return array array with names of the fields
		 * @throws miDBException
		 */
		public function &getTableFields($table)
		{
			$fieldsArray = array();
			$link = $this->pconnect();

			$fields = sql_list_fields($this->_dbName, $table, $link);
			if ($fields === false)
				throw new miDBException(sql_error($link), miDBException::EXCEPTION_QUERY, sql_errno($link));
			$columns = sql_num_fields($fields);
			if ($columns === false)
				throw new miDBException(sql_error($link), miDBException::EXCEPTION_QUERY, sql_errno($link));
			
			for ($i = 0; $i < $columns; $i++) {
				$fieldsArray[] = sql_field_name($fields, $i);
			}
			return $fieldsArray;
		}
	}
?>