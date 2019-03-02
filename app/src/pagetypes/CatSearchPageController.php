<?php
namespace Streunerkatzen;

use PageController;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\PaginatedList;

class CatSearchPageController extends PageController {

    public function index(HTTPRequest $request) {
        $cats = Cat::get();
        $search = $request->getVar('SearchValue');
        if ($search) {
            $cats = $cats->filter([
                'Title:PartialMatch' => $search,
            ]);
        }
        $paginatedCats = PaginatedList::create(
            $cats,
            $request
        )->setPageLength(25);
        $result = [
            'Results' => $paginatedCats,
            'SearchDone' => isset($search)
        ];
        if ($request->isAjax()) {
            return $this
                ->customise($result)
                ->renderWith('Streunerkatzen/Includes/CatSearchResults');
        }
        return $result;
    }

    public function CatSearchForm() {
        return Form::create(
            $this,
            __FUNCTION__,
            FieldList::create(
                TextField::create('SearchValue', 'Suche')
            ),
            FieldList::create(
                FormAction::create('sendSearch', 'Suchen')
            ),
            RequiredFields::create('SearchValue')
        )
        ->setFormMethod('GET')
        ->setFormAction($this->Link())
        ->disableSecurityToken()
        ->loadDataFrom($this->request->getVars());
    }

    public function sendSearch($data, $form) {
        $searchValue = $data['SearchValue'];
        //Cat::get()->filter()->
    }
}
