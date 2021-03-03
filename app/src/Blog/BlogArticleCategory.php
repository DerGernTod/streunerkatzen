<?php

namespace Streunerkatzen\Blog;

use SilverStripe\Control\Director;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use Streunerkatzen\Elements\BlogArticleListElement;
use Streunerkatzen\Utils\Utils;

class BlogArticleCategory extends DataObject {
    private static $singular_name = 'Kategorie';
    private static $plural_name = 'Kategorien';
    private static $table_name = 'Streunerkatzen_BlogArticleCategories';
    private static $default_sort = 'Title ASC';

    private static $db = array(
        "Title" => "Varchar",
        'URLPath' => 'Varchar'
    );

    private static $belongs_many_many = array(
        "Articles" => BlogArticle::class,
        "BlogArticleListElements" => BlogArticleListElement::class
    );

    public function onAfterWrite() {
        // check if url path already exists --> if not, generate it
        if (!$this->URLPath) {
            $this->URLPath = Utils::generateURLPath($this->Title, $this->ID);
            $this->write();
        }

        parent::onAfterWrite();
    }

    public function getCMSFields() {
        return FieldList::create(
            TextField::create('Title', 'Kategorie'),
            ReadonlyField::create(
                'LinkPreview',
                'Link zu Kategorieseite',
                Director::absoluteBaseURL().'blog/category/'.$this->URLPath
            ),
        );
    }

    public function getCMSValidator() {
        return RequiredFields::create(
            'Title'
        );
    }

    public function Link() {
        return 'blog/category/'.$this->URLPath;
    }
}
