<?php
namespace Streunerkatzen\Forms\CatFormFields;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextField;
use SilverStripe\UserForms\Model\EditableCustomRule;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\UserForms\Model\EditableFormField\EditableDropdown;

class BaseCatDataDropdownField extends EditableFormField {
    private static $singular_name = 'Katzendaten Dropdown';
    private static $plural_name = 'Katzendaten Dropdowns';
    private static $table_name = 'Streunerkatzen_BaseCatDataDropdownFields';
    private static $abstract = true;
    private static $is_cat_field = true;

    private static $db = array(
        'UseEmptyString' => 'Boolean',
        'EmptyString' => 'Varchar(255)',
    );

    /**
     * @return \SilverStripe\Forms\FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName('Default');
        $fields->removeByName('Name');

        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create('Default', 'Standardwert')
                ->setSource($this->getData())
                ->setHasEmptyDefault(true)
                ->setEmptyString('---')
        );

        $fields->addFieldToTab(
            'Root.Main',
            CheckboxField::create('UseEmptyString', 'Eigenen Standardwert verwenden, wenn noch nichts ausgewählt wurde')
        );

        $fields->addFieldToTab(
            'Root.Main',
            TextField::create('EmptyString', 'Eigener Standardwert:')
        );

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

    protected function getData() {
        return [];
    }

    public function getFormField() {
        $field = DropdownField::create($this->Name, $this->Title ?: false)
            ->setSource($this->getData())
            ->setFieldHolderTemplate(EditableFormField::class . '_holder')
            ->setTemplate(EditableDropdown::class);

        // Empty string
        if ($this->UseEmptyString) {
            $field->setEmptyString($this->EmptyString ?: '');
        }

        // Set default
        if ($this->Default) {
            $field->setValue($this->Default);
        }

        $this->doUpdateFormField($field);

        return $field;
    }

    public function getValueFromData($data) {
        if (!empty($data[$this->Name])) {
            $source = $this->getFormField()->getSource();
            return $source[$data[$this->Name]];
        }
    }

    public function getSelectorField(EditableCustomRule $rule, $forOnLoad = false) {
        return "$(\"select[name='{$this->Name}']\")";
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
