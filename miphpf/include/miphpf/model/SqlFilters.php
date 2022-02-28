<?php
	/**
	 * The standard filters
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
	 * Abstract base sql filter class
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	abstract class miSqlFilter {
		/**
		 * The filter name
		 */
		protected $_name;
		
		/**
		 * Creates miSqlFilter object
		 * 
		 * @param string $name
		 */
		public function __construct($name)
		{
			$this->_name = $name;
		}
		
		/**
		 * Returns the name of the filter
		 * 
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}
		
		/**
		 * Returns the sql field names
		 * Used by miSqlRecordset to validate the fields are valid
		 * 
		 * @access public
		 * @return array the field names
		 */
		public function getSqlFields()
		{
			return array($this->_name);
		}
		
		/**
		 * Returns the sql for the filter
		 * Return empty string if the filter doesn't want to add filter clause
		 * Abstract method
		 * 
		 * @access public
		 * @return string the sql code for the filter
		 */
		abstract public function getSql();
	}
	
	/**
	 * Abstract filter class for filters with one parametized value
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	abstract class miSqlFilterOneValue extends miSqlFilter {
		/**
		 * The filter value
		 */
		protected $_value;
		
		/**
		 * Construct the filter
		 * 
		 * @param string $name
		 * @param string $value
		 */
		public function __construct($name, $value)
		{
			$this->_value = $value;
			parent::__construct($name);
		}
		
		/**
		 * Returns the filter value
		 * 
		 * @return string the filter value
		 */
		public function getValue()
		{
			return $this->_value;
		}
	}
	
	/**
	 * Filter for matching substrings. Uses the like operator
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterSubstring extends miSqlFilterOneValue {
		public function getSql()
		{
			if ($this->_value == '')
				return '';
			return sql_escape_string($this->_name) . ' LIKE "%' . sql_escape_string($this->_value) . '%"';
		}
	}
	
	/**
	 * Filter for matching the begining of strings. Uses the like operator
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterStarts extends miSqlFilterOneValue {
		public function getSql()
		{
			if ($this->_value == '')
				return '';
			return sql_escape_string($this->_name) . ' LIKE "' . sql_escape_string($this->_value) . '%"';
		}
	}
	
	/**
	 * Filter for matching the endings of strings. Uses the like operator
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterEnds extends miSqlFilterOneValue {
		public function getSql()
		{
			if ($this->_value == '')
				return '';
			return sql_escape_string($this->_name) . ' LIKE "%' . sql_escape_string($this->_value) . '"';
		}
	}
	
	/**
	 * Base filter class for simple comparion operator filters
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterSimple extends miSqlFilterOneValue {
		/**
		 * The filter operator. The subclasses define it
		 */
		protected $_operator;
		
		public function getSql()
		{
			if ((string)$this->_value == '')
				return '';
			return sql_escape_string($this->_name) . $this->_operator . '"' . sql_escape_string($this->_value) . '"';
		}
	}
	
	/**
	 * 
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterEqual extends miSqlFilterSimple {
		protected $_operator = '=';
	}
	
	/**
	 * 
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterNotEqual extends miSqlFilterSimple {
		protected $_operator = '!=';
	}
	
	/**
	 * Simple filter for the > (bigger than) operator
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterBiggerThan extends miSqlFilterSimple {
		protected $_operator = '>';
	}
	
	/**
	 * Simple filter for the >= (bigger or equal) operator
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterBiggerOrEqual extends miSqlFilterSimple {
		protected $_operator = '>=';
	}
	
	/**
	 * Simple filter for the < (smaller than) operator
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterSmallerThan extends miSqlFilterSimple {
		protected $_operator = '<';
	}
	
	/**
	 * Simple filter for the <= (smaller or equal) operator
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterSmallerOrEqual extends miSqlFilterSimple {
		protected $_operator = '<=';
	}
	
	/**
	 * Regular expression filter
	 * 
	 * Please note that regexp operator is not portable.
	 * Some databases use the ANSI "similar to" operator, and others don't support regular expressions at all.
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterRegExp extends miSqlFilterSimple {
		protected $_operator = ' REGEXP ';
	}


	/**
	 * Filter checking that the field is equal to any of the vaues
	 * The values are expected to be separated by comma
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterIn extends miSqlFilterOneValue {
		protected $_operator = ' IN ';
		
		public function getSql()
		{
			if (is_array($this->_value)) {
				$values = $this->_value;
			} else {
				if ($this->_value == '')
					return '';
				$values = explode(',', $this->_value);
			}
			foreach ($values as $key => $value) {
				$values[$key] = sql_escape_string($value);
			}
			return sql_escape_string($this->_name) . $this->_operator . '("' . implode('","', $values) . '")';
		}
	}
	
	/**
	 * Filter checking that the field is not equal to any of the vaues
	 * The values are expected to be separated by comma
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterNotIn extends miSqlFilterIn {
		protected $_operator = ' NOT IN ';
	}
	
	
	/**
	 * Sql filter with custom SQL code
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSqlFilterCustom extends miSqlFilter {
		/**
		 * The custom sql code
		 */
		protected $_sql;
		
		/**
		 * Creates miSqlFilter object
		 * 
		 * @access public
		 * @param string $name
		 */
		public function __construct($name, $sql)
		{
			$this->_sql = $sql;
			parent::__construct($name);
		}
		
		/**
		 * Returns the sql code
		 * 
		 * @access public
		 * @return string the custom sql code
		 */
		public function getSql()
		{
			return $this->_sql;
		}
	}
?>