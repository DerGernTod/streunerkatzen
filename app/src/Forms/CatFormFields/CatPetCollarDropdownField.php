<?php
namespace Streunerkatzen\Forms\CatFormFields;

use Streunerkatzen\Cats\Cat;

class CatPetCollarDropdownField extends BaseCatDataDropdownField {
    private static $singular_name = 'Katzen Halsband Dropdown';
    private static $plural_name = 'Katzen Halsband Dropdowns';
    private static $table_name = 'Streunerkatzen_CatPetCollarDropdownFields';

    protected function getData() {
        return singleton(Cat::class)->dbObject('HasPetCollar')->enumValues();
    }
}
