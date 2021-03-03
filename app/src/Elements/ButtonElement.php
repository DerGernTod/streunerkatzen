<?php
namespace Streunerkatzen\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\FieldType\DBField;

class ButtonElement extends BaseElement {
    private static $singular_name = 'Button';
    private static $plural_name = 'Buttons';
    private static $table_name = 'Streunerkatzen_ButtonElement';

    private static $db = [
        'Label' => 'Varchar(250)',
        'Link' => 'Text',
        'IsNewWindow' => 'Boolean'
    ];

    private static $has_one = [
        'Page' => SiteTree::class,
    ];

    public function onBeforeWrite() {
        parent::onBeforeWrite();

        if ($this->PageID != 0) {
            $this->Link = null;
        }
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Label', 'Buttontext'),
            TreeDropdownField::create(
                'PageID',
                'Interne Seite',
                SiteTree::class
            )->setDescription('Leer lassen falls eine externe URL verwendet werden soll.'),
            TextField::create('Link', 'Externe URL'),
            CheckboxField::create('IsNewWindow', 'Link in neuem Fenster öffnen')
        ]);

        return $fields;
    }

    /**
     * @return DBHTMLText
     */
    public function getSummary() {
        $summary = 'Button "'. $this->Label . '" mit Link ';

        if ($this->PageID != 0) {
            $summary .= 'zur Seite ' . $this->Page()->Title;
        } else {
            $summary .= 'zu ' . $this->Link;
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
        return _t(__CLASS__ . '.BlockType', 'Button');
    }

    public function getCorrectLink() {
        if ($this->PageID != 0) {
            return $this->Page()->Link();
        } else {
            return $this->Link;
        }
    }
}
