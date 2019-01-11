<?php

namespace Streunerkatzen;

use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class TextComponent extends Component {

    private static $singular_name = 'Textbox';
    private static $plural_name = 'Textboxen';
    private static $table_name = 'Streunerkatzen_TextComponent';

    public function canCreate($member = null, $context = []) {
        return true;
    }

    private static $db = [
        "Content" => 'HTMLText'
    ];

    public function getCMSfields() {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            HtmlEditorField::create('Content', 'Inhalt'),
        ]);

        return $fields;
    }
}
