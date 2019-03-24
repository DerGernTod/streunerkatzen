<?php

namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;


class BlogArticleCategory extends DataObject {
    private static $singular_name = 'Kategorie';
    private static $plural_name = 'Kategorien';
    private static $table_name = 'Streunerkatzen_BlogArticleCategories';

    private static $db = array(
        "Title" => "Varchar"
    );

    private static $belongs_many_many = array(
        "Articles" => BlogArticle::class
    );
}
