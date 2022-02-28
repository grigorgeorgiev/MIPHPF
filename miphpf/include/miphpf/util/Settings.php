<?php
	/**
	 * Contains the settings class
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Retrieves settings
	 * 
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSettings {
		protected $_settings = array();
		
		/**
		 * Returns the settings object
		 *
		 * @return object the settings object object
		 */
		public static function singleton()
		{
			static $settingObj = null;
			
			if ($settingObj == null)
				$settingObj = new miSettings;
			
			return $settingObj;
		}
		
		/**
		 * Returns the setting value for a setting with name $name
		 * 
		 * @return mixed the value
		 */
		public function get($name)
		{
			return $this->_settings[$name];
		}
		
		/**
		 * Sets the setting value for setting with name $name
		 * Overwrites previous setting
		 *
		 * @param string $name
		 * @param mixed $value
		 */
		public function set($name, $value)
		{
			$this->_settings[$name] = $value;
		}
		
		/**
		 * Sets multiple settings value
		 * Overwrites previous setting
		 *
		 * @param array $settings associative array with setting values
		 */
		public function setArray($settings)
		{
			$this->_settings = array_merge($this->_settings, $settings);
		}
	}
?>