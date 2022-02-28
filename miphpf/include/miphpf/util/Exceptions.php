<?php
	/**
	 * Exception classes
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	 
	/**
	 * Base class for all MIPHPF exceptions
	 * Use it in a catch block if you want to catch any MIPHPF exception
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miException extends Exception {
	}
	
	/**
	 * Database operation exception
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miDBException extends miException {
		const EXCEPTION_CONNECT = 1;
		const EXCEPTION_SELECTDB = 2;
		const EXCEPTION_QUERY = 3;
		const EXCEPTION_RECORD_NOT_FOUND = 4;
		
		/**
		 * The actual database error code
		 */
		protected $_dbCode;
		
		/**
		 * Construct DB exception
		 * 
		 * @access public
		 * @param string $message
		 * @param int $code
		 * @param int $dbCode
		 * @param string $query
		 */
		public function __construct($message, $code, $dbCode = 0, $query = '')
		{
			$this->_dbCode = $dbCode;
			parent::__construct($message . ' (' . $query . ')', $code);
		}
		
		/**
		 * Returns the actual db error code
		 * 
		 * @access public
		 * @return int
		 */
		public function getDbCode()
		{
			return $this->_dbCode;
		}
	}
	
	/**
	 * Configuration exception
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miConfigurationException extends miException {
		const EXCEPTION_FILTER_CONDITION = 1;
		const EXCEPTION_FILTER_SAME_ARRAY_SIZE = 2;
		const EXCEPTION_FILTER_ONE_IS_ARRAY = 3;
		const EXCEPTION_FILTER_INVALID_FIELD = 4;
		const EXCEPTION_UNDEFINED_TEMPLATE_FILENAME = 5;
		const EXCEPTION_HEADERS_ALREADY_SENT = 6;
		const EXCEPTION_DEFAULT_RADIO_INDEX_INVALID = 7;
		const EXCEPTION_DEFAULT_OPTION_INDEX_INVALID = 8;
	}
?>