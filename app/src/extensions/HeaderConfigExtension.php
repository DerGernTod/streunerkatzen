<?php

namespace Streunerkatzen;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\TextareaField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class HeaderConfigExtension extends DataExtension {
    private const ALLOWED_FILE_ENDINGS = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
    private static $singular_name = 'Header Einstellungen';
    private static $table_name = 'Streunerkatzen_HeaderConfig';

    private static $db = [
        "MainText" => "Varchar",
        "SubText" => "Text"
    ];

    private static $has_one = [
        "LogoImage" => Image::class,
        "HeaderImage" => Image::class
    ];

    private static $owns = [
        "LogoImage",
        "HeaderImage"
    ];

    public function updateCMSFields(FieldList $fields) {

        $fields->addFieldsToTab('Root.Header', [
            TextField::create('MainText', 'Seitentitel'),
            TextareaField::create('SubText', 'Untertitel'),
            $logo = UploadField::create('LogoImage', 'Logo'),
            $header = UploadField::create('HeaderImage', 'Headerbild')
        ]);
        $logo
            ->setFolderName('HeaderImages')
            ->getValidator()
            ->setAllowedExtensions(HeaderConfigExtension::ALLOWED_FILE_ENDINGS);
        $header
            ->setFolderName('HeaderImages')
            ->getValidator()
            ->setAllowedExtensions(HeaderConfigExtension::ALLOWED_FILE_ENDINGS);

        return $fields;
    }
}
