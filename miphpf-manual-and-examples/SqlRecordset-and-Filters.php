<?php
//miSqlRecordset reads a collection of records from the database. It supports filters, can sort, group and can join multiple tables.
//Get all records:
$recordset = new miSqlRecordset('Customers');
$records = $recordset->getAllRecords();
