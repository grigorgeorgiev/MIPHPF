<?php

//miSqlRecord is an active record class. It can be used to read, insert, update and delete a specific record from the database. When reading if the record cannot be found in the database, miDBException will be thrown.

//* Reading a record by primary key example:
try  {
    $sqlRecord = new miSqlRecord('Customers', 'CustomerID');
    $sqlRecord->readPK(6);
    // echo '<pre>';
    // print_r($sqlRecord);
    // echo '</pre>';
    // miSqlRecord Object
    // (
    //     [_row:protected] => Array
    //         (
    //             [CustomerID] => 6
    //             [CustomerEmail] => test123@gmail.com
    //             [CustomerPassword] => 5fcovwnA67QQ4oN/WhwupQ==$f26db146cc00678ed1f990a2383
    //             [CustomerPasswordHashMode] => 1
    //             [CustomerStatus] => 1
    //             [CustomerGroupID] => 1
    //             [CustomerBonusPoints] => 0
    //             [CustomerReferrerID] => 
    //             [CustomerMembershipNumber] => 
    //         )
    
    //     [_table:protected] => Customers
    //     [_primaryKey:protected] => CustomerID
    // )
}
catch (miDBException $exception)
{
    // Error handling here ...
}

//* Reading a record by any key example:

$sqlRecord = new miSqlRecord('CustomerEmail', 'test123@gmail.com');
$sqlRecord->read($key, $value);

//* Inserting new record example:
//If the table has "auto increment" key, the newly created id is returned by the insert method.
try{
    $sqlRecord = new miSqlRecord('Customers', 'CustomerID');
    $sqlRecord->set('CustomerGroupID', '1');
    $sqlRecord->set('CustomerEmail', 'test987654321@test.bg');
    $id = $sqlRecord->insert();
    var_dump($id);
}

catch (miDBException $e){
    var_dump($e);
}

//* Updating a record example:
$sqlRecord = new miSqlRecord('Customers', 'CustomerID');
$sqlRecord->readPK(6);
$sqlRecord->set('CustomerEmail', 'test321@gmail.com');
$sqlRecord->update();
