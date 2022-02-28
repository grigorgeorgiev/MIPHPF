<?php
	/**
	 * Configuration file
	 *
	 * @copyright Copyright (c) 2004-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * The database settings
	 */
	miSettings::singleton()->setArray(array(
		'MI_DEFAULT_DB_HOST' => '10.200.200.50',
		'MI_DEFAULT_DB_USER' => 'mi_miphpf',
		'MI_DEFAULT_DB_PASS' => 'mi_miphpf',
		'MI_DEFAULT_DB_NAME' => 'mi_miphpf',
	));
	
	/**
	 * Misc settings
	 */
	miSettings::singleton()->setArray(array(
		'MI_RECORDS_PER_PAGE' => 10,	// How many items per page will be displayed by default
		'MI_DEFAULT_LANGUAGE' => 'en',	// Default language
		'MI_TEMPLATE_BASEDIR' => dirname(__FILE__) . '/../../',
	));
?>