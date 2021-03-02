<?php

namespace Streunerkatzen\Cats;

use SilverStripe\Admin\ModelAdmin;

class CatAdmin extends ModelAdmin {

    private static $menu_title = 'Katzen';

    private static $url_segment = 'cats';

    private static $managed_models = [
        Cat::class,
        FurColor::class
    ];
}
