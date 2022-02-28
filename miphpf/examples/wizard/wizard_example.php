<?php
	require_once('../../include/miphpf/Init.php');
	
	require_once('WizardController.php');	
	require_once('WizardPage.php');	
	require_once('WizardActions.php');	
	
	class ExampleWizardController extends miWizardController {
		protected function onWizardStart()
		{
			echo 'Wizard started.';
		}
		
		protected function onWizardCancel()
		{
			echo 'Wizard cancelled.';
		}
		
		protected function onWizardFinish()
		{
			echo 'Wizard finish.';
		}
	}
	
	class WizardPage1 extends miWizardPage {
		protected $_templates = array(
			self::TEMPLATE_ID_WIZARD_PAGE => 'examples/wizard/wizard1.tmpl'
		);
		
		var $_dataFields = array(
       		array(
				'field' => 'miBaseTextWidget',
				'data' => 'ContactName'
        		),
       		array(
				'field' => 'miBaseTextWidget',
				'data' => 'ContactTitle'
        		),
        );
	}
	
	class WizardPage2 extends miWizardPage {
		protected $_templates = array(
			self::TEMPLATE_ID_WIZARD_PAGE => 'examples/wizard/wizard2.tmpl'
		);
		
		var $_dataFields = array(
        	array(
				'field' => 'miBaseSelectWidget',
				'data' => 'ContactPreferredMethod'
        		),
        	array(
				'field' => 'miCheckboxWidget',
				'data' => 'ContactSubscription'
        		),
		);
		
        protected $contactPreferredMethods = array(
				'0' => 'Email',
				'1' => 'Phone',
				'2' => 'Fax',
				'3' => 'Instant Messanger',
			);
			
		public function initWebForm(&$form)
		{
			parent::initWebForm($form);
			
			$field = $form->getWidget('ContactPreferredMethod');
			$field->setOptions($this->contactPreferredMethods, '2');
		}
	}
	
	session_start();
	//$_SESSION = array();
	
	$wizard = new ExampleWizardController();
	
	$wizardPage = new WizardPage1($wizard);
	$wizardPage->setRecord(new miSqlRecord('Validators', 'ValidatorID'));
	$wizard->addWizardPage($wizardPage);
	
	$wizardPage = new WizardPage2($wizard);
	$wizardPage->setRecord(new miSqlRecord('Validators', 'ValidatorID'));
	$wizard->addWizardPage($wizardPage);
	
	$page = new miPage(array($wizard));
	$page->setHeader('../header.html');
	$page->setFooter('../footer.html');
	$page->dispatchAndShow();
?>