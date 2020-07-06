<?php

namespace Streunerkatzen;

use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;

class Notifier extends DataObject {
    private static $db = [
        'NextReminder' => 'Date',
        'EditToken' => 'Varchar'
    ];
    private static $has_one = [
        'Cat' => Cat::class
    ];

    private static $singular_name = 'Notifier';
    private static $plural_name = 'Notifier';
    private static $table_name = 'Streunerkatzen_Notifier';

    public function ConfigureURL() {
        return Director::absoluteBaseURL()."notifications/configure?token=".$this->EditToken;
    }
}
?>
