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
        $searchTitle = $request->getVar('SearchTitle');
        if ($searchTitle) {
            $cats = $cats->filter([
                'Title:PartialMatch' => $searchTitle,
            ]);
        }
        $paginatedCats = PaginatedList::create(
            $cats,
            $request
        )->setPageLength(25);
        $result = [
            'Results' => $paginatedCats,
            'SearchDone' => isset($searchTitle)
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
            ),
            RequiredFields::create('SearchTitle')
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

    public function getDropdownOptions($dropdown) {
        $arrListData = [];
        foreach ($this->dropdowns['CatField_'.$dropdown] as $value) {
            $arrListData[] = ArrayData::create(["Text" => $value]);
        }
        return ArrayList::create($arrListData);
    }
}
