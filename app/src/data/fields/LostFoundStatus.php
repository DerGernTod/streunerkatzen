<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;

class LostFoundStatus extends DataObject {

    private static $singular_name = 'Fundstatus';
    private static $plural_name = 'Fundstatus';
    private static $table_name = 'fields_LostFoundStatus';
    private static $db = [
        'Name' => 'Varchar(50)'
    ];
}
