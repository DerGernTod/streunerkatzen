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

    private static $has_many = [
        "CollageImages" => Image::class
    ];

    private static $owns = [
        "LogoImage",
        "HeaderImage",
        "CollageImages"
    ];

    public function updateCMSFields(FieldList $fields) {

        $fields->addFieldsToTab('Root.Streunerkatzen', [
            TextField::create('MainText', 'Seitentitel'),
            TextareaField::create('SubText', 'Untertitel'),
            $logo = UploadField::create('LogoImage', 'Logo'),
            $header = UploadField::create('HeaderImage', 'Headerbild'),
            $collageImages = UploadField::create('CollageImages', 'Collage Fotos')
        ]);
        $logo
            ->setFolderName('CollageImages')
            ->getValidator()
            ->setAllowedExtensions(HeaderConfigExtension::ALLOWED_FILE_ENDINGS);
        $header
            ->setFolderName('CollageImages')
            ->getValidator()
            ->setAllowedExtensions(HeaderConfigExtension::ALLOWED_FILE_ENDINGS);
        $collageImages
            ->setFolderName('CollageImages')
            ->getValidator()
            ->setAllowedExtensions(HeaderConfigExtension::ALLOWED_FILE_ENDINGS);

        return $fields;
    }

    public function getShuffledCollage() {
        return $this->owner->CollageImages()->sort("RAND()")->limit(10);
    }
}
