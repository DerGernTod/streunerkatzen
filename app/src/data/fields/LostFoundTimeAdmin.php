<?php

namespace Streunerkatzen;

use SilverStripe\Admin\ModelAdmin;

class LostFoundTimeAdmin extends ModelAdmin {

    private static $menu_title = 'Tageszeit';

    private static $url_segment = 'lostfoundtime';

    private static $managed_models = [
        LostFoundTime::class,
    ];

}
