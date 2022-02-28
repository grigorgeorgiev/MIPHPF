<?php
	/**
	 * SQL Recordset Class
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Handle and manage the reading of data from database.
	 * This class only read data from a given database table with necessary
	 * ordering and filtering.
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */	
	class miSqlRecordset {
		
		
		/**
		 * @access protected
		 */
		protected $_table;
		
		/**
		 * @access protected
		 */
		protected $_firstRecordIndex = 0;
		
		/**
		 * @access protected
		 */
		protected $_numRecords = false;
		
		/**
		 * @access protected
		 */
		protected $_sortBy = '';
		
		/**
		 * @access protected
		 */
		protected $_sortDir = '';
		
		/**
		 * @access protected
		 */
		protected $_groupBy = '';
		
		/**
		 * @access protected
		 */
		protected $_filters = array();
		
		/**
		 * @access protected
		 */
		protected $_havingFilterNames = array();
		
		/**
		 * @access protected
		 */
		protected $_allFields = null;
		
		/**
		 * @access protected
		 */
		protected $_selectFields = array();
		
		/**
		 * @access protected
		 */
		protected $_selectedFields = array();
		
		/**
		 * @access protected
		 */
		protected $_joinConditions = array();
		
		
		/**
		 * miSqlRecordset constructor
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $recordset = new miSqlRecordset('tableName');
		 * ?>
		 * <?php
		 * $recordset = new miSqlRecordset(array('firstTable', 'secondTable'));
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string|array $table database table name, or several table names
		 */
		public function __construct($table)
		{
			$this->_table = $table;
		}
		
		/**
		 * Adds join condition
		 * 
		 * @param string $joinType the type of the join, eg. INNER, LEFT
		 * @param string $joinTable the table to be joined
		 * @param string $joinCondition the condition of the join
		 */
		public function addJoinCondition($joinType, $joinTable, $joinCondition)
		{
			$this->_joinConditions[] = array($joinType, $joinTable, $joinCondition);
		}
		
		/**
		 * Sets the field by which the result of the query will be ordered and 
		 * the direction of ordering - ascending or descending.
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $recordset = new miSqlRecordset('tableName');
		 * $recordset->setOrder($tableField, 'ASC');
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param string $sortBy database table field name
		 * @param string $sortDir direction of ordering - ascending or descending
		 */
		public function setOrder($sortBy, $sortDir)
		{
			$this->_sortBy = $sortBy;
			$this->_sortDir = $sortDir;
		}
		
		
		/**
		 * Sets group by fields
		 * The param is directly added after the GROUP BY clause
		 * 
		 * @param string the group by fields
		 */
		public function setGroupBy($groupBy)
		{
			$this->_groupBy = $groupBy;
		}
		
		/**
		 * Adds a filter object
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $recordset = new miSqlRecordset('tableName');
		 * $recordset->addFilter($filter);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param array $filter a filter object
		 */
		public function addFilter(miSqlFilter $filter)
		{
			$this->_filters[] = $filter;
		}
		
		/**
		 * Adds multiple filters
		 * 
		 * @access public
		 * @param array $filters array of filter objects
		 */
		public function addFilters($filters)
		{
			$this->_filters = array_merge($this->_filters, $filters);
		}

		/**
		 * Sets the names of the filters to be used in a HAVING clause instead of WHERE
		 * 
		 * @access public
		 * @param array $filterNames array of filter names to be used in a HAVING clause instead of WHERE
		 */
		function setHavingFilterNames($filterNames)
		{
			$this->_havingFilterNames = $filterNames;
		}
		
		/**
		 * Sets the select fields
		 * Defaults to * (all columns)
		 * 
		 * @access public
		 * @param array $fields
		 */
		public function setSelectFields($selectFields)
		{
			$this->_selectFields = $selectFields;
		}
		
		/**
		 * Sets how many rows to be read from the databse 
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $recordset = new miSqlRecordset('tableName');
		 * $recordset->setRecordsLimit(5, 20);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param int $from from which record index to start
		 * @param int $num how many rows to read
		 */
		public function setRecordsLimit($from, $num)
		{
			$this->_firstRecordIndex = $from;
			$this->_numRecords = $num;
		}
		
		
		/**
		 * Return the order clause
		 *
		 * Example:
		 * <code>
		 * <?php
		 * $recordset = new miSqlRecordset('tableName');
		 * $recordset->setOrder($tableField, 'ASC');
		 * $order = $recordset->getSqlOrderClause();
		 * ?>
		 * </code>
		 *
		 * @access protected
		 * @return string order clause
		 */
		protected function getSqlOrderClause()
		{
			if ($this->_sortBy == '')
				return '';
			
			$fields = $this->getAllFields();
			if (!in_array($this->_sortBy, $fields))
				throw new miConfigurationException('Invalid sort column: ' . $this->_sortBy);
			if (($this->_sortDir != 'ASC') and ($this->_sortDir != 'DESC') and ($this->_sortDir != ''))
				throw new miConfigurationException('Invalid sort direction: ' . $this->_sortDir);
			
			return ' ORDER BY ' . $this->_sortBy . ' ' . $this->_sortDir;
		}
		
		
		/**
		 * Return all the specified filter clauses as a string
		 *
		 * @access protected
		 * @return string all the specified filter clauses as a string
		 * @throws miConfigurationException
		 */
		protected function getSqlFilterClause()
		{
			if (empty($this->_filters))
				return '';
			
			$filtersSql = array();
			$fields = $this->getAllFields();
			
			foreach ($this->_filters as $filter) {				
				
				if (in_array($filter->getName(), $this->_havingFilterNames)) {
					continue;
				}

				// Validate the filter fields
				$filterFields = $filter->getSqlFields();
				foreach ($filterFields as $filterField) {
					if (in_array($filterField, $fields))
						continue;
					if (substr_compare($filterField, $this->_table .  '.', 0, strlen($this->_table)+1) == 0) {
						if (in_array(substr($filterField, strlen($this->_table)+1), $fields))
							continue;
					}
					
					throw new miConfigurationException('Invalid filter field: ' . $filterField, miConfigurationException::EXCEPTION_FILTER_INVALID_FIELD);
				}
				
				$sql = $filter->getSql();
				if ($sql != '')
					$filtersSql[] = $sql;
			}
			return implode(' AND ', $filtersSql);
		}
		
		/**
		 * Return all the specified HAVING filter clauses as a string
		 *
		 * @access protected
		 * @return string all the specified HAVING filter clauses as a string
		 * @throws miConfigurationException
		 */
		protected function getSqlFilterHavingClause()
		{
			if (empty($this->_filters))
				return '';
			
			$filtersSql = array();
			
			foreach ($this->_filters as $filter) {				
				
				if (!in_array($filter->getName(), $this->_havingFilterNames)) {
					continue;
				}
				
				$sql = $filter->getSql();
				if ($sql != '')
					$filtersSql[] = $sql;
			}
			return implode(' AND ', $filtersSql);
		}


		/**
		 * Return the limit as a sql code
		 *
		 * @access protected
		 * @param int $fromIndex from which record index to start
		 * @param int $numRecords number of rows to be read
		 * @return string the limit as a sql code
		 */
		protected function getSqlLimitClause($fromIndex, $numRecords)
		{
			$fromIndex = (int)$fromIndex;
			$numRecords = (int)$numRecords;
			return ' LIMIT ' . $fromIndex . ',' . $numRecords;
		}
		
		/**
		 * Return the join conditions sql
		 *
		 * @access protected
		 * @return string the join conditions sql
		 */
		protected function getJoinConditionsClause()
		{
			$sql = '';
			foreach ($this->_joinConditions as $joinCondition) {
				$sql .= ' ' . $joinCondition[0] . ' JOIN ' . $joinCondition[1] . ' ' . $joinCondition[2];
			}
			return $sql;
		}
		
		/**
		 * Returns the table sql clause
		 * 
		 * @access protected
		 * @return string the table clause
		 */
		protected function getTablesClause()
		{
			if (count($this->_joinConditions) == 0)
				return ' FROM ' . $this->_table;
			
			return ' FROM ' . $this->_table . $this->getJoinConditionsClause();
		}
		
		/**
		 * Returns the group by clause
		 * 
		 * @return string sql clause
		 */
		protected function getGroupByClause()
		{
			if ($this->_groupBy != '')
				return ' GROUP BY ' . $this->_groupBy;
			return '';
		}
		
		/**
		 * Returns the query to select all records for the recordset (without a LIMIT clause)
		 * 
		 * @return string sql clause
		 */
		public function getSelectQuery()
		{
			$filterClause = $this->getSqlFilterClause();
			if ($filterClause != '')
				$filterClause = ' WHERE ' . $filterClause;
				
			$filterHavingClause = $this->getSqlFilterHavingClause();
			if ($filterHavingClause != '')
				$filterHavingClause = ' HAVING ' . $filterHavingClause;			
			
			$fields = (count($this->_selectFields) > 0) ? implode(',', $this->_selectFields) : '*';
			
			$query = 'SELECT ' . $fields . $this->getTablesClause() .
				$filterClause . $this->getGroupByClause() . $filterHavingClause . $this->getSqlOrderClause();
			
			return $query;
		}
		
		/**
		 * Return the number of all rows in a database table that reply to the 
		 * given filter clause
		 *
		 * Example:
		 * <code>
		 * <?php
		 * $recordset = new miSqlRecordset('tableName');
		 * $recordset->addFilter($arrayFilter);
		 * $count = $recordset->getRecordsCount();
		 * ?>
		 * </code>
		 *
		 * @access public
		 * @return int number of all rows in a database table
		 * @throws miDBException
		 */
		public function getRecordsCount()
		{
			if (empty($this->_havingFilterNames)) {
				$filterClause = $this->getSqlFilterClause();
				if ($filterClause != '')
					$filterClause = ' WHERE ' . $filterClause;
				
				$query = 'SELECT COUNT(*) AS N ' . $this->getTablesClause() . $filterClause  . $this->getGroupByClause();				
			} else {
				// If there are fields for the HAVING clause, the only way to get the number of records is to execute the actual query as a subselect
				$query = 'SELECT COUNT(*) AS N FROM ('.$this->getSelectQuery().') SubQuery';				
			}
			
			$rows = miStaticDBUtil::execSelect($query);
			return (int)$rows[0]['N'];
		}
		
		
		/**
		 * Read rows from a database table. 
		 * The result reply to the given filter clause and is returned in order 
		 * specify by order clause  
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $recordset = new miSqlRecordset('tableName');
		 * $recordset->setOrder($tableField, 'ASC');
		 * $recordset->addFilter($filter);
		 * $rows = $recordset->getRecords();
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @return array rows returned from database
		 * @throws miDBException
		 */
		public function getRecords()
		{
			return $this->getRecordsByIndex($this->_firstRecordIndex, $this->_numRecords);
		}
		
		
		/**
		 * Read fixed number of rows from a given index in a database table. 
		 * The result reply to the given filter clause and is returned in order 
		 * specify by order clause  
		 * If both $fromIndex and $numRecords are false returns all records
		 * 
		 * Example:
		 * <code>
		 * <?php
		 * $recordset = new miSqlRecordset('tableName');
		 * $recordset->setOrder($tableField, 'ASC');
		 * $rows = $recordset->getRecordsByIndex($fromIndex, $numRecords);
		 * ?>
		 * </code>
		 * 
		 * @access public
		 * @param int $fromIndex from which record index to start
		 * @param int $numRecords number of rows to be read
		 * @return array rows returned from database
		 * @throws miDBException
		 */
		public function getRecordsByIndex($fromIndex, $numRecords)
		{
			$query = $this->getSelectQuery();
			
			if (($fromIndex !== false) && ($numRecords !== false))
				$query .= $this->getSqlLimitClause($fromIndex, $numRecords);
			
			return miStaticDBUtil::execSelectAndGetFields($query, $this->_selectedFields);
		}
		
		
		/**
		 * Read all records from the database table
		 * The result is limited to the given filter clause and is returned in order 
		 * specify by order clause	
		 * 
		 * @access public
		 * @return array rows returned from database
		 * @throws miDBException
		 */
		public function getAllRecords()
		{
			return $this->getRecordsByIndex(false, false);
		}
		
		/**
		 * Returns the list of all selected fields
		 * An empty array is returned of no query has been executed
		 * 
		 * @access public
		 * @return array the selected field names
		 */
		public function getSelectedFields()
		{
			return $this->_selectedFields;
		}
		
		/**
		 * Returns all fields of the recordset table(s)
		 * The result of the function is cached. Call only after all join conditions have been added.
		 * 
		 * @access public
		 * @return array the field names
		 */
		public function getAllFields()
		{
			if ($this->_allFields != null)
				return $this->_allFields;
			
			$tables = array($this->_table);
			foreach ($this->_joinConditions as $joinCondition)
				$tables[] = $joinCondition[1];
			
			$this->_allFields = $this->_havingFilterNames;
			foreach ($tables as $table)
				$this->_allFields = array_merge($this->_allFields, miStaticDBUtil::getTableFields($table));
			return $this->_allFields;
		}
		
		/**
		 * Returns the table name for the current recordset
		 * 
		 * @access public
		 * @return string
		 */
		public function getTable()
		{
			return $this->_table;
		}
	}
?>