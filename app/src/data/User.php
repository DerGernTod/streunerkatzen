<?php
namespace Streunerkatzen;

use Streunerkatzen\Cat;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;

class User extends DataObject {
    private static $singular_name = 'Benutzer';
    private static $plural_name = 'Benutzer';
    private static $table_name = 'data_Users';

    private static $db = [
        'FirstName' => 'Varchar(250)',
        'LastName' => 'Varchar(250)',
        'Street' => 'Varchar(250)',
        'HouseNumber' => 'Varchar(25)',
        'Town' => 'Varchar(250)',
        'Zipcode' => 'Int',
        'Country' => 'Varchar(250)',
        'PhoneNumber' => 'Varchar(250)'
    ];

    private static $has_many = [
        'ReportedCats' => 'Streunerkatzen\\Cat.Reporter',
        'OwnedCats' => 'Streunerkatzen\\Cat.Owner'
    ];

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $reportedCatsGridField = GridField::create(
            'ReportedCats',
            'Gemeldete Katzen',
            $this->ReportedCats(),
            GridFieldConfig_RelationEditor::create()
        );

        $fields->addFieldToTab(
            'Root.ReportedCats',
            $reportedCatsGridField
        );

        return $fields;
    }
}
