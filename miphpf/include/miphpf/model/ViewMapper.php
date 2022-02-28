<?php
	/**
	 * View Mapper Classes
	 *
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * The view mapper controller class
	 *
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miViewMapperController {
		protected $_view;
		protected $_viewMappers = array();
		
		/**
		 * Constructs the object
		 * 
		 * @param miView $view
		 * @param miRecord $record
		 */
		public function __construct(miView $view)
		{
			$this->_view = $view;
			
			$this->setupViewMappers();
		}
		
		/**
		 * Create all view mapper objects
		 */
		protected function setupViewMappers()
		{
			$dataFields = $this->_view->getDataFields();
			$viewMappers = array();
			foreach ($dataFields as $dataField) {
				if (isset($dataField['modelStrategy']))
					$viewMappers[] = $dataField['modelStrategy'];
			}
			foreach ($viewMappers as $viewMapper)
				$this->addViewMapper($viewMapper, new $viewMapper($this));
		}
		
		/**
		 * Return the view
		 * 
		 * @return miView the view object
		 */
		public function getView()
		{
			return $this->_view;
		}
		
		/**
		 * Adds new view mapper object
		 * 
		 * @param string $viewMapperName view mapper name
		 * @param miViewMapper $viewMapper the view mapper object
		 */
		public function addViewMapper($viewMapperName, miViewMapper $viewMapper)
		{
			$this->_viewMappers[$viewMapperName] = $viewMapper;
		}
		
		/**
		 * Returns the view mapper with name $viewMapperName
		 * 
		 * @param string $viewMapperName the view mapper name (optional). If not given returns the default view mapper
		 * @return miViewMapper object
		 */
		public function getViewMapper($viewMapperName = '')
		{
			return $this->_viewMappers[$viewMapperName];
		}
		
		/**
		 * Updates the form with the newly submitted values
		 * 
		 * @param miWebForm $webForm
		 */
		public function updateSubmittedForm(miWebForm $webForm)
		{
			$formData = $webForm->getSubmittedDataRow();
			$webForm->setFormDataRow($formData);
		}
		
		/**
		 * Call all view mappers
		 * 
		 * @param string $operation 'read', 'insert', 'update', 'preDelete' or 'delete'
		 */
		protected function callViewMappers($operation)
		{
			foreach ($this->_viewMappers as $viewMapper)
				call_user_func(array($viewMapper, $operation));
		}
		
		public function read()
		{
			$this->callViewMappers('read');
		}
		
		public function insert()
		{
			$this->callViewMappers('insert');
		}
		public function update()
		{
			$this->callViewMappers('update');
		}
		public function delete()
		{
			$this->callViewMappers('preDelete');
			$this->callViewMappers('delete');
		}
	}
	
	
	/**
	 * Default View to Domain Object Mapper Controller
	 * 
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miViewMapperControllerDefault extends miViewMapperController {
		protected $_domainObj;
		protected $_webForm = null;
		
		public function __construct(miView $view, miDomainObject $domainObj, $pkValue)
		{
			$this->_domainObj = $domainObj;
			$this->addViewMapper('', new miViewMapperDefault($this, $domainObj, $pkValue));
			
			parent::__construct($view);
		}
		
		public function getDomainObject()
		{
			return $this->_domainObj;
		}
		
		public function setWebForm($webForm)
		{
			$this->_webForm = $webForm;
		}
		
		public function getWebForm()
		{
			return $this->_webForm;
		}
		
		public function getProperies($fieldName)
		{
			$dataFields = $this->_view->getDataFields();
			foreach ($dataFields as $dataField)
				if ($dataField['data'] == $fieldName)
					return $dataField['properties'];
			throw new miException(sprintf('Cannot find properties for %s', $fieldName));
		}
		
		public function getViewMapperDataFields($viewMapperName)
		{
			$result = array();
			$dataFields = $this->_view->getDataFields();
			foreach ($dataFields as $dataField)
				if (isset($dataField['modelStrategy']) and ($dataField['modelStrategy'] == $viewMapperName))
					$result[] = $dataField;
			return $result;
		}
		
		/**
		 * Retrieve the data from the web form
		 */
		public function setDataFromWebForm()
		{
			$formData = $this->_webForm->getSubmittedDataRow();
			$dataFields = $this->_view->getDataFields();
			foreach ($dataFields as $dataField) {
				if (!isset($formData[$dataField['data']]))
					continue;
				
				$viewMapperName = isset($dataField['modelStrategy']) ? $dataField['modelStrategy'] : '';
				$viewMapper = $this->_viewMappers[$viewMapperName];
				$viewMapper->set($dataField['data'], $formData[$dataField['data']]);
			}
		}
		
		/**
		 * Set the data to the web form
		 */
		public function setDataToWebForm()
		{
			$dataFields = $this->_view->getDataFields();
			foreach ($dataFields as $dataField) {
				$viewMapperName = isset($dataField['modelStrategy']) ? $dataField['modelStrategy'] : '';
				$viewMapper = $this->_viewMappers[$viewMapperName];
				$this->_webForm->setFormData($dataField['data'], $viewMapper->get($dataField['data']));
			}
		}
	}
	
	/**
	 * The View Mapper base class
	 *
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miViewMapper {
		protected $_viewMapperController;
		
		public function __construct(miViewMapperController $viewMapperController)
		{
			$this->_viewMapperController = $viewMapperController;
		}
		
		public function getViewMapperController()
		{
			return $this->_viewMapperController;
		}
		
		public function get($fieldName) {}
		public function set($fieldName, $fieldValue) {}
		
		public function read() {}
		public function insert() {}
		public function update() {}
		public function delete() {}
		
		public function preDelete() { return true; }
	}
	
	/**
	 * The view to domain object mapper default class
	 *
	 * @copyright Copyright (c) 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miViewMapperDefault extends miViewMapper {
		protected $_domainObj;
		protected $_pkValue;
		
		public function __construct(miViewMapperController $viewMapperController, miDomainObject $domainObj, $pkValue)
		{
			parent::__construct($viewMapperController);
			$this->_domainObj = $domainObj;
			$this->_pkValue = $pkValue;
		}
		
		public function getDomainObject()
		{
			return $this->_domainObj;
		}
		
		public function get($fieldName)
		{
			return $this->_domainObj->get($fieldName);
		}
		
		public function set($fieldName, $fieldValue)
		{
			$this->_domainObj->set($fieldName, $fieldValue);
		}
		
		/**
		 * Read the model data from storage
		 */
		public function read()
		{
			$this->_domainObj->read($this->_pkValue);
		}
		
		/**
		 * Insert the model data as new record
		 */
		public function insert()
		{
			return $this->_domainObj->insert();
		}
		
		/**
		 * Update existing record
		 */
		public function update()
		{
			return $this->_domainObj->update();
		}
		
		/**
		 * Delete existing record
		 */
		public function delete()
		{
			return $this->_domainObj->delete($this->_pkValue);
		}
	}
?>