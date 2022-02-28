<?php
	/**
	 * The state manager class
	 * 
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Saves and restores the state base class
	 * 
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miState {
		protected $_category;
		protected $_stateVars = array();
		
		protected $_persistVars = array();
		
		/**
		 * Constucts the state manager class
		 *
		 * @param string $category
		 */
		public function __construct($category = '')
		{
			$this->_category = $category;
		}
		
		public function setDefaultCategory($category)
		{
			$this->_category = $category;
		}
		
		/**
		 * Get the names of variables to be persisted
		 *
		 * @return array
		 */
		public function getPersistVars()
		{
			return $this->_persistVars[$this->_category];
		}
		
		/**
		 * Set the names of variables to be persisted
		 *
		 * @param array $persistVars
		 */
		public function setPersistVars($persistVars)
		{
			$this->_persistVars[$this->_category] = $persistVars;
		}
		
//		public function selectCategory($category)
//		{
//			$this->_category = $category;
//		}
		
//		public function get($name)
//		{
//			return $this->_stateVars[$this->_category][$name];
//		}
//		
//		public function set($name, $value)
//		{
//			$this->_stateVars[$this->_category][$name] = $value;
//		}
		
		public function getArray($category = false)
		{
			if ($category == false)
				$category = $this->_category;
			
			if (!isset($this->_stateVars[$category]))
				return array();
			return $this->_stateVars[$category];
		}
		
		public function setArray($values, $category = false)
		{
			if ($category == false)
				$category = $this->_category;
			
			if (!isset($this->_stateVars[$category])) {
				$this->_stateVars[$category] = $values;
				return;
			}
			$this->_stateVars[$category] = array_merge($this->_stateVars[$category], $values);
		}
		
		public function restoreState()
		{
			$this->_stateVars[$this->_category] = $_REQUEST;
		}
		
		public function saveState()
		{
		}
		
		public function getStateUrlParams()
		{
			if (!isset($this->_stateVars[$this->_category]))
				return '';
			
			// If there are no persist vars for the category don't persist
			if (!isset($this->_persistVars[$this->_category]))
				return '';
			
			$vars = array_combine($this->_persistVars[$this->_category], $this->_persistVars[$this->_category]);
			$stateVars = array_intersect_key($this->_stateVars[$this->_category], $vars);
			if (empty($stateVars))
				return '';
			return http_build_query($stateVars);
		}
	}
?>