<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;

class LostFoundStatus extends DataObject {
    private static $table_name = 'fields_LostFoundStatus';
    private static $db = [
        'Name' => 'Varchar(50)'
    ];
}
