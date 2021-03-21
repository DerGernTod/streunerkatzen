<?php
namespace Streunerkatzen\Forms\CatFormFields;

use Streunerkatzen\Cats\Cat;

class CatFurLengthDropdownField extends BaseCatDataDropdownField {
    private static $singular_name = 'Katzen Haarlänge Dropdown';
    private static $plural_name = 'Katzen Haarlänge Dropdowns';
    private static $table_name = 'Streunerkatzen_CatFurLengthDropdownFields';

    protected function getData() {
        return singleton(Cat::class)->dbObject('FurLength')->enumValues();
    }
}
