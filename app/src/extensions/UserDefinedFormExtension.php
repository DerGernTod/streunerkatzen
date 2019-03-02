<?php
namespace Streunerkatzen;

use SilverStripe\Forms\Tab;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\UserForms\Form\GridFieldAddClassesButton;

class UserDefinedFormExtension extends DataExtension {
    private static $db = [
        'IsCatEntryForm' => 'Boolean'
    ];
    public function updateCMSFields(FieldList $fields) {
        $fields->addFieldsToTab('Root.FormOptions', CheckboxField::create('IsCatEntryForm', 'Nutze dieses Formular um Katzen einzutragen'));
        if ($this->owner->IsCatEntryForm) {
            $summaryarray = [
                'ID' => 'ID',
                'Created' => 'Erstellt',
                'LastEdited' => 'Zuletzt bearbeitet',
                'ActivationStatus' => 'Status'
            ];
            $gridField = $fields->findOrMakeTab('Root.Submissions', 'Einreichungen')->children->items[0];
            $gridField->getConfig()->getComponentByType(GridFieldDataColumns::class)->setDisplayFields($summaryarray);

            $formFieldTab = $fields->findOrMakeTab('Root.FormFields');
            $fieldGrid = $formFieldTab->children->items[0];
            $config = $fieldGrid->getConfig();
            $this->updateGridFieldConfig($config);
        }
    }
    private function updateGridFieldConfig(GridFieldConfig $config) {
        $config->removeComponentsByType(GridFieldAddClassesButton::class);
        $config->removeComponentsByType(GridFieldButtonRow::class);
        $config->removeComponentsByType(GridFieldDeleteAction::class);
    }
}
