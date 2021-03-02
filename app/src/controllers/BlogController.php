<?php

namespace Streunerkatzen\Controllers;

use DateTime;
use PageController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use Streunerkatzen\Blog\BlogArticle;
use Streunerkatzen\Blog\BlogArticleCategory;

class BlogController extends PageController {
    private const LIMIT = 5;

    private static $allowed_actions = [
        'view',
        'category'
    ];

    public function init() {
        parent::init();
        Requirements::themedJavascript("blog.js");
    }

    public function view(HTTPRequest $request) {
        $articleURL = $request->param('ID');
        $articleID = substr(strrchr($articleURL, "-"), 1);

        $blogArticle = BlogArticle::get()->byID($articleID);
        if (!$blogArticle) {
            return $this->httpError(404, 'Dieser Artikel existiert nicht.');
        }

        $publishTime = $blogArticle->PublishTime;

        if ((!isset($publishTime) || new DateTime($publishTime) > new DateTime("now")) && $member = Security::getCurrentUser() == null) {
            return $this->httpError(404, 'Dieser Artikel existiert nicht.');
        }

        return [
            'BlogArticle' => $blogArticle
        ];
    }

    public function category(HTTPRequest $request) {
        $catURL = $request->param('ID');
        $catID = substr(strrchr($catURL, "-"), 1);

        $cat = BlogArticleCategory::get()->byID($catID);
        if (!$cat) {
            return $this->httpError(404, 'Diese Kategorie existiert nicht.');
        }

        $offset = $request->getVar('offset');
        if (!isset($offset)) {
            $offset = 0;
        }

        $numArticlesLeft = BlogArticle::get()
            ->filter([
                'Categories.ID' => $catID,
                'PublishTime:LessThanOrEqual' => date('Y-m-d H:i:s', time())
            ])->count();
        $numArticlesLeft = $numArticlesLeft - ($offset + self::LIMIT);

        $blogArticles = BlogArticle::get()
            ->filter([
                'Categories.ID' => $catID,
                'PublishTime:LessThanOrEqual' => date('Y-m-d H:i:s', time())
            ])
            ->sort('PublishTime DESC')
            ->limit(self::LIMIT, $offset);

        $this->getResponse()->addHeader('x-offset', $offset + self::LIMIT);
        $this->getResponse()->addHeader('x-articles-left', $numArticlesLeft);

        $result = [
            'BlogArticles' => $blogArticles,
            'Category' => $cat,
            'NumArticlesLeft' => $numArticlesLeft,
            'Offset' => $offset + self::LIMIT
        ];

        if ($request->isAjax()) {
            return $this->customise($result)->renderWith('Streunerkatzen/Blog/Includes/BlogArticleList');
        }

        return $result;
    }
}
