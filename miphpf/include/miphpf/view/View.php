<?php
	/**
	 * Presentation layer miView and miDefaultView
	 *
	 * @copyright Copyright (c) 2003-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Base class for the presentation tier
	 * 
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miView {
		/**
		 * The controller command objects
		 */
		protected $_controllerCommands = array();
		
		/**
		 * Reference to the controller object
		 */
		protected $_controller;
		
		public function setController($controller)
		{
			$this->_controller = $controller;
		}
		
		public function getController()
		{
			return $this->_controller;
		}
		
		/**
		 * Retrieves the controller commands
		 * 
		 * @access public
		 * @return array the controller command objects
		 */
		public function getControllerCommands()
		{
			return $this->_controllerCommands;
		}
		
		/**
		 * Add new controller command
		 * 
		 * @access public
		 * @param miControllerCommand $controllerCommand
		 */
		public function addControllerCommand(miControllerCommand $controllerCommand)
		{
			$this->_controllerCommands[] = $controllerCommand;
		}
		
		public function preProcess($actionObj)
		{
		}
		
		public function postProcess($actionObj, $actionResult)
		{
		}
	}
	
	/**
	 * Handles the presentation tier
	 *
	 * @copyright Copyright (c) 2003-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miDefaultView extends miView {
		const TEMPLATE_ID_CREATE = 'create';
		const TEMPLATE_ID_EDIT = 'edit';
		const TEMPLATE_ID_VIEW = 'view';
		const TEMPLATE_ID_LIST = 'list';
		
		
		/**
		 * An array of template names needed to visualize all viewes
		 *
		 * @access protected
		 * @var array
		 */
		protected $_templates = array();
		
		/**
		 * Object of class miSQLRecord or its subclasses
		 *
		 * @access protected
		 * @var SQLRecord
		 */
		protected $_record;
		
		/**
		 * Reference to the view mapper controller object
		 */
		protected $_viewMapperController = null;
		
		/**
		 * Reference to the domain object
		 */
		protected $_domainObject = null;
		
		/**
		 * Object of class miTable or its subclasses
		 *
		 * @access protected
		 * @var miTable
		 */
		protected $_table;

		/**
		 * Array that describes the output and input of each database field
		 *
		 * @access protected
		 * @var array
		 */
		protected $_dataFields = array();

		/**
		 * Array that describes the output and input of each field of type FILE
		 *
		 * @access protected
		 * @var array
		 */
		protected $_submitFields = array();
		
		/**
		 * Stores page specific elements
		 * 
		 * @access protected
		 */
		protected $_mainPageElements = array();
		
		/**
		 * The default filter values
		 * 
		 * @access protected
		 */
		protected $_defaultFilterValues = array();
		
		/**
		 * @access protected
		 */
		protected $_recordset;
		
		/**
		 * Plugin objects
		 */
		protected $_plugins = array();
		
		protected $_persistVars = array('sortBy', 'sortDir', 'page', 'recordsPerPage');
		
		/**
		 * Constructor
		 */
		function __construct($recordset)
		{
			$this->_recordset = &$recordset;
			
			$stateObj = miAppFactory::singleton()->getStateObj();
			$stateObj->setDefaultCategory(get_class($this));
			
			// Setup the persist vars: persist the filters 
			foreach ($_REQUEST as $name => $value) {
				if (!substr_compare($name, 'Filter', -6) or !substr_compare($name, 'Condition', -9))
					$this->_persistVars[] = $name;
			}
			$stateObj->setPersistVars($this->_persistVars);
			
			$stateObj->restoreState();
		}
		
		
		/**
		 * Creates the table pager object. Singleton pattern
		 * 
		 * @access public
		 * @return object
		 */
		public function &getTablePagerObj()
		{
			static $pagerObj = null;
			if ($pagerObj == null)
				$pagerObj = new miDefaultTablePager($this->_table);
			return $pagerObj;
		}
		
		/**
		 * Creates the table filter object. Singleton pattern
		 * 
		 * @access public
		 * @return object
		 */
		public function &getTableFilterObj()
		{
			static $filterObj = null;
			if ($filterObj == null) {
				$filterObj = new miTableFilters($this->_table);
				
				$defaultFilterValues = $this->_defaultFilterValues;
				// By default all fields that do not have explicit default
				// filter value will have empty default filter value
				foreach ($this->_dataFields as $dataField) {
					if (!isset($defaultFilterValues[$dataField['data']]))
						$defaultFilterValues[$dataField['data']] = '';
				}
				$filterObj->setDefaultFilterValues($defaultFilterValues);
			}
			return $filterObj;
		}
		
		/**
		 * Adds additional table features
		 * 
		 * @access protected
		 * @param miTable $table
		 */
		protected function addTableFeatures($table)
		{
			new miTableSorter($table);
			// Init the table pager and table filter
			$this->getTablePagerObj();
			$this->getTableFilterObj();
		}
		
		/**
		 * Return Object of class miSqlRecord or its subclasses
		 * which was set by setRecord method
		 *
		 * @access public
		 * @return object Object of class miSqlRecord or its subclasses 
		 */
		public function &getRecord()
		{
			return $this->_record;
		}
		
		
		/**
		 * Sets object of class miSqlRecord or its subclasses.
		 * 
		 * miSqlRecord object is needed to handle and manage the inserting, 
		 * updating, deleting and reading of data into the database table specified
		 * by the "tableName" in miSqlRecord constructor. The database table primary
		 * key is also needed in miSqlRecord constructor.
		 * Example:
		 * <code>
		 * <?php
		 * $view = new miView;
		 * $view->setRecord(new miSqlRecord('tableName', 'tablePrimaryKey'));
		 * ?>
		 * </code>
		 * @access public
		 * @param object $u Object of class miSqlRecord or its subclasses 
		 */
		public function setRecord($u)
		{
			$this->_record = $u;
		}
		
		public function getDomainObject()
		{
			if ($this->_domainObject == null)
				$this->_domainObject = $this->createDomainObject();
			return $this->_domainObject;
		}
		
		protected function createDomainObject()
		{
			return new miDefaultDomainObject($this->_record);
		}
		
		public function getViewMapperController()
		{
			if ($this->_viewMapperController == null) {
				$this->_viewMapperController = $this->createViewMapperController();
				$this->initViewMapperController($this->_viewMapperController);
			}
			return $this->_viewMapperController;
		}
		
		protected function createViewMapperController()
		{
			$pkValue = miGetParamDefault($this->_record->getPrimaryKeyColumn(), 0);
			return new miViewMapperControllerDefault($this, $this->getDomainObject(), $pkValue);
		}
		
		/**
		 * Returns the recordset
		 * 
		 * @access public
		 */
		public function getRecordset()
		{
			return $this->_recordset;
		}
		
		/**
		 * Return array that describes the output and input of each database field
		 *
		 * @access public
		 * @return array that describes the output and input of each database field 
		 */
		public function getDataFields()
		{
			return $this->_dataFields;
		}
		
		
		/**
		 * Sets array that describes the output and input of each database field
		 *
		 * @access public
		 * @param array $u Array that describes the output and input of each database field
		 */
		public function setDataFields($u)
		{
			$this->_dataFields = $u;
		}
	   

		/**
		 * Return object of class miTable or its subclasses
		 * which was set by setTable method
		 *
		 * @access public
		 * @return object Object of class miTable or its subclasses 
		 */
		public function getTable()
		{
			return $this->_table;
		}
		
		
		/**
		 * Sets Object of class miTable or its subclasses.
		 *
		 * miTable object is needed to manage the representation in table format of data, 
		 * which is readed from miSqlRecordset object set in miTable constructor.
		 * The data is readed from table specified by the "tableName" in the miSqlRecordset 
		 * constructor.
		 * Example:
		 * <code>
		 * <?php
		 * $view = new miView;
		 * $recordset = new miSqlRecordset('tableName'); 
		 * $view->setTable(new miTable($recordset));
		 * ?>
		 * </code>
		 *
		 * @access public
		 * @param object $u Object of class miTable or its subclasses 
		 */
		public function setTable($u)
		{
			$this->_table = $u;
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
		 * Adds main page elements
		 *
		 * @access public
		 * @param array $mainPageElements array with the main page elements
		 */
		public function addMainPageElements($mainPageElements)
		{
			$this->_mainPageElements = array_merge($this->_mainPageElements, $mainPageElements);
		}
		
		/**
		 * Returns the main page elements
		 * 
		 * @access public
		 * @return array
		 */
		public function getMainPageElements()
		{
			return $this->_mainPageElements;
		}
		
		/**
		 * Registers new plugin
		 * 
		 * @access public
		 * @param miViewPlugin $plugin the plugin to register
		 */
		public function registerPlugin(miViewPlugin $plugin)
		{
			$this->_plugins[] = $plugin;
		}
		
		/**
		 * Adds redirect to list controller command
		 * Used after dmExecCreate, dmExecEdit and dmExecDelete to prevent refreshing
		 * the page to cause repetition of the operation
		 * 
		 * @access public
		 * @param string $msg message to display on the list page
		 * @param int $msgType the type of the message (optional)
		 */
		public function addRedirectToListControllerCommand($msg, $msgType = miMessage::MSG_TYPE_ERROR)
		{
			$loc = $_SERVER['PHP_SELF'] . '?' . $this->getRedirectParams($msg, $msgType);
			$cmd = new miControllerCommand(miControllerCommand::CONTROLLER_COMMAND_REDIRECT, $loc);
			$this->addControllerCommand($cmd);
		}
		
		/**
		 * Returns the redirect params
		 *
		 * @access protected 
		 * @param string $msg message to display on the list page
		 * @param int $msgType the type of the message (optional)
		 * @return string the redirect params url string
		 */
		public function getRedirectParams($msg, $msgType = miMessage::MSG_TYPE_ERROR)
		{
			$params = miMessage::PARAM_MESSAGE . '=' . urlencode($msg);
			if ($msgType != miMessage::MSG_TYPE_ERROR)
				$params .= '&' . miMessage::PARAM_MESSAGE_TYPE . '=' . urlencode($msgType);
			
			$stateParams = miAppFactory::singleton()->getStateObj()->getStateUrlParams();
			if ($stateParams != '')
				$params .= '&' . $stateParams;
			
			return $params;
		}
		
		/**
		 * Calls each of the plugins passing the $actionObj and the $actionStep
		 * 
		 * @access public
		 * @param miAction $actionObj action object subclassed from miAction
		 * @param string $actionStep the particular action step
		 */
		public function callPlugin($actionObj, $actionStep)
		{
			foreach ($this->_plugins as $plugin)
				$plugin->processActionStep($actionObj, $actionStep);
		}
		
		/**
		 * Called before the action is executed
		 * Subclasses can add functionlity to be executed before the action
		 * 
		 * @access protected
		 * @param miAction $actionObj action object, extended from miAction
		 */
		public function preProcess($actionObj)
		{
			foreach ($this->_plugins as $plugin)
				$plugin->preProcessAction($actionObj);
		}
		
		/**
		 * Called after the action is executed
		 * Subclasses can add functionlity to be executed after the action
		 * 
		 * @access protected
		 * @param miAction $actionObj action object, extended from miAction
		 * @param boolean $actionResult the result of the action command
		 */
		public function postProcess($actionObj, $actionResult)
		{
			foreach ($this->_plugins as $plugin)
				$plugin->postProcessAction($actionObj, $actionResult);
		}
		
		/**
		 * Subclasses can init the table
		 * 
		 * @param miTable $table reference to the table object
		 */
		public function initTable($table)
		{
			$this->addTableFeatures($this->_table);
		}
		
		/**
		 * Inits the web form
		 * 
		 * @access public
		 * @param miWebForm $form
		 */
		public function initWebForm(&$form)
		{
			$form->initWidgets($this->_dataFields);
		}
		
		/**
		 * Inits the view mapper controller
		 */
		public function initViewMapperController($viewMapperController)
		{
		}
		
		/**
		 * Returns the template file for the $templateId
		 * 
		 * @access public
		 * @param string $templateId the id of the template
		 * @return string the template file name relative to the document root
		 * @throws miConfigurationException
		 */
		public function getTemplateFileName($templateId)
		{
			if (isset($this->_templates[$templateId]))
				return $this->_templates[$templateId];
			
			// Find the prefix
			$prefix = $_SERVER['SCRIPT_NAME'];
			if (substr_compare($prefix, '_mgmt.php', strlen($prefix)-9))
				throw new miConfigurationException('The template filename needs to be defined for template: ' . $templateId, miConfigurationException::EXCEPTION_UNDEFINED_TEMPLATE_FILENAME);
			
			$prefix = substr($prefix, 1, -9);
			switch ($templateId) {
				case self::TEMPLATE_ID_LIST:
					return $prefix . '_mgmt.tmpl';
				case self::TEMPLATE_ID_VIEW:
					return $prefix . '_view.tmpl';
				case self::TEMPLATE_ID_CREATE:
					return $prefix . '_create.tmpl';
				case self::TEMPLATE_ID_EDIT:
					return $prefix . '_edit.tmpl';
			}
			throw new miConfigurationException('The template filename needs to be defined for template: ' . $templateId, miConfigurationException::EXCEPTION_UNDEFINED_TEMPLATE_FILENAME);
		}
		
		/**
		 * Returns message based on code
		 * 
		 * @param string $msgCode
		 * @return string the message
		 */
		public function getMessage($msgCode)
		{
			return miI18N::getSystemMessage($msgCode);
		}
	}
?>