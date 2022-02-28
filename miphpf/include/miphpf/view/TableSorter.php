<?php
	/**
	 * The miTableSorter class
	 * 
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * 
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miTableSorter extends miTableFeature {
		
		const TMPL_VAR_ORDER_PARAMS = 'ORDER_PARAMS';
		const TMPL_VAR_SORT_DIR_SUFFIX = '_SORT_DIR';
		const PARAM_SORTBY = 'sortBy';
		const PARAM_SORTDIR = 'sortDir';
		
		/**
		 * Get the feature values
		 * 
		 * @access public
		 * @return array
		 */
		public function getValues()
		{
			$values = array();
			$fields = $this->_table->getRecordset()->getAllFields();
			
			$sortBy = $this->getStateValue(self::PARAM_SORTBY, '');
			$sortDir = $this->getStateValue(self::PARAM_SORTDIR, 'DESC');
			foreach ($fields as $index => $field) {
				$newSortDir = 'ASC';
				if ($sortBy == $field)
					$newSortDir = ($sortDir == 'ASC') ? 'DESC' : 'ASC';
				$values[strtoupper($field) . self::TMPL_VAR_SORT_DIR_SUFFIX] = $newSortDir;
			}
			$values[self::TMPL_VAR_ORDER_PARAMS] = $this->_table->paramsArrayToUrl($this->getStateParams());
			return $values;
		}
		
		/**
		 * Returns associative array with params that save the feature state
		 * 
		 * @return array
		 */
		public function getStateParams()
		{
			return array(
				self::PARAM_SORTBY => $this->getStateValue(self::PARAM_SORTBY, ''),
				self::PARAM_SORTDIR => $this->getStateValue(self::PARAM_SORTDIR, '')
			);
		}
	}
?>