<?php
namespace Streunerkatzen\Extensions;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataExtension;

class BaseElementExtension extends DataExtension {
    public function updateCMSEditLink(&$link): void
    {
        if (!$this->owner->inlineEditable()) {
            $page = $this->owner->getPage();

            if (!$page || $page instanceof SiteTree) {
                return;
            }

            // As non-page DataObject's are managed via GridFields, we have to grab their CMS edit URL
            // and replace the trailing /edit/ with a link to the nested ElementalArea edit form
            $relationName = $this->owner->getAreaRelationName();
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
