<?php

namespace Streunerkatzen;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;

class UserAdmin extends ModelAdmin {

    private static $menu_title = 'Benutzer';

    private static $url_segment = 'users';

    private static $managed_models = [
        User::class,
    ];

}
