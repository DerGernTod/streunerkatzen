<?php
namespace Streunerkatzen;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldPrintButton;

class CatUserFormSubmissionExtension extends DataExtension {
    private static $db = [
        'ActivationStatus' => 'Enum("'
            .Constants::CAT_STATUS_NEW.','
            .Constants::CAT_STATUS_IN_REVIEW.','
            .Constants::CAT_STATUS_EDITED.','
            .Constants::CAT_STATUS_REJECTED.','
            .Constants::CAT_STATUS_APPROVED.'")'
    ];

    public function updateCMSFields(FieldList $fields) {
        if ($this->owner->Parent()->IsCatEntryForm) {
            $activationStatus = $this->owner->ActivationStatus;
            $fields->replaceField('ActivationStatus',
                ReadonlyField::create('ActivationStatus', 'Status', $activationStatus));
            // unfortunately we can't get the values field because apparently if you remove
            // a field and add it afterwards, it doesn't get an id, and the userforms
            // guys forgot to add it...
            // roottab->main->Values
            $values = $fields->items[0]->children->items[0]->children->items[2];
            $config = GridFieldConfig::create();
            $config->addComponent(new GridFieldDataColumns());
            $config->addComponent(new GridFieldButtonRow('after'));
            $addRequestReviewButton = false;
            $addRejectButton = false;
            $addAcceptButton = false;
            switch ($activationStatus) {
                case Constants::CAT_STATUS_NEW:
                case Constants::CAT_STATUS_EDITED:
                    $addRequestReviewButton = true;
                case Constants::CAT_STATUS_IN_REVIEW:
                    $addRejectButton = true;
                case Constants::CAT_STATUS_REJECTED:
                    $addAcceptButton = true;
                    break;
                case Constants::CAT_STATUS_APPROVED:
                    // this shouldn't be the case since the entry should be removed after aproval...
                    // or should it?
                    break;
                default:
                    break;
            }
            if ($addAcceptButton) {
                $config->addComponent(new GridFieldStatusChangeButton('after', Constants::CAT_STATUS_APPROVED, $this->owner));
            }
            if ($addRequestReviewButton) {
                $config->addComponent(new GridFieldStatusChangeButton('after', Constants::CAT_STATUS_IN_REVIEW, $this->owner));
            }
            if ($addRejectButton) {
                $config->addComponent(new GridFieldStatusChangeButton('after', Constants::CAT_STATUS_REJECTED, $this->owner));
            }
            $values->setConfig($config);
        }
    }
}
