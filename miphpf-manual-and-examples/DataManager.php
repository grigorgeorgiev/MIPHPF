<?php

// Data Manager is a base class for a CRUD or a listing page. CRUD stand for Create, Retrieve, Update, Delete. In other words by subclassing Data Manager any kind of management screen can be easily created.

// To set up a data manager derive the base class (miDataManager) and map the page templates and form fields. Create a object of this class and pass it to the newly created page object.

// When a data manager object is created, it initializes the table, and the form object through initTable() and initWebForm() methods. When they are initialized, action handling mechanism is invoked - action object is created, and its doAction() method is called.
// The action object processes the request and displays the page.

// Basic actions are provided by the framework to create, edit , view, list, and delete data.
// Creation: miCreate and miExecCreate classes
// Editing: miEdit and miExecEdit classes
// View: miViewAction class
// Delete: miExecDelete class
// Listing: miListAction class

// Description how to implement various common situations follows:
// 1. Specify own domain object
// Domain object is created within the method createDomainObject(). Overriding this method allows creation of specific domain objects.
// * Example

class UserDataManager extends miDataManager
{
    // Set the templates location
    protected $_templates = array(
        self::TEMPLATE_ID_CREATE => 'public/user/user_create.tmpl'
    );
   
    // Map the page fields
    protected $_dataFields = array(
        array(
            'field' => 'miWebFormWidgetText',
            'data' => 'UserLogin',
        ),
        array(
            'field' => 'WebFormWidgetCreatePassword',    // Use custom widget class
            'data' => 'UserPassword',
        )
    );
       
    protected function createDomainObject()
    {
        // Get the miRecord object from the data manager
        $record = new miSqlRecord('Users', 'UserID');
        // Just create the custom domain object and return the control to the data manager
        return new UserCreateDomainObject($record);
    }
}
