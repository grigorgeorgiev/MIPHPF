<?php
	/**
	 * The CSV export action class
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Support for CSV export
	 * @copyright Copyright (c) 2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miCsvExportAction extends miAction {
		const CSV_LINE_DELIMITER = "\n";
		
		/**
		 * The filename to suggest to the user
		 * If the filename contains special chars it must be escaped in quoted printable format
		 * 
		 * @access protected
		 * @var string
		 */
		protected $_csvFilename = 'export.csv';
		
		/**
		 * Print export headers
		 * 
		 * @access protected
		 */
		protected function showCsvExportHeaders()
		{
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename=' . $this->_csvFilename);
			header('Content-Transfer-Encoding: binary');
		}
		
		/**
		 * Escape a single CSV value
		 * If it detects a comma then it double quotes the string. Then double quotes themselves are escaped by doubling
		 * 
		 * @access protected
		 * @param string $value
		 * @return string
		 */
		protected function escapeCsv($value)
		{
			if ($value == '')
				return $value;
			
			if (strtok($value, ",\"\n") !== $value) {
				if (strpos($value, '"') !== false)	// If there are double quotes, escape them by doubling them
					$value = str_replace('"', '""', $value);
				return '"' . $value . '"';
			}
			return $value;
		}
		
		/**
		 * Exports the records as CSV
		 * 
		 * @access protected
		 * @param array $records
		 */
		protected function exportRecords($records)
		{
			if (count($records) == 0)
				return;
				
			// Print header line
			$header = array();
			foreach ($records[0] as $key => $value)
				$header[] = $this->escapeCsv($key);
			echo implode(',', $header);
			echo miCsvExportAction::CSV_LINE_DELIMITER;
			
			// Print the values
			foreach ($records as $record) {
				$line = array();
				foreach ($record as $value)
					$line[] = $this->escapeCsv($value);
				echo implode(',', $line);
				echo miCsvExportAction::CSV_LINE_DELIMITER;
			}
		}
		
		/**
		 * Performs the export as CSV action
		 * 
		 * @access public
		 */
		public function doAction()
		{
			// Init the recordset
			$recordSet = $this->_view->getRecordset();
			$recordSet->setOrder(miGetParamDefault(miTableSorter::PARAM_SORTBY, ''), miGetParamDefault(miTableSorter::PARAM_SORTDIR, ''));
			
			$filterObjects = $this->_view->getTableFilterObj()->getFilterObjs();
			$recordSet->addFilters($filterObjects);
			
			// Perform the export
			if (headers_sent()) {
				throw new miConfigurationException('Cannot export as CSV. Headers have already been sent', miConfigurationException::EXCEPTION_HEADERS_ALREADY_SENT);
			}
			
			// Remove possible header and do gzip encoding for speeding up the CSV file download
			if (ob_get_level()) {
				ob_end_clean();
				header('Content-Encoding: none');
			}
			ob_start('ob_gzhandler');
			
			$this->showCsvExportHeaders();
			$this->exportRecords($recordSet->getAllRecords());
			exit;
		}
	}
?>