<?php
namespace Streunerkatzen\CatSearch;

use PageController;
use SilverStripe\Control\Director;
use SilverStripe\Control\Email\Email;
use SilverStripe\View\Requirements;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Security\RandomGenerator;
use SilverStripe\View\ArrayData;
use Streunerkatzen\Cats\Cat;

class CatSearchPageController extends PageController {

    private static $allowed_actions = [
        'NotificationForm',
        'send'
    ];

    private $dropdowns;

    protected function init() {
        parent::init();
        $this->dropdowns = Cat::getFilterOptions();
        Requirements::themedJavascript("search.js");
    }

    public function index(HTTPRequest $request) {
        $cats = Cat::get();

        $filters = self::getCatFiltersFromSearchParams($request->getVars());

        if (count($filters) > 0) {
            $cats = $cats->filter($filters);
        }

        $paginatedCats = PaginatedList::create(
            $cats,
            $request
        )->setPageLength(25);

        $result = [
            'Results' => $paginatedCats
        ];

        if ($request->isAjax()) {
            return $this
                ->customise($result)
                ->renderWith('Streunerkatzen/CatSearch/Includes/CatSearchResult');
        }

        return $result;
    }

    private static function getCatFiltersFromSearchParams(array $params) {
        $filters = [];

        if (!isset($params) || empty($params)) {
            return $filters;
        }

        // title
        if (isset($params['SearchTitle']) && !empty($params['SearchTitle'])) {
            $title = trim($params['SearchTitle']);
            if (strcmp($title, "" != 0)) {
                $filters['Title:PartialMatch'] = $title;
            }
        }

        // from to
        if (isset($params['LostFoundDate-from']) && !empty($params['LostFoundDate-from'])) {
            $filters['LostFoundDate:GreaterThanOrEqual'] = $params['LostFoundDate-from'];
        }
        if (isset($params['LostFoundDate-to']) && !empty($params['LostFoundDate-to'])) {
            $filters['LostFoundDate:LessThanOrEqual'] = $params['LostFoundDate-to'];
        }

        // fur color
        if (isset($params['FurColor']) && !empty($params['FurColor'])) {
            $filters['FurColors.Title'] = $params['FurColor'];
        }

        // gender, castrated, chipped
        $filterParamKeys = ['Gender', 'IsCastrated', 'IsChipped'];
        foreach ($filterParamKeys as $paramKey) {
            if (isset($params[$paramKey]) && !empty($params[$paramKey])) {
                // if value is "nicht bekannt" --> reset filter
                if ($params[$paramKey][0] != "nicht bekannt") {
                    $filters[$paramKey] = $params[$paramKey][0];
                }
            }
        }

        return $filters;
    }

    // public function send(HTTPRequest $request) {
    //     // TODO: check captcha
    //     if (!SecurityToken::inst()->checkRequest($request)) {
    //         $this->httpError(400, "SecurityID doesn't match, possible CSRF attack.");
    //     }

    //     $text = $request->postVar('text');
    //     $cat = Cat::get_by_id($request->postVar('catId'));
    //     if (!$cat) {
    //         $this->httpError(404, "Diese Katze hat sich versteckt.");
    //     }
    //     $contact = $cat->Contact;
    //     if (!filter_var($contact, FILTER_VALIDATE_EMAIL)) {
    //         // contact is not an email address
    //         // TODO: send an email to sabine instead, containing data about $contact and $text
    //     } else {
    //         // TODO: send an email with $text and $cat to $contact
    //     }
    //     return 'success';
    // }

    // public function SendMessageForm($catId) {
    //     return Form::create(
    //         $this,
    //         __FUNCTION__,
    //         FieldList::create(
    //             TextareaField::create('cat-msg', 'Sende eine Nachricht an den Ersteller dieses Eintrags. Denke daran, Kontaktdaten anzufügen, sodass dieser dich erreichen kann.'),
    //             HiddenField::create('cat-id')->setValue($catId)
    //         ),
    //         FieldList::create(
    //             FormAction::create('sendMessage', 'Absenden')
    //         )
    //     )
    //     ->setFormMethod('POST')
    //     ->setFormAction($this->Link('send'));
    // }

