<?php

namespace Streunerkatzen;

use SilverStripe\ORM\DataExtension;
use SilverStripe\SiteConfig\SiteConfig;

class GalleryImageExtension extends DataExtension {
    private static $has_one = [
        "GalleryImage" => SiteConfig::class
    ];
}
