<?php
	/**
	 * Template Parser Class
	 *
	 * @copyright Copyright (c) 2003-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Template parser section
	 * @copyright Copyright (c) 2003-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miTemplateParserSectionInfo
	{
		public $_sectionName;
		public $_iterationsCount;
		public $_values;
		
		/**
		 * Associative array with the subsections. The key is the subsection name
		 */
		public $_subsections = array();
		
		/**
		 * Constructs the object
		 * All params are optional
		 *
		 * @see setSectionInfo for an example
		 * @param string $sectionName the name of the section as named in the template
		 * @param int $iterationsCount the number of times this section will be repeated
		 * @param array $values array of arrays with the values
		 */
		public function __construct($sectionName = '', $iterationsCount = 1, $values = array())
		{
			$this->setSectionInfo($sectionName, $iterationsCount, $values);
		}
		
		/**
		 * Sets up the section
		 * 
		 * Example:
		 * <code>
		 * $values = array(
		 *     'VariableName' => 'VariableValue',
		 *     'VariableName2' => 'VariableValue2',
		 *     'VariableName3' => array('Value1', 'Value2', 'Value3', 'Value4'),
		 * );
		 * setSectionInfo('testSection', 4, $values);
		 * </code>
		 * 
		 * @param string $sectionName the name of the section as named in the template
		 * @param int $iterationsCount the number of times this section will be repeated
		 * @param array $values array of arrays with the values
		 */
		public function setSectionInfo($sectionName, $iterationsCount, $values = array())
		{
			$this->_sectionName = $sectionName;
			$this->_iterationsCount = $iterationsCount;
			$this->_values = $values;
		}
		
		/**
		 * Sets the section name
		 *  
		 * @param string $sectionName the name of the section as named in the template
		 */
		public function setSectionName($sectionName)
		{
			$this->_sectionName = $sectionName;
		}
		
		/**
		 * Set the number of times this section will be repated
		 * 
		 * @param int $iterationsCount the number of times this section will be repeated
		 */
		public function setIterationsCount($iterationsCount)
		{
			$this->_iterationsCount = $iterationsCount;
		}
		
		/**
		 * Sets the section values
		 * 
		 * @see setSectionInfo for more info
		 * @param array $values array of arrays with the values
		 */
		public function setValues($values)
		{
			$this->_values = $values;
		}
		
		/**
		 * Adds section values
		 * 
		 * @see setSectionInfo for more info
		 * @param array $values array of arrays with the values
		 */
		public function addValues($values)
		{
			$this->_values = $this->_values + $values;
		}
		
		/**
		 * Adds a subsection to this section.
		 * In the template the subsection must be inside this section
		 * 
		 * @param miTemplateParserSectionInfo $subsection
		 */
		public function addSubsection(miTemplateParserSectionInfo $subsection)
		{
			$this->_subsections[$subsection->_sectionName] = $subsection;
		}
		
		/**
		 * Adds subsections array
		 * The number of subsections must match the number of iterations of the outer section
		 * 
		 * @param string $subsectionName
		 * @param array $subsections
		 */
		public function addSubsectionsArray($subsectionName, $subsections)
		{
			$this->_subsections[$subsectionName] = $subsections;
		}
	}
	
	
	/**
	 * Parses templates
	 * 
	 * Usage:
	 * 1. create a template parser object
	 * 2. add template parser section infos (optional step)
	 * 3. use readTemplate() ot setContents()
	 * 4. assign template variables
	 * 5. parse the template
	 *
	 * @copyright Copyright (c) 2003-2006 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miTemplateParser {
		const TEMPLATE_PARSER_START_TAG = '<mi:section name="';
		const TEMPLATE_PARSER_START_TAG_LEN = '18';
		const TEMPLATE_PARSER_START_TAG2 = '">';
		const TEMPLATE_PARSER_START_TAG2_LEN = '2';
		const TEMPLATE_PARSER_END_TAG = '</mi:section>';
		const TEMPLATE_PARSER_END_TAG_LEN = '13';
		const TEMPLATE_PARSER_SECTION_TAG = 'mi:section';
	
		const TEMPLATE_PARSER_PREPROCESS_AT_ITERATIONS = 5;

		
		/**
		 * Array with sections
		 * 
		 * @access protected
		 */
		protected $_sectionInfos = array();
		
		/**
		 * Array with sections
		 * 
		 * @access protected
		 */
		protected $_templateSections = array();
				
		/**
		 * Associative array of template variables
		 * They will be used after all sections have been parsed
		 * 
		 * @access protected
		 */
		protected $_templateVars = array();
		
		
		/**
		 * The contents of the template file
		 * 
		 * @access protected
		 */
		protected $_templateFileContents = '';
		

		/**
		 * Reads the template file
		 *
		 * @access public
		 * @param String $filename
		 */
		public function readTemplate($filename)
		{
			$pathname = miSettings::singleton()->get('MI_TEMPLATE_BASEDIR') . $filename;
			$fd = fopen($pathname, 'rb');
			$this->_templateFileContents = fread($fd, filesize($pathname));
			fclose($fd);
			
			$this->parseSections();
		}
		
		
		/**
		 * Sets the contents of the template from string
		 * Alternative to reading it from file
		 * 
		 * @access public
		 * @param string $contents
		 */
		public function setContents($contents)
		{
			$this->_templateFileContents = $contents;
			$this->parseSections();
		}
		
		
		/**
		 * Parses the template and returns the result
		 *
		 * @access public
		 * @return string the parsed contents
		 */
		public function templateParse()
		{
			$html = $this->templateParseSubsection($this->_templateSections, $this->_sectionInfos);
			return strtr($html, $this->_templateVars);
		}
		
		/**
		 * Shows the parsed template
		 * 
		 * @access public
		 */
		public function templateShow()
		{			
			echo $this->templateParse();
		}
		
		/**
		 * Parses a subsection
		 * 
		 * @param array $templateSections
		 * @param array $sectionInfos
		 * @param int $subsectionIndex (optional)
		 * @return string the parsed string
		 */
		protected function templateParseSubsection($templateSections, $sectionInfos, $subsectionIndex = 0)
		{
			$html = '';
			foreach ($templateSections as $templateSection) {
				$name = $templateSection['name'];
				
				// If it is an end just add the contents
				if ($name == 'END') {
					$html .= $templateSection['contents'];
					continue;
				}
				
				$html .= $templateSection['prefix'];
				
				// Find the section info
				if (empty($sectionInfos[$name]))
					continue;
				$sectionInfo = $sectionInfos[$name];
				if (is_array($sectionInfo)) {
					$sectionInfo = $sectionInfo[$subsectionIndex];
				}
				
				// Check if we have subsections that are arrays
				$hasSubsectionsArray = false;
				foreach ($sectionInfo->_subsections as $subsection) {
					if (is_array($subsection)) {
						$hasSubsectionsArray = true;
						break;
					}
				}
				
				if (!$hasSubsectionsArray) {
					if (isset($templateSection['subsections']))
						$contents = $this->templateParseSubsection($templateSection['subsections'], $sectionInfo->_subsections);
					else
						$contents = $templateSection['contents'];
				} else
					$contents = '';
				
				if ($sectionInfo->_iterationsCount > self::TEMPLATE_PARSER_PREPROCESS_AT_ITERATIONS) {
					// If more than 5 iterations do preprocessing -
					// split the array variables from normal variables
					$arrayValues = $arrayKeys = $normalValues = $normalKeys = array();
					foreach ($sectionInfo->_values as $key => $r) {
						if (is_array($r)) {
							$arrayValues[] = $r;
							$arrayKeys[] = $key;
						} else {
							$normalValues[] = $r;
							$normalKeys[] = $key;
						}
					}
					$search = array_merge($normalKeys, $arrayKeys);
					
					for ($i = 0; $i < $sectionInfo->_iterationsCount; $i++) {
						
						$replace = $normalValues;
						foreach ($arrayValues as $r)
							$replace[] = $r[$i];
						
						if ($hasSubsectionsArray)
							$contents = $this->templateParseSubsection($templateSection['subsections'], $sectionInfo->_subsections, $i);
						$html .= str_replace($search, $replace, $contents);
					}
				} else {
					$search = array_keys($sectionInfo->_values);
					for ($i = 0; $i < $sectionInfo->_iterationsCount; $i++) {
						
						$replace = array();
						foreach ($sectionInfo->_values as $r)
							$replace[] = is_array($r) ? $r[$i] : $r;
						
						if ($hasSubsectionsArray and isset($templateSection['subsections']))
							$contents = $this->templateParseSubsection($templateSection['subsections'], $sectionInfo->_subsections, $i);
						$html .= str_replace($search, $replace, $contents);
					}
				}
			}
			
			return $html;
		}
		
		
		/**
		 * Parse the template file sections
		 * 
		 * @access protected
		 */
		protected function parseSections()
		{
			$this->_templateSections = array();
			$this->parseSingleSection(0, 0, $this->_templateSections);
		}
		
		/**
		 * Parse a template section recursive method
		 * 
		 * @param int $lastTagEnd
		 * @param int $startPos
		 * @param array $sections
		 * @access protected
		 */
		protected function parseSingleSection($lastTagEnd, $startPos, &$sections)
		{
			$theTemplateContents = &$this->_templateFileContents;
			
//			$nameStart = 0; // name start
//			$nameEnd = 0; // This is start of end of the start tag
//			$nextTagStart = 0; // The points to the current tag beginning - to the mi:section
//			$pos = 0;	// The end of the last tag
			
			$pos = $startPos;
			$inSection = false;
			$section = array();

			do {
				// Next tag start
				$nextTagStart = strpos($theTemplateContents, self::TEMPLATE_PARSER_SECTION_TAG, $pos);
				if ($nextTagStart === false) {
					$section['name'] = 'END';
					$section['contents'] = substr($theTemplateContents, $pos);
					$sections[] = $section;
					break;
				}
				
				// Check if it is an end tag
				if (($nextTagStart >= 2) and substr_compare($theTemplateContents, self::TEMPLATE_PARSER_END_TAG, $nextTagStart-2, self::TEMPLATE_PARSER_END_TAG_LEN) == 0) {
					
					if ($inSection) {
						$section['contents'] = substr($theTemplateContents, $pos, $nextTagStart-2 - $pos);
						$sections[] = $section;
						$section = array();
						
						$inSection = false;
						$lastTagEnd = $nextTagStart-2 + self::TEMPLATE_PARSER_END_TAG_LEN;
						$pos = $lastTagEnd;
					} else {
						$section['name'] = 'END';
						$section['contents'] = substr($theTemplateContents, $pos, $nextTagStart-2 - $pos);
						$sections[] = $section;
						
						return $nextTagStart-2;
					}
					continue;
				}
				
				// Check if it is a start tag
				if (($nextTagStart >= 1) and substr_compare($theTemplateContents, self::TEMPLATE_PARSER_START_TAG, $nextTagStart-1, self::TEMPLATE_PARSER_START_TAG_LEN) == 0) {
					
					if ($inSection) {
						// Do subcall
						$section['subsections'] = array();
						$pos = $this->parseSingleSection($pos, $nextTagStart-1, $section['subsections']);
						$sections[] = $section;
						$section = array();
						
						// Skip past the end tag that had the subcall return
						$inSection = false;
						$lastTagEnd = $pos + self::TEMPLATE_PARSER_END_TAG_LEN;
						$pos = $lastTagEnd;
					} else {
						$inSection = true;
						
						$nameStart = $nextTagStart-1+self::TEMPLATE_PARSER_START_TAG_LEN;
						$nameEnd = strpos($theTemplateContents, self::TEMPLATE_PARSER_START_TAG2, $nameStart);
						if ($nameEnd === false) {
							break;	// Exception
						}

						$name = substr($theTemplateContents, $nameStart, $nameEnd - $nameStart);
						$section['name'] = $name;
						$section['prefix'] = substr($theTemplateContents, $lastTagEnd, $nextTagStart-1 - $lastTagEnd);
						
						$pos = $nameEnd + self::TEMPLATE_PARSER_START_TAG2_LEN;
					}
					continue;
				}
				
				// not a tag
				$pos = $nextTagStart+1;
			} while (true);
			
			return $pos;
		}
		
		/**
		 * Set the section infos. Clears all previous section infos
		 * 
		 * @access public
		 * @param array $sectionInfos
		 */
		public function setSectionInfos($sectionInfos)
		{
			$this->_sectionInfos = array();
			foreach ($sectionInfos as $sectionInfo)
				$this->_sectionInfos[$sectionInfo->_sectionName] = $sectionInfo;
		}
		
		/**
		 * Add new section info.
		 * If a previous section info with the same name exists it will be overwritten.
		 * 
		 * @access public
		 * @param miTemplateParserSectionInfo $sectionInfo
		 */
		public function addSectionInfo($sectionInfo)
		{
			$this->_sectionInfos[$sectionInfo->_sectionName] = $sectionInfo;
		}		
		
		/**
		 * Returns the section info with the requested name
		 * 
		 * @param string $sectionInfoName
		 * @return miTemplateParserSectionInfo|false the section info object, or false if not found
		 */
		public function getSectionInfo($sectionInfoName)
		{
			if (isset($this->_sectionInfos[$sectionInfoName]))
				return $this->_sectionInfos[$sectionInfoName];
			return false;
		}
		
		/**
		 * Assigns a variable to be replaced
		 *
		 * @access public
		 * @param string $name variable name, usually %%VAR_NAME%%
		 * @param string $value the contents of the variable. usually escaped with htmlenities()
		 */
		public function assign($name, $value)
		{
			$this->_templateVars[$name] = $value;
		}
		
		/**
		 * Assigns a array of variables to be replaced
		 *
		 * @access public
		 * @param array $pairs variable name is the key, the contents is the value
		 */
		public function assignArray($pairs)
		{
			$this->_templateVars = array_merge($this->_templateVars, $pairs);
		}
		
		/**
		 * Assigns a list of variables. Both array should have equal sizes, as counted by count()
		 * The first variable in the list is assigned the first value, the second variable, the seoncd value, and so on
		 * 
		 * @access public
		 * @param array $keys the keys with the variable names
		 * @param array $values the values
		 */
		public function assignList($keys, $values)
		{
			reset($values);
			foreach ($keys as $key) {
				$value = each($values);
				$this->_templateVars[$key] = $value[1];
			}
		}

		/**
		 * Retrieves an assigned variable value
		 *
		 * @access public
		 * @param string $name the name of the variable
		 * @return mixed the value of the variable
		 */
		public function &get($name)
		{
			return $this->_templateVars[$name];
		}
	}
?>