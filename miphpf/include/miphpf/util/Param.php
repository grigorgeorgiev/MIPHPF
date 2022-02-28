<?php
	/**
	 * Parameter accessing helper functions
	 *
	 * @copyright Copyright (c) 2004-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Retrieves a param. First tryies from $_POST, and then $_GET. If the param is not found
	 * prints an error and terminates the application  
	 *
	 * @param string $param The param name
	 * @return string the value 
	 * @throws miException
	 */
	function miGetParam($param)
	{
		if (array_key_exists($param, $_POST))
			return $_POST[$param];
		if (array_key_exists($param, $_GET))
			return $_GET[$param];
			
		throw new miException(sprintf(miI18N::getSystemMessage('MI_EXPECTED_PARAM_ERROR_MSG'), $param));
	}
	
	
	/**
	 * Retrieves a param or returns a default value.
	 * First tryies from $_POST, and then $_GET. If the param is not found returns $defult
	 *
	 * @param string $param The param name
	 * @param string $default the default value
	 * @return string the value 
	 */
	function miGetParamDefault($param, $default)
	{
		if (array_key_exists($param, $_POST))
			return $_POST[$param];
		if (array_key_exists($param, $_GET))
			return $_GET[$param];
		
		return $default;
	}
	
	/**
	 * Returns true if the param is set
	 *
	 * @param string $param
	 * @return bool true if the param is set
	 */
	function miHasParam($param)
	{
		return (array_key_exists($param, $_POST)) or (array_key_exists($param, $_GET));
	}
	
	/**
	 * Sets the param value. This value can be then obtained from miGetParam
	 * It clears the $_GET array value, and sets the value into the $_POST and $_REQUEST arrays
	 * 
	 * @access public
	 * @param string $param The param name
	 * @param any $value the param value
	 */ 
	function miSetParam($param, $value)
	{
		unset($_GET[$param]);
		$_POST[$param] = $value;
		$_REQUEST[$param] = $value; 
	}
?>