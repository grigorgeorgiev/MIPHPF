<?php ob_start('ob_gzhandler');?>
<?php
	/**
	 * Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 */

	require_once('../../include/miphpf/Init.php');
	
/*
	# AUTH CHECK
	require_once('../../include/miproject/util/ProjectAuth.php');
	miprojectUtil::checkPermissions(SECTION_CONTACTS);
	# AUTH CHECK
*/
	class ContactsView extends miDefaultView {
		var $_templates = array(
			self::TEMPLATE_ID_LIST => 'examples/contacts/contacts_mgmt.tmpl',
			self::TEMPLATE_ID_CREATE => 'examples/contacts/contacts_create.tmpl',
			self::TEMPLATE_ID_EDIT => 'examples/contacts/contacts_edit.tmpl',
		);
	
		var $_defaultFilterValues = array(
			'ContactID' => '',
			'ContactName' => '',
			'ContactPreferredMethod' => '1',
			);
		
		var $_dataFields = array(
       		array(
				'field' => 'miBaseTextWidget',
				'data' => 'ContactID'
        		),
        	array(
				'field' => 'miBaseRadioWidget',
				'data' => 'ContactTitle'
        		),
        	array(
				'field' => 'miBaseTextWidget',
				'data' => 'ContactName',
				'properties' => array('minLength' => 2, 'maxLength' => 12)
        		),
        	array(
				'field' => 'miBaseSelectWidget',
				'data' => 'ContactPreferredMethod'
        		),
        	array(
				'field' => 'miBaseCheckboxWidget',
				'data' => 'ContactSubscription'
        		),
        	);
		
        protected $contactPreferredMethods = array(
				'0' => 'Email',
				'1' => 'Phone',
				'2' => 'Fax',
				'3' => 'Instant Messanger',
			); 
        
		/**
		 * Init the web form
		 */
		public function initWebForm(&$form)
		{
			$contactTitles = array(
				'Mr' => 'Mr1',
				'Miss' => 'Miss1',
				'Mrs' => 'Mrs1',
				'Ms' => 'Ms1'
			);
			
			parent::initWebForm($form);
			$field = $form->getWidget('ContactTitle');
			$field->setRadioButtons($contactTitles, 'Miss');
			
			$field = $form->getWidget('ContactPreferredMethod');
			$field->setOptions($this->contactPreferredMethods, '2');
			
			// Set our error hanlder
			$form->setErrorsHandler(new miWebFormMessageErrorsHandler);
		}
		
		/**
		 * Adds table features - in this case record per page feature
		 */
		public function addTableFeatures($table)
		{
			parent::addTableFeatures($table);
			new miTableRecordsPerPage($table, $this->getTablePagerObj());
			
			// Drop-down with preferred contact methods
			$contactMethods = array('' => 'All') + $this->contactPreferredMethods;
			miUIUtil::createDropdownFilter($this, $table, 'ContactPreferredMethod', $contactMethods, '=');
		}
	}
	
	/**
	 * Records Per Page drop-down handling class
	 */
	class miTableRecordsPerPage extends miTableFeature {
		protected $_pagerObj;
		
		public function __construct($table, $pagerObj)
		{
			parent::__construct($table);
			$this->_pagerObj = $pagerObj;
		}
		
		public function getValues()
		{
			$recordsPerPageArray = array(1, 2, 5, 10, 20, 50);
			$options = miUIUtil::getConstDropdown(
				miTablePager::PARAM_RECORDS_PER_PAGE,
				$this->_pagerObj->getRecordsPerPageParam(),
				array_combine($recordsPerPageArray, $recordsPerPageArray));
			return array('HTML_RECORDSPERPAGE_DROPDOWN' => $options);
		}
	}
	
	
	$contacts = new miSqlRecordset('Contacts');
	$table = new miTable($contacts);
	
	$view = new ContactsView($contacts);
	$view->setRecord(new miSqlRecord('Contacts', 'ContactID'));
	
/*
	$view->addMainPageElements(miprojectUtil::getNavigation());
*/

	$view->setTable($table);
	
	$page = new miPage(array($view));
	$page->setHeader('../header.html');
	$page->setFooter('../footer.html');
	$page->dispatchAndShow();
?>