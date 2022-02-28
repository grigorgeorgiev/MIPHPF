<?php
	/**
	 * miTable Class
	 *
	 * @copyright Copyright (c) 2003-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Manage the representation in table format of data, 
	 * which is read from miSqlRecordset object
	 * 
	 * @copyright Copyright (c) 2003-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miTable {
		
		
		/**
		 * @access protected
		 */
		protected $_recordset;
		
				
		/**
		 * @access protected
		 */
		protected $_mainPageElements = array();
		
		
		/**
		 * @var array with the miTableFeature-extending objects
		 * @access protected
		 */
		protected $_tableFeatures = array();
		
		/**
		 * Reference to the message object, or null
		 */
		protected $_messageObj = null;
		
		/**
		 * @var array hash with the current state
		 */
		protected $_state = array();
		
		/**
		 * miTable constructor. It takes one parameter - object of class 
		 * miSqlRecordset or its subclasses
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $recordset = new miSqlRecordset('tableName');
		 * $table = new miTable($recordset);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param object $recordset object of class miSqlRecordset or its subclasses
		 */
		function __construct(&$recordset)
		{
			$this->_recordset = &$recordset;
		}
		
		/**
		 * Adds a table feature
		 * 
		 * @access public
		 * @param object $tableFeature
		 */
		public function addTableFeature($tableFeature)
		{
			$this->_tableFeatures[] = $tableFeature;
		}
		
		/**
		 * Retrieves the recordset object
		 */
		public function &getRecordset()
		{
			return $this->_recordset;
		}
		
		
		/**
		 * Returns the params saving the state of all table features
		 */
		public function getTableFeaturesStateParams()
		{
			$params = array();
			foreach ($this->_tableFeatures as $tableFeature) {
				$params = array_merge($params, $tableFeature->getStateParams());
			}
			return $params;
		}
		
		/**
		 * Converts an associative array of params into url string
		 * 
		 * @param array $params
		 * @return string
		 */
		function paramsArrayToUrl($params)
		{
			$encParams = array();
			foreach ($params as $key => $value)
				$encParams[] = $key . '=' . urlencode($value);
			return implode('&', $encParams);
		}
		
		
		/**
		 * Sets main page elements
		 *
		 * @access public
		 * @param array $mainPageElements array with the main page elements
		 */
		public function setMainPageElements($mainPageElements)
		{
			$this->_mainPageElements = $mainPageElements;
		}
		
		/**
		 * Add main page elements
		 *
		 * @access public
		 * @param array $mainPageElements array with the main page elements
		 */
		public function addMainPageElements($mainPageElements)
		{
			$this->_mainPageElements = array_merge($this->_mainPageElements, $mainPageElements);
		}
		
		/**
		 * Return the state
		 * 
		 * @return array
		 */
		public function getState()
		{
			return $this->_state;
		}
		
		/**
		 * Set the state
		 * 
		 * @param array $state
		 */
		public function setState($state)
		{
			$this->_state = $state;
		}
		
		/**
		 * Updates the current state.
		 * The new state is retrieved from the table features
		 */
		public function updateState()
		{
			$state = array();
			foreach ($this->_tableFeatures as $tableFeature) {
				$state = array_merge($state, $tableFeature->getStateParams());
			}
			$this->_state = $state;
		}
		
		/**
		 * Build webform Html using the template file
		 * 
		 * @access protected
		 * @param string $templateFilename name of the template file
		 */
		public function parse($templateFilename)
		{
			$rows = $this->_recordset->getRecords();
			
			$section = new miTemplateParserSectionInfo();
			if (count($rows) > 0) {
				$this->addUiElements($rows);
				$section->setSectionInfo('rows', count($rows), miUIUtil::templetizeRows(miUIUtil::transposeRows($rows)));
			} else {
				$section->setSectionInfo('norows', 1);
			}

			$t = new miTemplateParser();
			$t->setSectionInfos(array($section));
			$t->readTemplate($templateFilename);
			
			$this->assignFeatureValues($t);
			$t->assignArray($this->getMessageObj()->getMessageTemplateVars());
			self::escapeAndAssignArray($t, $this->_mainPageElements);
			$this->addMainUiElements($t);
			
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
		 * Get all table feature values
		 * 
		 * @access public
		 * @return array
		 */
		public function getFeatureValues()
		{
			$featureValues = array();
			foreach ($this->_tableFeatures as $tableFeature) {
				$featureValues = array_merge($featureValues, $tableFeature->getValues());
			}
			$values = array();
			foreach ($featureValues as $key => $value) {
				$values['%%' . $key . '%%'] = $value;
			}
			
			$values['%%ALL_PARAMS%%'] = miAppFactory::singleton()->getStateObj()->getStateUrlParams();
			return $values;
		}
		
		/**
		 * Assign all table feature values to the template
		 * 
		 * @access public
		 * @param $t miTemplateParser
		 */
		public function assignFeatureValues(&$t)
		{
			$values = $this->getFeatureValues();
			self::escapeAndAssignArray($t, $values);
		}
		
		/**
		 * Shows the table using the specified template
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $recordset = new miSqlRecordset('tableName');
		 * $table = new miTable($recordset);
		 * $table->showPage($mainTemplate)
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $mainTemplate name of the main template file
		 */
		public function showPage($mainTemplate)
		{
			echo $this->parse($mainTemplate);
		}
		
		/**
		 * Escapes and assigns values
		 * 
		 * @param object
		 * @param array $values
		 * @access public
		 */
		public static function escapeAndAssignArray(&$t, $values)
		{
			$t->assignArray(self::escapeArray($values));
		}
		
		
		/**
		 * Escapes the values in the array
		 * 
		 * @param array $values array withe values to be escaped
		 * @return array the escaped array
		 */
		static public function escapeArray($values)
		{
			foreach ($values as $key => $value) {
				if (strncmp($key, '%%HTML_', 7)) {
					$values[$key] = miI18N::htmlEscape($value);
				}
			}
			return $values;
		}
		
		
		/**
		 * Assigns a value to a specify main page element
		 * 
		 * @access public
		 * @param string $name name of the main page element
		 * @param mixed $value value of the main page element
		 */
		public function assign($name, $value)
		{
			$this->_mainPageElements[$name] = $value;
		}
		
		/**
		 * In this function subclasses will add table specific processing
		 * 
		 * @access protected
		 * @param array $rows readed rows from the database
		 */
		protected function addUiElements(&$rows)
		{
			/* in this function subclasses will add table specific processing */
		}
		
		
		/**
		 * In this function subclasses will add specific elements to the main
		 * template page
		 * 
		 * @access protected
		 * @param object $t of class miTemplateParser() or its subclasses
		 */
		protected function addMainUiElements(&$t)
		{
			/*
			 * in this function subclasses will add specific elements to the main
			 * template page
			 */
		}
	}
?>