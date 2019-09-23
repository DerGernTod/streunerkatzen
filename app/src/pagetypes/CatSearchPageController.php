<?php
namespace Streunerkatzen;

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

class CatSearchPageController extends PageController {

    private static $allowed_actions = [
        'view'
    ];

    private $dropdowns;

    protected function init() {
        parent::init();
        $this->dropdowns = Cat::getCatDropdownsWithOptions();
        Requirements::themedJavascript("search.js");
    }

    public function index(HTTPRequest $request) {
        $cats = Cat::get();
        $searchDone = false;

        $params = $request->getVars();
        $filter = [];

        if ($params) {
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
        }
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
