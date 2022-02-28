<?php
	/**
	 * I18N define file
	 *
	 * @copyright Copyright (c) 2004-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Internationalization class
	 * @copyright Copyright (c) 2004-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miI18N {
		static protected $_fromArray = array('&', '"', '<', '>');
		static protected $_toArray = array('&amp;', '&quot;', '&lt;', '&gt;');
		
		static protected $_systemMessages = array(
			'MI_RECORD_CREATED_SUCCESSFULLY_MSG' => 'Record [ %u ] created successfully',
			'MI_RECORD_UPDATED_SUCCESSFULLY_MSG' => 'Record [ %u ] updated successfully',
			'MI_RECORD_DELETED_SUCCESSFULLY_MSG' => 'Record [ %u ] deleted successfully',
			'MI_RECORD_LIST_FAILED_MSG' => 'Error appeared. Listing records failed: ',
			'MI_RECORD_CREATE_FAILED_MSG' => 'Error appeared. Record creation failed: ',
			'MI_RECORD_EDIT_FAILED_MSG' => 'Error appeared. Edit operation failed: ',
			'MI_RECORD_UPDATE_FAILED_MSG' => 'Error appeared. Record update failed: ',
			'MI_RECORD_DELETE_FAILED_MSG' => 'Error appeared. Record delete failed: ',
			'MI_EXPECTED_PARAM_ERROR_MSG' => 'Internal error! Expected param %s! Please contact the administrator.',
			'MI_RECORDSETPAGER_POSITION_MSG' => 'You are viewing records %u to %u of %u',
			'MI_RECORDSETPAGER_POSITION_NO_RECORDS_MSG' => 'There are no records.',
			'MI_RECORDSETPAGER_NAVIGATIONS_MSG' => 'First | Previous | Next | Last',
			'MI_RECORDS_PER_PAGE_MUST_BE_AT_LEAST_ONE' => 'Records per page must be at least 1.'
		);
		
		static public function getSystemMessage($msgCode)
		{
			return miI18N::$_systemMessages[$msgCode];
		}
		
		/**
		 * Static HTML escape function
		 * 
		 * @access public
		 * @param string|array $values a string, or array of strings to be escaped
		 * @return string|array the escaped string, or array of strings
		 */
		static public function htmlEscape($values)
		{
			return str_replace(self::$_fromArray, self::$_toArray, $values);
		}
	}
?>