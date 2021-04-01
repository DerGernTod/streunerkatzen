<?php
namespace Streunerkatzen\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\ORM\DataExtension;
use SilverStripe\UserForms\Model\Submission\SubmittedForm;
use Streunerkatzen\Elements\CatElementForm;

class GridFieldDetailForm_ItemRequestExtension extends DataExtension {
    public function updateFormActions(FieldList $actions) {
        $record = $this->owner->getRecord();

        // only change for submitted cat forms
        if (!$record instanceof SubmittedForm
            || !$record->exists()
            || strcmp($record->ParentClass, CatElementForm::class) != 0) {
            return;
        }

        $record->updateCMSActions($actions);
    }

    public function getToplevelController() {
        $c = $this->owner->getController();
        while ($c && $c instanceof GridFieldDetailForm_ItemRequest) {
            $c = $c->getController();
        }
        return $c;
    }

    public function doApproveCat($data, $form) {
        return $this->owner->getRecord()->approveCat($data, $form, $this);
    }

    public function doRejectCat($data, $form) {
        return $this->owner->getRecord()->rejectCat($data, $form, $this);
    }

    public function doRequestChange($data, $form) {
        return $this->owner->getRecord()->requestChange($data, $form, $this);
    }

    public function doRestoreCat($data, $form) {
        return $this->owner->getRecord()->restoreCat($data, $form, $this);
    }
}
