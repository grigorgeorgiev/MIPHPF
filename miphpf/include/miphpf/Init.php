<?php
	/**
	 * Initializes the MIPHPF framework
	 * 
	 * Should be included in all files using the framework
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	 
	/**
	 * Include the config
	 */
	require_once(dirname(__FILE__) . '/util/Settings.php');
	require_once(dirname(__FILE__) . '/Config.php');
	require_once(dirname(__FILE__) . '/util/Loader.php');
	require_once(dirname(__FILE__) . '/const/Consts.php');
	require_once(dirname(__FILE__) . '/util/Param.php');
	
	/**
	 * Test the magic quotes settings
	 */
	if (get_magic_quotes_gpc() or get_magic_quotes_runtime())
		die('Please make sure that magic_quotes_gpc and magic_quotes_runtime are off!');
	
	/**
	 * Autoloads classes upon usage
	 * 
	 * @param string $className the class to load
	 */
	function __autoload($className)
	{
		if (miLoader::load($className) === false) {
			$backTrace = debug_backtrace();
			die('__autoload: Autoloading failed. Unknown class "' . $className .
				'" at ' . $backTrace[0]['file'] . ':' . $backTrace[0]['line']);
		}
	}
?>