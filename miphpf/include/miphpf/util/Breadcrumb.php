<?php
	/**
	 * Contains the Breadcrumb class
	 * 
	 * @copyright Copyright (c) 2003-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	
	/**
	 * Class for displaying the breadcrumb showing the hierarchical structure of the website
	 * 
	 * @copyright Copyright (c) 2003-2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miBreadcrumb {
		
		/**
		 * Tree containing all pages and the hierarchy between them.
		 * A page can be in both as a key or as a value. If it is as a key it might have
		 * an array of subpages.
		 * @access protected
		 */
		protected $_pages = array();
		
		
		/**
		 * Array with the names of all pages
		 * @access protected
		 */
		protected $_pageNames = array();
		
		
		/**
		 * Contains the separator that is put between the breadcrumb links
		 * @access protected
		 */
		protected $_separator = '&nbsp;->&nbsp;';
		
		
		/**
		 * Contains the link template for each breadcrumb link
		 * The %%LINK%% and %%NAME%% params are replaced within it
		 * @access protected
		 */
		protected $_linkTemplate = '<a href="%%LINK%%">%%NAME%%</a>';
		
		
		/**
		 * Contains all index pages names
		 * @access protected
		 */
		protected $_indexPages = array('/index.html', '/index.htm', '/index.php', '/index.phtml');
		
		
		/**
		 * Returns the breadcrumb as HTML
		 * 
		 * @access public
		 * @param string $page
		 * @return string
		 */
		public function getBreadcrumbHtml($page)
		{
			$page = $this->stripIndexPage($page);
			
			$path = $this->findPage($page, $this->_pages);
			if ($path === false)
				$path = array($page);
			
			$html = array();
			$t = new miTemplateParser;
			$t->setContents($this->_linkTemplate);
			foreach ($path as $page) {
				$t->assign('%%LINK%%', $page);
				if (isset($this->_pageNames[$page]))
					$t->assign('%%NAME%%', $this->_pageNames[$page]);
				else
					$t->assign('%%NAME%%', $page);
				$html[] = $t->templateParse();
			}
			return implode($this->_separator, $html);
		}
		
		
		/**
		 * If the page ends with an index page name, that index page name is stripped.
		 * Otherwise returns the $page unaltered
		 * 
		 * @access protected
		 * @param string $page
		 * @return string
		 */
		protected function stripIndexPage($page)
		{
			foreach ($this->_indexPages as $indexPage)
				if (strstr($page, $indexPage)) {
					return substr($page, 0, strlen($page) - strlen($indexPage) + 1);
				}
			return $page;
		}
		
		
		/**
		 * Sets the separator between the items in the breadcrumb
		 * 
		 * @access public
		 * @param string $separator
		 */
		public function setSeparator($separator)
		{
			$this->_separator = $separator;
		}
		
		
		/**
		 * Sets the link template for each breadcrumb link
		 * 
		 * @access public
		 * @param string $linkTemplate
		 */
		public function setLinkTemplate($linkTemplate)
		{
			$this->_linkTemplate = $linkTemplate;
		}
		
		
		/**
		 * Finds the $page in the $pages array and returns array containing the path to it
		 * 
		 * @access protected
		 * @param string $page
		 * @param array $pages
		 * @return array|boolean
		 */
		protected function findPage($page, $pages)
		{
			if (is_array($pages)) {
				foreach ($pages as $key => $value) {
					
					// Check if it the key
					if ($page === $key) {
						return array($key);
					}
					
					$result = $this->findPage($page, $value);
					if (is_array($result)) {
						array_unshift($result, $key);
						return $result;
					}
					if ($result === true) {
						return array($value);
					}
				}
			} else {
				if ($page === $pages) {
					return true;
				}
			}
			return false;
		}
	}
?>