<?php
	/**
	 * User Interface Util Class
	 *
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Filters support class
	 * 
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miTableFilters extends miTableFeature {
		const FILTER_NAME_SUFFIX = 'Filter';
		const FILTER_CONDITION_SUFFIX = 'Condition';
		
		/**
		 * An associative array with filter field and value
		 * 
		 * @access protected
		 * @var array
		 */
		protected $_defaultFilterValues = array();
		
		
		/**
		 * Additional filters added by application logic
		 * 
		 * @access protected
		 * @var array
		 */
		protected $_additionalFilters = array();
		
		
		/**
		 * Map between the filter conditions and the actual filter class
		 * 
		 * @access protected
		 * @var array
		 */
		protected $_conditionMap = array(
			'=' => 'miSqlFilterEqual',
			'!=' => 'miSqlFilterNotEqual',
			'>' => 'miSqlFilterBiggerThan',
			'>=' => 'miSqlFilterBiggerOrEqual',
			'<' => 'miSqlFilterSmallerThan',
			'<=' => 'miSqlFilterSmallerOrEqual',
			'substring' => 'miSqlFilterSubstring',
			'starts' => 'miSqlFilterStarts',
			'ends' => 'miSqlFilterEnds',
			'regexp' => 'miSqlFilterRegExp',
			'in' => 'miSqlFilterIn',
			'notin' => 'miSqlFilterNotIn',
		);
			
		
		/**
		 * Sets a condition handler
		 * The miView expects the handler class be subclassed from miSqlFilter
		 * 
		 * @access public
		 * @param string $condition
		 * @param string $handlerClassName
		 */
		public function setConditionHandler($condition, $handlerClassName)
		{
			$this->_conditionMap[$condition] = $handlerClassName;
		}
		
		/**
		 * Set the default filter values
		 * 
		 * @param array $defaultFilterValues an associative array with filter field and value
		 */
		public function setDefaultFilterValues($defaultFilterValues)
		{
			$this->_defaultFilterValues = $defaultFilterValues;
		}
		
		
		/**
		 * Adds an additional filter
		 * 
		 * @param string $field
		 * @param string $value
		 * @param string $condition
		 */
		public function addAdditionalFilter($field, $value, $condition)
		{
			$this->_additionalFilters[] = array($field, $value, $condition);
		}
		
		
		/**
		 * The the current filters values
		 * 
		 * @return array associative array with each filter and it's value
		 * @throws miConfigurationException
		 */
		public function getFilterValues()
		{
			$filters = $this->getFiltersFromRequest();
			
			$filterValues = $this->_defaultFilterValues;
			foreach ($filters as $filter) {
				$filterValues[$filter[0]] = $filter[1];
			}
			return $filterValues;
		}
		
		/**
		 * Returns the filter value of $filterName
		 * The request is checked first, then the default filter values,
		 * and if the $filterName cannot not found returns empty string
		 * 
		 * TODO: improve efficiency by not using getFilterValues()
		 * 
		 * @access public
		 * @param string $filterName
		 * @return string the filter value
		 */
		public function getFilterValue($filterName)
		{
			$filters = $this->getFilterValues();
			
			if (isset($filters[$filterName]))
				return $filters[$filterName];
			
			if (isset($this->_defaultFilterValues[$filterName]))
				return $this->_defaultFilterValues[$filterName];
			
			return '';
		}
		
		/**
		 * Get the feature values
		 * 
		 * @access public
		 * @return array
		 * @throws miConfigurationException
		 */
		public function getValues()
		{
			$filterValues = $this->getFilterValues();
			$values = array();
			foreach ($filterValues as $field => $filter) {
				if (!is_array($filter)) {
					$values[strtoupper($field) . '_FILTERVALUE'] = $filter;
					continue;
				}
				foreach ($filter as $key => $value) {
					$values[strtoupper($field) . '_FILTERVALUE[' . $key . ']'] = $value;
				}
			}
			$values['FILTER_PARAMS'] = $this->_table->paramsArrayToUrl($this->getStateParams());
			return $values;
		}
		
		/**
		 * Returns associative array with params that save the feature state
		 * 
		 * @return array
		 * @throws miConfigurationException
		 */
		public function getStateParams()
		{
			$filterArray = $this->getFiltersFromRequest();
			$params = array();
			foreach ($filterArray as $filter) {
				if (!is_array($filter[1])) {
					$params[$filter[0] . self::FILTER_NAME_SUFFIX] = $filter[1];
					$params[$filter[0] . self::FILTER_CONDITION_SUFFIX] = $filter[2];
					continue;
				}
				foreach ($filter[1] as $key => $value) {
					$params[$filter[0] . self::FILTER_NAME_SUFFIX . '[' . $key . ']'] = $value;
					$params[$filter[0] . self::FILTER_CONDITION_SUFFIX . '[' . $key . ']'] = $filter[2][$key];
				}
			}
			return $params;
		}
		
		/**
		 * Gets all filter parameters from the request
		 * Returns the additional filters along with the filters from the request
		 * Uses $_REQUEST superglobal instead of $_GET or $_POST
		 * Note: Value and Condition can be arrays
		 * 
		 * @access public
		 * @return array array of arrays that each holding the field name[0], value[1] and condition[2]
		 * @throws miConfigurationException
		 */ 
		public function getFiltersFromRequest()
		{
			$filters = array();
			$suffixLen = strlen(self::FILTER_NAME_SUFFIX);
			$state = $this->_table->getState();
			foreach ($state as $property => $value) {
				if (substr_compare($property, self::FILTER_NAME_SUFFIX, -$suffixLen))
					continue;
				
				$field = substr($property, 0, -$suffixLen);
				$condition = isset($state[$field . self::FILTER_CONDITION_SUFFIX]) ? $state[$field . self::FILTER_CONDITION_SUFFIX] : '=';
				
				if (is_array($value) and is_array($condition)) {
					if (count($value) != count($condition))
						throw new miConfigurationException('Filter values and filter conditions arrays must be same size:' . $field, miConfigurationException::EXCEPTION_FILTER_SAME_ARRAY_SIZE);
					
					// For security purposes only numeric indexes are allowed
					$filters[] = array($field, array_values($value), array_values($condition));
					continue;
				}
				
				if (is_array($value) or is_array($condition))
					throw new miConfigurationException('Filter values and filter conditions must be either both array or both non-array inputs: ' . $field, miConfigurationException::EXCEPTION_FILTER_ONE_IS_ARRAY);
				
				$filters[] = array($field, $value, $condition);
			}
			
			return $filters + $this->_additionalFilters;
		}
		
		
		/**
		 * Returns an array of filter objects
		 * 
		 * @return array array of objects, subclasses of miSqlFilter
		 * @throws miConfigurationException
		 */
		public function getFilterObjs()
		{
			$filterObjs = array();
			
			$filters = $this->getFiltersFromRequest();
			foreach ($filters as $filter) {
				if (!is_array($filter[1])) {
					$filterObjs[] = $this->createFilterObj($filter[0], $filter[1], $filter[2]);
					continue;
				}
				foreach ($filter[1] as $key => $value)
					$filterObjs[] = $this->createFilterObj($filter[0], $value, $filter[2][$key]);
			}
			return $filterObjs;
		}
		
		
		/**
		 * Creates a filter object
		 * The object depends on the $condition
		 * 
		 * @param string $field the filter field
		 * @param string $value
		 * @param string $condition
		 * @throws miConfigurationException
		 */
		protected function createFilterObj($field, $value, $condition)
		{
			if (empty($this->_conditionMap[$condition]))
				throw new miConfigurationException('Invalid filter condition ' . $condition . ' for ' . $field, miConfigurationException::EXCEPTION_FILTER_CONDITION);
			
			return new $this->_conditionMap[$condition]($field, $value);
		}
	}
?>