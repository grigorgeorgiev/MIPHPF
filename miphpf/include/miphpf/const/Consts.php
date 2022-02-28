<?php
	/**
	 * Constants define file
	 *
	 * @copyright Copyright (c) 2004-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 */
	require_once(dirname(__FILE__) . '/../i18n/' . miSettings::singleton()->get('MI_DEFAULT_LANGUAGE') . '.php');
	
	# Period selection and viewing consts
	define('MI_DISPLAY_DATE_FORMAT', 'D j M, Y');
	define('MI_DISPLAY_TIME_FORMAT', 'j M Y G:i:s');

	# Database engine
	define('MI_DATABASE_ENGINE', 'MySQL');
	
	# System version
	define('MIPHPF_VERSION', '2.0');
?>