<?php

// miPropertiesRecord class is used for management of property tables. 
//   These are tables with one column for property name and one column for property value.

// * Example usage

$record = new miPropertiesRecord('Settings', 'SettingName', 'SettingValue');
$record->readPK();

$companyName = $record->get('CompanyName');
