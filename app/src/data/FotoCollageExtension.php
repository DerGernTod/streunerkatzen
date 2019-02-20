<?php

namespace Streunerkatzen;

use SilverStripe\Assets\Image;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use Streunerkatzen\FotoCollageExtension;
use SilverStripe\AssetAdmin\Forms\UploadField;

class FotoCollageExtension extends DataExtension {
    private const ALLOWED_FILE_ENDINGS = ['jpg', 'jpeg', 'png', 'gif'];
    private static $singular_name = 'Fotocollage';
    private static $plural_name = 'Fotocollagen';
    private static $table_name = 'Streunerkatzen_FotoCollage';

    private static $has_one = [
        "LogoImage" => Image::class
    ];

    private static $many_many = [
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
            ->setAllowedExtensions(FotoCollageExtension::ALLOWED_FILE_ENDINGS);
        $collageImages
            ->setFolderName('CollageImages')
            ->getValidator()
            ->setAllowedExtensions(FotoCollageExtension::ALLOWED_FILE_ENDINGS);

        return $fields;
    }

    public function getShuffledCollage() {
        $newArray = $this->owner->CollageImages()->sort("RAND()");
        $result = ArrayList::create();
        $hasLogo = false;
        foreach ($newArray as $image) {
            $result->push($image);
            if (!$hasLogo) {
                $hasLogo = true;
                $result->push($this->owner->LogoImage());
            }
        }
        return $result;
    }
}
