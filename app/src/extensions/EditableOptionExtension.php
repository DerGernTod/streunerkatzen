<?php
namespace Streunerkatzen;

use Streunerkatzen\Cat;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataExtension;

class EditableOptionExtension extends DataExtension {
    private static $many_many = [
        'Cats' => Cat::class,
        'Examples' => File::class
    ];

    private static $owns = [
        'Examples'
    ];
}
