<?php ob_start('ob_gzhandler');?>
<?php
	/**
	 * Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 */

	require_once('../../include/miphpf/Init.php');
	
	class FiltersDemoView extends miDefaultView {
		var $_templates = array(
			self::TEMPLATE_ID_LIST => 'examples/filters/filters_demo.tmpl'
		);
		
		var $_defaultFilterValues = array(
			'ControlID' => array('', '', '', '', '', ''),
			'ControlTextField' => array('', '', '', '')
		);
		
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
		 * Init the table
		 */
		public function initTable($table)
		{
			$inFilter = array(
				'' => 'All',
				'0,1' => 'Phone or Email',
				'0' => 'Email Only',
				'1' => 'Phone Only',
				'2,1,0' => 'Fax, Phone Or Email',
			);
			$notInFilter = array(
				'' => 'All',
				'0,1' => 'All But Phone or Email',
				'0' => 'All But Email',
				'1' => 'All But Phone Only',
				'2,1,0' => 'All But Fax, Phone Or Email',
			);
			
			$controlSelectFilter = miGetParamDefault('ControlSelectFieldFilter', array('', ''));
			$inFilterHtml = miUIUtil::getConstDropdown('ControlSelectFieldFilter[0]', $controlSelectFilter[0], $inFilter);
			$this->addMainPageElements(array('%%HTML_IN_FILTER%%' => $inFilterHtml));
			$notInFilterHtml = miUIUtil::getConstDropdown('ControlSelectFieldFilter[1]', $controlSelectFilter[1], $notInFilter);
			$this->addMainPageElements(array('%%HTML_NOT_IN_FILTER%%' => $notInFilterHtml));
		}
	}
	
	$controls = new miSqlRecordset('Controls');
	$table = new miTable($controls);
	
	$view = new FiltersDemoView($controls);
	$view->setRecord(new miSqlRecord('Controls', 'ControlID'));
	
	$view->setTable($table);
	
	$page = new miPage(array($view));
	$page->setHeader('../header.html');
	$page->setFooter('../footer.html');
	$page->dispatchAndShow();
?>