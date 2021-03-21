<?php
namespace Streunerkatzen\Forms\CatFormFields;

use Streunerkatzen\Cats\Cat;

class CatCastratedDropdownField extends BaseCatDataDropdownField {
    private static $singular_name = 'Katzen Kastriert Dropdown';
    private static $plural_name = 'Katzen Kastriert Dropdowns';
    private static $table_name = 'Streunerkatzen_CatCastratedDropdownFields';

    protected function getData() {
        return singleton(Cat::class)->dbObject('IsCastrated')->enumValues();
    }
}
