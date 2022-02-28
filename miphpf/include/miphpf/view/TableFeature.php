<?php
	/**
	 * The miTableFeature class
	 * 
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * miTableFeature base class
	 * Each table feature subclasses this class
	 * 
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 * @abstract the subclasses must implement getValues()
	 */
	abstract class miTableFeature {
		
		/**
		 * A reference to the miTable object associated with this table feature
		 * 
		 * @access protected
		 */
		protected $_table;
		
		/**
		 * Constructs a table feature
		 */
		function __construct($table)
		{
			$this->_table = $table;
			$table->addTableFeature($this);
		}
		
		/**
		 * Get the feature values
		 * 
		 * @access public
		 * @return array
		 */
		abstract public function getValues();
		
		/**
		 * Returns associative array with params that save the feature state
		 * 
		 * @return array
		 */
		public function getStateParams()
		{
			return array();
		}
		
		/**
		 * Returns the value of a named state variable
		 * Returns the default value if the state variable is not set
		 *
		 * @param string $name
		 * @param mixed $defaultValue
		 * @return mixed
		 */
		protected function getStateValue($name, $defaultValue)
		{
			$state = $this->_table->getState();
			return isset($state[$name]) ? $state[$name] : $defaultValue;
		}
	}
?>