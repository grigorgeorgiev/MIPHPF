<?php ob_start('ob_gzhandler');?>
<?php
	/**
	 * Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 */

	require_once('../../include/miphpf/Init.php');
	
	class ControlDemoView extends miDefaultView {
		
		var $_dataFields = array(
       		array(
				'field' => 'miBaseTextWidget',
				'data' => 'ControlID'
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ControlTextField'
        		),
        	array(
				'field' => 'miBaseRadioWidget',
				'data' => 'ControlRadioField'
        		),
        	array(
				'field' => 'miBaseCheckboxWidget',
				'data' => 'ControlCheckboxField'
        		),
        	array(
				'field' => 'miBaseSelectWidget',
				'data' => 'ControlSelectField'
        		),
			
        	array(
				'field' => 'miTextWidget',
				'data' => 'ControlTextWidget'
        		),
        	array(
				'field' => 'miRadioWidget',
				'data' => 'ControlRadioWidget'
        		),
        	array(
				'field' => 'miCheckboxWidget',
				'data' => 'ControlCheckboxWidget'
        		),
        	array(
				'field' => 'miSelectWidget',
				'data' => 'ControlSelectWidget'
        		),
        	);
			
		/**
		 * Init the web form
		 */
		public function initWebForm(&$form)
		{
			$radios = array(
				'0' => 'Mr',
				'1' => 'Miss',
				'2' => 'Mrs',
				'3' => 'Ms'
			);
			
			$selectOptions = array(
				'0' => 'Email',
				'1' => 'Phone',
				'2' => 'Fax',
				'3' => 'Instant Messanger',
			);
			
			parent::initWebForm($form);
			$field = $form->getWidget('ControlRadioField');
			$field->setRadioButtons($radios, '1');
			
			$field = $form->getWidget('ControlRadioWidget');
			$field->setRadioButtons($radios, '2');
			
			$field = $form->getWidget('ControlSelectField');
			$field->setOptions($selectOptions, '1');
			
			$field = $form->getWidget('ControlSelectWidget');
			$field->setOptions($selectOptions, '3');
			
			// Set our error hanlder
			$form->setErrorsHandler(new miWebFormMessageErrorsHandler);
		}
	}
	
	$controls = new miSqlRecordset('Controls');
	$table = new miTable($controls);
	
	$view = new ControlDemoView($controls);
	$view->setRecord(new miSqlRecord('Controls', 'ControlID'));
	$view->setTable($table);
	
	$submitFields = array(
		array('fieldName' => 'SubmitField', 'namePrefixSave' => '/tmp/miphpf_', 'namePrefixWeb' => '/the-url-to-tmp/miphpf_', 'nameSuffix' => '.png'),
	);
	new miSubmitFieldsPlugin($view, $submitFields);
	
	$page = new miPage(array($view));
	$page->getDispatcher()->setActionHandler('dmCsvExport', 'miCsvExportAction'); // Add CSV export functionality
	$page->setHeader('../header.html');
	$page->setFooter('../footer.html');
	$page->dispatchAndShow();
?>