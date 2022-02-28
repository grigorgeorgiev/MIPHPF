<?php
	/**
	 * The base form widget class
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Base class form web form widget
	 * One web form widget can handle single data item, or multiple data items
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miWidget {
		/**
		 * A reference to the web form that contains this widget
		 */
		protected $_webForm;
		
		/**
		 * Contains the form widget properties
		 */
		protected $_properties;
		
		/**
		 * Construct miWidget object
		 * 
		 * @access public
		 * @param miWebForm $webForm
		 * @param array $properties widget properties (optional)
		 */
		public function __construct(miWebForm &$webForm, $properties = array())
		{
			$this->_webForm = $webForm;
			$this->_properties = $properties;
		}
		
		/**
		 * Returns the widget properties
		 *
		 * @access public
		 * @return array
		 */
		public function getProperties()
		{
			return $this->_properties;
		}
		
		/**
		 * Sets a property value
		 *
		 * @param string $name
		 * @param mixed $value
		 */
		public function setProperty($name, $value)
		{
			$this->_properties[$name] = $value;
		}
		
		/**
		 * Returns array with template variables to be displayed for non-editable control
		 * 
		 * @access public
		 * @return array
		 */
		public function getControl()
		{
			return array();
		}
		
		/**
		 * Returns array with template variables to be displayed for editable control
		 * 
		 * @access public
		 * @return array
		 */
		public function getEditableControl()
		{
			return array();
		}
		
		/**
		 * Process the form submissions and returns the data
		 * 
		 * @access public
		 * @return array
		 */
		public function getData()
		{
			return array();
		}
		
		/**
		 * Validates the submitted data for this widget
		 * Called upon form submission
		 * 
		 * @access public
		 * @param miWebFormErrorsHandler
		 */
		public function validateData(miWebFormErrorsHandler &$errors)
		{
		}
	}
?>