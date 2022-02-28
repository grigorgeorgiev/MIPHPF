<?php
	/**
	 * General Options Record Class
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Handle and manage deleting and reading of data from the
	 * database.
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miPropertiesRecord extends miSqlRecord {

		/**
		 * @access protected
		 */
		protected $_propertyField;
		
		
		/**
		 * @access protected
		 */
		protected $_valueField;

		
		/**
		 * miPropertiesRecord constructor. It takes three parameters -	
		 * table name, property field name and value field name
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $propertiesRecord = new miPropertiesRecord('tableName', 'propertyField', 'valueField');
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $table database table name
		 * @param string $propertyField the name of the property field
		 * @param string $valueField the name of the value field
		 */
		public function __construct($table, $propertyField, $valueField)
		{
			$this->_table = $table;
			$this->_propertyField = $propertyField;
			$this->_valueField = $valueField;
		}
		
		
		/**
		 * Reads all properties from the db table
		 *
		 * Example:
		 * <code>
		 * <?php
		 * $propertiesRecord = new miPropertiesRecord('tableName', 'propertyField', 'valueField');
		 * $propertiesRecord->readPK($value);
		 * $row = $propertiesRecord->getRow();
		 * ?>
		 * </code>
		 * 
		 * @return void
		 * @throws miDBException
		 */
		public function readPK($value)
		{
			$query = 'SELECT * FROM ' . $this->_table;
			$rows = miStaticDBUtil::execSelect($query);
	
			foreach ($rows as $key => $row) {
				$this->_row[$row[$this->_propertyField]] = $row[$this->_valueField];
			}
		}
		
		
		/**
		 * Updates the properties table
		 *
		 * @access public
		 * @return void
		 * @throws miDBException
		 */
		public function update()
		{
			foreach ($this->_row as $property => $value) {
				$row = array($this->_valueField => $value);
				miStaticDBUtil::execUpdate($this->_table, $row, $this->_propertyField, $property);
			}
		}
	}
?>