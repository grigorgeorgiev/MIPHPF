<?php
	/**
	* Authentication Functions
	*
	* Currently unused
	*
	* @copyright Copyright (c) 2003, 2004 Mirchev Ideas Ltd. All rights reserved.
	* @package MIPHPF
	*/
	
	/**
	*/
/*
	require_once('../include/miphpf/model/Users.php');
	require_once('../include/miphpf/const/Consts.php');

	# Session Name
	define('MI_SESSION_NAME', 'MIPHPF_SESSION');
	
	# Check if the user is authorized
	function isAuthorized($section = 0)
	{
		mi_session_name(MI_SESSION_NAME);
		session_start();
		if (!array_key_exists('UserID', $_SESSION))
			return false;

		$user = new ModelUser;
		$user->readUser($_SESSION['UserID']);
		if ($user->getPrivilege() >= $section)
			return true;
		
		return false;
	}
	
	function getSessionUserID()
	{
		return $_SESSION['UserID'];
	}
*/
?>