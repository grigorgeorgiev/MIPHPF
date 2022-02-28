<?php
	class miAppFactory {
		protected static $_appFactory;
		
		protected $_state;
		
		/**
		 * Return reference to the app factory object
		 *
		 * @return miAppFactory
		 */
		public static function singleton()
		{
			if (!miAppFactory::$_appFactory)
				miAppFactory::$_appFactory = new miAppFactory;
			return miAppFactory::$_appFactory;
		}
		
		/**
		 * Set new app factory object
		 * This method is used to change the default miAppFactory
		 *
		 * @param miAppFactory $appFactory
		 */
		public static function setAppFactory(miAppFactory $appFactory)
		{
			miAppFactory::$_appFactory = $appFactory;
		}
		
		/**
		 * Returns miState object
		 *
		 * @return miState
		 */
		public function getStateObj()
		{
			if (!$this->_state)
				$this->_state = new miState;
			return $this->_state;
		}
	}
?>