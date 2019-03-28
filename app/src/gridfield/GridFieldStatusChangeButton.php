<?php
namespace Streunerkatzen;

use Streunerkatzen\Constants;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_URLHandler;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Control\Controller;

class GridFieldStatusChangeButton implements GridField_HTMLProvider, GridField_ActionProvider, GridField_URLHandler {

    private const ACCEPT = 'Annehmen';
    private const REJECT = 'Ablehnen';
    private const REQUEST_REVIEW = 'Bearbeitung anfordern';

    private $targetFragment;
    private $targetStatus;
    private $buttonTitle;
    private $actionName;
    private $sourceData;
    private $buttonStyleClass = "btn";

    //TargetFragment is just for positioning control of the HTML fragment
    public function __construct(string $targetFragment = "after", string $targetStatus, DataObject $sourceData) {
        $this->targetFragment = $targetFragment;
        $this->targetStatus = $targetStatus;
        $this->actionName = 'state-transition-' . strtolower($targetStatus);
        $this->sourceData = $sourceData;
        switch ($targetStatus) {
            case Constants::CAT_STATUS_APPROVED:
                $this->buttonTitle = GridFieldStatusChangeButton::ACCEPT;
                $this->buttonStyleClass .= ' font-icon-check-mark-2 btn-primary';
                break;
            case Constants::CAT_STATUS_IN_REVIEW:
                $this->buttonTitle = GridFieldStatusChangeButton::REQUEST_REVIEW;
                $this->buttonStyleClass .= ' font-icon-comment btn-outline-warning';
                break;
            case Constants::CAT_STATUS_REJECTED:
                $this->buttonTitle = GridFieldStatusChangeButton::REJECT;
                $this->buttonStyleClass .= ' font-icon-cross-mark btn-outline-danger';
                break;
            default:
                $this->buttonTitle = $targetStatus;
        }
    }

    //Generate the HTML fragment for the GridField
    public function getHTMLFragments($gridField) {
        $button = new GridField_FormAction(
            $gridField,
            'btn-state-transition-' . $this->targetStatus,
            $this->buttonTitle,
            $this->actionName,
            null
        );
        $button->addExtraClass($this->buttonStyleClass);
        return array(
            $this->targetFragment => $button->Field(),
        );
    }

    public function getActions($gridField) {
        return array($this->actionName);
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        if($actionName == $this->actionName) {
            return $this->handleButtonAction();
        }
    }

    //For accessing the custom action from the URL
    public function getURLHandlers($gridField) {
        return array(
            $this->actionName => 'handleButtonAction',
        );
    }

    public function createCat($fields) {
        $cat = Cat::create();
        $cat->PublishTime = date("Y-m-d H:i:s");
        $catHairColors = [];
        foreach ($fields as $key => $value) {
            // strip away "CatField_"
            $catKey = substr($key, 9);
            if (str_contains($catKey, 'HairColor')) {
                $colors = preg_split('/;/', $value);
                foreach ($colors as $color) {
                    $colorDataObject = HairColor::get()->filter("Title", $color)->first();
                    if (!$colorDataObject) {
                        $colorDataObject = HairColor::create();
                        $colorDataObject->Title = $color;
                        $colorDataObject->write();
                    }
                    ob_start();
                    var_dump($colorDataObject);

                    Injector::inst()->get(LoggerInterface::class)->warning('color data object is '.ob_get_clean());
                    $cat->HairColors()->Add($colorDataObject);
                    $colorDataObject->Cat()->Add($cat);
                }
            }
            if (str_contains($catKey, 'Attachment')) {
                // if it's an attachment field, the value should be a file or empty
                if ($value) {
                    $cat->Attachments()->add($value);
                    $value->publishSingle();
                }
            }
            $cat->$catKey = $value;
        }
        $cat->LostFoundDate = date('Y-m-d H:i:s', strtotime($fields["CatField_LostFoundDate"]));
        $contact = $fields["CatField_Contact"];
        // search user email
        $matchingMembers = Member::get()->filter(array('Email' => $contact));
        if (count($matchingMembers) === 1) {
            $userId = $matchingMembers[0]->ID;
            if ($fields["CatField_LostFoundStatus"] == "Vermisst") {
                $cat->OwnerID = $userId;
            }
            $cat->ReporterID = $userId;
        }
        $cat->write();
    }

    //Handle the custom action, for both the action button and the URL
    public function handleButtonAction() {
        $values = $this->sourceData->Values();
        if ($this->targetStatus == Constants::CAT_STATUS_APPROVED) {
            try {
                $fields = [];
                foreach ($values as $field) {
                    if (str_contains($field->Name, 'Attachment')) {
                        $fields[$field->Name] = $field->UploadedFile();
                    } else {
                        $fields[$field->Name] = $field->Value;
                    }
                }
                $this->createCat($fields);
            } catch (Exception $e) {
                die("Katze konnte nicht eingetragen werden: " . $e->message);
            }
            // this only deletes the content of the grid field, not the submission entry. weird
            // $this->sourceData->delete();
        }
        $this->sourceData->ActivationStatus = $this->targetStatus;
        $this->sourceData->write();
        Controller::curr()->getResponse()->setStatusCode(200, utf8_decode("Status geändert auf '$this->targetStatus'"));
    }
}
