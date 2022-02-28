<?php ob_start('ob_gzhandler');?>
<?php
	/**
	 * Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 */

	require_once('../../include/miphpf/Init.php');
	
	class ValidatorsView extends miDefaultView {
		
		var $_dataFields = array(
       		array(
				'field' => 'miBaseTextWidget',
				'data' => 'ValidatorID'
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ValidatorDate',
				'validator' => 'miValidatorDate'
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ValidatorEmail',
				'validator' => 'miValidatorEmail'
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ValidatorInt',
				'validator' => 'miValidatorInt'
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ValidatorDecimal',
				'validator' => 'miValidatorDecimal'
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ValidatorIcq',
				'validator' => 'miValidatorIcq'
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ValidatorHttp',
				'validator' => 'miValidatorHttp'
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ValidatorGsm',
				'validator' => 'miValidatorGsm'
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ValidatorIp',
				'validator' => 'miValidatorIp'
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ValidatorMultiple',
				'validator' => array('customValidator', 'miValidatorHttp')
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ValidatorUnique'
        		),
/*        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ContactName',
				'properties' => array('minLength' => 2, 'maxLength' => 12)
        		),*/
        	);
			
		/**
		 * Init the web form
		 */
		public function initWebForm(&$form)
		{
			parent::initWebForm($form);
			
			// Set our error hanlder
			$form->setErrorsHandler(new miWebFormMessageErrorsHandler);
			
			// Add a special validator - the unique key
			$form->addValidator('ValidatorUnique', new miValidatorUnique($form, 'ValidatorUnique', $this->getRecord()));
		}
	}
	
	/**
	 * Custom validator class
	 * Checks if the value is more than 2 characters
	 */
	class customValidator extends miValidator {
		public function validate(miWebFormErrorsHandler &$errors)
		{
			$fieldValue = $this->_webForm->getSubmittedData($this->_fieldName);
			if (strlen($fieldValue) > 2)
				return;
			
			$errors->addError($this->_fieldName, 'Invalid length - should be more than 2 characters');
		}
	}
	
	$validators = new miSqlRecordset('Validators');
	$table = new miTable($validators);
	
	$view = new ValidatorsView($validators);
	$view->setRecord(new miSqlRecord('Validators', 'ValidatorID'));
	$view->setTable($table);
	
	$page = new miPage(array($view));
	$page->setHeader('../header.html');
	$page->setFooter('../footer.html');
	$page->dispatchAndShow();
?>