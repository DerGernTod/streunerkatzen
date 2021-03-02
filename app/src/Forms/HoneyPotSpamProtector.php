<?php
namespace Streunerkatzen\Forms;

use SilverStripe\SpamProtection\SpamProtector;

class HoneyPotSpamProtector implements SpamProtector {

    public function getFormField($name = "hp-mail", $title = "HP Mail", $value = null) {
        if (strcmp($name, "Captcha") == 0) {
            $name = "hp-mail";
        }
        return new HoneyPotField($name, $title, $value);
    }

    public function setFieldMapping($fieldMapping) {
    }
}
