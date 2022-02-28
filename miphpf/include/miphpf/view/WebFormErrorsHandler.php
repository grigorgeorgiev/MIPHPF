<?php
	/**
	 * The standard web form error handlers
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Collects and displays errors
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miWebFormErrorsHandler {
		/**
		 * The errors go here
		 * 
		 * @access protected
		 */
		protected $_errors = array();
		
		/**
		 * Add new error
		 * 
		 * @access public
		 * @param string $errorMessage
		 */
		public function addError($fieldName, $errorMessage)
		{
			if (isset($this->_errors[$fieldName]))
				$this->_errors[$fieldName] .= '. ' . $errorMessage;
			else
				$this->_errors[$fieldName] = $errorMessage;
		}
		
		/**
		 * Assign the errors to the template
		 * 
		 * @access public
		 * @param miTemplateParser $t
		 */
		public function assignErrors(&$t)
		{
		}
		
		/**
		 * Returns true if there are any errors in the object
		 * 
		 * @access public
		 * @return bool true if there are errors, false otherwise
		 */
		public function hasErrors()
		{
			return !empty($this->_errors);
		}
	}
	
	
	/**
	 * Message based errors handler
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miWebFormMessageErrorsHandler extends miWebFormErrorsHandler {
		const ERRORS_SEPARATOR = '<br/>';
		
		/**
		 * Assign the errors to the template
		 * 
		 * @access public
		 * @param miTemplateParser $t
		 */
		public function assignErrors(&$t)
		{
			$errorsArray = array();
			foreach ($this->_errors as $fieldName => $errors) {
				$errorsArray[] = $fieldName . ' : ' . $errors;
			}
			$t->assign('%%ERRORS%%', implode(self::ERRORS_SEPARATOR, miI18N::htmlEscape($errorsArray)));
		}
	}
?>