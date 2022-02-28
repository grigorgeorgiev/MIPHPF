<?php
	require_once('../../include/miphpf/Init.php');
	require_once('../header.html');

	// Test 1
	$test1Section = new miTemplateParserSectionInfo();
	$test1Section->setSectionInfo('test1', 1, array('%%VAR1%%' => 'a variable'));
	
	// Test 2 - no need to do anything - unknown sections are hidden
	
	// Test 3
	$test3Section = new miTemplateParserSectionInfo();
	$test3SectionVars = array('%%VAR1%%' => 'V', '%%VAR2%%' => array(1,2,3));
	$test3Section->setSectionInfo('test3', 3, $test3SectionVars);
	
	// Test 4
	$test4Section = new miTemplateParserSectionInfo();
	$test4Section->setSectionInfo('test4', 1);
	$test4SubSectionA = new miTemplateParserSectionInfo();
	$test4SubSectionA->setSectionInfo('subtest4a', 1, array('%%SUBSECTION_VAR%%' => 'subsection var'));
	$test4SubSectionB = new miTemplateParserSectionInfo();
	$test4SubSectionB->setSectionInfo('subtest4b', 1);
	$test4Section->addSubsection($test4SubSectionA);
	$test4Section->addSubsection($test4SubSectionB);
	
	// Test 5
	$test5Section = new miTemplateParserSectionInfo();
	$test5Section->setSectionInfo('test5', 3, array('%%ITERNO%%' => array(1,2,3)));
	$test5SubSectionA = new miTemplateParserSectionInfo();
	$test5SubSectionA->setSectionInfo('test5sub', 2, array('%%VAR%%' => 'x'));
	$test5SubSectionB = new miTemplateParserSectionInfo();
	$test5SubSectionB->setSectionInfo('test5sub', 4, array('%%VAR%%' => 'y'));
	$test5SubSectionC = new miTemplateParserSectionInfo();
	$test5SubSectionC->setSectionInfo('test5sub', 3, array('%%VAR%%' => array('z1', 'z2', 'z3')));
	$test5Section->addSubsectionsArray('test5sub', array($test5SubSectionA, $test5SubSectionB, $test5SubSectionC));
	
	$t = new miTemplateParser();
	$t->readTemplate('examples/misc/template_parser.tmpl');
	$t->setSectionInfos(array($test1Section, $test3Section, $test4Section, $test5Section));
	$t->templateShow();
	
	require_once('../footer.html');
?>