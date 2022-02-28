<?php
	/**
	 * Domain object interface and classes
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Domain object interface
	 * 
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	interface miDomainObject {
		public function read($pkValue);
		public function insert();
		public function update();
		public function delete($pkValue = null);
	}
	
	/**
	 * Default domain object class
	 *
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miDefaultDomainObject implements miDomainObject {
		protected $_record;
		
		public function __construct($record)
		{
			$this->_record = $record;
		}
		
		/**
		 * Return the name of the primary key of the underlying SQLRecord
		 * Used by miDefaultView
		 * 
		 * @return string the name of the primary key
		 */
		public function getPKName()
		{
			return $this->_record->getPrimaryKeyColumn();
		}
		
		/**
		 * Get domain object value
		 *
		 * @param string $name
		 * @return mixed
		 */
		public function get($name)
		{
			return $this->_record->get($name);
		}

		/**
		 * Get all domain object values
		 *
		 * @return array
		 */
		public function getRow()
		{
			return $this->_record->getRow();
		}
		
		/**
		 * Set all domain object values
		 * 
		 * @param array $row
		 * @return void
		 */
		public function setRow(array $row)
		{
			$this->_record->setRow($row);
		}
		
		/**
		 * Set domain object value
		 *
		 * @param string $name
		 * @param mixed $value
		 */
		public function set($name, $value)
		{
			// Do not update the primary key
			if ($this->_record->getPrimaryKeyColumn() == $name)
				return;
			$this->_record->set($name, $value);
		}
		
		/**
		 * Read the domain object
		 *
		 * @param mixed $pkValue
		 */
		public function read($pkValue)
		{
			$this->_record->readPK($pkValue);
		}
		
		/**
		 * Insert the domain object as new record
		 */
		public function insert()
		{
			$this->validate();
			$this->validateOnInsert();
			return $this->_record->insert();
		}
		
		/**
		 * Update the domain object
		 */
		public function update()
		{
			$this->validate();
			$this->validateOnUpdate();
			return $this->_record->update();
		}
		
		/**
		 * Delete the domain object
		 */
		public function delete($pkValue = null)
		{
			if ($pkValue !== null)
				$this->_record->set($this->_record->getPrimaryKeyColumn(), $pkValue);
			return $this->_record->delete();
		}
		
		/**
		 * Validates the object
		 *
		 * @throws miException
		 */
		public function validate()
		{
			
		}
		
		/**
		 * Validates the object for insert operation
		 *
		 * @throws miException
		 */
		public function validateOnInsert()
		{
			
		}
		
		/**
		 * Validates the object for update operation
		 *
		 * @throws miException
		 */
		public function validateOnUpdate()
		{
			
		}
	}
?>