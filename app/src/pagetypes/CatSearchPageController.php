<?php
namespace Streunerkatzen;

use Exception;
use PageController;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\View\Requirements;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;
use SilverStripe\ORM\DataList;
use SilverStripe\View\ArrayData;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\RandomGenerator;

class CatSearchPageController extends PageController {

    private static $allowed_actions = [
        'view',
        'agent',
        'unsubscribe'
    ];

    private $dropdowns;

    protected function init() {
        parent::init();
        $this->dropdowns = Cat::getCatDropdownsWithOptions();
        Requirements::themedJavascript("search.js");
    }

    private static function buildFilterArrayFromParams(array $params) {
        $filter = [];
        if (!$params) {
            return $filter;
        }

        foreach($params as $key => $value) {
            if ($key == 'SearchTitle') {
                if ($value != '') {
                    $filter['Title:PartialMatch'] = $value;
                }
            } else if ($key == 'ajax' || $key == 'start') {
                continue;
            } else if(str_contains($key, 'LostFoundDate')) {
                if (str_contains($key, 'from')) {
                    $filter['LostFoundDate:GreaterThanOrEqual'] = $value;
                } else {
                    $filter['LostFoundDate:LessThanOrEqual'] = $value;
                }
            } else {
                $filteredResult = array_filter($value, function ($curVal) {
                    return $curVal != 'nicht bekannt';
                });
                if (count($filteredResult) > 0) {
                    if ($key == 'HairColor') {
                        $filter['HairColors.Title'] = $filteredResult;
                    } else {
                        $filter[$key] = $filteredResult;
                    }
                }
            }
        }
        return $filter;
    }

    public function index(HTTPRequest $request) {
        $cats = Cat::get();
        $searchDone = false;

        $filter = CatSearchPageController::buildFilterArrayFromParams($request->getVars());

        if (count($filter) > 0) {
            $searchDone = true;
            $cats = $cats->filter($filter);
        }
        $paginatedCats = PaginatedList::create(
            $cats,
            $request
        )->setPageLength(25);
        $result = [
            'Results' => $paginatedCats,
            'SearchDone' => $searchDone,
            'Filters' => $filter
        ];
        if ($request->isAjax()) {
            return $this
                ->customise($result)
                ->renderWith('Streunerkatzen/Includes/CatSearchResult');
        }
        return $result;
    }

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

    public function view(HTTPRequest $request) {
        $cat = Cat::get()->byID($request->param('ID'));
        if (!$cat) {
            return $this->httpError(404, 'Diese Katze hat sich versteckt!');
        }
        if ($request->isAjax()) {
            return $cat->renderWith('Streunerkatzen/Includes/CatPage');
        } else {
            return [ 'Cat' => $cat ];
        }
    }

    public function unsubscribe(HTTPRequest $request) {
        $getVars = $request->getVars();
        $matchingAgents = SearchAgent::get()->filter(["Token" => $getVars["token"], "Email" => $getVars["email"]]);
        if (count($matchingAgents) != 1) {
            $searchAgent = SearchAgent::create();
        } else {
            $searchAgent = $matchingAgents[0];
            $searchAgent->delete();
        }
        return $searchAgent->renderWith('Streunerkatzen/Includes/Search/AgentUnsubscribe');
    }

    public function agent(HTTPRequest $request) {
        $filter = CatSearchPageController::buildFilterArrayFromParams($request->getVars());
        $email = $request->postVars()['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->httpError(500, 'Ungültige E-Mail Adresse!');
        }
        $token = (new RandomGenerator())->randomToken();
        $template = $request->postVars()['email-template'];
        $searchAgent = SearchAgent::create();
        $searchAgent->Filter = json_encode($filter);
        $searchAgent->Email = $email;
        $searchAgent->Token = $token;

        $link = $this->AbsoluteLink('unsubscribe')."?token=$token&email=".$email;
        $emailContent = $template
            ."<p>"
            ."Falls das nicht du warst, oder du die Benachrichtigungen doch nicht erhalten "
            ."willst, klicke bitte auf <a href='$link'>abbestellen</a>."
            ."</p>";
        $searchAgent->write();
        // TODO send an email including a link with the token where they can unsubscribe
        return $searchAgent->renderWith('Streunerkatzen/Includes/Search/AgentPopup');
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
                "Title" => "IsCastrated",
                "Label" => "Kastriert?",
                "InputType" => "radio",
                "Values" => $this->getDropdownOptions("IsCastrated")
            ]),
            ArrayData::create([
                "Title" => "HairColor",
                "Label" => "Fellfarben",
                "InputType" => "checkbox",
                "Values" => $this->getDropdownOptions("HairColor")
            ])
        ]);
    }

    public function getDropdownOptions($dropdown) {
        $arrListData = [];
        foreach ($this->dropdowns['CatField_'.$dropdown] as $value) {
            $arrListData[] = ArrayData::create(["Text" => $value]);
        }
        return ArrayList::create($arrListData);
    }
}
