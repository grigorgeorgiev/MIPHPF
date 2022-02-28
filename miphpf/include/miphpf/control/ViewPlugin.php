<?php
	/**
	 * This file contains the base class for the view plugins
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Base class for view plugins
	 * The view plugins can add miscellaneous functionality into the view
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miViewPlugin {
		/**
		 * Reference to the view
		 */
		protected $_view;
		
		/**
		 * Constructs the plugin
		 * 
		 * @param miView $view reference to the view
		 */
		public function __construct(miView $view)
		{
			$this->_view = $view;
			$view->registerPlugin($this);
		}
		
		/**
		 * Called before an action
		 * 
		 * @param miAction $actionObj
		 */
		public function preProcessAction(miAction $actionObj)
		{
		}
		
		/**
		 * Called after an action
		 * 
		 * @param miAction $actionObj
		 * @param boolean $actionResult the result returned from the action
		 */
		public function postProcessAction(miAction $actionObj, $actionResult)
		{
		}
		
		/**
		 * Called by the action, indicating particluar step
		 * 
		 * @param miAction $actionObj
		 * @param string $actionStep the name of the current step in the action
		 */
		public function processActionStep(miAction $actionObj, $actionStep)
		{
		}
	}
?>