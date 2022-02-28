<?php
	/**
	 * Contains the loader class
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Loads other classes upon construction
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miLoader {
		protected static $_includePath = null;
		
		/**
		 * Map from class names to files
		 * 
		 * @access protected
		 */
		protected static $_classMap = array(
			'miDBUtilImpl' => 'database/DBUtilImpl.php',
			'miStaticDBUtil' => 'database/StaticDBUtil.php',
		
			'miAction' => 'control/Actions.php',
			'miActionWithWebForm' => 'control/Actions.php',
			'miViewAction' => 'control/Actions.php',
			'miCreateAction' => 'control/Actions.php',
			'miExecCreateAction' => 'control/Actions.php',
			'miEditAction' => 'control/Actions.php',
			'miExecEditAction' => 'control/Actions.php',
			'miExecDeleteAction' => 'control/Actions.php',
			'miListAction' => 'control/Actions.php',
			'miAppFactory' => 'control/AppFactory.php',
			'miControllerCommand' => 'control/ControllerCommand.php',
			'miViewPlugin' => 'control/ViewPlugin.php',
			'miCsvExportAction' => 'control/CsvExport.php',
			'miDispatcher' => 'control/Dispatcher.php',
			'miDefaultDispatcher' => 'control/Dispatcher.php',
			'miSubmitFieldsPlugin' => 'control/SubmitFieldsPlugin.php',
			'miPage' => 'control/Page.php',
			
			'miDomainObject' => 'model/DomainObject.php',
			'miDefaultDomainObject' => 'model/DomainObject.php',
			'miViewMapperController' => 'model/ViewMapper.php',
			'miViewMapperControllerDefault' => 'model/ViewMapper.php',
			'miViewMapper' => 'model/ViewMapper.php',
			'miViewMapperDefault' => 'model/ViewMapper.php',
			
			'miPropertiesRecord' => 'model/PropertiesRecord.php',
			'miSqlComplexPKRecord' => 'model/SqlComplexPKRecord.php',
			'miSqlFilter' => 'model/SqlFilters.php',
			'miSqlFilterOneValue' => 'model/SqlFilters.php',
			'miSqlFilterSubstring' => 'model/SqlFilters.php',
			'miSqlFilterStarts' => 'model/SqlFilters.php',
			'miSqlFilterEnds' => 'model/SqlFilters.php',
			'miSqlFilterSimple' => 'model/SqlFilters.php',
			'miSqlFilterEqual' => 'model/SqlFilters.php',
			'miSqlFilterNotEqual' => 'model/SqlFilters.php',
			'miSqlFilterBiggerThan' => 'model/SqlFilters.php',
			'miSqlFilterBiggerOrEqual' => 'model/SqlFilters.php',
			'miSqlFilterSmallerThan' => 'model/SqlFilters.php',
			'miSqlFilterSmallerOrEqual' => 'model/SqlFilters.php',
			'miSqlFilterRegExp' => 'model/SqlFilters.php',
			'miSqlFilterIn' => 'model/SqlFilters.php',
			'miSqlFilterNotIn' => 'model/SqlFilters.php',
			'miSqlFilterCustom' => 'model/SqlFilters.php',
			'miSqlRecord' => 'model/SqlRecord.php',
			'miSqlRecordset' => 'model/SqlRecordset.php',
			
			'miBreadcrumb' => 'util/Breadcrumb.php',
			'miException' => 'util/Exceptions.php',
			'miDBException' => 'util/Exceptions.php',
			'miConfigurationException' => 'util/Exceptions.php',
			'miTemplateParserSectionInfo' => 'util/TemplateParser.php',
			'miTemplateParser' => 'util/TemplateParser.php',
			'miSettings' => 'util/Settings.php',
			'miState' => 'util/State.php',
			'miUtil' => 'util/Util.php',
			
			'miUIUtil' => 'view/UIUtil.php',
			'miValidator' => 'view/Validators.php',
			'miValidatorEmail' => 'view/Validators.php',
			'miValidatorDate' => 'view/Validators.php',
			'miValidatorInt' => 'view/Validators.php',
			'miValidatorDecimal' => 'view/Validators.php',
			'miValidatorIcq' => 'view/Validators.php',
			'miValidatorHttp' => 'view/Validators.php',
			'miValidatorGsm' => 'view/Validators.php',
			'miValidatorIp' => 'view/Validators.php',
			'miValidatorUnique' => 'view/Validators.php',
			'miTable' => 'view/Table.php',
			'miTableFeature' => 'view/TableFeature.php',
			'miTableFilters' => 'view/TableFilters.php',
			'miTablePager' => 'view/TablePager.php',
			'miDefaultTablePager' => 'view/TablePager.php',
			'miSimpleTablePager' => 'view/TablePager.php',
			'miTableCustomParams' => 'view/TableCustomParams.php',
			'miTableSorter' => 'view/TableSorter.php',
			'miMessage' => 'view/Message.php',
			'miWebForm' => 'view/WebForm.php',
			'miWebFormErrorsHandler' => 'view/WebFormErrorsHandler.php',
			'miWebFormMessageErrorsHandler' => 'view/WebFormErrorsHandler.php',
			'miWidget' => 'view/Widget.php',
			'miBaseWidget' => 'view/BaseWidgets.php',
			'miBaseTextWidget' => 'view/BaseWidgets.php',
			'miBaseCheckboxWidget' => 'view/BaseWidgets.php',
			'miBaseRadioWidget' => 'view/BaseWidgets.php',
			'miBaseSelectWidget' => 'view/BaseWidgets.php',
			'miTextWidget' => 'view/StandardWidgets.php',
			'miCheckboxWidget' => 'view/StandardWidgets.php',
			'miRadioWidget' => 'view/StandardWidgets.php',
			'miSelectWidget' => 'view/StandardWidgets.php',
			'miView' => 'view/View.php',
			'miDefaultView' => 'view/View.php',
		);
		
		/**
		 * Loads a class
		 * 
		 * @access public
		 * @param string $className the class to load
		 * @return bool true on success
		 */
		public static function load($className)
		{
			if (self::$_includePath == null)
				self::$_includePath = dirname(__FILE__) . '/../'; 
			
			if (empty(self::$_classMap[$className]))
				return false;
			
			require_once(self::$_includePath . self::$_classMap[$className]);
			return true;
		}
		
		/**
		 * Adds a new class to the loader
		 * 
		 * @access public
		 * @param string $className
		 * @param string $classFile
		 */
		public static function addClass($className, $classFile)
		{
			self::$_classMap[$className] = $classFile;
		}
	}
?>