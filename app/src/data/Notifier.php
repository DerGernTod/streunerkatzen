<?php

namespace Streunerkatzen;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;

class Notifier extends DataObject {
    private static $db = [
        'NextReminder' => 'Date'
    ];
    private static $has_one = [
        'Cat' => Cat::class
    ];

    private static $singular_name = 'Notifier';
    private static $plural_name = 'Notifier';
    private static $table_name = 'Streunerkatzen_Notifier';
}
?>
