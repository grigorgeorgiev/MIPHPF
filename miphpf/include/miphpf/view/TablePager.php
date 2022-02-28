<?php
	/**
	 * Recordset Pager Classes
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Recordset Pager Class
	 * Manage the movement between pages when data is shown in table fomat
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miTablePager extends miTableFeature {
		
		const TMPL_VAR_RECORDSET_NAVIGATOR = 'HTML_RECORDSET_NAVIGATOR';
		const TMPL_VAR_RECORDSET_POSITION = 'RECORDSET_POSITION';
		const TMPL_VAR_FIRST_RECORD = 'FIRST_RECORD';
		const TMPL_VAR_LAST_RECORD = 'LAST_RECORD';
		const TMPL_VAR_TOTAL_RECORDS = 'TOTAL_RECORDS';
		
		const PARAM_PAGE = 'page';
		const PARAM_RECORDS_PER_PAGE = 'recordsPerPage';
		
		/**
		 * The current page number
		 * 
		 * @access protected
		 */		
		protected $_page = 0;
		
		/**
		 * Number of records per page
		 * 
		 * @access protected
		 */
		protected $_recordsPerPage;
		
		/**
		 * Total number of records
		 * 
		 * @access protected
		 */
		protected $_totalRecords = 0;
		
		/**
		 * The records per page by default
		 * 
		 * @access protected
		 */
		protected $_defaultRecordsPerPage;
		
		/**
		 * 
		 */
		function __construct($table)
		{
			parent::__construct($table);
			
			$this->_recordsPerPage = $this->_defaultRecordsPerPage = miSettings::singleton()->get('MI_RECORDS_PER_PAGE');
		}
		
		/**
		 * Sets the pager location
		 * 
		 * @access public
		 * @param $page integer
		 * @param $recordsPerPage integer
		 * @param $totalRecords integer
		 */
		public function setPagerLocation($page, $recordsPerPage, $totalRecords)
		{
			$this->_page = $page;
			$this->_recordsPerPage = $recordsPerPage;
			$this->_totalRecords = $totalRecords;
		}
		
		/**
		 * In this function subclasses will return Navigator
		 * 
		 * @access public
		 * @return array
		 */
		public function getValues()
		{
			/* in this function subclasses will return specific Navigator */
		}
		
		/**
		 * Returns associative array with params that save the feature state
		 * 
		 * @return array
		 */
		public function getStateParams()
		{
			$params = array(self::PARAM_PAGE => $this->getStateValue(self::PARAM_PAGE, ''));
			if ($this->_defaultRecordsPerPage != $this->getStateValue(self::PARAM_RECORDS_PER_PAGE, $this->_defaultRecordsPerPage))
				$params[self::PARAM_RECORDS_PER_PAGE] = $this->getStateValue(self::PARAM_RECORDS_PER_PAGE, $this->_defaultRecordsPerPage);
			return $params;
		}
		
		
		/**
		 * Sets the default records per page
		 * 
		 * @param int $defaultRecordsPerPage the number of records per page by default
		 * @access public
		 */
		public function setDefaultRecordsPerPage($defaultRecordsPerPage)
		{
			$this->_defaultRecordsPerPage = $defaultRecordsPerPage;
		}
		
		/**
		 * Gets the value of "page" param
		 * 
		 * @access public
		 * @return string
		 */
		public function getPageParam()
		{
			return $this->getStateValue(self::PARAM_PAGE, 1);
		}
		
		/**
		 * Gets the value of "records per page" param
		 * 
		 * @access public
		 * @return string
		 */
		public function getRecordsPerPageParam()
		{
			return $this->getStateValue(self::PARAM_RECORDS_PER_PAGE, $this->_defaultRecordsPerPage);
		}
		
		/**
		 * Calculates first and last positions for a page from the total recodrs
		 * 
		 * @access protected
		 * @return array
		 */
		protected function assignPosition()
		{
			$firstRecord = ((($this->_page-1) * $this->_recordsPerPage)+1);
			$lastRecord = ($this->_page * $this->_recordsPerPage) > $this->_totalRecords ? $this->_totalRecords : ($this->_page * $this->_recordsPerPage);
			
			$values = array();
			$values[self::TMPL_VAR_FIRST_RECORD] = $firstRecord;
			$values[self::TMPL_VAR_LAST_RECORD] = $lastRecord;
			$values[self::TMPL_VAR_TOTAL_RECORDS] = $this->_totalRecords;
			if ($this->_totalRecords > 0) {
				$values[self::TMPL_VAR_RECORDSET_POSITION] =
					sprintf(miI18N::getSystemMessage('MI_RECORDSETPAGER_POSITION_MSG'), $firstRecord, $lastRecord, $this->_totalRecords);
					//'You are viewing records ' . $firstRecord	 . ' to ' . $lastRecord . ' of ' . $totalRecords);
			} else {
				$values[self::TMPL_VAR_RECORDSET_POSITION] = miI18N::getSystemMessage('MI_RECORDSETPAGER_POSITION_NO_RECORDS_MSG');
			}
			return $values;
		}
		
		
		/**
		 * Gets navigator links params
		 * 
		 * @access protected
		 */
		protected function getNavigatorParams()
		{
			$navigatorParams = $this->_table->getTableFeaturesStateParams();
			unset($navigatorParams[self::PARAM_PAGE]);
			return $this->_table->paramsArrayToUrl($navigatorParams);
		}
	}
	
	
	/**
	 * Default Recordset Pager Class
	 * Manage the movement between pages when data is shown in table fomat
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miDefaultTablePager extends miTablePager {
	
		
		/**
		 * Return Default Navigator
		 * 
		 * @access public
		 * @return array
		 */
		function getValues()
		{
			$page = $this->_page;

			$recordsPerPage = $this->_recordsPerPage;
			if ($recordsPerPage < 1)
				throw new miException(miI18N::getSystemMessage('MI_RECORDS_PER_PAGE_MUST_BE_AT_LEAST_ONE'));
			
			$strNavParams = $this->getNavigatorParams();
			
			if ($this->_totalRecords <= $recordsPerPage)
				$navigator = '<< | < | <b>1</b> | > | >>';
			else {
				$navigator = '';
				$firstpage = 1;
				$lastpage = (intval(($this->_totalRecords-1) / $recordsPerPage)) + 1;
				$first = $firstpage;
				$last = $lastpage;
				if ($last > ($page + 5)) $last = $page + 5; 
				if ($first < ($page - 5)) $first = $page - 5; 
				$prevpage = ($page > 1) ? $page - 1 : 1;
				$nextpage = ($page < $lastpage) ? $page + 1 : $lastpage;
				
				$navigator .= '<a href="?' . self::PARAM_PAGE . '=1&' . $strNavParams . '">&lt;&lt;</a> | ';
				$navigator .= '<a href="?' . self::PARAM_PAGE. '=' . $prevpage . '&' . $strNavParams . '">&lt;</a> | ';
				for ($i = $first; $i <= $last; $i++) {
					if ($i == $page)
						$navigator .= '<b>' . $page . '</b> | ';
					else
						$navigator .= '<a href="?' . self::PARAM_PAGE . '=' . $i . '&' . $strNavParams . '">' . $i . '</a> | ';
				}
				$navigator .= '<a href="?' . self::PARAM_PAGE . '=' . $nextpage . '&' . $strNavParams . '">&gt;</a> | ';
				$navigator .= '<a href="?' . self::PARAM_PAGE . '=' . $lastpage . '&' . $strNavParams . '">&gt;&gt;</a>';
			}
			return array_merge(array(self::TMPL_VAR_RECORDSET_NAVIGATOR => $navigator), $this->assignPosition());
		}
	}
	
	
	/**
	 * Default Recordset Pager Class
	 * Manage the movement between pages when data is shown in table fomat
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSimpleTablePager extends miTablePager {
		
		/**
		 * Return Simple Navigator
		 * 
		 * @access public
		 * @return array
		 */
		function getValues()
		{
			$page = $this->_page;
			$recordsPerPage = $this->_recordsPerPage;
			$strNavParams = $this->getNavigatorParams();
			
			if ($this->_totalRecords <= $recordsPerPage)
				$navigator = miI18N::getSystemMessage('MI_RECORDSETPAGER_NAVIGATIONS_MSG');
			else {
				$lastpage = (intval(($this->_totalRecords-1) / $recordsPerPage)) + 1;
				$prevpage = ($page > 1) ? $page - 1 : 1;
				$nextpage = ($page < $lastpage) ? $page + 1 : $lastpage;
				$navigator =
					'<a href="?' . self::PARAM_PAGE . '=1&' . $strNavParams . '">First</a> | ' .
					'<a href="?' . self::PARAM_PAGE . '=' . $prevpage . '&' . $strNavParams . '">Previous</a> | ' .
					'<a href="?' . self::PARAM_PAGE . '=' . $nextpage . '&' . $strNavParams . '">Next</a> | ' .
					'<a href="?' . self::PARAM_PAGE . '=' . $lastpage . '&' . $strNavParams . '">Last</a>';
			}
			return array_merge(array(self::TMPL_VAR_RECORDSET_NAVIGATOR => $navigator), $this->assignPosition());
		}
	}
?>