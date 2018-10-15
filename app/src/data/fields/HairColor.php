<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;

class HairColor extends DataObject {
    private static $table_name = 'fields_HairColor';
    private static $singular_name = 'Fellfarbe';
    private static $plural_name = 'Fellfarben';
    private static $db = [
        'Name' => 'Varchar(50)'
    ];
}
