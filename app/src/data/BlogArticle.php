<?php

namespace Streunerkatzen;

use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Member;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\AssetAdmin\Forms\UploadField;

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
}