<?php
namespace Streunerkatzen;

use Psr\Log\LoggerInterface;
use Streunerkatzen\Constants;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\GridField\GridField;
use Streunerkatzen\GridFieldStatusChangeButton;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_URLHandler;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\ORM\HasManyList;
use SilverStripe\UserForms\Model\Submission\SubmittedFormField;

class GridFieldStatusChangeButton implements GridField_HTMLProvider, GridField_ActionProvider, GridField_URLHandler {

    private const ACCEPT = 'Annehmen';
    private const REJECT = 'Ablehnen';
    private const REQUEST_REVIEW = 'Bearbeitung anfordern';

    private $targetFragment;
    private $targetStatus;
    private $buttonTitle;
    private $actionName;
    private $data;
    private $buttonStyleClass = "btn";

    //TargetFragment is just for positioning control of the HTML fragment
    public function __construct(string $targetFragment = "after", string $targetStatus, DataObject $data) {
        $this->targetFragment = $targetFragment;
        $this->targetStatus = $targetStatus;
        $this->actionName = 'state-transition-' . strtolower($targetStatus);
        $this->data = $data;
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

    public function handleValues(HasManyList $values) {
        $values->each(function (SubmittedFormField $item) {
            Injector::inst()->get(LoggerInterface::class)->warning('got value: ',
                [
                    $item->Title,
                    $item->Value
                ]);
        });

    }

    public function handleValue(SubmittedFormField $value) {
    }
    //Handle the custom action, for both the action button and the URL
    public function handleButtonAction() {
        $this->handleValues($this->data->Values());
    }
}
