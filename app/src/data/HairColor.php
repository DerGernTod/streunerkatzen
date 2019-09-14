<?php

namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;

class HairColor extends DataObject {

    private static $singular_name = 'Fellfarbe';
    private static $plural_name = 'Fellfarben';
    private static $table_name = 'Streunerkatzen_HairColors';

    private static $db = [
        "Title" => "Varchar(250)"
    ];

    private static $many_many = [
        "Cat" => Cat::class
    ];

}
