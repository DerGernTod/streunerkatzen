<?php
namespace Streunerkatzen\Extensions;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataExtension;
use SilverStripe\SiteConfig\SiteConfig;

class BaseElementExtension extends DataExtension {
    public function updateCMSEditLink(&$link): void {
        if (!$this->owner->inlineEditable()) {
            $page = $this->owner->getPage();

            if (!$page || $page instanceof SiteTree) {
                return;
            }

            if ($page instanceof SiteConfig) {
                $link = Controller::join_links(
                    $page->CMSEditLink(),
                    'EditForm',
                    'field',
                    'ElementalArea',
                    'item',
                    $this->owner->ID,
                    'edit'
                );

                return;
            }

            // As non-page DataObject's are managed via GridFields, we have to grab their CMS edit URL
            // and replace the trailing /edit/ with a link to the nested ElementalArea edit form
            $link = Controller::join_links(
                $page->CMSEditLink(),
                'ItemEditForm',
                'field',
                'ElementalArea',
                'item',
                $this->owner->ID,
                'edit'
            );
        }
    }
}
