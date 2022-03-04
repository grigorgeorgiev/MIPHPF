<!-- The database is abstracted into a pair of classes - miDBUtilImpl and miStaticDBUtil. The miDBUtilImpl has a number of methods for accessing the database and performing queries.
The miStaticDBUtil implements the proxy design pattern by duplicating all methods of miDBUtilImpl. There is no need to instantiate the miDBUtilImpl object, as it will be created implicitly by miStaticDBUtil.

By default database objects are working with the database configuration defined in the config.inc file.

Here is an example usage: -->
<?php
miStaticDBUtil::execQuery('UPDATE Example SET ExampleColumn = 1');
?>
