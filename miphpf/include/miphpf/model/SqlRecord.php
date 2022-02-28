<?php
	/**
	 * SQL Record Class
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 */
	if (class_exists('miStaticDBUtil', false) == false) {
		require_once(dirname(__FILE__) . '/../database/StaticDBUtil.php');
	}
	
	/**
	 * Handle and manage the inserting, updating, deleting and reading of data from the
	 * database. This class works only with one record row specified by a table primary key or
	 * unique field.
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlRecord {
		
		
		/**
		 * @access protected
		 */
		protected $_row = array();
		
		
		/**
		 * @access protected
		 */
		protected $_table = '';
		
		
		/**
		 * @access protected
		 */
		protected $_primaryKey = '';
		
		
		/**
		 * miSqlRecord constructor. It takes two parameters table name and 
		 * table primary key
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $record = new miSqlRecord('tableName', 'tablePrimaryKey');
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $table database table name
		 * @param string $primaryKey database table primary key
		 */
		public function __construct($table, $primaryKey)
		{
			$this->_table = $table;
			$this->_primaryKey = $primaryKey;
		}
		
		
		/**
		 * Gets the primary key column of the table that this SQLRecord uses
		 * 
		 * @access public
		 * @return string the primary key column name
		 */
		public function getPrimaryKeyColumn()
		{
			return $this->_primaryKey;
		}
		
		
		/**
		 * Gets the table name of this SQLRecord
		 * 
		 * @access public
		 * @return string the table name
		 */
		public function getTableName()
		{
			return $this->_table;
		}
		
		
		/**
		 * Gets the value of a field
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $record = new miSqlRecord('tableName', 'tablePrimaryKey');
		 * $record->readPK($value);
		 * $value = $record->get($someField);
		 * ?>
		 * </code>
		 * @access public
		 * @param string $field field name
		 * @return mixed the field value
		 */
		public function get($field) {
			return $this->_row[$field];
		}
		
		/**
		 * Gets the value of the primary key
		 * 
		 * @access public
		 * @return mixed the primary key value
		 */
		public function getPK()
		{
			return $this->_row[$this->_primaryKey];
		}
		
		/**
		 * Sets the value of a field
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $record = new miSqlRecord('tableName', 'tablePrimaryKey');
		 * $record->set($field, $value);
		 * ?>
		 * </code>
		 * @access public
		 * @param string $field the name of the field
		 * @param mixed $value value of the field
		 */
		public function set($field, $value)
		{
			$this->_row[$field] = $value;
		}
		
		
		/**
		 * Return the data row as array
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $record = new miSqlRecord('tableName', 'tablePrimaryKey');
		 * $record->readPK($value);
		 * $row = $record->getRow();
		 * ?>
		 * </code>
		 * @access public
		 * @return array the data row
		 */
		public function getRow()
		{
			return $this->_row;
		}
		
		
		/**
		 * Directly set the data row
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $record = new miSqlRecord('tableName', 'tablePrimaryKey');
		 * $record->setRow($row);
		 * ?>
		 * </code>
		 * @access public
		 * @param array $dataRow the data row
		 */
		public function setRow($dataRow)
		{
			$this->_row = $dataRow;
		}
		
		
		/**
		 * Read a record where the specified field represented by $key
		 * is equal to the value represented by $value
		 * If there are is than one record matching the first one will be read in
		 *
		 * Example:
		 * <code>
		 * <?php
		 * $record = new miSqlRecord('tableName', 'tablePrimaryKey');
		 * $record->read('uniqueField', $uniqueFieldValue);
		 * ?>
		 * </code>
		 * @param string $key the name of the field
		 * @param string $value the value 
		 * @return void
		 * @throws miDBException
		 */
		public function read($key, $value)
		{
			$query = 'SELECT * FROM ' . $this->_table . ' WHERE ' . $key . '="' . sql_escape_string($value) . '"';
			$rows = miStaticDBUtil::execSelect($query);
			if (count($rows)<1) {
				throw new miDBException('Record not found', miDBException::EXCEPTION_RECORD_NOT_FOUND);
			}
			
			$this->_row = $rows[0];
		}
		
		
		/**
		 * Read a record using the specified primary key value
		 *
		 * Example:
		 * <code>
		 * <?php
		 * $record = new miSqlRecord('tableName', 'tablePrimaryKey');
		 * $record->readPK($value);
		 * ?>
		 * </code>
		 * @param mixed $value primary key value
		 * @return void
		 * @throws miDBException
		 */
		public function readPK($value)
		{
			$this->read($this->_primaryKey, $value);
		}
		
		
		/**
		 * Insert a record in the db table
		 * It also updates the the PK value of the inserted row
		 *
		 * @access public
		 * @return PK the primary key of the newly created record
		 * @throws miDBException
		 */
		public function insert()
		{
			return $this->_row[$this->_primaryKey] = miStaticDBUtil::execInsert($this->_table, $this->_row);
		}
		
		
		/**
		 * Update a record in the db table
		 *
		 * @access public
		 * @return PK the primary key of the updated record
		 * @throws miDBException
		 */
		public function update()
		{
			miStaticDBUtil::execUpdate($this->_table, $this->_row, $this->_primaryKey, $this->_row[$this->_primaryKey]);
			return $this->_row[$this->_primaryKey];
		}
		
		
		/**
		 * Delete a record in the db table
		 *
		 * @access public
		 * @return PK the primary key of the deleted record
		 * @throws miDBException
		 */
		public function delete()
		{
			if ($this->_row[$this->_primaryKey] === null)
				throw new miDBException('Cannot delete: primary key not set');
			miStaticDBUtil::execDelete($this->_table, $this->_primaryKey, $this->_row[$this->_primaryKey]);
			return $this->_row[$this->_primaryKey];
		}
	}
?>