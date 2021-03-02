<?php

namespace Streunerkatzen\Cats;

use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;

class Notifier extends DataObject {
    private static $db = [
        'NextReminder' => 'Date',
        'EditToken' => 'Varchar'
    ];
    private static $belongs_to = [
        'Cat' => Cat::class.".Notifier"
    ];

    private static $singular_name = 'Notifier';
    private static $plural_name = 'Notifier';
    private static $table_name = 'Streunerkatzen_Notifier';

    public function ConfigureURL() {
        return Director::absoluteBaseURL()."notifications/configure?token=".$this->EditToken;
    }
}
?>
