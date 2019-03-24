<?php

namespace Streunerkatzen;

use SilverStripe\Forms\DropdownField;

class SingleCatComponent extends Component {

    private static $singular_name = 'Katzenbox';
    private static $plural_name = 'Katzenboxen';
    private static $table_name = 'Streunerkatzen_SingleCatComponent';

    public function canCreate($member = null, $context = []) {
        return true;
    }

    private static $has_one = [
        'Cat' => Cat::class
    ];

    public function getCMSfields() {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            DropdownField::create('CatID', 'Katze', Cat::get()->map('ID', 'Title'))
        ]);

        return $fields;
    }
}
