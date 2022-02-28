<?php
	/**
	 * User Interface Util Class
	 *
	 * @copyright Copyright (c) 2003, 2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Offer same often used user interface functions
	 *
	 * @copyright Copyright (c) 2003-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miUIUtil {
		
		/**
		 * Returns HTML code for a dropdown menu ("select" element) from the specified array
		 * 
		 * @access public
		 * @param string $name name of the select element
		 * @param int $selected which key is selected
		 * @param array $optionsArray array whit the dropdown menu elements values
		 */
		public static function &getConstDropdown($name, $selected, $optionsArray)
		{
			$html = '<select name="' . $name . '" id="' . $name . '">' . "\n";
			$selected = (string)$selected;
			foreach ($optionsArray as $key => $value)
				$html .= '<option value="' . miI18N::htmlEscape($key) . '"' . ((string)$key === $selected?' selected="selected"':'') . '>' . miI18N::htmlEscape($value) . '</option>' . "\n";
			$html .= '</select>' . "\n";
			return $html;
		}
		
		/**
		 * Creates drop-down filter
		 * The filter is applied to the $table
		 * 
		 * @access public
		 * @param miView $view
		 * @param miTable $table the table to apply the filter to
		 * @param string $field the field name
		 * @param array $values associative array with the values of the filter
		 * @param string $condition the filter condition
		 */
		public static function createDropdownFilter(miView $view, miTable $table, $field, $values, $condition)
		{
			$filterObj = $view->getTableFilterObj();
			$filterValues = $filterObj->getFilterValues();
			$defaultValue = isset($filterValues[$field]) ? $filterValues[$field] : '';
			
			// Make sure that the filter is applied
			if (!isset($_REQUEST[$field . 'Filter']))
				$filterObj->addAdditionalFilter($field, $defaultValue, $condition);
			
			$dropdown = miUIUtil::getConstDropdown($field . 'Filter', $defaultValue, $values);
			$condition = '<input type="hidden" name="' . miI18N::htmlEscape($field) . 'Condition" value="' . miI18N::htmlEscape($condition) . '"/>';
			$table->assign('%%HTML_' . strtoupper($field) . '_FILTER%%', $dropdown . $condition);
		}
		
		/**
		 * Transposes the $rows array from array of hashes into a hash of
		 * arrays. All values will have the &, ", < and > escaped, except
		 * the values for keys starting with HTML_
		 * 
		 * @access public
		 * @param array $rows readed rows from the database
		 * @return array array with the new transposed rows
		 */
		public static function transposeRows($rows)
		{
			$newArray = array();
			
			$count = count($rows);
			if ($count == 0)
				return $newArray;
			
			foreach ($rows[0] as $key => $row) {
					if (strncmp($key, 'HTML_', 5)) {
						for ($i=0; $i < $count; $i++)
							$newArray[$key][] = $rows[$i][$key];
						$newArray[$key] = miI18N::htmlEscape($newArray[$key]);
					} else {
						for ($i=0; $i < $count; $i++)
							$newArray[$key][] = $rows[$i][$key];
					}
			}
			return $newArray;
		}
		
		/**
		 * Makes the hash of arrays $rows suitable for the miTemplateParserSectionInfo values
		 * 
		 * @access public
		 * @param array $rows transposed rows read from the database
		 * @return array
		 */
		public static function templetizeRows($rows)
		{
			$newRows = array();
			foreach ($rows as $key => $row) {
				$newRows['%%' . strtoupper($key) . '%%'] = $row;
			}
			return $newRows;
		}
		
		/**
		 * Makes the hash of arrays $rows suitable for the miTemplateParserSectionInfo values
		 * 
		 * @access public
		 * @param array $rows transposed rows read from the database
		 * @return array
		 */
		public static function templetizeAndEscapeRows($rows)
		{
			$newRows = array();
			foreach ($rows as $key => $row) {
				if (strncmp($key, 'HTML_', 5))
					$newRows['%%' . strtoupper($key) . '%%'] = miI18N::htmlEscape($row);
				else
					$newRows['%%' . strtoupper($key) . '%%'] = $row;
			}
			return $newRows;
		}
	}
?>