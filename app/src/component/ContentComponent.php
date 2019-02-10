<?php

namespace Streunerkatzen;

use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class ContentComponent extends Component {

    private static $singular_name = 'Contentbox';
    private static $plural_name = 'Contentboxen';
    private static $table_name = 'Streunerkatzen_ContentComponent';

    public function canCreate($member = null, $context = []) {
        return true;
    }

    private static $db = [
        'Content' => 'HTMLText'
    ];

    public function getCMSfields() {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            HtmlEditorField::create('Content', 'Inhalt'),
        ]);

        return $fields;
    }
}
