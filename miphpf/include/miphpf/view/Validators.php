<?php
	/**
	 * Web form validator classes
	 * 
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * The base validator class
	 * 
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miValidator {
		/**
		 * @var miWebForm
		 */
		protected $_webForm;
		
		/**
		 * 
		 * @var string
		 */
		protected $_fieldName;
		
		/**
		 * Constructs the validator class
		 * 
		 * @access public
		 * @param miWebForm $webForm
		 * @param string $fieldName
		 */
		public function __construct(miWebForm &$webForm, $fieldName)
		{
			$this->_webForm = $webForm;
			$this->_fieldName = $fieldName;
		}
		
		/**
		 * Retrieve the field name
		 * 
		 * @return string the field name
		 */
		public function getFieldName()
		{
			return $this->_fieldName;
		}
		
		/**
		 * Set the field name
		 * 
		 * @param string $fieldName the field name
		 */
		public function setFieldName($fieldName)
		{
			$this->_fieldName = $fieldName;
		}
		
		/**
		 * Performs validation
		 *
		 * @access public
		 * @param miWebFormErrorsHandler
		 * @return void
		 */
		public function validate(miWebFormErrorsHandler &$errors)
		{
		}
	}
	
	/**
	 * Validates email set by user
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miValidatorEmail extends miValidator {
		public function validate(miWebFormErrorsHandler &$errors)
		{
			$fieldValue = $this->_webForm->getSubmittedData($this->_fieldName);
			if (preg_match('/^[A-Za-z0-9]+([_\.-A-Za-z0-9]+)*@[A-Za-z0-9]+([_\.-][A-Za-z0-9]+)*\.([A-Za-z]){2,4}$/iU', $fieldValue))
				return;
			$errors->addError($this->_fieldName, 'Invalid email format');
		}
	}
	
	/**
	 * Validates date set by user
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miValidatorDate extends miValidator {
		public function validate(miWebFormErrorsHandler &$errors)
		{
			$fieldValue = $this->_webForm->getSubmittedData($this->_fieldName);
			if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $fieldValue)) {
				$pieces = explode("-", $fieldValue);
				if (checkdate($pieces[1], $pieces[2], $pieces[0]))
					return;
			}
			$errors->addError($this->_fieldName, 'Invalid date format');
		}
	}
	
	/**
	 * Validates integer set by user
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miValidatorInt extends miValidator {
		public function validate(miWebFormErrorsHandler &$errors)
		{
			$fieldValue = $this->_webForm->getSubmittedData($this->_fieldName);
			if (preg_match('#^[0-9]+$#', $fieldValue))
				return;
			
			$errors->addError($this->_fieldName, 'Invalid integer');
		}
	}

	/**
	 * Validates decimal set by user
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miValidatorDecimal extends miValidator {
		public function validate(miWebFormErrorsHandler &$errors)
		{
			$fieldValue = $this->_webForm->getSubmittedData($this->_fieldName);
			if (preg_match('#^[0-9]{1,10}\.?([0-9]){0,2}$#', $fieldValue))
				return;
			
			$errors->addError($this->_fieldName, 'Invalid decimal');
		}
	}
	
	/**
	 * Validates ICQ set by user
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miValidatorIcq extends miValidator {
		public function validate(miWebFormErrorsHandler &$errors)
		{
			$fieldValue = $this->_webForm->getSubmittedData($this->_fieldName);
			if ($fieldValue > 10000 and $fieldValue < 2147483646)
				return;
			
			$errors->addError($this->_fieldName, 'Invalid ICQ');
		}
	}
	
	/**
	 * Validates http url set by user
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miValidatorHttp extends miValidator {
		public function validate(miWebFormErrorsHandler &$errors)
		{
			$fieldValue = $this->_webForm->getSubmittedData($this->_fieldName);
			if (substr($fieldValue, 0, 7) == 'http://')
				return;
			
			$errors->addError($this->_fieldName, 'Invalid http url');
		}
	}
	
	
	/**
	 * Validates GSM set by user
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miValidatorGsm extends miValidator {
		public function validate(miWebFormErrorsHandler &$errors)
		{
			$fieldValue = $this->_webForm->getSubmittedData($this->_fieldName);
			if (preg_match('#^[0-9]{10}$#', $fieldValue))
				return;
			
			$errors->addError($this->_fieldName, 'Invalid GSM');
		}
	}
	
	/**
	 * Validates IP address set by user
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miValidatorIp extends miValidator {
		public function validate(miWebFormErrorsHandler &$errors)
		{
			$fieldValue = $this->_webForm->getSubmittedData($this->_fieldName);
			if (ip2long($fieldValue) !== -1)
				return;
				
			$errors->addError($this->_fieldName, 'Invalid IP address');
		}
	}
	
	/**
	 * Unique validator
	 * Makes sure that the field value is unqiue.
	 * There is a special allowence that if the value is already in the database it is allowed even if duplicate
	 * 
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miValidatorUnique extends miValidator {
		/**
		 * The actual record used to read the data
		 * 
		 * @access protected
		 */
		protected $_record;
		
		/**
		 * Constructs the unique validator class
		 * 
		 * @access public
		 * @param miWebForm $webForm
		 * @param string $fieldName
		 * @param miSqlRecord $record
		 */
		public function __construct(miWebForm &$webForm, $fieldName, miSqlRecord $record)
		{
			$this->_record = $record;
			parent::__construct($webForm, $fieldName);
		}
		
		/**
		 * Validate
		 */
		public function validate(miWebFormErrorsHandler &$errors)
		{
			$fieldValue = $this->_webForm->getSubmittedData($this->_fieldName);
			
			// In case such record already exists, i.e. editing
			if (count($this->_record->getRow()) > 0) {
				$oldValue = $this->_record->get($this->_fieldName);
				// If there is no change, do not bother validating
				if ($fieldValue == $oldValue)
					return;
			}
			
			// Check if there is another record with the value
			$checkRecord = new miSqlRecord($this->_record->getTableName(), $this->_record->getPrimaryKeyColumn());
			try {
				$checkRecord->read($this->_fieldName, $fieldValue);
			} catch (miDBException $exception) {
				// Record doesn't exists. The value is unqiue
				if ($exception->getCode() == miDBException::EXCEPTION_RECORD_NOT_FOUND)
					return;
				
				// OOps, different exception. Rethrow
				throw $exception;
			}
			
			// The value is not unique
			$errors->addError($this->_fieldName, 'Not unique');
		}
	}
?>