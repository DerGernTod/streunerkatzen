<?php
namespace Streunerkatzen;

use Page;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class CatSearchPage extends Page {
    private static $singular_name = 'Katzensuche-Seite';
    private static $plural_name = 'Katzensuche-Seiten';
    private static $table_name = 'Streunerkatzen_CatSearchPage';

    private static $db = [
        'NotificationEmailTemplate' => 'HTMLText',
        'NotificationTemplate' => 'HTMLText'
    ];

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->add(HTMLEditorField::create('NotificationTemplate', 'Vorlage für Suchagent'));
        $fields->add(HTMLEditorField::create('NotificationEmailTemplate', 'Vorlage für Suchagent E-Mail'));
        return $fields;
    }

    public function getStrippedNotificationTemplate() {
        return strip_tags($this->NotificationTemplate);
    }
}
