<?php
	require_once('../../include/miphpf/Init.php');
	require_once('../header.html');
	
	define('COMPLEX_TABLE_KEY1', 3);
	define('COMPLEX_TABLE_KEY2', 'xx');
	
	echo 'TEST 1: Reading and updating record using the primary key<br>';
	$sqlRecord = new miSqlRecord('Contacts', 'ContactID');
	try {
		echo 'Inserting contact record.<br>';
		$sqlRecord->set('ContactTitle', 'Testing SQLRecord');
		$id = $sqlRecord->insert();
		
		echo 'Reading the contact record.<br>';
		$sqlRecord->readPK($id);
		
		echo 'Updating the contact title.<br>';
		$sqlRecord->set('ContactTitle', 'Testing SQLRecord');
		$sqlRecord->update();
		
		echo 'Deleting the contact record.<br>';
		$sqlRecord->delete();
		
		echo 'Test completed successfully.<br>';
	} catch (miDBException $dbException) {
		echo 'An exception occured: ' . $dbException->getMessage() . '<br>';
	}
	
	echo '<br>';
	echo 'TEST 2: Reading and updating record in a table with complex primary key<br>';
	$complexSqlRecord = new miSqlComplexPKRecord('ComplexTable', array('ComplexTableKey1', 'ComplexTableKey2'));
	try {
		echo 'Inserting ComplexTable record.<br>';
		$complexSqlRecord->set('ComplexTableKey1', COMPLEX_TABLE_KEY1);
		$complexSqlRecord->set('ComplexTableKey2', COMPLEX_TABLE_KEY2);
		$complexSqlRecord->set('ComplexTableValue', '12');
		$complexSqlRecord->insert();
		
		echo 'Reading the ComplexTable record.<br>';
		$complexSqlRecord->readPK(array('ComplexTableKey1' => COMPLEX_TABLE_KEY1, 'ComplexTableKey2' => COMPLEX_TABLE_KEY2));
		
		echo 'Updating the ComplexTable record.<br>';
		$complexSqlRecord->set('ComplexTableValue', '13');
		$complexSqlRecord->update();
		
		echo 'Deleting the ComplexTable record.<br>';
		$complexSqlRecord->delete();
	} catch (miDBException $dbException) {
		echo 'An exception occured: ' . $dbException->getMessage() . '<br>';
	}
	
	require_once('../footer.html');
?>