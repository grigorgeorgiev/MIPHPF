<?php
	/**
	 * The page class
	 * @copyright Copyright (c) 2006-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Handles page control and display
	 * There could be multiple views on one page
	 * 
	 * @copyright Copyright (c) 2006-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miPage {
		/**
		 * @access protected
		 */
		protected $_views = array();
		
		/**
		 * @access protected
		 */
		protected $_headerFileName;
		
		/**
		 * @access protected
		 */
		protected $_footerFileName;
		
		/**
		 * The dispatcher object
		 *
		 * @var miDispatcher
		 */
		protected $_dispatcher;
		
		/**
		 * Initializes the page object
		 * 
		 * @access public
		 * @param miView|array $views view or array of views
		 */
		public function __construct($views)
		{
			if (is_array($views))
				$this->_views = $views;
			else
				$this->_views = array($views);
		}
		
		/**
		 * Sets the header file
		 * 
		 * @param string $headerFileName
		 */
		public function setHeader($headerFileName)
		{
			$this->_headerFileName = $headerFileName;
		}
		
		/**
		 * Sets the footer file
		 * 
		 * @param string $footerFileName
		 */
		public function setFooter($footerFileName)
		{
			$this->_footerFileName = $footerFileName;
		}
		
		/**
		 * Process the controller commands
		 * 
		 * @param array $controllerCommands array of controller command objects
		 */
		public function processControllerCommands($controllerCommands)
		{
			foreach ($controllerCommands as $controllerCommand) {
				switch ($controllerCommand->getCommandType()) {
					case miControllerCommand::CONTROLLER_COMMAND_HTML:
						echo $controllerCommand->getParam();
						break;
					
					case miControllerCommand::CONTROLLER_COMMAND_REDIRECT:
						header('Location: ' . $controllerCommand->getParam());
						break;
				}
			}
		}
		
		/**
		 * Shows the page
		 * 
		 * @access public
		 */
		public function showPage()
		{
			if ($this->_headerFileName)
				require_once($this->_headerFileName);
			
			foreach ($this->_views as $view) {
				$this->processControllerCommands($view->getControllerCommands());
			}
			
			if ($this->_footerFileName)
				require_once($this->_footerFileName);
		}
		
		/**
		 * Returns the dispatcher object
		 * Instantiates a miDefaultDispatcher object, if needed
		 *
		 * @return miDispatcher
		 */
		public function getDispatcher()
		{
			if (count($this->_views) != 1)
				throw new miException('miPage->getDispatcher() supports only one view.');
			
			if (!isset($this->_dispatcher))
				$this->_dispatcher = new miDefaultDispatcher($this->_views[0]);
			return $this->_dispatcher;
		}
		
		/**
		 * Dispatch an action and show a page with a one view
		 */
		public function dispatchAndShow()
		{
			foreach ($this->_views as $view)
				$view->setController($this);
			
			$this->getDispatcher()->process();
			$this->showPage();
		}
		
		/**
		 * Allows runtime changing of the dispatcher object
		 * 
		 * @param miDispatcher $dispatcher
		 * @return void
		 */
		public function setDispatcher(miDispatcher $dispatcher)
		{
			$this->_dispatcher = $dispatcher;
		}
	}
?>