<?php
//miSqlRecordset reads a collection of records from the database. It supports filters, can sort, group and can join multiple tables.
//Get all records:
$recordset = new miSqlRecordset('Customers');
$records = $recordset->getAllRecords();

//Using filters:
$recordset = new miSqlRecordset('Contacts');
$recordset->addFilter(new miSqlFilterEqual('ContactName', 'John Smith'));
$recordset->getAllRecords();

//in two tables example:
$recordset = new miSqlRecordset('Contacts');
$recordset->addJoinCondition('INNER', 'Users', 'ON Users.UserID = Contacts.UserID');
