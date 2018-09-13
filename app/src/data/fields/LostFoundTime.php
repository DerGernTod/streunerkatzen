<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;

class LostFoundTime extends DataObject {
    private static $table_name = 'fields_LostFoundTime';
    private static $db = [
        'Name' => 'Varchar(50)'
    ];
}
