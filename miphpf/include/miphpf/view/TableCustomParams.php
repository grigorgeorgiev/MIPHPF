<?php
	/**
	 * Table Custom Params Class
	 * 
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Handles custom params
	 * 
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miTableCustomParams extends miTableFeature {
		
		const TMPL_VAR_CUSTOM_PARAMS = 'RL_PARAMS';
		
		/**
		 * Used to forward parameters from top-level to sub-level views
		 * It usually contains ID(s) of a field that limits the managed records
		 * 
		 * @access protected
		 */
		protected $_recordLocatorParams = array();
		
		
		/**
		 * Sets the record locator params. They are used to forward params from
		 * top level to sub-level views
		 * 
		 * @access public
		 * @param array the record locator params
		 */
		public function setRecordLocatorParams($rlParams)
		{
			$this->_recordLocatorParams = $rlParams;
		}

		
		/**
		 * Gets the array of record locator params
		 * 
		 * @access public
		 * @return array the record locator params
		 */
		public function getRecordLocatorParams()
		{
			return $this->_recordLocatorParams;
		}

		/**
		 * Get the feature values
		 * 
		 * @access public
		 * @return array
		 */
		public function getValues()
		{
			$values = array();
			$values[self::TMPL_VAR_CUSTOM_PARAMS] = $this->_table->paramsArrayToUrl($this->getStateParams());
			foreach ($this->_recordLocatorParams as $name => $value) {
				$values[strtoupper($name)] = $value;
			}
			return $values;
		}
		
		/**
		 * Returns associative array with params that save the feature state
		 * 
		 * @return array
		 */
		public function getStateParams()
		{
			return $this->_recordLocatorParams;
		}
	}
?>