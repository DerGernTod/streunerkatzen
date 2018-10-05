<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;

class LostFoundTime extends DataObject {
    private static $singular_name = 'Zeitpunkt';
    private static $plural_name = 'Zeitpunkte';
    private static $table_name = 'fields_LostFoundTime';
    private static $db = [
        'Name' => 'Varchar(50)'
    ];
}
