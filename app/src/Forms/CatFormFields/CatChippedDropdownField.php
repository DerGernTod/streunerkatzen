<?php
namespace Streunerkatzen\Forms\CatFormFields;

use Streunerkatzen\Cats\Cat;

class CatChippedDropdownField extends BaseCatDataDropdownField {
    private static $singular_name = 'Katzen Gechippt Dropdown';
    private static $plural_name = 'Katzen Gechippt Dropdowns';
    private static $table_name = 'Streunerkatzen_CatChippedDropdownFields';

    protected function getData() {
        return singleton(Cat::class)->dbObject('IsChipped')->enumValues();
    }
}
