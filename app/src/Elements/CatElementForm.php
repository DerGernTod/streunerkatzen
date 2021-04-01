<?php
namespace Streunerkatzen\Elements;

use Colymba\BulkManager\BulkManager;
use DNADesign\ElementalUserForms\Model\ElementForm;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LabelField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DB;
use SilverStripe\UserForms\Form\UserFormsGridFieldFilterHeader;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\UserForms\Model\Recipient\UserFormRecipientItemRequest;
use SilverStripe\View\Requirements;

class CatElementForm extends ElementForm {
    private static $table_name = 'Streunerkatzen_CatElementForm';
    private static $singular_name = 'Katzenformular';
    private static $plural_name = 'Katzenformulare';

    public function getType() {
        return _t(__CLASS__ . '.BlockType', 'Katzenformular');
    }

    /**
     * @return FieldList
     */
    public function getCMSFields() {
        Requirements::css('silverstripe/userforms:client/dist/styles/userforms-cms.css');

        $this->beforeUpdateCMSFields(function ($fields) {

            // remove
            $fields->removeByName('OnCompleteMessageLabel');
            $fields->removeByName('OnCompleteMessage');
            $fields->removeByName('Fields');
            $fields->removeByName('EmailRecipients');

            // define tabs
            $fields->findOrMakeTab('Root.FormOptions', _t(__CLASS__.'.CONFIGURATION', 'Configuration'));
            $fields->findOrMakeTab('Root.Recipients', _t(__CLASS__.'.RECIPIENTS', 'Recipients'));
            $fields->findOrMakeTab('Root.Submissions', _t(__CLASS__.'.SUBMISSIONS', 'Submissions'));


            // text to show on complete
            $onCompleteFieldSet = CompositeField::create(
                $label = LabelField::create(
                    'OnCompleteMessageLabel',
                    _t(__CLASS__.'.ONCOMPLETELABEL', 'Show on completion')
                ),
                $editor = HTMLEditorField::create(
                    'OnCompleteMessage',
                    '',
                    $this->OnCompleteMessage
                )
            );

            $onCompleteFieldSet->addExtraClass('field');

            $editor->setRows(3);
            $label->addExtraClass('left');

            // Define config for email recipients
            $emailRecipientsConfig = GridFieldConfig_RecordEditor::create(10);
            $emailRecipientsConfig->getComponentByType(GridFieldAddNewButton::class)
                ->setButtonName(
                    _t(__CLASS__.'.ADDEMAILRECIPIENT', 'Add Email Recipient')
                );

            // who do we email on submission
            $emailRecipients = GridField::create(
                'EmailRecipients',
                '',
                $this->EmailRecipients(),
                $emailRecipientsConfig
            );
            $emailRecipients
                ->getConfig()
                ->getComponentByType(GridFieldDetailForm::class)
                ->setItemRequestClass(UserFormRecipientItemRequest::class);

            $fields->addFieldsToTab('Root.FormOptions', $onCompleteFieldSet);
            $fields->addFieldToTab('Root.Recipients', $emailRecipients);
            $fields->addFieldsToTab('Root.FormOptions', $this->getFormOptions());


            // view the submissions
            // make sure a numeric not a empty string is checked against this int column for SQL server
            $parentID = (!empty($this->ID)) ? (int) $this->ID : 0;

            // get a list of all field names and values used for print and export CSV views of the GridField below.
            $columnSQL = <<<SQL
SELECT "SubmittedFormField"."Name" as "Name", COALESCE("EditableFormField"."Title", "SubmittedFormField"."Title") as "Title", COALESCE("EditableFormField"."Sort", 999) AS "Sort"
FROM "SubmittedFormField"
LEFT JOIN "SubmittedForm" ON "SubmittedForm"."ID" = "SubmittedFormField"."ParentID"
LEFT JOIN "EditableFormField" ON "EditableFormField"."Name" = "SubmittedFormField"."Name" AND "EditableFormField"."ParentID" = '$parentID'
WHERE "SubmittedForm"."ParentID" = '$parentID'
ORDER BY "Sort", "Title"
SQL;
            // Sanitise periods in title
            $columns = array();

            foreach (DB::query($columnSQL)->map() as $name => $title) {
                $columns[$name] = trim(strtr($title, '.', ' '));
            }

            $config = GridFieldConfig::create();
            $config->addComponent(new GridFieldToolbarHeader());
            $config->addComponent($sort = new GridFieldSortableHeader());
            $config->addComponent($filter = new UserFormsGridFieldFilterHeader());
            $config->addComponent(new GridFieldDataColumns());
            $config->addComponent(new GridFieldEditButton());
            $config->addComponent(new GridFieldDeleteAction());
            $config->addComponent(new GridFieldPageCount('toolbar-header-right'));
            $config->addComponent($pagination = new GridFieldPaginator(25));
            $config->addComponent(new GridFieldDetailForm(null, true, false));
            $config->addComponent(new GridFieldButtonRow('after'));
            $config->addComponent($export = new GridFieldExportButton('buttons-after-left'));
            $config->addComponent($print = new GridFieldPrintButton('buttons-after-left'));

            // show user form items in the summary tab
            $summaryarray = array(
                'ID' => 'ID',
                'Created' => 'Erstellt',
                'LastEdited' => 'Zuletzt bearbeitet',
                'CatFormSubmissionStatus' => 'Status'
            );

            foreach (EditableFormField::get()->filter(array('ParentID' => $parentID)) as $eff) {
                if ($eff->ShowInSummary) {
                    $summaryarray[$eff->Name] = $eff->Title ?: $eff->Name;
                }
            }

            $config->getComponentByType(GridFieldDataColumns::class)->setDisplayFields($summaryarray);

            /**
             * Support for {@link https://github.com/colymba/GridFieldBulkEditingTools}
             */
            if (class_exists(BulkManager::class)) {
                $config->addComponent(new BulkManager);
            }

            $sort->setThrowExceptionOnBadDataType(false);
            $filter->setThrowExceptionOnBadDataType(false);
            $pagination->setThrowExceptionOnBadDataType(false);

            // attach every column to the print view form
            $columns['Created'] = 'Created';
            $columns['SubmittedBy.Email'] = 'Submitter';
            $filter->setColumns($columns);

            // print configuration

            $print->setPrintHasHeader(true);
            $print->setPrintColumns($columns);

            // export configuration
            $export->setCsvHasHeader(true);
            $export->setExportColumns($columns);

            $submissions = GridField::create(
                'Submissions',
                '',
                $this->Submissions()->sort([
                    'CatFormSubmissionStatus' => 'ASC',
                    'Created' => 'DESC'
                ]),
                $config
            );
            $fields->addFieldToTab('Root.Submissions', $submissions);
            $fields->addFieldToTab(
                'Root.FormOptions',
                CheckboxField::create(
                    'DisableSaveSubmissions',
                    _t(__CLASS__.'.SAVESUBMISSIONS', 'Disable Saving Submissions to Server')
                )
            );
        });

        $fields = parent::getCMSFields();

        if ($this->EmailRecipients()->Count() == 0 && static::config()->recipients_warning_enabled) {
            $fields->addFieldToTab('Root.Main', LiteralField::create(
                'EmailRecipientsWarning',
                '<p class="alert alert-warning">' . _t(
                    __CLASS__.'.NORECIPIENTS',
                    'Warning: You have not configured any recipients. Form submissions may be missed.'
                )
                . '</p>'
            ), 'Title');
        }

        return $fields;
    }
}
