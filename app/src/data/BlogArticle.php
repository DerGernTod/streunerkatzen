<?php

namespace Streunerkatzen;

use Streunerkatzen\Cat;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Member;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use Streunerkatzen\BlogArticleCategory;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\ListboxField;

class BlogArticle extends DataObject {
    private const ALLOWED_FILE_ENDINGS = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
    private static $singular_name = 'Blogartikel';
    private static $plural_name = 'Blogartikel';
    private static $table_name = 'Streunerkatzen_BlogArticles';

    private static $db = array(
        "Title" => "Varchar",
        "Content" => "HTMLText",
        "Abstract" => "Varchar",
        "PublishTime" => "Datetime"
    );

    private static $has_one = array(
        "PostImage" => Image::class
    );

    private static $many_many = array(
        "Categories" => BlogArticleCategory::class
    );

    private static $owns = array(
        "PostImage"
    );

    public function getCMSFields() {
        $fields = FieldList::create(
            TextField::create('Title', 'Titel'),
            DateField::create('PublishTime', 'Datum der Veröffentlichung'),
            TextareaField::create('Abstract', 'Kurzfassung'),
            HtmlEditorField::create('Content', 'Inhalt'),
            $postImage = UploadField::create('PostImage', 'Titelbild'),
            CheckboxSetField::create('Categories', 'Kategorien', BlogArticleCategory::get()->map('ID', 'Title'))
        );
        $postImage
            ->setFolderName('Blog')
            ->getValidator()
            ->setAllowedExtensions(BlogArticle::ALLOWED_FILE_ENDINGS);
        return $fields;
    }

    public function getListView() {
        return $this->renderWith('Streunerkatzen/Includes/BlogArticleListView');
    }

    public static function getNewestBlogArticles($count) {
        if ($count <= 0) {
            return null;
        }
        return BlogArticle::get()->sort("PublishTime", "DESC")->limit($count);
    }

    public function getFullView() {
        return $this->renderWith('Streunerkatzen/Includes/BlogArticleFullView');
    }

    public function CategoryLink() {
        return Director::baseURL().'blog/blogtag';
    }

    public function Link() {
        return Director::baseURL().'blog/view';
    }

    public static function CatShortcode($arguments) {
        if (!isset($arguments['id'])) {
            return '';
        }
        $cat = Cat::get_by_id($arguments['id']);
        if (!$cat) {
            return "Katze mit der ID ".$arguments['id']." nicht gefunden!";
        }
        return $cat->getShortcodeView($arguments);
    }
}
