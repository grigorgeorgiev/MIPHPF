<?php

// The sql filters are used with miSqlRecordset to filter the set of records to be retrieved.

// Records can be matched with one of the built-in filters or a custom filter. Specialized filter can be created through subclassing one of the bult-in filters.

// The built-in filter matches records where:
// miSqlFilterSubstring - field contains a string
// miSqlFilterStarts - field starts with a string
// miSqlFilterEnds - field ends a string
// miSqlFilterEqual - field is equal to a value
// miSqlFilterNotEqual - field is not equal to a value
// miSqlFilterBiggerThan - field is bigger than a value
// miSqlFilterBiggerOrEqual - field is bigger than or equal to a value
// miSqlFilterSmallerThan - field is smaller than a value
// miSqlFilterSmallerOrEqual - field is smaller than or equal to a value
// miSqlFilterRegExp - field matches a regular expression
// miSqlFilterIn - field is one of the values
// miSqlFilterNotIn - field is not one of the values

// miSqlFilterCustom  - Matches records using custom sql.

// The usage of filters is quite straightforward as can be seen in the following examples:
?>
<?php
$recordset = new miSqlRecordset('Products');
$recordset->addFilter(new miSqlFilterEqual('CategoryID', $categoryId));
?>
<?php
$products = array(2, 4, 8);
$recordset = new miSqlRecordset('Products');
$recordset->addFilter(new miSqlFilterIn('ProductID', $products));
?>
<?php
$recordset = new miSqlRecordset('Products');
$recordset->addFilter(new miSqlFilterIn('ProductID', '2,4,8'));
?>
<?php
$recordset = new miSqlRecordset('Users');
$recordset->addFilter(new miSqlFilterCustom('UserType', "UserType = 'Manufacturer' OR UserType = 'Supplier'"));
?>
