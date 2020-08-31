<?php

namespace Streunerkatzen;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\TextareaField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\NumericField;

class SidebarConfigExtension extends DataExtension {
    private static $singular_name = 'Sidebar Einstellungen';
    private static $table_name = 'Streunerkatzen_SidebarConfig';

    private static $db = [
        "NumBlogArticles" => "Int",
        "SidebarText" => "HTMLText",
        "DonateButtonLabel" => "Text"
    ];

    public function updateCMSFields(FieldList $fields) {

        $fields->addFieldsToTab('Root.Sidebar', [
            NumericField::create("NumBlogArticles", "Anzahl Blogartikel"),
            TextField::create('DonateButtonLabel', 'Spendenbutton Text'),
            HTMLEditorField::create("SidebarText", "Sidebar Text")
        ]);

        return $fields;
    }
}
