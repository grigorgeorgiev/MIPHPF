<?php
	/**
	 * The submit fields plugin
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Handles submit fields
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSubmitFieldsPlugin extends miViewPlugin {
		/**
		 * Array with the submit fields this plugin works on
		 * The array has the following format
		 * $submitFields = array(
		 *     array('fieldName' => '', 'namePrefixSave' => '', 'namePrefixWeb' => '', 'nameSuffix' => ''),
		 *     array('fieldName' => '', 'namePrefixSave' => '', 'namePrefixWeb' => '', 'nameSuffix' => ''),
		 *     ...
		 * );
		 * 
		 * @access protected
		 */
		protected $_submitFields;
		
		/**
		 * Constructs the submit fields hanlder
		 * 
		 * @access public
		 * @param miView $view
		 * @param array $submitFields the submit fields
		 */
		public function __construct($view, $submitFields)
		{
			$this->_submitFields = $submitFields;
			parent::__construct($view);
		}
		
		/**
		 * Assign the submit fields template variables as page elements
		 *
		 * @access protected
		 */
		protected function assignSubmitFields($id)
		{
			$templateVars = array();
			foreach ($this->_submitFields as $field) {
				if (file_exists($field['namePrefixSave'] . $id . $field['nameSuffix'])) {
					$templateVars['%%' . strtoupper($field['fieldName']) . '%%'] =  $field['namePrefixWeb'] . $id . $field['nameSuffix'];
					$templateVars['%%' . strtoupper($field['fieldName']) . '_MISSING%%'] = '';
				} else {
					$templateVars['%%' . strtoupper($field['fieldName']) . '%%'] =  '';
					$templateVars['%%' . strtoupper($field['fieldName']) . '_MISSING%%'] = 'style="display: none"';
				}
			}
			$this->_view->addMainPageElements($templateVars);
		}
		
		/**
		 * Processes the submit fields
		 *
		 * @access protected
		 * @param int $id the id of the currently created/edited row
		 * @throws miException
		 */
		protected function processSubmitFields($id)
		{
			foreach ($this->_submitFields as $field) {
				if (empty($_FILES[$field['fieldName']]['name'])) 
					continue;
				
				$dest = $field['namePrefixSave'] . $id . $field['nameSuffix'];
				if (move_uploaded_file($_FILES[$field['fieldName']]['tmp_name'], $dest) === false)
					throw new miException('Cannot upload file. Saving into "' . $dest . '" failed.');
			}
		}
		
		
		/**
		 * Delete all attached files
		 *
		 * @param int $id the id of the deleted row
		 * @access protected
		 */
		protected function deleteSubmitFields($id)
		{
			foreach ($this->_submitFields as $field) {
				$name = $field['namePrefixSave'] . $id . $field['nameSuffix'];
				@unlink($name); // Ignore errors
			}
		}
		
		/**
		 * If the action is one of View, Create or Edit assign the submit fields display vars
		 * 
		 * @access public
		 * @param miAction $actionObj action object, subclassed from miAction
		 * @param string $actionStep the action step
		 */
		public function processActionStep(miAction $actionObj, $actionStep)
		{
			if (($actionObj instanceof miViewAction) or
				($actionObj instanceof miCreateAction) or
				($actionObj instanceof miEditAction))
			{
				if ($actionStep == 'preShowForm' && !($actionObj instanceof miCreateAction)) {
					$this->assignSubmitFields($this->_view->getRecord()->getPK());
				}
				if (($actionStep == 'postCreate') or ($actionStep == 'postUpdate')) {
					$this->processSubmitFields($this->_view->getRecord()->getPK());
				}
			}
			
			if ($actionObj instanceof miExecDeleteAction) {
				if ($actionStep == 'postDelete') {
					$this->deleteSubmitFields($this->_view->getRecord()->getPK());
				}
			}
		}
	}
?>