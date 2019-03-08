<?php

namespace Streunerkatzen;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\AssetAdmin\Forms\UploadField;

class HeaderConfigExtension extends DataExtension {
    private const ALLOWED_FILE_ENDINGS = ['jpg', 'jpeg', 'png', 'gif'];
    private static $singular_name = 'Header Einstellungen';
    private static $table_name = 'Streunerkatzen_HeaderConfig';

    private static $has_one = [
        "LogoImage" => Image::class
    ];

    private static $has_many = [
        "CollageImages" => Image::class
    ];

    private static $owns = [
        "LogoImage",
        "CollageImages"
    ];

    public function updateCMSFields(FieldList $fields) {

        $fields->addFieldsToTab('Root.FotoCollage', [
            $logo = UploadField::create('LogoImage', 'Logo'),
            $collageImages = UploadField::create('CollageImages', 'Collage Fotos')
        ]);
        $logo
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
