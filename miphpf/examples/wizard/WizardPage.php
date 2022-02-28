<?php
	/**
	 * A wizard page base class
	 */
	class miWizardPage extends miDefaultView {
		const SESSION_WIZARD_PAGE_DATA = 'WizardPageData';
		const TEMPLATE_ID_WIZARD_PAGE = 'wizardPage';
		
		protected $_wizardController;
		
		protected $_actionHandlers = array(
			'' => 'miViewWizardPageAction',
			'miExecWizardPageAction' => 'miExecWizardPageAction',
		);
		
		
		/**
		 * Wizard page constructor
		 * 
		 * @param miWizardController $wizardController the wizard controller
		 * @param miSqlRecordset $recordset the recordset (optional)
		 */
		public function __construct(miWizardController $wizardController, miSqlRecordset $recordset = null)
		{
			parent::__construct($recordset);
			$this->_wizardController = $wizardController;
		}
		
		/**
		 * Returns the wizard controller
		 * 
		 * @return miWizardController the wizard controller
		 */
		public function getWizardController()
		{
			return $this->_wizardController;
		}
		
		/**
		 * Executes the user action for the wizard page
		 */
		public function process($action)
		{
			$this->_action = $action;
			
			$actionObj = $this->createActionObj($this->_action);
			if ($actionObj === false)
				return false;
			
			foreach ($this->_plugins as $plugin)
				$plugin->preProcessAction($actionObj);
			$this->preProcess($actionObj);
			
			$actionResult = $actionObj->doAction();
			
			foreach ($this->_plugins as $plugin)
				$plugin->postProcessAction($actionObj, $actionResult);
			$this->postProcess($actionObj, $actionResult);
			return true;
		}
		
		/**
		 * Saves tha page data
		 * If $page is not given the current class name is used
		 * 
		 * @param array $data
		 * @param string $page the page name (optional)
		 */
		public function savePageData($data, $page = false)
		{
			if ($page === false)
				$page = get_class($this);
			$_SESSION[self::SESSION_WIZARD_PAGE_DATA][$page] = $data;
		}
		
		/**
		 * Clears the page data
		 * If $page is not given the current class name is used
		 */
		public function clearPageData($page = false)
		{
			if ($page === false)
				$page = get_class($this);
			unset($_SESSION[self::SESSION_WIZARD_PAGE_DATA][$page]);
		}
		
		/**
		 * Returns the page data
		 * If $page is not given the current class name is used
		 * 
		 * @param string $page the page name (optional)
		 * @return array the page data
		 */
		public function getPageData($page = false)
		{
			if ($page === false)
				$page = get_class($this);
			if (isset($_SESSION[self::SESSION_WIZARD_PAGE_DATA][$page]))
				return $_SESSION[self::SESSION_WIZARD_PAGE_DATA][$page];
			return array();
		}
	}
?>