    public function CatSearchForm() {
        return Form::create(
            $this,
            __FUNCTION__,
            FieldList::create(
                TextField::create('SearchTitle', 'Suche')
            ),
            FieldList::create(
                FormAction::create('sendSearch', 'Suchen')
            )
        )
        ->setFormMethod('GET')
        ->setFormAction($this->Link())
        ->disableSecurityToken()
        ->loadDataFrom($this->request->getVars());
    }

    public function NotificationForm() {
        $fields = new FieldList(
            EmailField::create('E-Mail', 'E-Mail'),
            HiddenField::create('QueryString', 'Suchfilter')
        );

        $actions = new FieldList(
            FormAction::create('handleNotification')
                ->setTitle('Speichern')
        );

        $required = new RequiredFields('E-Mail');

        $form = new Form($this, 'NotificationForm', $fields, $actions, $required);
        $form->enableSpamProtection();
        $form->addExtraClass('ajax-form');

        return $form;
    }

    public function handleNotification($data, Form $form) {
        $params = [];
        parse_str($data['QueryString'], $params);
        $filter = self::getCatFiltersFromSearchParams($params);

        $emailParam = $data['E-Mail'];
        if (!isset($emailParam) || empty($emailParam) || !filter_var($emailParam, FILTER_VALIDATE_EMAIL)) {
            return $this->httpError(500, 'Ungültige E-Mail Adresse!');
        }

        $token = (new RandomGenerator())->randomToken();

        $searchAgent = SearchAgent::create();
        $searchAgent->Filter = json_encode($filter);
        $searchAgent->Email = $emailParam;
        $searchAgent->Token = $token;
        $searchAgent->write();

        $unsubLink = Director::absoluteBaseURL().'notifications/unsubcatsearch'.'?token='.$token.'&email='.$emailParam;

        $templateData = new ArrayData([
            'ReadableSearch' => $searchAgent->getReadableSearch(),
            'EMailContent' => $this->NotificationEmailTemplate,
            'UnsubLink' => $unsubLink
        ]);

        $emailContent = $templateData->renderWith('Streunerkatzen/CatSearch/Includes/SearchAgentConfirmationEmail');

        $email = new Email();
        $email->setTo($emailParam);
        $email->setSubject("Streunerkatzen Suchbenachrichtigung");
        $email->setBody($emailContent);
        if (!$email->send()) {
            return $this->httpError(500, 'Fehler beim E-Mail senden!');
        }

        return $this->customise(new ArrayData([
            'Email' => $emailParam
        ]))->renderWith('Streunerkatzen/CatSearch/Includes/SearchAgentConfirmationPopup');
    }

    public function getFilters() {
        return ArrayList::create([
            ArrayData::create([
                "Title" => "Gender",
                "Label" => "Geschlecht",
                "InputType" => "radio",
                "Values" => $this->getDropdownOptions("Gender")
            ]),
            ArrayData::create([
                "Title" => "FurColor",
                "Label" => "Fellfarben",
                "InputType" => "checkbox",
                "Values" => $this->getDropdownOptions("FurColor")
            ]),
            ArrayData::create([
                "Title" => "IsCastrated",
                "Label" => "Kastriert?",
                "InputType" => "radio",
                "Values" => $this->getDropdownOptions("IsCastrated")
            ]),
            ArrayData::create([
                "Title" => "IsChipped",
                "Label" => "Gechippt?",
                "InputType" => "radio",
                "Values" => $this->getDropdownOptions("IsChipped")
            ])
        ]);
    }

    public function getDropdownOptions($dropdown) {
        $arrListData = [];
        foreach ($this->dropdowns[$dropdown] as $value) {
            $arrListData[] = ArrayData::create(["Text" => $value]);
        }
        return ArrayList::create($arrListData);
    }
}
