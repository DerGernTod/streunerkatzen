<?php
namespace Streunerkatzen\Forms;

use SilverStripe\Forms\TextField;

class HoneyPotField extends TextField {

    public function __construct($name, $title = null, $value = '', $maxLength = null, $form = null) {
        $this->setAttribute('class', 'hp-mail');
        parent::__construct($name, $title, $value, $maxLength, $form);
    }

    public function validate($validator) {
        if (!(is_null($this->value) || $this->value === '')) {
            $validator->validationError(
                $this->name,
                "Dieses Feld darf nicht ausgefüllt werden",
                "validation"
            );

            return false;
        }

        return true;
    }

    public function FieldHolder($properties = array()) {
        return $this->renderWith('Streunerkatzen\Forms\HoneyPotField_holder');
    }
}
