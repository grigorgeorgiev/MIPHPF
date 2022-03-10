<?php
//miSqlRecordset reads a collection of records from the database. It supports filters, can sort, group and can join multiple tables.
//Get all records:
$recordset = new miSqlRecordset('Countries');
$records = $recordset->getAllRecords();
//$records
// Array
// (
//     [0] => Array
//         (
//             [CountryID] => 3
//             [CountryCode2] => AL
//             [CountryCode3] => ALB
//             [CountryRequireValidCity] => 0
//             [CountryAddressFormat] => 0
//             [LanguageCode] => 
//             [CurrencyCode] => 
//         )

//     [1] => Array
//         (
//             [CountryID] => 4
//             [CountryCode2] => DZ
//             [CountryCode3] => DZA
//             [CountryRequireValidCity] => 0
//             [CountryAddressFormat] => 0
//             [LanguageCode] => 
//             [CurrencyCode] => 
//         )


//Using filters:
$recordset = new miSqlRecordset('Contacts');
$recordset->addFilter(new miSqlFilterEqual('ContactName', 'John Smith'));
$recordset->getAllRecords();

//Join two tables example:
$recordset = new miSqlRecordset('Contacts');
$recordset->addJoinCondition('INNER', 'Users', 'ON Users.UserID = Contacts.UserID');

//Get the number of records and retrieve the last 10:
$recordset = new miSqlRecordset('Contacts');
$numRecords = $recordset->getRecordsCount();
$records = $recordset->getRecordsByIndex($numRecords-10, 10);


//ternative way to retrieve specific records:
$recordset = new miSqlRecordset('Contacts');
$recordset->setRecordsLimit(10, 5);  // Retrieve 5 records, starting from 10th
$records = $recordset->getRecords();
