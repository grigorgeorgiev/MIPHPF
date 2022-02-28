<?php
	/**
	 * Contains the miWebForm class
	 *
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Manages a web form
	 * 
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miWebForm {
		
		
		/**
		 * Array that describes the output and input of each web field
		 *
		 * @access protected
		 * @var array
		 */
		protected $_formProperties = array();
		
		/**
		 * Array with field objects
		 * 
		 * @access protected
		 * @var array
		 */
		protected $_formFields = array();
		
		/**
		 * The validators
		 * 
		 * @access protected
		 * @var array
		 */
		protected $_validators = array();
		
		/**
		 * @access protected
		 * @var array
		 */
		protected $_mainPageElements = array();
		
		/**
		 * 
		 */
		protected $_submittedData = array();
		
		/**
		 * Array with data to displayed by the widgets
		 *
		 * @access protected
		 * @var array
		 */
		protected $_formDataRow;
		
		/**
		 * A reference to the error hanlder object
		 */
		protected $_errorsHandler = null;
		
		/**
		 * Template section infos to be added when parsing the template
		 */
		protected $_templateSectionInfos = array();
		
		/**
		 * Reference to the message object, or null
		 */
		protected $_messageObj = null;
		
		/**
		 * Constructs the miWebForm
		 * 
		 * @access public
		 * @param array $formData
		 */
		function __construct($formDataRow)
		{
			$this->_formDataRow = $formDataRow;
		}
		
		/**
		 * Sets the form data row
		 * 
		 * @access public
		 * @param array $formDataRow
		 */
		public function setFormDataRow($formDataRow)
		{
			$this->_formDataRow = $formDataRow;
		}
				
		/**
		 * Returns the form data row
		 * 
		 * @access public
		 * @return array
		 */
		public function &getFormDataRow()
		{
			return $this->_formDataRow;
		}
		
		/**
		 * Sets form data
		 * 
		 * @access public
		 * @param string $key
		 * @param string $formData
		 */
		public function setFormData($key, $formData)
		{
			$this->_formDataRow[$key] = $formData;
		}
				
		/**
		 * Returns a form data value
		 * 
		 * @access public
		 * @param string $key
		 * @return mixed
		 */
		public function &getFormData($key)
		{
			return $this->_formDataRow[$key];
		}
		
		/**
		 * Adds new template section info
		 */
		public function addTemplateSectionInfo($templateSectionInfo)
		{
			$this->_templateSectionInfos[] = $templateSectionInfo;
		}
		
		/**
		 * Set the form fields
		 * 
		 * @param array $formProperties the form properties array
		 */
		public function initWidgets($formProperties)
		{
			$this->_formProperties = $formProperties;
			
			// Build all field objects
			foreach ($formProperties as $key => $formField) {
				$properties = isset($formField['properties']) ? $formField['properties'] : array();
				$field = new $formField['field']($this, $properties);
				$field->setFieldName($formField['data']);
				$this->_formFields[$key] = $field;
				
				if (isset($formField['validator'])) {
					// Multiple validators per field
					if (is_array($formField['validator'])) {
						$this->_validators[$key] = array();
						foreach ($formField['validator'] as $validator) {
							$this->_validators[$key][] = new $validator($this, $formField['data']);
						}
						continue;
					}
					
					// Single validator
					$this->_validators[$key] = new $formField['validator']($this, $formField['data']);
				}
			}
		}
		
		/**
		 * Adds a validator for a field
		 * 
		 * @access public
		 * @param string $fieldName
		 * @param miValidator $validator
		 */
		public function addValidator($fieldName, miValidator $validator)
		{
			foreach ($this->_formProperties as $key => $formField) {
				if ($formField['data'] != $fieldName)
					continue;
				
				// Check for 3 possible cases:
				// 1. there is no validator - set it as reference
				// 2. there is one validator - convert to array of validators
				// 3. there are already more than one validators - add the validator to the array of validators
				if (isset($this->_validators[$key])) {
					if (is_array($this->_validators[$key]))
						$this->_validators[$key][] = $validator;
					else
						$this->_validators[$key] = array($this->_validators[$key], $validator);
				} else
					$this->_validators[$key] = $validator;
			}
		}
		
		/**
		 * Removes all validators for a field
		 * 
		 * @access public
		 * @param string $fieldName
		 */
		public function removeValidators($fieldName)
		{
			foreach ($this->_formProperties as $key => $formField) {
				if ($formField['data'] != $fieldName)
					continue;
				
				unset($this->_validators[$key]);
			}
		}
		
		/**
		 * Returns the widget
		 * 
		 * @param string $fieldName the name of the field whose widget to return
		 * @return miWidget|null an object that is extends miWidget, or null if not found
		 */
		public function getWidget($fieldName)
		{
			foreach ($this->_formProperties as $key => $formProperty) {
				if ($formProperty['data'] == $fieldName)
					return $this->_formFields[$key];
			}
			return null;
		}
		
		
		/**
		 * Adds new form field object
		 * 
		 * @param string $fieldName
		 * @param miBaseWidget $widget
		 */
		public function addWidget($fieldName, miBaseWidget $widget)
		{
			$this->_formProperties[] = array('data' => $fieldName);
			end($this->_formProperties);
			$this->_formFields[key($this->_formProperties)] = $widget;
		}
		
		/**
		 * 
		 */
		public function addMainPageElements($mainPageElements)
		{
			$this->_mainPageElements = array_merge($this->_mainPageElements, $mainPageElements);
		}
		
		/**
		 * Parses the form template
		 * 
		 * @param string $templateName
		 * @param boolean $isEditable if the form will contain the editable fields/widgets
		 * @return string the html for the form
		 */
		public function parse($templateName, $isEditable)
		{
			$t = new miTemplateParser();
			$t->readTemplate($templateName);
			
			foreach ($this->_formFields as $key => $formField) {
				if ($isEditable)
					$disp = $formField->getEditableControl();
				else
					$disp = $formField->getControl();
				
				$fieldValues = array();
				foreach ($disp as $key => $value) {
					$fieldValues['%%' . strtoupper($key) . '%%'] = $value;
				}
				$t->assignArray($fieldValues);
			}

			$errors = $this->getWebFormErrorsHandler();
			$errors->assignErrors($t);
			
			$t->assignArray($this->getMessageObj()->getMessageTemplateVars());
			$t->assignArray($this->_mainPageElements);
			$t->setSectionInfos($this->_templateSectionInfos);
			$this->onParse($t);
			return $t->templateParse();
		}
		
		/**
		 * Returns the message object
		 * if $this->_messageObj is null creates new miMessage object
		 * 
		 * @return miMessage the message object
		 */
		protected function getMessageObj()
		{
			if ($this->_messageObj == null)
				$this->_messageObj = new miMessage;
			return $this->_messageObj;
		}
		
		/**
		 * Shows the form
		 * 
		 * @param string $templateName
		 * @param boolean $isEditable if the form will contain the editable fields/widgets
		 */
		public function show($templateName, $isEditable)
		{
			echo $this->parse($templateName, $isEditable);
		}
		
		/**
		 * Called just before the template is parsed
		 * Subclasses can add custom functionality using this method
		 * 
		 * @param miTemplateParser $templateParser the template parser
		 */
		protected function onParse(miTemplateParser &$templateParser)
		{
		}
		
		/**
		 * Returns the web form errors handling object
		 * 
		 * @access protected
		 * @return miWebFormErrorsHandler
		 */
		public function getWebFormErrorsHandler()
		{
			if ($this->_errorsHandler == null)
				$this->_errorsHandler = new miWebFormErrorsHandler;
			return $this->_errorsHandler;
		}
		
		public function setErrorsHandler($errorsHandler)
		{
			$this->_errorsHandler = $errorsHandler;
		}
		
		/**
		 * Process the form submission
		 * 
		 * @return boolean true on success, false if errors were found
		 */
		function processSubmit()
		{
			$this->_submittedData = array();
			
			foreach ($this->_formFields as $key => $formField) {
				$submittedData = $formField->getData();
				$this->_submittedData = array_merge($this->_submittedData, $submittedData);
			}
			
			// Validate the form
			$errors = $this->getWebFormErrorsHandler();
			foreach ($this->_formFields as $key => $formField) {
				$formField->validateData($errors);
			}
			foreach ($this->_formFields as $key => $formField) {
				if (!isset($this->_validators[$key]))
					continue;
				
				// Multiple validators per field
				if (is_array($this->_validators[$key])) {
					foreach ($this->_validators[$key] as $validator) {
						$validator->validate($errors);
					}
					continue;
				}
				
				// Single validator
				$this->_validators[$key]->validate($errors);
			}
			
			return !$errors->hasErrors();
		}
		
		/**
		 * Returns the submitted data row
		 * 
		 * @access public
		 * @return array
		 */
		public function getSubmittedDataRow()
		{
			return $this->_submittedData;
		}
		
		/**
		 * Return the submitted field value
		 * 
		 * @access public
		 * @return string
		 */
		public function getSubmittedData($fieldName)
		{
			return $this->_submittedData[$fieldName];
		}
	}
?>