<?php

namespace Streunerkatzen;

use SilverStripe\Admin\ModelAdmin;

class FieldAdmin extends ModelAdmin {

    private static $menu_title = 'Dropdowns';

    private static $url_segment = 'fields';

    private static $managed_models = [
        LostFoundTime::class,
        LostFoundStatus::class,
        HairLength::class,
        HairColor::class
    ];

}
