<?php

namespace Streunerkatzen;

use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;

class EditableMultipleOptionFieldExtension extends DataExtension
{

    public function updateCMSFields(\SilverStripe\Forms\FieldList $fields)
    {
        echo 'update fields';
    }
    public function getCMSFields()
    {
            $this->owner->beforeUpdateCMSFields(function ($fields) {
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
                    'title' => 'Beispielbilder',
                    'callback' => function ($record, $column, $grid) {
                        return UploadField::create($column);
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
                $this->owner->Options(),
                $optionsConfig
            );

            $fields->insertAfter(Tab::create('Options', 'Optionen'), 'Main');
            $fields->addFieldToTab('Root.Options', $optionsGrid);
        });
        $fields = $this->owner->parent::getCMSFields();

        return $fields;
    }
}
