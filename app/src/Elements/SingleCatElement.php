<?php
namespace Streunerkatzen\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\FieldType\DBField;
use Streunerkatzen\Cats\Cat;

class SingleCatElement extends BaseElement {
    private static $singular_name = 'Katze';
    private static $plural_name = 'Katzen';
    private static $table_name = 'Streunerkatzen_SingleCatElement';

    private static $has_one = [
        'Cat' => Cat::class
    ];

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            DropdownField::create(
                'CatID',
                'Katze',
                Cat::get()->map('ID', 'Title')
            )->setEmptyString('Keine Katze ausgewählt')
        ]);

        return $fields;
    }

    /**
     * @return DBHTMLText
     */
    public function getSummary() {
        $summary = '';

        if ($this->CatID != 0) {
            $summary = 'Katze ' . $this->Cat()->Title;
        } else {
            $summary .= 'Keine Katze ausgewählt';
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
        return _t(__CLASS__ . '.BlockType', 'Katze');
    }
}
