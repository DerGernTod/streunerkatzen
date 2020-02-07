<?php
namespace Streunerkatzen;

use Exception;
use Streunerkatzen\Constants;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_URLHandler;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Security\RandomGenerator;
use SilverStripe\UserForms\Model\EditableFormField\EditableOption;

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
    private $textArea;

    //TargetFragment is just for positioning control of the HTML fragment
    public function __construct(string $targetFragment = "after", string $targetStatus, DataObject $sourceData, TextareaField $textArea = null) {
        $this->targetFragment = $targetFragment;
        $this->targetStatus = $targetStatus;
        $this->actionName = 'state-transition-' . strtolower($targetStatus);
        $this->sourceData = $sourceData;
        $this->textArea = $textArea;
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
            return $this->handleButtonAction($data);
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
                $colorOptions = EditableOption::get()->filter(array('Title' => $colors));
                foreach ($colorOptions as $colorOption) {
                    $cat->HairColors()->Add($colorOption);
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
        $searchAgents = SearchAgent::get();
        foreach($searchAgents as $searchAgent) {
            $filter = json_decode($searchAgent->Filter, true);
            $filter["ID"] = $cat->ID;
            $filteredCats = Cat::get()->filter($filter);
            // the filter matches, so we send a notification!
            if (count($filteredCats) == 1) {
                // TODO: create url for unsubscribe (see CatSearchPageController.unsubscribe)
                // TODO: find correct absolute url: /vermisst-und-gefunden/katzensuche/view/ID
                EmailHelper::sendSearchAgentNotificationMail($searchAgent->Email, $cat, "the url to the cat", "the url to the unsubscribe token");
            }
        }
    }

    //Handle the custom action, for both the action button and the URL
    public function handleButtonAction($submittedData) {
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
        $this->sourceData->ReviewMessage = $submittedData["ReviewMessage"];
        $token = (new RandomGenerator())->randomToken();
        $this->sourceData->EditToken = $token;
        $this->sourceData->write();
        if ($this->targetStatus == Constants::CAT_STATUS_IN_REVIEW) {
            try {
                $mailFields = $values->filter(array('Name' => ['CatField_Contact', 'CatField_Title']));
                foreach($mailFields as $field) {
                    switch($field->Name) {
                        case 'CatField_Contact':
                        $address = $field->Value;
                        break;
                        case 'CatField_Title':
                        $catName = $field->Value;
                        break;
                    }
                }
                $address = 'admin@localhost';
                EmailHelper::sendReviewMail(
                    $this->sourceData->Parent()->AbsoluteLink()."?token=".$token,
                    $catName,
                    $this->sourceData->Parent()->CatReviewTemplate,
                    $$submittedData["ReviewMessage"],
                    $address);
            } catch (Exception $e) {
                var_dump($e);
                Controller::curr()->getResponse()->setStatusCode(200, utf8_decode("Status geändert auf '$this->targetStatus'. Beachte, dass keine Email versandt wurde!"));
                return;
            }
        }
        Controller::curr()->getResponse()->setStatusCode(200, utf8_decode("Status geändert auf '$this->targetStatus'"));
    }
}

