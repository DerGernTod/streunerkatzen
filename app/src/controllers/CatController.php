<?php

namespace Streunerkatzen\Controllers;

use PageController;
use SilverStripe\Control\HTTPRequest;
use Streunerkatzen\Cats\Cat;

class CatController extends PageController {
    private static $allowed_actions = [
        'view'
    ];

    public function view(HTTPRequest $request) {
        $cat = Cat::get()->byID($request->param('ID'));
        if (!$cat) {
            return $this->owner->httpError(404, 'Diese Katze hat sich versteckt!');
        }
        if ($request->isAjax()) {
            return $cat->renderWith('Streunerkatzen/Includes/CatSingle');
        } else {
            return [ 'Cat' => $cat ];
        }
    }
}
