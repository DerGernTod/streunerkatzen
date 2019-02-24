<?php
namespace Streunerkatzen;

use Psr\Log\LoggerInterface;
use Streunerkatzen\Constants;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\Member;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_URLHandler;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\UserForms\Model\Submission\SubmittedFormField;
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
        $cat->Title = $fields["Name"];
        $cat->PublishTime = date("Y-m-d H:i:s");
        // TODO: uncomment this when date input validation is fixed
        // $cat->LostFoundDate = $fields["Datum"];
        $cat->Gender = $fields["Geschlecht"];
        $cat->IsCastrated = $fields["Kastriert?"];
        $cat->isHouseCat = $fields["Hauskatze?"];
        $cat->Breed = $fields["Rasse"];
        $cat->EyeColor = $fields["Augenfarbe"];
        $cat->BehaviourOwner = $fields["Verhalten gegenüber Besitzer"];
        $cat->BehaviourStranger = $fields["Verhalten gegenüber Fremden"];
        $cat->Street = $fields["Straße"];
        $cat->Country = $fields["Bundesland"];
        $cat->IsChipped = $fields["Gechippt?"];
        $cat->HasPetCollar = $fields["Halsband?"];
        $cat->LostFoundStatus = $fields["Status"];
        $cat->HairColor = $fields["Fellfarbe"];
        $cat->HairLength = $fields["Haarlänge"];
        $cat->LostFoundTime = $fields["Zeit"];
        $cat->Attachments = $fields["Anhänge"];
        $contact = $fields["Kontakt"];
        // search user email
        $matchingMembers = Member::get()->filter(array('Email' => $contact));
        if (count($matchingMembers) === 1) {
            $userId = $matchingMembers[0]->ID;
            if ($fields["Status"] == "Vermisst") {
                $cat->OwnerID = $userId;
            } else {
                $cat->ReporterID = $userId;
            }
        }
        $cat->write();
    }

    //Handle the custom action, for both the action button and the URL
    public function handleButtonAction() {
        $values = $this->sourceData->Values();
        if ($this->targetStatus == Constants::CAT_STATUS_APPROVED) {
            try {
                $fields = [];
                foreach ($values as $id => $field) {
                    $fields[$field->Title] = $field->Value;
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
