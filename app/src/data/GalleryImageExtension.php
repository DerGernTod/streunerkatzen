<?php

namespace Streunerkatzen;

use SilverStripe\ORM\DataExtension;

class GalleryImageExtension extends DataExtension {
    private static $has_many = [
        "LogoImageFotoCollage" => "Streunerkatzen\\FotoCollageExtension.LogoImage",
        "CollageImagesFotoCollage" => "Streunerkatzen\\FotoCollageExtension.CollageImages"
    ];
}
