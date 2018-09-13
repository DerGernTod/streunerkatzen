<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;

class HairColor extends DataObject {
    private static $table_name = 'fields_HairColor';
    private static $db = [
        'Name' => 'Varchar(50)'
    ];
}
