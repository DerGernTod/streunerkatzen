<?php
namespace Streunerkatzen\Extensions;

use SilverStripe\UserForms\Model\UserDefinedForm;

class CatUserDefinedForm extends UserDefinedForm {
    private static $table_name = 'Streunerkatzen_CatUserDefinedForm';
    private static $description = 'Erstellt ein Katzen-Formular.';
}
