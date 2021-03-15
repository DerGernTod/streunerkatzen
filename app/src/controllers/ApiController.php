<?php

namespace Streunerkatzen\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use Streunerkatzen\Cats\Cat;

class ApiController extends Controller {
    private static $allowed_actions = [
        'catsearch'
    ];

    public function catsearch(HTTPRequest $request) {
        $search = $request->getVar('search');
        $queryResult = Cat::get()
            ->sort('PublishTime DESC')
            ->filter(['Title:StartsWith' => $search])
            ->limit(10);
        $json = [];

        foreach ($queryResult as $catId => $cat) {
            $json[$catId] = ['id' => $cat->ID, 'publishTime' => $cat->PublishTime, 'title' => $cat->Title];
        }

        $this->getResponse()->addHeader('content-type', 'application/json');

        return json_encode($json);
    }
}
