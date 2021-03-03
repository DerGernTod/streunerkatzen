<?php

namespace Streunerkatzen\Blog;

use DNADesign\Elemental\Extensions\ElementalPageExtension;
use DNADesign\Elemental\Forms\ElementalAreaField;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Versioned\Versioned;
use Streunerkatzen\Utils\Utils;

class BlogArticle extends DataObject {
    private const ALLOWED_FILE_ENDINGS = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
    private static $singular_name = 'Blogartikel';
    private static $plural_name = 'Blogartikel';
    private static $table_name = 'Streunerkatzen_BlogArticles';
    private static $default_sort = 'PublishTime DESC';

    private static $extensions = [
        Versioned::class,
        ElementalPageExtension::class,
    ];

    public function CanView($member = null) {
        return true;
    }

    private static $db = array(
        'Title' => 'Varchar',
        'Abstract' => 'Varchar',
        'PublishTime' => 'Datetime',
        'URLPath' => 'Varchar'
    );

    private static $has_one = array(
        'PostImage' => Image::class
    );

    private static $many_many = array(
        'Categories' => BlogArticleCategory::class
    );

    private static $owns = array(
        'PostImage'
    );

    private static $summary_fields = [
        'PublishTime' => 'Veröffentlichung',
        'GridThumbnail' => 'Bild',
        'Title' => 'Titel',
        'CategoryList' => 'Kategorien'
    ];

    public function getGridThumbnail() {
        if ($this->PostImage()->exists()) {
            return $this->PostImage()->ScaleWidth(100);
        }

        return '(kein Bild)';
    }

    public function getCategoryList() {
        if ($this->Categories()->Count() > 0) {
            $catString = '';
            foreach ($this->Categories() as $cat) {
                $catString .= $cat->Title.', ';
            }
            $catString = substr($catString, 0, -2);

            return $catString;
        }

        return '(keine Kategorien)';
    }

    public function onAfterWrite() {
        // check if url path already exists --> if not, generate it
        $changes = false;

        if (!$this->URLPath) {
            $this->URLPath = Utils::generateURLPath($this->Title, $this->ID);
            $changes = true;
        }

        // if no publish time was set --> publish now
        if (!$this->PublishTime) {
            $this->PublishTime = date('Y-m-d H:i:s', time());
            $changes = true;
        }

        if ($changes) {
            $this->write();
        }

        parent::onAfterWrite();
    }

    public function getCMSFields() {
        $fields = FieldList::create(
            TextField::create('Title', 'Titel'),
            ReadonlyField::create(
                'LinkPreview',
                'Link',
                Director::absoluteBaseURL().'blog/view/'.$this->URLPath
            ),
            DatetimeField::create('PublishTime', 'Datum der Veröffentlichung'),
            TextareaField::create('Abstract', 'Kurzfassung'),
            ElementalAreaField::create('ElementalArea', $this->ElementalArea(), $this->getElementalTypes()),
            $postImage = UploadField::create('PostImage', 'Titelbild'),
            CheckboxSetField::create(
                'Categories',
                'Kategorien',
                BlogArticleCategory::get()->map('ID', 'Title')
            )
        );
        $postImage
            ->setFolderName('Blog')
            ->getValidator()->setAllowedExtensions(BlogArticle::ALLOWED_FILE_ENDINGS);

        return $fields;
    }

    public function getCMSValidator() {
        return RequiredFields::create(
            'Title'
        );
    }

    public static function getNewestBlogArticles($count) {
        if ($count <= 0) {
            return null;
        }
        return BlogArticle::get()->sort('PublishTime', 'DESC')->limit($count);
    }

    public function getPublicationTimestamp() {
        return strtotime($this->PublishTime);
    }

    public function Link() {
        return 'blog/view/'.$this->URLPath;
    }

    public function CMSEditLink() {
        $admin = Injector::inst()->get(BlogAdmin::class);

        // Classname needs to be passed as an action to ModelAdmin
        $classname = str_replace('\\', '-', $this->ClassName);

        return Controller::join_links(
            $admin->Link($classname),
            "EditForm",
            "field",
            $classname,
            "item",
            $this->ID
        );
    }

    public static function getArticlesByCats($catIDs, $limit = -1, $offset = 0) {
        if ($limit > 0) {
            return BlogArticle::get()
                ->filter([
                    'Categories.ID' => $catIDs,
                    'PublishTime:LessThanOrEqual' => date('Y-m-d H:i:s', time())
                ])
                ->sort('PublishTime DESC')
                ->limit($limit, $offset);
        } elseif ($limit < 0) {     // load all
            return BlogArticle::get()
                ->filter([
                    'Categories.ID' => $catIDs,
                    'PublishTime:LessThanOrEqual' => date('Y-m-d H:i:s', time())
                ])
                ->sort('PublishTime DESC');
        }

        return new ArrayList();
    }

    public static function getArticles($limit = -1, $offset = 0) {
        if ($limit > 0) {
            return BlogArticle::get()
                ->filter([
                    'PublishTime:LessThanOrEqual' => date('Y-m-d H:i:s', time())
                ])
                ->sort([
                    'PublishTime DESC',
                    'ID ASC'
                ])
                ->limit($limit, $offset);
        } elseif ($limit < 0) {     // load all
            return BlogArticle::get()
                ->filter([
                    'PublishTime:LessThanOrEqual' => date('Y-m-d H:i:s', time())
                ])
                ->sort([
                    'PublishTime DESC',
                    'ID ASC'
                ]);
        }

        return new ArrayList();
    }
}
