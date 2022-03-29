<?php
//Like the miSqlRecord class, miSqlComplexPKRecord is purposed to read, insert, 
//update and delete records from the database. miSqlComplexPKRecord is used when the 
//programmer needs to manage records in a table, where primary key is defined by more than one column.

// Reading a record by primary key example: -->

$record = new miSqlComplexPKRecord('Reciepts', array('OrderID', 'InvoiceID'));
$record->readPK(array('OrderID' => 3, 'InvoiceID' => 4));
