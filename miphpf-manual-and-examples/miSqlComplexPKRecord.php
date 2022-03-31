<?php
//Like the miSqlRecord class, miSqlComplexPKRecord is purposed to read, insert, 
//update and delete records from the database. miSqlComplexPKRecord is used when the 
//programmer needs to manage records in a table, where primary key is defined by more than one column.

// Reading a record by primary key example: -->

$record = new miSqlComplexPKRecord('Reciepts', array('OrderID', 'InvoiceID'));
$record->readPK(array('OrderID' => 3, 'InvoiceID' => 4));

//* Inserting new record example: Unlike miSqlRecord, here the insert() method will not return the primary key.
$record = new miSqlComplexPKRecord('Receipts', array('OrderID', 'InvoiceID'));
$record->set('OrderID', 3);
$record->set('InvoiceID', 4);
$record->set('ReceiptsCreateTime', time());
$record->insert();

//* Updating a record example:
$record = new miSqlComplexPKRecord('Receipts', array('OrderID', 'InvoiceID'));
$record->set('OrderID', 3);
$record->set('InvoiceID', 4);
$record->set('ReceiptLastUpdate', time());
$record->update();

//The following example is also a valid approach:
$record = new miSqlComplexPKRecord('Receipts', array('OrderID', 'InvoiceID'));
$record->readPK(array('OrderID' => 3, 'InvoiceID' => 4));
$record->set('ReceiptsLastUpdate', time());
$record->update();

//* Deleting a record example:
$record = new miSqlComplexPKRecord('Receipts', array('OrderID', 'InvoiceID'));
$record->set('OrderID', 3);
$record->set('InvoiceID', 4);
$record->delete();
