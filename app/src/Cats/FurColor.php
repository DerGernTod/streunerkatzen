<?php

namespace Streunerkatzen\Cats;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;

class FurColor extends DataObject {

    private static $singular_name = 'Fellfarbe';
    private static $plural_name = 'Fellfarben';
    private static $table_name = 'Streunerkatzen_FurColors';

    private static $db = [
        'Title' => 'Varchar(250)'
    ];

    private static $many_many = [
        'Cat' => Cat::class,
        'ExampleImages' => Image::class
    ];

    private static $owns = [
        'ExampleImages'
    ];

    private static $summary_fields = [
        'Title' => 'Beschreibung',
        'NumExampleImages' => 'Beispielbilder'
    ];

    public function getNumExampleImages() {
        if ($this->ExampleImages()->Count() == 1) {
            return "1 Beispielbild";
        } else {
            return $this->ExampleImages()->Count() . " Beispielbilder";
        }
    }

    public function getCMSFields() {
        $fields = FieldList::create(
            TextField::create('Title', 'Beschreibung'),
            $exampleImgs = UploadField::create('ExampleImages', 'Beispielbilder')
        );

        $exampleImgs->setFolderName('Fellfarben');

        return $fields;
    }
}
