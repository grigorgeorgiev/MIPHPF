<?php
	/**
	 * Dispatcher Class
	 *
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Handles and dispatches the actions
	 *
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miDispatcher {
		/**
		 * Reference to the view object
		 * 
		 * @var miView
		 */
		protected $_view;
		
		/**
		 * String parameter from the request specifing which action to be processed
		 *
		 * @var string
		 */
		protected $_action;
		
		/**
		 * The action object
		 *  
		 * @var object
		 */
		protected $_actionObj = null;
		
		/**
		 * Array map between action and action processing class names
		 * 
		 * @var array
		 */
		protected $_actionHandlers = array();
		
		/**
		 * Initializes the dispatcher
		 * 
		 * @param miView $view
		 */
		public function __construct(miView $view)
		{
			$this->_view = $view;
		}
		
		/**
		 * Adds a class that handles a specific action
		 * The action processing class must inherit from miAction
		 * 
		 * @param string $action the action name
		 * @param string $actionHandlerClass the name of the action processing class
		 */
		public function setActionHandler($action, $actionHandlerClass)
		{
			$this->_actionHandlers[$action] = $actionHandlerClass;
		}
		
		/**
		 * Clears all action handlers
		 */
		public function clearActionHandlers()
		{
			$this->_actionHandlers = array();
		}
		
		/**
		 * Returns the action string
		 * 
		 * @return string the action string
		 */
		public function getAction()
		{
			return $this->_action;
		}
		
		/**
		 * Returns the action object
		 * The action object is initialized in process()
		 * 
		 * @return miAction the action object
		 */
		public function getActionObj()
		{
			return $this->_actionObj;
		}
		
		/**
		 * Determines which action will be performed depending
		 * on the value of the "action" parameter from the request.
		 *
		 * @return boolean true if the action was recognized
		 */
		public function process()
		{
			$this->_action = miGetParamDefault('action', '');
			
			$actionObj = $this->createActionObj($this->_action);
			if ($actionObj === false)
				return false;
			
			$this->_actionObj = $actionObj;
			
			$this->_view->preProcess($actionObj);
			$actionResult = $actionObj->doAction();
			$this->_view->postProcess($actionObj, $actionResult);
			return true;
		}
		
		/**
		 * Creates new action object, subclassed from miAction. The object depends on $action
		 * If the action is unknown returns false
		 * 
		 * @param string $action
		 * @return miAction|false
		 */
		protected function createActionObj($action)
		{
			if (empty($this->_actionHandlers[$action]))
				return false;
			
			return new $this->_actionHandlers[$action]($this->_view);
		}
	}
	
	/**
	 * The default dispatcher class
	 * It has predefined actions for list, view, edit, create and delete
	 */
	class miDefaultDispatcher extends miDispatcher {
		/**
		 * Array map between action and action processing class
		 * 
		 * @access protected
		 */
		protected $_actionHandlers = array(
				'' => 'miListAction',
				'dmView' => 'miViewAction',
				'dmEdit' => 'miEditAction',
				'dmExecEdit' => 'miExecEditAction',
				'dmExecDelete' => 'miExecDeleteAction',
				'dmCreate' => 'miCreateAction',
				'dmExecCreate' => 'miExecCreateAction'
			);
	}
?>