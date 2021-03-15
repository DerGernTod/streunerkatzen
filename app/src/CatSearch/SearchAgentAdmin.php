<?php

namespace Streunerkatzen\CatSearch;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;

class SearchAgentAdmin extends ModelAdmin {

    private static $menu_title = 'Katzensuche Benachrichtigungen';

    private static $url_segment = 'catsearchagents';

    private static $managed_models = [
        SearchAgent::class
    ];

    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm($id, $fields);
        if ($this->modelClass === SearchAgent::class) {
            $fieldName = $this->sanitiseClassName($this->modelClass);
            /** @var GridField $grid */
            if ($grid = $form->Fields()->dataFieldByName($fieldName)) {
                $grid->getConfig()->removeComponentsByType([
                    GridFieldAddNewButton::class
                ]);
            }
        }
        return $form;
    }
}
