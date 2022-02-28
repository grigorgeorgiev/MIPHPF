<?php
	/**
	 * The miMessage class
	 * 
	 * @copyright Copyright (c) 2006-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Handles the displaying of error/warning/info messages
	 * 
	 * @copyright Copyright (c) 2006-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miMessage {
		
		const TMPL_VAR_MSG = '%%MSG%%';
		const TMPL_VAR_MSG_TYPE_STYLE = '%%MSG_TYPE_STYLE%%';
		
		const PARAM_MESSAGE = 'msg';
		const PARAM_MESSAGE_TYPE = 'msgType';
		
		const MSG_TYPE_HIDDEN = 0;
		const MSG_TYPE_ERROR = 1;
		const MSG_TYPE_WARNING = 2;
		const MSG_TYPE_INFO = 3;
		
		static protected $_messageTypeStyles = array(
			self::MSG_TYPE_HIDDEN => 'hidden',
			self::MSG_TYPE_ERROR => 'error',
			self::MSG_TYPE_WARNING => 'warning',
			self::MSG_TYPE_INFO => 'info',
		);
		
		/**
		 * Get the template variable values
		 * 
		 * @return array
		 */
		public function getMessageTemplateVars()
		{
			$msg = miGetParamDefault(self::PARAM_MESSAGE, '');
			$messageType = miGetParamDefault(self::PARAM_MESSAGE_TYPE, self::MSG_TYPE_ERROR);
			if (empty($msg))
				$messageType = self::MSG_TYPE_HIDDEN;
			
			$messageTypeStyle = isset(self::$_messageTypeStyles[$messageType]) ?
				self::$_messageTypeStyles[$messageType] :
				self::$_messageTypeStyles[self::MSG_TYPE_ERROR];
			
			return array(
				self::TMPL_VAR_MSG => miI18N::htmlEscape($msg),
				self::TMPL_VAR_MSG_TYPE_STYLE => $messageTypeStyle
			);
		}
	}
?>