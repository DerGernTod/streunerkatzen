<?php

namespace Streunerkatzen;


class NewLineComponent extends Component {

    private static $singular_name = 'Zeilenumbruch';
    private static $plural_name = 'Zeilenumbrüche';
    private static $table_name = 'Streunerkatzen_NewLineComponent';

    public function canCreate($member = null, $context = []) {
        return true;
    }
}
