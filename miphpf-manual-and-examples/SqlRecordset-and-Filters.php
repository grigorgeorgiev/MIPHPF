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

//in two tables example:
$recordset = new miSqlRecordset('Contacts');
$recordset->addJoinCondition('INNER', 'Users', 'ON Users.UserID = Contacts.UserID');
