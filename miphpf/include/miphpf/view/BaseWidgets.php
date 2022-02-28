<?php
	/**
	 * The standard widgets
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Base class for the base widgets: text, checkbox, radio, select
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miBaseWidget extends miWidget {
		protected $_fieldName;
		
		/**
		 * Sets the field name
		 * 
		 * @param string $fieldName the name of the field
		 */
		public function setFieldName($fieldName)
		{
			$this->_fieldName = $fieldName;
		}
		
		/**
		 * Retrieves the field name
		 * 
		 * @return string the field name
		 */
		public function getFieldName()
		{
			return $this->_fieldName;
		}
	}
	
	
	/**
	 * Text widget
	 * Use it with input type="text" and with textarea
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miBaseTextWidget extends miBaseWidget {
		/**
		 * 
		 */
		function getControl()
		{
			return array($this->_fieldName => miI18N::htmlEscape($this->_webForm->getFormData($this->_fieldName)));
		}
		
		/**
		 * Returns the text widget contents to be displayed
		 * 
		 * @return array
		 */
		function getEditableControl()
		{
			return array($this->_fieldName => miI18N::htmlEscape($this->_webForm->getFormData($this->_fieldName)));
		}
		
		/**
		 * Process the form submissions and returns the text widget data
		 * 
		 * @return array
		 */
		function getData()
		{
			if (isset($this->_properties['readOnly']) && $this->_properties['readOnly']) {
				return array();	
			}
			return array($this->_fieldName => miGetParamDefault($this->_fieldName, ''));
		}
		
		/**
		 * Validates the text widget
		 */
		public function validateData(miWebFormErrorsHandler &$errors)
		{
			if (isset($this->_properties['minLength'])) {
				if (strlen(miGetParamDefault($this->_fieldName, '')) < $this->_properties['minLength'])
					$errors->addError($this->_fieldName, 'Field too short');
			}
			if (isset($this->_properties['maxLength'])) {
				if (strlen(miGetParamDefault($this->_fieldName, '')) > $this->_properties['maxLength'])
					$errors->addError($this->_fieldName, 'Field too long');
			}
		}
	}
	
	
	/**
	 * Checkbox widget
	 * Use it with input type="checkbox"
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miBaseCheckboxWidget extends miBaseWidget {
		const VIEW_CHECKED = 'Yes';
		const VIEW_UNCHECKED = 'No';
		
		protected $_defaultValue = false;
		
		public function __construct(miWebForm &$webForm, $properties = array())
		{
			parent::__construct($webForm, $properties);
			if (isset($this->_properties['defaultValue']))
				$this->_defaultValue = $this->_properties['defaultValue'];
		}
		
		/**
		 * Sets the default state of the checkbox
		 * 
		 * @param boolean $defaultValue
		 */
		public function setDefaultValue($defaultValue)
		{
			$this->_defaultValue = $defaultValue;
		}
		
		protected function getValue()
		{
			$value = $this->_webForm->getFormData($this->_fieldName);
			if (is_null($value) and !is_null($this->_defaultValue))
				return $this->_defaultValue;
			return $value;
		}
		
		/**
		 * 
		 */
		function getControl()
		{
			return array($this->_fieldName => $this->getValue() ? miBaseCheckboxWidget::VIEW_CHECKED : miBaseCheckboxWidget::VIEW_UNCHECKED);
		}
		
		/**
		 * Returns the checkbox widget contents to be displayed
		 * 
		 * @return array
		 */
		function getEditableControl()
		{
			return array($this->_fieldName => $this->getValue() ? 'checked="checked"' : '');
		}
		
		/**
		 * Process the form submissions and returns the checkbox widget data
		 * 
		 * @return array
		 */
		function getData()
		{
			// The checkbox is checked if the value is non-empty string
			return array($this->_fieldName => (miGetParamDefault($this->_fieldName, '') == '') ? false : true);
		}
	}
	
	
	/**
	 * Radio buttons widget
	 * Use it with input type="radio"
	 * 
	 * This class supports a group of radio buttons
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miBaseRadioWidget extends miBaseWidget {
		/**
		 * Array with the radio buttons
		 * 
		 * @access protected
		 */
		protected $_radioButtons = array();
		
		/**
		 * The index of the default radio button
		 * 
		 * @access protected
		 */
		protected $_defaultRadioIndex = null;
		
		/**
		 * Sets the radio buttons
		 * The $radioButtons is an associative array
		 * where the key is used to separate the radio buttons within a group and
		 * the value is the display value of the radio button
		 * If $defaultRadioIndex is not set the first radio button is checked.
		 * 
		 * @access public
		 * @param array $radioButtons
		 * @param string|int $defaultRadioIndex the index of the default radio button (optional)
		 */
		public function setRadioButtons($radioButtons, $defaultRadioIndex = null)
		{
			$this->_radioButtons = $radioButtons;
			
			// Make sure that the default index is valid
			if ($defaultRadioIndex !== null) {
				if (empty($radioButtons[$defaultRadioIndex]))
					throw new miConfigurationException('The default radio index "' . $defaultRadioIndex . '" is invalid. Must be either valid, or null.', miConfigurationException::EXCEPTION_DEFAULT_RADIO_INDEX_INVALID);
				$this->_defaultRadioIndex = $defaultRadioIndex;
			}
		}
		
		/**
		 * 
		 */
		function getControl()
		{
			$value = $this->_webForm->getFormData($this->_fieldName);
			return array($this->_fieldName => miI18N::htmlEscape(@$this->_radioButtons[$value]));
		}
		
		protected function getValue()
		{
			$value = $this->_webForm->getFormData($this->_fieldName);
			if (!empty($this->_radioButtons[$value]))
				return $value;
			
			// If we cannot find the $value in the radioButtons group use the default
			if ($this->_defaultRadioIndex === null) {
				reset($this->_radioButtons);
				return key($this->_radioButtons);
			} else
				return $this->_defaultRadioIndex;
		}
		
		/**
		 * Returns the widget contents to be displayed
		 * 
		 * @return array
		 */
		public function getEditableControl()
		{
			$value = $this->getValue();
			
			$display = array();
			foreach ($this->_radioButtons as $key => $option) {
				$display[$this->_fieldName . '_' . $key] = ($value == $key) ? 'checked="checked"' : '';
			}
			return $display;
		}
		
		/**
		 * Process the form submissions and returns the widget data
		 * 
		 * @return array
		 */
		public function getData()
		{
			return array($this->_fieldName => miGetParamDefault($this->_fieldName, ''));
		}
	}
	
	
	/**
	 * Select widget
	 * Use it with select tag
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miBaseSelectWidget extends miBaseWidget {
		/**
		 * 
		 * @access protected
		 */
		protected $_options = array();
		
		/**
		 * 
		 * @access protected
		 */
		protected $_defaultOptionIndex = null;
		
		/**
		 * @access public
		 */
		public function setOptions($options, $defaultOptionIndex = null)
		{
			$this->_options = $options;
			
			// Make sure the _defaultOptionIndex is valid or null
			if ($defaultOptionIndex !== null) {
				if (empty($options[$defaultOptionIndex]))
					throw new miConfigurationException('The default select option index "' . $defaultOptionIndex . '" is invalid. Must be either valid, or null.', miConfigurationException::EXCEPTION_DEFAULT_OPTION_INDEX_INVALID);
				$this->_defaultOptionIndex = $defaultOptionIndex;
			}
		}
		
		/**
		 * 
		 */
		function getControl()
		{
			$value = $this->_webForm->getFormData($this->_fieldName);
			return array($this->_fieldName => miI18N::htmlEscape(@$this->_options[$value]));
		}
		
		protected function getValue()
		{
			$value = $this->_webForm->getFormData($this->_fieldName);
			if (!empty($this->_options[$value]))
				return $value;
			
			if ($this->_defaultOptionIndex === null) {
				reset($this->_options);
				return key($this->_options);
			} else
				return $this->_defaultOptionIndex;
		}
		
		/**
		 * Returns the widget contents to be displayed
		 * 
		 * @return array
		 */
		public function getEditableControl()
		{
			$value = $this->getValue();
			
			$html = '';
			foreach ($this->_options as $key => $option)
				$html .= '<option value="' . miI18N::htmlEscape($key) . '"' . ($key == $value?' selected="selected"':'') . '>' . miI18N::htmlEscape($option) . '</option>';
			return array($this->_fieldName => $html);
		}
		
		/**
		 * Process the form submissions and returns the widget data
		 * 
		 * @return array
		 */
		public function getData()
		{
			if (isset($this->_properties['readOnly']) && $this->_properties['readOnly']) {
				return array();	
			}
			return array($this->_fieldName => miGetParamDefault($this->_fieldName, ''));
		}
	}
?>