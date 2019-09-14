<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataExtension;

class EditableOptionExtension extends DataExtension {
    private static $many_many = [
        'Cats' => Cat::class
    ];
}
