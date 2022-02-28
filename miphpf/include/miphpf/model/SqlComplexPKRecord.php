<?php
	/**
	 * Contains the complex primary key sql record class
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Include the db class
	 */
	if (class_exists('miStaticDBUtil', false) == false) {
		require_once(dirname(__FILE__) . '/../database/StaticDBUtil.php');
	}

	/**
	 * SQL Record managerment class for records with complex primary keys
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlComplexPKRecord extends miSqlRecord {
		
		/**
		 * Creates miSqlComplexPKRecord object
		 * 
		 * @access public
		 * @param string $table the table name
		 * @param array $primaryKey array of the primary key columns
		 */
		public function __construct($table, $primaryKey)
		{
			parent::__construct($table, $primaryKey);
		}
		
		/**
		 * Reads the record from the db by primary key
		 * 
		 * @param array $values an associative array with the values of each of the primary key columns
		 * @throws miDBException
		 */
		public function readPK($values)
		{
			$query = 'SELECT * FROM ' . $this->_table . ' WHERE ' . $this->getPKWhereSql($values);
			$rows = miStaticDBUtil::execSelect($query);
			if (count($rows) < 1) {
				throw new miDBException('Record not found', miDBException::EXCEPTION_RECORD_NOT_FOUND);
			}
			
			$this->_row = $rows[0];
		}
		
		/**
		 * Inserts the record into the db
		 * 
		 * @access public
		 * @return void
		 * @throws miDBException
		 */
		public function insert()
		{
			miStaticDBUtil::execInsert($this->_table, $this->_row);
		}
		
		/**
		 * Updates the record in the db
		 * 
		 * @access public
		 * @return void
		 * @throws miDBException
		 */
		public function update()
		{
			$data = array();
			foreach ($this->_row as $field => $fieldValue) {
				if (in_array($field, $this->_primaryKey))
					continue;
				$data[] = $field . '="' . sql_escape_string($fieldValue) . '"';
			}

			$query = 'UPDATE ' . $this->_table . ' SET ';
			$query .= implode(',', $data);
			$query .= ' WHERE ' . $this->getPKWhereSql($this->_row);
			
			miStaticDBUtil::execSQL($query);
		}
		
		/**
		 * Delete a record in the db table
		 * 
		 * @access public
		 */
		public function delete()
		{
			$query = 'DELETE FROM ' . $this->_table . ' WHERE ' . $this->getPKWhereSql($this->_row);
			miStaticDBUtil::execSQL($query);
		}
		
		/**
		 * Returns the where sql clauses for selecting by primary key
		 * 
		 * @access protected
		 * @return string the sql code
		 */
		protected function getPKWhereSql($values)
		{
			$whereSql = array();
			foreach ($this->_primaryKey as $primaryKey) {
				$whereSql[] = $primaryKey . '="' . sql_escape_string($values[$primaryKey]) . '"';
			}
			return implode(' AND ', $whereSql);
		}
	}
?>