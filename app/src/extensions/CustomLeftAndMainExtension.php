<?php

namespace Streunerkatzen;

use SilverStripe\Admin\CMSMenu;
use SilverStripe\Admin\LeftAndMainExtension;

class CustomLeftAndMainExtension extends LeftAndMainExtension {
    public function init() {
        $id = "CatSubmissionsLink";
        $title = "Katzen - Einreichungen";
        $link = "admin/pages/edit/show/33#Root_Submissions";
        $priority = 1;     // lower number --> lower in the list
        $attributes = [
            'target' => '_self'
        ];

        CMSMenu::add_link($id, $title, $link, $priority, $attributes);
    }
}
