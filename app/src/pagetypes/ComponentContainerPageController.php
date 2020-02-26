<?php

namespace Streunerkatzen;

use PageController;
use Streunerkatzen\BlogArticle;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\Debug;

class ComponentContainerPageController extends PageController {
    private static $allowed_actions = [
        'blogtag',
        'view'
    ];

    public function blogtag(HTTPRequest $request) {
        $articles = BlogArticle::get()->filter('Categories.ID:PartialMatch', $request->param('ID'))->sort('PublishTime DESC');
        if (count($articles) < 1) {
            return $this->httpError(404, 'Keine Blogartikel in dieser Kategorie gefunden!');
        }
        $filterCatName = BlogArticleCategory::get_by_id($request->param('ID'))->Title;
        return [
            'FilteredArticles' => $articles,
            'FilterCategory' => $filterCatName
        ];
    }
    public function view(HTTPRequest $request) {
        $blog = BlogArticle::get_by_id($request->param('ID'));
        if (!$blog) {
            return $this->httpError(404, 'Blogeintrag nicht gefunden!');
        }
        return [
            'SingleArticle' => $blog
        ];
    }
}
