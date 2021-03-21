<?php
namespace Streunerkatzen\Forms\CatFormFields;

use Streunerkatzen\Cats\Cat;

class CatLostFoundTimeDropdownField extends BaseCatDataDropdownField {
    private static $singular_name = 'Katzen Zeitpunkt Dropdown';
    private static $plural_name = 'Katzen Zeitpunkt Dropdowns';
    private static $table_name = 'Streunerkatzen_CatLostFoundTimeDropdownFields';

    protected function getData() {
        return singleton(Cat::class)->dbObject('LostFoundTime')->enumValues();
    }
}
