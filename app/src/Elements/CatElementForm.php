<?php
namespace Streunerkatzen\Elements;

use DNADesign\ElementalUserForms\Model\ElementForm;

class CatElementForm extends ElementForm {
    private static $table_name = 'Streunerkatzen_CatElementForm';
    private static $singular_name = 'Katzenformular';
    private static $plural_name = 'Katzenformulare';

    public function getType() {
        return _t(__CLASS__ . '.BlockType', 'Katzenformular');
    }
}
