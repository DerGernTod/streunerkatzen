<?php
namespace Streunerkatzen;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;

class CatUserFormSubmissionExtension extends DataExtension {
    private static $db = [
        'ActivationStatus' => 'Enum("'
            .Constants::CAT_STATUS_NEW.','
            .Constants::CAT_STATUS_IN_REVIEW.','
            .Constants::CAT_STATUS_EDITED.','
            .Constants::CAT_STATUS_REJECTED.','
            .Constants::CAT_STATUS_APPROVED.'")',
        'ReviewMessage' => 'Varchar(1000)',
        'EditToken' => 'Varchar(128)'
    ];

    public function updateCMSFields(FieldList $fields) {
        if ($this->owner->Parent()->IsCatEntryForm) {
            $activationStatus = $this->owner->ActivationStatus;
            $fields->replaceField('ActivationStatus',
                ReadonlyField::create('ActivationStatus', 'Status', $activationStatus));
            $fields->removeByName('ReviewMessage');
            $targetFieldForButtons = $fields->dataFieldByName('Values');
            $config = GridFieldConfig::create();
            $config->addComponent(new GridFieldDataColumns());
            $config->addComponent(new GridFieldButtonRow('after'));
            $addRequestReviewButton = true;
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
                $textArea = TextareaField::create(
                    'ReviewMessage',
                    'Nachricht an Verfasser',
                    'Bitte die folgenden Punkte ergänzen:');
                $fields->insertAfter('Values', $textArea);
                $config->addComponent(new GridFieldStatusChangeButton('after', Constants::CAT_STATUS_IN_REVIEW, $this->owner, $textArea));
            }
            if ($addRejectButton) {
                $config->addComponent(new GridFieldStatusChangeButton('after', Constants::CAT_STATUS_REJECTED, $this->owner));
            }
            $targetFieldForButtons->setConfig($config);
        }
    }
}
