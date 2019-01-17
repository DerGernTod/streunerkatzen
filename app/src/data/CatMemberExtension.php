<?php
namespace Streunerkatzen;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;

class CatMemberExtension extends DataExtension {

    private const REPORTED_CATS_LABEL = 'Gemeldete Katzen';
    private const OWNED_CATS_LABEL = 'Eigene Katzen';
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
            CatMemberExtension::REPORTED_CATS_LABEL,
            $this->owner->ReportedCats(),
            GridFieldConfig_RelationEditor::create()
        );

        $fields->addFieldToTab(
            'Root.ReportedCats',
            $reportedCatsGridField
        );

        $ownedCatsGridField = GridField::create(
            'OwnedCats',
            CatMemberExtension::OWNED_CATS_LABEL,
            $this->owner->OwnedCats(),
            GridFieldConfig_RelationEditor::create()
        );

        $fields->addFieldToTab(
            'Root.OwnedCats',
            $ownedCatsGridField
        );

        $fields->findOrMakeTab('Root.OwnedCats')->Title = CatMemberExtension::OWNED_CATS_LABEL;
        $fields->findOrMakeTab('Root.ReportedCats')->Title = CatMemberExtension::REPORTED_CATS_LABEL;
        $fields->addFieldsToTab('Root.Main', array(
            TextField::create('Street', 'Straße'),
            TextField::create('HouseNumber', 'Hausnummer'),
            TextField::create('Town', 'Ort'),
            TextField::create('Zipcode', 'PLZ'),
            TextField::create('Country', 'Land'),
            TextField::create('PhoneNumber', 'Telefonnummer')
        ), 'Email');
    }
}
