<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;

class HairLength extends DataObject {
    private static $table_name = 'fields_HairLength';
    private static $db = [
        'Name' => 'Varchar(50)'
    ];
}
