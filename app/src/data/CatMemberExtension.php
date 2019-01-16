<?php
namespace Streunerkatzen;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;

class CatMemberExtension extends DataExtension {

    private static $db = [
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

    public function updateCMSFields(FieldList $fields) {
        $reportedCatsGridField = GridField::create(
            'ReportedCats',
            'Gemeldete Katzen',
            $this->owner->ReportedCats(),
            GridFieldConfig_RelationEditor::create()
        );

        $fields->addFieldToTab(
            'Root.ReportedCats',
            $reportedCatsGridField
        );
    }
}
