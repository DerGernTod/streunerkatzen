<?php

namespace Streunerkatzen;

use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverStripe\View\ArrayData;

class PlainPage extends ComponentContainerPage {
    private static $singular_name = 'Standard Seite';
    private static $plural_name = 'Standard Seiten';
    private static $table_name = 'Streunerkatzen_PlainPage';

    public function canCreate($member = null, $context = []) {
        return true;
    }
}
