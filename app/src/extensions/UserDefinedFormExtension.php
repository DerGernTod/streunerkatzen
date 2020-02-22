<?php
namespace Streunerkatzen;

use SilverStripe\Dev\Debug;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\UserForms\Form\GridFieldAddClassesButton;
use SilverStripe\UserForms\Model\Submission\SubmittedForm;
use SilverStripe\UserForms\Model\Submission\SubmittedFormField;

class UserDefinedFormExtension extends DataExtension {
    private static $db = [
        'IsCatEntryForm' => 'Boolean',
        'CatReviewTemplate' => 'HTMLText'
    ];
    public function populateDefaults() {
        $token = Controller::curr()->getRequest()->getVar('token');
        if (!$token) {
            return;
        }
        $submittedForm = SubmittedForm::get()->filter(array('EditToken' => $token))->first();
        $submittedFormFields = SubmittedFormField::get()->filter(array('ParentID' => $submittedForm->ID))->map('Name', 'Value')->toArray();
        /** @var \SilverStripe\UserForms\Model\UserDefinedForm */
        $form = Controller::curr()->data();
        /** @var \SilverStripe\Forms\FieldList */
        $formFields = $form->Fields();
        /** @var \SilverStripe\UserForms\Model\EditableFormField $field */
        foreach ($formFields as $field) {
            if (array_key_exists($field->Name, $submittedFormFields)) {
                Debug::message('Setting Form field '.($field->Name).' to '.$submittedFormFields[$field->Name]);
                // TODO: setting the value doesn't work
                $field
                    ->getFormField()
                    ->setValue($submittedFormFields[$field->Name]);
            } else {
                Debug::message('Not setting Form field '.($field->Name));
            }
        }
        parent::populateDefaults();
    }
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
            $fields->addFieldToTab('Root.FormOptions', HTMLEditorField::create('CatReviewTemplate', 'Vorlage für "Bearbeitung erwünscht" Email', 'Benutze $EditLink um den Bearbeiten-Link anzuzeigen.'));
        }
    }
    private function updateGridFieldConfig(GridFieldConfig $config) {
        $config->removeComponentsByType(GridFieldAddClassesButton::class);
        $config->removeComponentsByType(GridFieldButtonRow::class);
        $config->removeComponentsByType(GridFieldDeleteAction::class);
    }
}
