<?php
namespace Streunerkatzen\Forms\CatFormFields;

use Streunerkatzen\Cats\Cat;

class CatGenderDropdownField extends BaseCatDataDropdownField {
    private static $singular_name = 'Katzen Geschlecht Dropdown';
    private static $plural_name = 'Katzen Geschlecht Dropdowns';
    private static $table_name = 'Streunerkatzen_CatGenderDropdownFields';

    protected function getData() {
        return singleton(Cat::class)->dbObject('Gender')->enumValues();
    }
}
