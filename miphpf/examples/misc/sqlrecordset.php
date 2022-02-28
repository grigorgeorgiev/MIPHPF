<?php
	require_once('../../include/miphpf/Init.php');
	require_once('../header.html');
	
	echo 'Example 1: SQLRecordset on Contacts table<br>';
	$sqlRecordSet = new miSqlRecordset('Contacts');
	testRecordset($sqlRecordSet);

	echo '<br>';
	echo 'TEST 2: SQLRecordset on Contacts table joined with Controls table<br>';
	$sqlRecordSet = new miSqlRecordset('Contacts');
	$sqlRecordSet->addJoinCondition('INNER', 'Controls', 'ON Contacts.ContactID=Controls.ControlID');
	testRecordset($sqlRecordSet);

	function testRecordset($sqlRecordSet)
	{
		try {
			echo 'Reading all records.<br>';
			$records = $sqlRecordSet->getAllRecords();
			
			echo 'Reading 4 records, skipping the first record.<br>';
			$records = $sqlRecordSet->getRecordsByIndex(1, 4);
			
			echo 'Adding sorting. Reading all sorted records.<br>';
			$sqlRecordSet->setOrder('ContactID', 'ASC');
			$records = $sqlRecordSet->getAllRecords();
				
			echo 'Adding filters. Reading all filtered records.<br>';
			$filter = new miSqlFilterEqual('ContactID', 3);
			$sqlRecordSet->addFilter($filter);
			$records = $sqlRecordSet->getAllRecords();
	
			echo 'Limiting the columns. Reading only selected columns.<br>';
			$sqlRecordSet->setSelectFields(array('ContactID', 'ContactName'));
			$records = $sqlRecordSet->getAllRecords();
		} catch (miDBException $dbException) {
			echo 'An exception occured: ' . $dbException->getMessage() . '<br>';
		}
	}
	
	require_once('../footer.html');
?>