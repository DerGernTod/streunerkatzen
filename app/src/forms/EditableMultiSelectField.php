<?php

namespace Streunerkatzen;

use SilverStripe\ORM\Map;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\Tab;
use SilverStripe\Assets\File;
use SilverStripe\Dev\Backtrace;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use SilverStripe\UserForms\Model\EditableFormField\EditableMultipleOptionField;

class EditableMultiSelectField extends EditableMultipleOptionField {

    private static $singular_name = 'Mehrfachauswahlfeld';

    private static $plural_name = 'Mehrfachauswahlfelder';

    private static $table_name = 'Streunerkatzen_EditableMultiSelectField';

     /**
     * @return ListboxField
     */
    public function getFormField() {
        $field = ListboxField::create($this->Name, $this->Title ?: false, $this->getOptionsMap())
            ->setFieldHolderTemplate(EditableFormField::class . '_holder')
            ->setTemplate(__CLASS__);

        if ($this->UseEmptyString) {
            $field->setEmptyString(($this->EmptyString) ? $this->EmptyString : '');
        }

        $defaultOption = $this->getDefaultOptions()->first();
        if ($defaultOption) {
            $field->setValue($defaultOption->Value);
        }
        $this->doUpdateFormField($field);
        return $field;
    }

    public function getValueFromData($data) {
        return join(';',$data[$this->Name]);
    }

    /**
     * Gets map of field options suitable for use in a form
     *
     * @return array
     */
    protected function getOptionsMap() {
        $optionSet = $this->Options();
        $options = [];
        foreach ($optionSet as $option) {
            $options[] = new ArrayData(array(
                "Value" => $option->Value,
                "Title" => $option->Title,
                "Examples" => $option->Examples()
            ));
        }
        return $options;
    }

    public function getCMSFields() {

        $this->beforeUpdateCMSFields(function (\SilverStripe\Forms\FieldList $fields) {
            $editableColumns = new GridFieldEditableColumns();
            $editableColumns->setDisplayFields([
                'Title' => [
                    'title' => 'Beschreibung',
                    'callback' => function ($record, $column, $grid) {
                        return TextField::create($column);
                    }
                ],
                'Value' => [
                    'title' => 'Wert',
                    'callback' => function ($record, $column, $grid) {
                        return TextField::create($column);
                    }
                ],
                'Default' => [
                    'title' => 'Standardmäßig aktiv?',
                    'callback' => function ($record, $column, $grid) {
                        return CheckboxField::create($column);
                    }
                ],
                'Examples' => [
                    'title' => 'Beispielbild',
                    'callback' => function ($record, $column, $grid) use ($editableColumns) {
                        $field = UploadField::create($column);
                        $form = $field->getForm();
                        if (!isset($form)) {
                            $form = $grid->getForm($grid, $record);
                        }
                        $field->setForm($form);
                        return $field;
                    }
                ]
            ]);

            $optionsConfig = GridFieldConfig::create()
                ->addComponents(
                    new GridFieldToolbarHeader(),
                    new GridFieldTitleHeader(),
                    new GridFieldOrderableRows('Sort'),
                    $editableColumns,
                    new GridFieldButtonRow(),
                    new GridFieldAddNewInlineButton(),
                    new GridFieldDeleteAction()
                );

            $optionsGrid = GridField::create(
                'Options',
                'Optionen',
                $this->Options(),
                $optionsConfig
            );
            $fields->removeByName('Options');
            $fields->insertAfter(Tab::create('Options', 'Optionen'), 'Main');
            $fields->addFieldToTab('Root.Options', $optionsGrid);
        });
        return parent::getCMSFields();
    }
}
