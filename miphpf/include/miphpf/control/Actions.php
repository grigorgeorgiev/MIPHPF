<?php
	/**
	 * Standard Action classes
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Actions base class
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miAction {
		protected $_view;
		
		/**
		 * Construct the action object
		 * 
		 * @param miView $view
		 */
		public function __construct(miView $view)
		{
			$this->_view = $view;
		}
		
		/**
		 * Performs the action
		 * 
		 * @access public
		 * @return boolean true on success, false if an error occured
		 */
		public function doAction()
		{
		}
	}
	
	/**
	 * Action class with support for miWebForm
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miActionWithWebForm extends miAction {
		protected $_webForm = null;
		
		/**
		 * Returns a web form object
		 * 
		 * @access public
		 * @return miWebForm
		 */
		public function getWebForm()
		{
			if ($this->_webForm == null) {
				$this->_webForm = new miWebForm(array());
				$this->_view->getViewMapperController()->setWebForm($this->_webForm);
				$this->_view->initWebForm($this->_webForm);
			}
			return $this->_webForm;
		}
		
		/**
		 * Show the web form
		 * 
		 * @access protected
		 * @param miWebForm $form the webform object
		 * @param string $templateName the template name
		 * @param boolean $isEditable if the form will contain the editable fields/widgets
		 */
		protected function showForm(&$form, $templateName, $isEditable)
		{
			$table = $this->_view->getTable();
			if ($table) {
				$params = $table->getFeatureValues();
				$this->_view->addMainPageElements(miTable::escapeArray($params));
			}
			
			$form->addMainPageElements($this->_view->getMainPageElements());
			
			$html = $form->parse($templateName, $isEditable);
			$cmd = new miControllerCommand(miControllerCommand::CONTROLLER_COMMAND_HTML, $html);
			$this->_view->addControllerCommand($cmd);
		}
	}
	
	/**
	 * The view action
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miViewAction extends miActionWithWebForm {
		public function doAction()
		{
			$vmController = $this->_view->getViewMapperController();
			$vmController->read();
			
			$this->_view->callPlugin($this, 'preShowForm');
			
			$form = $this->getWebForm();
			$vmController->setDataToWebForm();
			$this->showForm($form, $this->_view->getTemplateFileName(miDefaultView::TEMPLATE_ID_VIEW), false);
			return true;
		}
	}
	
	/**
	 * Show the page for creating a new record in the database
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miCreateAction extends miActionWithWebForm {
		public function doAction()
		{
			$this->_view->callPlugin($this, 'preShowForm');
			
			$form = $this->getWebForm();
			$this->showForm($form, $this->_view->getTemplateFileName(miDefaultView::TEMPLATE_ID_CREATE), true);
			return true;
		}
	}
	
	/**
	 * Takes the values from the user input and create a new record in the database
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miExecCreateAction extends miCreateAction {
		public function doAction()
		{
			try {
				$form = $this->getWebForm();
				$vmController = $this->_view->getViewMapperController();
				
				$isOk = $form->processSubmit();
				$vmController->setDataFromWebForm();
				
				if ($isOk === false) {
					$vmController->updateSubmittedForm($form);
					parent::doAction();	// Call miCreateAction
					return false;
				}
				
				$this->_view->callPlugin($this, 'preCreate');
				$recordId = $vmController->insert();
				$this->_view->callPlugin($this, 'postCreate');
				
			} catch (miException $exception) {
				$msg = $this->_view->getMessage('MI_RECORD_CREATE_FAILED_MSG');
				$this->_view->addRedirectToListControllerCommand($msg . $exception->getMessage(), miMessage::MSG_TYPE_ERROR);
				return false;
			}
			$msg = sprintf($this->_view->getMessage('MI_RECORD_CREATED_SUCCESSFULLY_MSG'), $recordId);
			$this->_view->addRedirectToListControllerCommand($msg, miMessage::MSG_TYPE_INFO);
			return true;
		}
	}
	
	/**
	 * Show the page for updating a record in the database
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miEditAction extends miActionWithWebForm {
		public function doAction()
		{
			try {
				$vmController = $this->_view->getViewMapperController();
				$vmController->read();
				
				$this->_view->callPlugin($this, 'preShowForm');
				
				$form = $this->getWebForm();
				$vmController->setDataToWebForm();
				$this->showForm($form, $this->_view->getTemplateFileName(miDefaultView::TEMPLATE_ID_EDIT), true);
				return true;
			} catch (miException $exception) {
				$msg = $this->_view->getMessage('MI_RECORD_EDIT_FAILED_MSG');
				$this->_view->addRedirectToListControllerCommand($msg . $exception->getMessage(), miMessage::MSG_TYPE_ERROR);
				return false;
			}
		}
	}
	
	/**
	 * Takes the values from the user input and update a record in the database
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miExecEditAction extends miEditAction {
		public function doAction()
		{
			try {
				$vmController = $this->_view->getViewMapperController();
				$vmController->read();
				
				$form = $this->getWebForm();
				$isOk = $form->processSubmit();
				$vmController->setDataFromWebForm();
				
				if ($isOk === false) {
					$vmController->updateSubmittedForm($form);
					$this->_view->callPlugin($this, 'preShowForm');
					$this->showForm($form, $this->_view->getTemplateFileName(miDefaultView::TEMPLATE_ID_EDIT), true);
					return false;
				}
				
				$this->_view->callPlugin($this, 'preUpdate');
				$recordId = $vmController->update();
				$this->_view->callPlugin($this, 'postUpdate');
				
			} catch (miException $exception) {
				$msg = $this->_view->getMessage('MI_RECORD_UPDATE_FAILED_MSG');
				$this->_view->addRedirectToListControllerCommand($msg . $exception->getMessage(), miMessage::MSG_TYPE_ERROR);
				return false;
			}
			$msg = sprintf($this->_view->getMessage('MI_RECORD_UPDATED_SUCCESSFULLY_MSG'), $recordId);
			$this->_view->addRedirectToListControllerCommand($msg, miMessage::MSG_TYPE_INFO);
			return true;
		}
	}
	
	/**
	 * Delete a record in the table
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miExecDeleteAction extends miAction {
		public function doAction()
		{
			try {
				$vmController = $this->_view->getViewMapperController();
				
				$this->_view->callPlugin($this, 'preDelete');
				$recordId = $vmController->delete();
				$this->_view->callPlugin($this, 'postDelete');
				
			} catch (miException $exception) {
				$msg = $this->_view->getMessage('MI_RECORD_DELETE_FAILED_MSG');
				$this->_view->addRedirectToListControllerCommand($msg . $exception->getMessage(), miMessage::MSG_TYPE_ERROR);
				return false;
			}
			$msg = sprintf($this->_view->getMessage('MI_RECORD_DELETED_SUCCESSFULLY_MSG'), $recordId);
			$this->_view->addRedirectToListControllerCommand($msg, miMessage::MSG_TYPE_INFO);
			return true;
		}
	}
	
	/**
	 * Shows data from the database in a table format
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miListAction extends miAction {
		/**
		 * Sets the current page to show and the number of records per page
		 *
		 * @access public
		 * @param int $page current page to show
		 * @param int $recordsPerPage define how many rows to show in each page
		 */
		protected function setPage($page, $recordsPerPage)
		{
			$recordset = $this->_view->getRecordset();
			$totalRecords = $recordset->getRecordsCount();
			if (($page-1) * $recordsPerPage >= $totalRecords)
				$page = intval(($totalRecords-1) / $recordsPerPage) + 1;
			
			$this->_view->getTablePagerObj()->setPagerLocation($page, $recordsPerPage, $totalRecords);
			$from = intval($page - 1) * $recordsPerPage;
			$recordset->setRecordsLimit($from, $recordsPerPage);
		}
		
		protected function doList()
		{
			// Add main page elements
			$table = $this->_view->getTable();
			
			// Set the page and records per page
			$page = $this->_view->getTablePagerObj()->getPageParam();
			$page = intval($page) > 0 ? intval($page) : 1;
			$this->setPage($page, $this->_view->getTablePagerObj()->getRecordsPerPageParam());
			
			$table->updateState();
			miAppFactory::singleton()->getStateObj()->setArray($table->getState());
			
			$table->addMainPageElements($this->_view->getMainPageElements());
			$html = $table->parse($this->_view->getTemplateFileName(miDefaultView::TEMPLATE_ID_LIST));
			$cmd = new miControllerCommand(miControllerCommand::CONTROLLER_COMMAND_HTML, $html);
			$this->_view->addControllerCommand($cmd);
		}
		
		public function doAction()
		{
			try {
				$table = $this->_view->getTable();
				
				$stateObj = miAppFactory::singleton()->getStateObj();
				$table->setState($stateObj->getArray());
				
				$this->_view->initTable($table);
				
				$recordSet = $this->_view->getTable()->getRecordset();
				$recordSet->setOrder(miGetParamDefault(miTableSorter::PARAM_SORTBY, ''), miGetParamDefault(miTableSorter::PARAM_SORTDIR, ''));
				
				$filterObjects = $this->_view->getTableFilterObj()->getFilterObjs();
				$recordSet->addFilters($filterObjects);
				
				$this->_view->callPlugin($this, 'preList');
				$this->doList();
				return true;
			
			} catch (miException $exception) {
				// TODO: we probably shouldn't die here
				$msg = $this->_view->getMessage('MI_RECORD_LIST_FAILED_MSG');
				die($msg . $exception->getMessage());
			};
		}
	}
?>