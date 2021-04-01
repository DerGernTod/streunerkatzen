<?php
namespace Streunerkatzen\Extensions;

use SilverStripe\Assets\Folder;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ValidationResult;
use Streunerkatzen\Cats\Cat;
use Streunerkatzen\Cats\FurColor;
use Streunerkatzen\Constants;
use Streunerkatzen\Elements\CatElementForm;

class CatSubmittedFormExtension extends DataExtension {
    private static $db = [
        'CatFormSubmissionStatus' => 'Enum("'.
            Constants::CAT_STATUS_NEW.','.
            Constants::CAT_STATUS_EDITED.','.
            Constants::CAT_STATUS_IN_REVIEW.','.
            Constants::CAT_STATUS_REJECTED.','.
            Constants::CAT_STATUS_APPROVED.'")',
        'ReviewMessage' => 'Text',
        'EditToken' => 'Varchar'
    ];

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields) {
        if (strcmp($this->owner->ParentClass, CatElementForm::class) == 0) {
            $fields->replaceField('CatFormSubmissionStatus', ReadonlyField::create('CatFormSubmissionStatus', 'Status'));
            $fields->removeByName("EditToken");
            $fields->removeByName("ReviewMessage");

            $config = GridFieldConfig::create();
            $config->addComponent(new GridFieldDataColumns());
            $fields->dataFieldByName('Values')->setConfig($config);

            switch ($this->owner->CatFormSubmissionStatus) {
                case Constants::CAT_STATUS_NEW:
                case Constants::CAT_STATUS_EDITED:
                    $this->addReviewMsg($fields);
                    break;
                case Constants::CAT_STATUS_IN_REVIEW:
                    $this->addEditToken($fields);
                    $this->addReviewMsg($fields);
                    break;
                case Constants::CAT_STATUS_REJECTED:
                case Constants::CAT_STATUS_APPROVED:
                    break;
                default:
                    break;
            }
        } else {
            $fields->removeByName("CatFormSubmissionStatus");
            $fields->removeByName("ReviewMessage");
            $fields->removeByName("EditToken");
        }
        return $fields;
    }

    public function updateCMSActions(FieldList $actions) {
        switch ($this->owner->CatFormSubmissionStatus) {
            case Constants::CAT_STATUS_NEW:
            case Constants::CAT_STATUS_EDITED:
            case Constants::CAT_STATUS_IN_REVIEW:
                $actions->insertAfter(
                    'action_doDelete',
                    $this->getApproveAction()
                );
                $actions->insertAfter(
                    'action_doApproveCat',
                    $this->getRequestChangeAction()
                );
                $actions->insertAfter(
                    'action_doRequestChange',
                    $this->getRejectAction()
                );
                break;
            case Constants::CAT_STATUS_REJECTED:
                $actions->insertAfter(
                    'action_doDelete',
                    $this->getRestoreAction()
                );
                $actions->removeByName('MajorActions');
                break;
            case Constants::CAT_STATUS_APPROVED:
                $actions->removeByName('MajorActions');
                break;
            default:
                break;
        }
    }

    private function addEditToken(FieldList $fields) {
        $fields->insertAfter(
            'CatFormSubmissionStatus',
            ReadonlyField::create('EditToken', 'Token')
        );
    }

    private function addReviewMsg(FieldList $fields) {
        $fields->insertAfter(
            'Submitter',
            TextareaField::create(
                'ReviewMessage',
                'Nachricht an den Verfasser des Eintrags:'
            )
        );
    }

    private function getApproveAction() {
        return FormAction::create('doApproveCat', 'Annehmen')
            ->setUseButtonTag(true)
            ->addExtraClass('btn font-icon-check-mark-2 btn-primary');
    }

    private function getRejectAction() {
        return FormAction::create('doRejectCat', 'Ablehnen')
            ->setUseButtonTag(true)
            ->addExtraClass('btn font-icon-cross-mark btn-danger');
    }

    private function getRequestChangeAction() {
        return FormAction::create('doRequestChange', 'Bearbeitung anfordern')
            ->setUseButtonTag(true)
            ->addExtraClass('btn font-icon-comment btn-warning');
    }

    private function getRestoreAction() {
        return FormAction::create('doRestoreCat', 'Wiederherstellen')
            ->setUseButtonTag(true)
            ->addExtraClass('btn btn-primary');
    }

    public function approveCat($data, $form, $itemRequestExt) {
        $this->owner->CatFormSubmissionStatus = Constants::CAT_STATUS_APPROVED;
        $this->owner->write();

        try {
            $catLink = $this->createCat();
        } catch (\Exception $e) {
            $message = "Katze konnte nicht eingetragen werden";
            $form->sessionMessage($message, 'bad', ValidationResult::CAST_HTML);
            $controller = $itemRequestExt->getToplevelController();
            return $itemRequestExt->owner->edit($controller->getRequest());
        }

        $message = "Eintrag angenommen: Falls nötig können die Daten vor der Veröffentlichung noch bearbeitet werden.";
        $form->sessionMessage($message, 'good', ValidationResult::CAST_HTML);
        $controller = $itemRequestExt->getToplevelController();
        return $controller->redirect($catLink);
    }

    public function rejectCat($data, $form, $itemRequestExt) {
        $this->owner->CatFormSubmissionStatus = Constants::CAT_STATUS_REJECTED;
        $this->owner->write();

        $message = "Eintrag wurde abgelehnt";
        $form->sessionMessage($message, 'good', ValidationResult::CAST_HTML);

        $controller = $itemRequestExt->getToplevelController();
        return $itemRequestExt->owner->edit($controller->getRequest());
    }

    public function requestChange($data, $form, $itemRequestExt) {
        return "Requested Change";
    }

    public function restoreCat($data, $form, $itemRequestExt) {
        $this->owner->CatFormSubmissionStatus = Constants::CAT_STATUS_NEW;
        $this->owner->write();

        $message = "Eintrag wurde wiederhergestellt";
        $form->sessionMessage($message, 'good', ValidationResult::CAST_HTML);

        $controller = $itemRequestExt->getToplevelController();
        return $itemRequestExt->owner->edit($controller->getRequest());
    }

    private function createCat() {
        $formFields = $this->owner->Values();
        $cat = Cat::create();

        foreach ($formFields as $field) {
            $name = $field->Name;

            // files
            if (substr($name, 0, strlen('EditableFileField')) === 'EditableFileField') {
                $file = $field->UploadedFile();
                $fileFolder = Folder::get()
                    ->filter('Name', 'Katzen');
                $file->ParentID = $fileFolder->ID;
                $file->write();

                $cat->Attachments()->add($file);
            // fed state
            } elseif (substr($name, 0, strlen('EditableFedStateField')) === 'EditableFedStateField') {
                $cat->Country = $field->Value;
            // fur colors
            } elseif (substr($name, 0, strlen('CatFurColorMultiSelectField')) === 'CatFurColorMultiSelectField') {
                $furColors = explode(",", $field->Value);
                foreach ($furColors as $color) {
                    $color = trim($color);
                    $furColorObj = FurColor::get()
                        ->filter('Title:ExactMatch', $color);
                    if ($furColorObj->count() > 0) {
                        $cat->FurColors()->add($furColorObj->offsetGet(0));
                    }
                }
            // other data
            } else {
                $catKey = substr($field->Name, 3);
                $catKey = substr($catKey, 0, strpos($catKey, '_'));
                $catKey = str_replace([
                    'Field',
                    'Dropdown',
                    'Text',
                    'Numeric',
                ], '', $catKey);

                $cat->$catKey = $field->Value;
            }
        }

        // publish time from form submit
        $cat->PublishTime = $this->owner->Created;

        $cat->write();

        return $cat->CMSEditLink();
    }
}
