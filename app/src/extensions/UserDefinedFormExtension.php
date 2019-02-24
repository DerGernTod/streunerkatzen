<?php
namespace Streunerkatzen;

use SilverStripe\Forms\Tab;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDataColumns;

class UserDefinedFormExtension extends DataExtension {
    private static $db = [
        'IsCatEntryForm' => 'Boolean'
    ];
    public function updateCMSFields(FieldList $fields) {
        $fields->addFieldsToTab('Root.FormOptions', CheckboxField::create('IsCatEntryForm', 'Nutze dieses Formular um Katzen einzutragen'));
        if ($this->owner->IsCatEntryForm) {
            $this->updateSubmissionGridField($fields->findOrMakeTab('Root.Submissions', 'Einreichungen')->children->items[0]);
            /*
            $submissions = GridField::create(
                'Submissions',
                '',
                $this->Submissions()->sort('Created', 'DESC'),
                $config
            );
            $fields->addFieldToTab('Root.Submissions', $submissions);
            */
        }
    }
    public function updateSubmissionGridField(GridField $gridField) {
        $cfg = $gridField->getConfig();
        $summaryarray = [
            'ID' => 'ID',
            'Created' => 'Erstellt',
            'LastEdited' => 'Zuletzt bearbeitet',
            'ActivationStatus' => 'Status'
        ];
        $cfg->getComponentByType(GridFieldDataColumns::class)->setDisplayFields($summaryarray);
    }
}
