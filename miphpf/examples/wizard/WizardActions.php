<?php
	/**
	 * Action to display a wizard page
	 */
	class miViewWizardPageAction extends miActionWithWebForm {
		public function doAction()
		{
			try {
				$record = $this->_controller->getRecord();
				$record->setRow($this->_controller->getPageData());
				
				$this->_controller->callPlugin($this, 'preShowForm');
				
				$form = $this->getWebForm();
				$form->setMainPageElements($this->_controller->getMainPageElements());
				$form->show($this->_controller->getTemplateFileName(miWizardPage::TEMPLATE_ID_WIZARD_PAGE), true);
				return true;
			} catch (miException $exception) {
				$msg = miI18N::getSystemMessage('MI_RECORD_EDIT_FAILED_MSG');
				$this->_controller->redirectToList($msg . $exception->getMessage(), miTableMessage::MSG_TYPE_ERROR);
				return false;
			}
		}
	}
	
	/**
	 * Action to save the wizard page state
	 */
	class miExecWizardPageAction extends miActionWithWebForm {
		public function doAction()
		{
			try {
				$record = $this->_controller->getRecord();
				
				$form = $this->getWebForm();
				
				$isOk = $form->processSubmit();
				$data = $form->getSubmittedDataRow();
				$this->webFormToRecord($data);
				
				$this->_controller->savePageData($record->getRow());
				
				if ($isOk === false) {
					$wizard = $this->_controller->getWizardController();
					$wizard->setNextPage($wizard->getCurrentPage());
					return false;
				}
				
				return true;
			} catch (miException $exception) {
				$msg = miI18N::getSystemMessage('MI_RECORD_EDIT_FAILED_MSG');
				$this->_controller->redirectToList($msg . $exception->getMessage(), miTableMessage::MSG_TYPE_ERROR);
				return false;
			}
		}
	}

?>