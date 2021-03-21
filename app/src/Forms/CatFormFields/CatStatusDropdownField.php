<?php
namespace Streunerkatzen\Forms\CatFormFields;

use Streunerkatzen\Cats\Cat;

class CatStatusDropdownField extends BaseCatDataDropdownField {
    private static $singular_name = 'Katzen Status Dropdown';
    private static $plural_name = 'Katzen Status Dropdowns';
    private static $table_name = 'Streunerkatzen_CatStatusDropdownFields';

    protected function getData() {
        return singleton(Cat::class)->dbObject('LostFoundStatus')->enumValues();
    }
}
