<?php
namespace Streunerkatzen\Forms\CatFormFields;

use SilverStripe\Forms\ReadonlyField;
use SilverStripe\UserForms\Model\EditableFormField\EditableTextField;

class BaseCatTextField extends EditableTextField {
    private static $singular_name = 'Katzendaten Textfeld';
    private static $plural_name = 'Katzendaten Textfelder';
    private static $table_name = 'Streunerkatzen_BaseCatTextFields';
    private static $abstract = true;
    private static $is_cat_field = true;

    /**
     * @return \SilverStripe\Forms\FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName('Name');

        $fields->addFieldToTab(
            'Root.Main',
            ReadonlyField::create(
                'Name',
                _t(__CLASS__.'.NAME', 'Name')
            ),
            'ExtraClass'
        );

        return $fields;
    }

    public function onBeforeWrite() {
        parent::onBeforeWrite();
        // check if field name starts with correct class
        $classNamePieces = explode('\\', static::class);
        $class = array_pop($classNamePieces);

        if (!(strpos($this->Name, $class) === 0)) {
            // if not generate new name
            $this->Name = $this->generateName();
        }
    }
}
