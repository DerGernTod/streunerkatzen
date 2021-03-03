<?php
namespace Streunerkatzen\Extensions;

use DNADesign\Elemental\Extensions\ElementalPageExtension;
use DNADesign\Elemental\Forms\ElementalAreaField;
use DNADesign\Elemental\Models\ElementalArea;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\SiteConfig\SiteConfig;
use Streunerkatzen\Cats\Cat;

class SidebarConfigExtension extends ElementalPageExtension {
    private static $singular_name = 'Sidebar Einstellungen';
    private static $table_name = 'Streunerkatzen_SidebarConfig';

    public function CanView($member = null) {
        return true;
    }

    public function updateCMSFields(FieldList $fields) {
        $fields->addFieldsToTab('Root.Sidebar', [
            ElementalAreaField::create('ElementalArea', $this->owner->ElementalArea(), $this->owner->getElementalTypes())
        ]);

        return $fields;
    }

    public function CMSEditLink() {
        return "test";
    }

    public function Link() {
        return '/';
    }

    public function canArchive($member = null) {
        return true;
    }

    // public function getElementalArea() {
    //     $elementalArea = ElementalArea::get();

    //     $count = $elementalArea->count();

    //     return $count;
    // }
}
