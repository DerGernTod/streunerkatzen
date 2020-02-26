<?php

namespace Streunerkatzen;

use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class BlogArticleListComponent extends Component {

    private static $singular_name = 'Blogartikelliste';
    private static $plural_name = 'Blogartikellisten';
    private static $table_name = 'Streunerkatzen_BlogArticleListComponent';

    public function canCreate($member = null, $context = []) {
        return true;
    }

    public function getCMSfields() {
        $fields = parent::getCMSFields();
        return $fields;
    }
}
