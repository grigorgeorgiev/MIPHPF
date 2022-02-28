<?php
	/**
	 * ControllerCommand Class
	 *
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * A controller command class
	 * Used to implement command design pattern
	 * 
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miControllerCommand {
		const CONTROLLER_COMMAND_HTML = 1;
		const CONTROLLER_COMMAND_REDIRECT = 2;
		
		protected $_commandType;
		protected $_param;
		
		public function __construct($commandType, $param)
		{
			$this->_commandType = $commandType;
			$this->_param = $param;
		}
		
		public function getCommandType()
		{
			return $this->_commandType;
		}
		
		public function getParam()
		{
			return $this->_param;
		}
	}
?>