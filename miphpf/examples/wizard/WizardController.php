<?php
	/**
	 * Wizard contoller class
	 */
	class miWizardController {
		const WIZARD_CURRENT_PAGE_PARAM = 'currentWizardPage';
		const WIZARD_ACTION_PARAM = 'wizardAction';
		const WIZARD_ACTION_NEXT = 'next';
		const WIZARD_ACTION_PREVIOUS = 'previous';
		const WIZARD_ACTION_CANCEL = 'cancel';
		
		const WIZARD_VAR_CURRENT_PAGE = '%%CURRENT_WIZARD_PAGE%%';
		const WIZARD_VAR_TOTAL_PAGES = '%%TOTAL_WIZARD_PAGES%%';
		
		protected $_wizardPages = array();
		protected $_wizardAction;
		protected $_currentPage = 0;
		protected $_nextPage = 0;
		
		/**
		 * Adds new wizard page
		 * 
		 * @param miWizardPage $wizardPage the wizard page object
		 */
		public function addWizardPage(miWizardPage $wizardPage)
		{
			$this->_wizardPages[] = $wizardPage;
		}
		
		/**
		 * Sets the current page number and computes the next page number
		 */
		protected function findCurrentAndNextPages()
		{
			$this->_currentPage = (int)miGetParamDefault(self::WIZARD_CURRENT_PAGE_PARAM, 1);
			if ($this->_currentPage > count($this->_wizardPages) or ($this->_currentPage < 1))
				throw new miException('Invalid wizard page');
			
			$this->_nextPage =  $this->_currentPage;
			if ($this->_wizardAction == self::WIZARD_ACTION_NEXT)
				$this->_nextPage++;
			if ($this->_wizardAction == self::WIZARD_ACTION_PREVIOUS)
				$this->_nextPage--;
		}
		
		/**
		 * Inits the wizard
		 */
		protected function initWizard()
		{
			foreach ($this->_wizardPages as $wizardPage) {
				$wizardPage->clearPageData();
			}
			$this->onWizardStart();
		}
		
		/**
		 * Wizard processing
		 */
		public function process()
		{
			$this->_wizardAction = miGetParamDefault(self::WIZARD_ACTION_PARAM, '');
			if ($this->_wizardAction == self::WIZARD_ACTION_CANCEL) {
				$this->onWizardCancel();
				return;
			}
			
			$this->findCurrentAndNextPages();
			
			// Process the action
			$action = miGetParamDefault('action', '');
			if ($action == '') {
				$this->initWizard(); 
			} else {
				$currentWizardPage = $this->_wizardPages[$this->_currentPage-1];
				$currentWizardPage->process($action);
			}
			
			if ($this->_nextPage == count($this->_wizardPages)+1) {
				$this->onWizardFinish();
				return;
			}
			
			// Show the wizard page
			if ($this->_nextPage != 0) {
				$nextWizardPage = $this->_wizardPages[$this->_nextPage-1];
				$nextWizardPage->addMainPageElements(array(
					self::WIZARD_VAR_CURRENT_PAGE => $this->_nextPage,
					self::WIZARD_VAR_TOTAL_PAGES => count($this->_wizardPages)
				));
				$nextWizardPage->process('');
			}
		}
		
		/**
		 * Returns the current page number
		 * Page numbers start from 0
		 * 
		 * @return int the current page number
		 */
		public function getCurrentPage()
		{
			return $this->_currentPage;
		}
		
		/**
		 * Sets the next page number
		 * Page numbers start from 0
		 * 
		 * @param int $nextPage the next page number
 		 */
		public function setNextPage($nextPage)
		{
			$this->_nextPage = $nextPage;
		}
		
		/**
		 * Returns next page number
		 * Page numbers start from 0
		 * 
		 * @return int the next page number
		 */
		public function getNextPage()
		{
			return $this->_nextPage;
		}
		
		/**
		 * Called when the user selects to cancel the wizard
		 */
		protected function onWizardStart()
		{
		}
		
		/**
		 * Called when the user selects to cancel the wizard
		 */
		protected function onWizardCancel()
		{
		}
		
		/**
		 * Called when the user finishes the wizard
		 */
		protected function onWizardFinish()
		{
		}
	}
?>