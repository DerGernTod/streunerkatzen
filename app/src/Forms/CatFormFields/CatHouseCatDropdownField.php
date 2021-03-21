<?php
namespace Streunerkatzen\Forms\CatFormFields;

use Streunerkatzen\Cats\Cat;

class CatHouseCatDropdownField extends BaseCatDataDropdownField {
    private static $singular_name = 'Katzen Hauskatze Dropdown';
    private static $plural_name = 'Katzen Hauskatze Dropdowns';
    private static $table_name = 'Streunerkatzen_CatHouseCatDropdownFields';

    protected function getData() {
        return singleton(Cat::class)->dbObject('IsHouseCat')->enumValues();
    }
}
