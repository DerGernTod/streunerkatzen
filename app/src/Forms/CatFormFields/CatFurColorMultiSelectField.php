<?php
namespace Streunerkatzen\Forms\CatFormFields;

use SilverStripe\Forms\ReadonlyField;
use SilverStripe\UserForms\Model\EditableCustomRule;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\UserForms\Model\EditableFormField\EditableMultipleOptionField;
use SilverStripe\View\ArrayData;
use Streunerkatzen\Cats\FurColor;
use Streunerkatzen\Forms\ImageListboxField;

class CatFurColorMultiSelectField extends EditableMultipleOptionField {
    private static $singular_name = 'Katzen Fellfarbe Dropdown';
    private static $plural_name = 'Katzen Fellfarbe Dropdowns';
    private static $table_name = 'Streunerkatzen_CatFurColorMultiSelectFields';

    /**
     * @return \SilverStripe\Forms\FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName('Default');
        $fields->removeByName('Options');
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

    private function getData() {
        $data = [];
        $furColors = FurColor::get();

        foreach ($furColors as $color) {
            $data[$color->ID] = new ArrayData([
                "Value" => $color->ID,
                "Title" => $color->Title,
                "Examples" => $color->ExampleImages()
            ]);
        }

        return $data;
    }

    public function getFormField() {
        $field = ImageListboxField::create($this->Name, $this->Title ?: false)
            ->setSource($this->getData())
            ->addExtraClass('hidden-field')
            ->setFieldHolderTemplate(EditableFormField::class . '_holder');

        $this->doUpdateFormField($field);

        return $field;
    }

    public function getValueFromData($data) {
        if (!empty($data[$this->Name])) {
            $source = $this->getFormField()->getSource();
            $value = "";
            foreach ($data[$this->Name] as $furColorID) {
                $value .= $source[$furColorID]->Title. ", ";
            }
            $value = substr($value, 0, -2);

            return $value;
        }
    }

    public function getSelectorField(EditableCustomRule $rule, $forOnLoad = false) {
        return "$(\"select[name='{$this->Name}']\")";
    }
}
