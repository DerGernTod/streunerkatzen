<?php
namespace Streunerkatzen\Forms;

use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;
use SilverStripe\UserForms\Model\EditableCustomRule;
use SilverStripe\UserForms\Model\EditableFormField;

/**
 * A dropdown field which allows the user to select a country
 *
 * @property bool $UseEmptyString
 * @property string $EmptyString
 *
 * @package userforms
 */
class EditableDistrictField extends EditableFormField {
    private static $singular_name = 'Bezirk Oberösterreich Dropdown';
    private static $plural_name = 'Bezirk Oberösterreich Dropdowns';
    private static $table_name = 'Streunerkatzen_EditableDistrictFields';

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
        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create('Default', _t(__CLASS__ . '.DEFAULT', 'Default value'))
                         ->setSource($this->getDistricts())
                         ->setHasEmptyDefault(true)
                         ->setEmptyString('---')
        );

        $fields->addFieldToTab(
            'Root.Main',
            CheckboxField::create('UseEmptyString', _t(__CLASS__ . '.USE_EMPTY_STRING', 'Set default empty string'))
        );

        $fields->addFieldToTab(
            'Root.Main',
            TextField::create('EmptyString', _t(__CLASS__ . '.EMPTY_STRING', 'Empty String'))
        );

        return $fields;
    }

    private function getDistricts() {
        return [
            'Braunau' => 'Braunau',
            'Eferding' => 'Eferding',
            'Freistadt' => 'Freistadt',
            'Gmunden' => 'Gmunden',
            'Grieskirchen' => 'Grieskirchen',
            'Kirchdorf' => 'Kirchdorf',
            'Linz' => 'Linz',
            'Linz-Land' => 'Linz-Land',
            'Perg' => 'Perg',
            'Ried' => 'Ried',
            'Rohrbach' => 'Rohrbach',
            'Schärding' => 'Schärding',
            'Steyr' => 'Steyr',
            'Steyr-Land' => 'Steyr-Land',
            'Urfahr-Umgebung' => 'Urfahr-Umgebung',
            'Vöcklabruck' => 'Vöcklabruck',
            'Wels' => 'Wels',
            'Wels-Land' => 'Wels-Land'
        ];
    }

    public function getFormField() {
        $field = DropdownField::create($this->Name, $this->Title ?: false)
            ->setSource($this->getDistricts())
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

    public function getIcon() {
        $resource = ModuleLoader::getModule('silverstripe/userforms')->getResource('images/editabledropdown.png');

        if (!$resource->exists()) {
            return '';
        }

        return $resource->getURL();
    }

    public function getSelectorField(EditableCustomRule $rule, $forOnLoad = false) {
        return "$(\"select[name='{$this->Name}']\")";
    }
}
