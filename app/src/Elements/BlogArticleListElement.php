<?php
namespace Streunerkatzen\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\FieldType\DBField;
use Streunerkatzen\Blog\BlogArticle;
use Streunerkatzen\Blog\BlogArticleCategory;

class BlogArticleListElement extends BaseElement {
    private static $singular_name = 'Blogartikel Liste';
    private static $plural_name = 'Blogartikel Listen';
    private static $table_name = 'Streunerkatzen_BlogArticleListElement';

    private static $db = [
        'NumArticles' => 'Int',
        'DisplayLoadMore' => 'Boolean',
        'AllCategoriesSelected' => 'Boolean'
    ];

    private static $many_many = array(
        'Categories' => BlogArticleCategory::class
    );

    public function getCategoryList() {
        if ($this->Categories()->Count() > 0) {
            $catString = "";
            foreach ($this->Categories() as $cat) {
                $catString .= $cat->Title.", ";
            }
            $catString = substr($catString, 0, -2);

            return $catString;
        }

        return '';
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            NumericField::create(
                'NumArticles',
                'Anzahl Artikel (-1 eingeben, wenn alle Artikel sofort angezeigt werden sollen - nicht empfohlen)'
            ),
            CheckboxField::create(
                'DisplayLoadMore',
                '"Mehr Artikel laden" Button anzeigen'
            ),
            CheckboxField::create(
                'AllCategoriesSelected',
                'Artikel von allen Kategorien anzeigen'
            ),
            CheckboxSetField::create(
                'Categories',
                'Kategorien, von denen Artikel angezeigt werden (nur wenn oben nicht ausgewählt wurde, dass alle Kategorien angezeigt werden sollen)',
                BlogArticleCategory::get()->map('ID', 'Title')
            )
        ]);

        return $fields;
    }

    /**
     * @return DBHTMLText
     */
    public function getSummary() {
        $summary = "";

        if ($this->NumArticles >= 0) {
            $summary .= $this->NumArticles . " Artikel";
        } else {
            $summary .= "Alle Artikel";
        }

        if ($this->AllCategoriesSelected) {
            $summary .= " aus allen Kategorien";
        } else {
            $summary .= " aus folgenden Kategorien: " . $this->getCategoryList();
        }

        return DBField::create_field('HTMLText', $summary)->Summary(20);
    }

    /**
     * @return array
     */
    protected function provideBlockSchema() {
        $blockSchema = parent::provideBlockSchema();
        $blockSchema['content'] = $this->getSummary();

        return $blockSchema;
    }

    /**
     * @return string
     */
    public function getType() {
        return _t(__CLASS__ . '.BlockType', 'Blog Artikel Liste');
    }

    public function getCatIDs() {
        $catIDs = [];
        foreach ($this->Categories() as $cat) {
            array_push($catIDs, $cat->ID);
        }

        return $catIDs;
    }

    public function getBlogArticles($limit = null, $offset = 0) {
        if (is_null($limit)) {
            $limit = $this->NumArticles;
        }

        if ($this->AllCategoriesSelected) {
            return BlogArticle::getArticles($limit, $offset);
        } else {
            $catIDs = $this->getCatIDs();

            return BlogArticle::getArticlesByCats($catIDs, $limit, $offset);
        }
    }
}
