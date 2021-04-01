<?php
namespace Streunerkatzen\Forms\CatFormFields;

use Streunerkatzen\Cats\Cat;

class CatIsHouseCatDropdownField extends BaseCatDataDropdownField {
    private static $singular_name = 'Katzen Hauskatze Dropdown';
    private static $plural_name = 'Katzen Hauskatze Dropdowns';
    private static $table_name = 'Streunerkatzen_CatIsHouseCatDropdownFields';

    protected function getData() {
        return singleton(Cat::class)->dbObject('IsHouseCat')->enumValues();
    }
}
