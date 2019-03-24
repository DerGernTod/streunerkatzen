<?php

namespace Streunerkatzen;

use SilverStripe\Admin\ModelAdmin;

class BlogAdmin extends ModelAdmin {

    private static $menu_title = 'Blogartikel';

    private static $url_segment = 'blogarticles';

    private static $managed_models = [
        BlogArticle::class,
        BlogArticleCategory::class
    ];
}
