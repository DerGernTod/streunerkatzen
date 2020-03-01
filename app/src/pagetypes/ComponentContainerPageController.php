<?php

namespace Streunerkatzen;

use PageController;
use Streunerkatzen\BlogArticle;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Dev\Debug;
use SilverStripe\View\Requirements;

class ComponentContainerPageController extends PageController {
    private const LIMIT = 5;
    private static $allowed_actions = [
        'blogtag',
        'view'
    ];

    public function init() {
        parent::init();
        Requirements::themedJavascript("component-container.js");
    }

    public function blogtag(HTTPRequest $request) {
        $offset = $request->getVar('offset');
        if (!isset($offset)) {
            $offset = 0;
        }
        $limit = ComponentContainerPageController::LIMIT;
        $articlesLeft = BlogArticle::get()
            ->filter('Categories.ID:PartialMatch', $request->param('ID'))
            ->count() - ($offset + $limit);
        $articles = BlogArticle::get()
            ->filter('Categories.ID:PartialMatch', $request->param('ID'))
            ->sort('PublishTime DESC')
            ->limit($limit, $offset);

        if (count($articles) < 1) {
            return $this->httpError(404, 'Keine Blogartikel in dieser Kategorie gefunden!');
        }
        $filterCatName = BlogArticleCategory::get_by_id($request->param('ID'))->Title;
        $result = [
            'BlogArticleList' => $articles,
            'FilterCategory' => $filterCatName,
            'ArticlesLeft' => $articlesLeft,
            'Offset' => $offset + $limit
        ];
        $this->getResponse()->addHeader('x-offset', $offset + $limit);
        $this->getResponse()->addHeader('x-articles-left', $articlesLeft);
        if ($request->isAjax()) {
            return $this->customise($result)->renderWith('Streunerkatzen/Includes/BlogArticleList');
        }
        return $result;
    }

    public function index(HTTPRequest $request) {
        $offset = $request->getVar('offset');
        if (!isset($offset)) {
            $offset = 0;
        }
        $limit = ComponentContainerPageController::LIMIT;
        $articlesLeft = BlogArticle::get()
            ->count() - ($offset + $limit);
        $articles = BlogArticle::get()
            ->sort('PublishTime DESC')
            ->limit($limit, $offset);

        $result = [
            'BlogArticleList' => $articles,
            'ArticlesLeft' => $articlesLeft,
            'Offset' => $offset + $limit
        ];
        $this->getResponse()->addHeader('x-offset', $offset + $limit);
        $this->getResponse()->addHeader('x-articles-left', $articlesLeft);
        if ($request->isAjax()) {
            return $this->customise($result)->renderWith('Streunerkatzen/Includes/BlogArticleList');
        }
        return $result;
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
