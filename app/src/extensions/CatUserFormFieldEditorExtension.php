<?php
namespace Streunerkatzen\Extensions;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\UserForms\Control\UserDefinedFormAdmin;
use SilverStripe\UserForms\Extension\UserFormFieldEditorExtension;
use SilverStripe\UserForms\Form\GridFieldAddClassesButton;
use SilverStripe\UserForms\Model\EditableCustomRule;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\UserForms\Model\EditableFormField\EditableFieldGroup;
use SilverStripe\UserForms\Model\EditableFormField\EditableFieldGroupEnd;
use SilverStripe\UserForms\Model\EditableFormField\EditableFileField;
use SilverStripe\UserForms\Model\EditableFormField\EditableFormStep;
use SilverStripe\UserForms\Model\EditableFormField\EditableTextField;
use SilverStripe\View\Requirements;
use Streunerkatzen\Elements\CatElementForm;
use Streunerkatzen\Forms\CatFormFields\CatAgeTextField;
use Streunerkatzen\Forms\CatFormFields\CatBehaviourOwnerTextField;
use Streunerkatzen\Forms\CatFormFields\CatBehaviourStrangerTextField;
use Streunerkatzen\Forms\CatFormFields\CatBreedTextField;
use Streunerkatzen\Forms\CatFormFields\CatIsCastratedDropdownField;
use Streunerkatzen\Forms\CatFormFields\CatCharacteristicsTextField;
use Streunerkatzen\Forms\CatFormFields\CatChipNumberTextField;
use Streunerkatzen\Forms\CatFormFields\CatIsChippedDropdownField;
use Streunerkatzen\Forms\CatFormFields\CatColorCharacteristicsTextField;
use Streunerkatzen\Forms\CatFormFields\CatContactField;
use Streunerkatzen\Forms\CatFormFields\CatEyeColorTextField;
use Streunerkatzen\Forms\CatFormFields\CatFurColorMultiSelectField;
use Streunerkatzen\Forms\CatFormFields\CatFurLengthDropdownField;
use Streunerkatzen\Forms\CatFormFields\CatGenderDropdownField;
use Streunerkatzen\Forms\CatFormFields\CatIsHouseCatDropdownField;
use Streunerkatzen\Forms\CatFormFields\CatLostFoundDateField;
use Streunerkatzen\Forms\CatFormFields\CatLostFoundDescriptionTextField;
use Streunerkatzen\Forms\CatFormFields\CatLostFoundTimeDropdownField;
use Streunerkatzen\Forms\CatFormFields\CatMoreInfoTextField;
use Streunerkatzen\Forms\CatFormFields\CatTitleTextField;
use Streunerkatzen\Forms\CatFormFields\CatPetCollarDescriptionTextField;
use Streunerkatzen\Forms\CatFormFields\CatHasPetCollarDropdownField;
use Streunerkatzen\Forms\CatFormFields\CatLostFoundStatusDropdownField;
use Streunerkatzen\Forms\CatFormFields\CatStreetTextField;
use Streunerkatzen\Forms\CatFormFields\CatTattooTextField;
use Streunerkatzen\Forms\CatFormFields\CatTownTextField;
use Streunerkatzen\Forms\CatFormFields\CatZipcodeNumericField;
use Streunerkatzen\Forms\EditableFedStateField;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class CatUserFormFieldEditorExtension extends UserFormFieldEditorExtension {
/**
     * Gets the field editor, for adding and removing EditableFormFields.
     *
     * @return GridField
     */
    public function getFieldEditorGrid() {
        Requirements::javascript('silverstripe/admin:client/dist/js/vendor.js');
        Requirements::javascript('silverstripe/admin:client/dist/js/bundle.js');
        Requirements::javascript('silverstripe/userforms:client/dist/js/userforms-cms.js');

        $fields = $this->owner->Fields();

        $this->owner->createInitialFormStep(true);

        $editableColumns = new GridFieldEditableColumns();

        $isCatForm = strcmp(get_class($this->owner), CatElementForm::class) == 0;
        $fieldClasses = [];
        if ($isCatForm) {
            $fieldClasses = singleton(EditableFormField::class)->getEditableCatFieldClasses();
        } else {
            $fieldClasses = singleton(EditableFormField::class)->getEditableStandardFieldClasses();
        }

        $editableColumns->setDisplayFields([
            'ClassName' => function ($record, $column, $grid) use ($fieldClasses) {
                if ($record instanceof EditableFormField) {
                    $field = $record->getInlineClassnameField($column, $fieldClasses);
                    if ($record instanceof EditableFileField) {
                        $field->setAttribute('data-folderconfirmed', $record->FolderConfirmed ? 1 : 0);
                    }
                    return $field;
                }
            },
            'Title' => function ($record, $column, $grid) {
                if ($record instanceof EditableFormField) {
                    return $record->getInlineTitleField($column);
                }
            }
        ]);

        $config = GridFieldConfig::create()
            ->addComponents(
                $editableColumns,
                new GridFieldButtonRow(),
                (new GridFieldAddClassesButton(EditableTextField::class))
                    ->setButtonName(_t(__CLASS__.'.ADD_FIELD', 'Add Field'))
                    ->setButtonClass('btn-primary'),
                (new GridFieldAddClassesButton(EditableFormStep::class))
                    ->setButtonName(_t(__CLASS__.'.ADD_PAGE_BREAK', 'Add Page Break'))
                    ->setButtonClass('btn-secondary'),
                (new GridFieldAddClassesButton([EditableFieldGroup::class, EditableFieldGroupEnd::class]))
                    ->setButtonName(_t(__CLASS__.'.ADD_FIELD_GROUP', 'Add Field Group'))
                    ->setButtonClass('btn-secondary'),
                $editButton = new GridFieldEditButton(),
                new GridFieldDeleteAction(),
                new GridFieldToolbarHeader(),
                new GridFieldOrderableRows('Sort'),
                new GridFieldDetailForm(),
                // Betterbuttons prev and next is enabled by adding a GridFieldPaginator component
                new GridFieldPaginator(999)
            );

        $editButton->removeExtraClass('grid-field__icon-action--hidden-on-hover');

        $fieldEditor = GridField::create(
            'Fields',
            '',
            $fields,
            $config
        )->addExtraClass('uf-field-editor');

        return $fieldEditor;
    }

    /**
     * A UserForm must have at least one step.
     * If no steps exist, create an initial step, and put all fields inside it.
     *
     * @param bool $force
     * @return void
     */
    public function createInitialFormStep($force = false) {
        // Only invoke once saved
        if (!$this->owner->exists()) {
            return;
        }

        // Check if first field is a step
        $fields = $this->owner->Fields();
        $firstField = $fields->first();
        if ($firstField instanceof EditableFormStep) {
            return;
        }

        // Don't create steps on write if there are no formfields, as this
        // can create duplicate first steps during publish of new records
        if (!$force && !$firstField) {
            return;
        }

        // Re-apply sort to each field starting at 2
        $next = 2;
        foreach ($fields as $field) {
            $field->Sort = $next++;
            $field->write();
        }

        $isCatForm = strcmp(get_class($this->owner), CatElementForm::class) == 0;
        if ($isCatForm) {
            // Add step
            $step = EditableFormStep::create();
            $step->Title = "Beschreibung zur Katze";
            $step->Sort = 1;
            $step->write();
            $fields->add($step);
            $step->publish("Stage", "Live");

            // add initial cat form fields if there is only 1 form step
            if ($fields->count() == 1) {
                $this->owner->initCatFormFields();
            }
        } else {
            // Add step
            $step = EditableFormStep::create();
            $step->Title = _t('SilverStripe\\UserForms\\Model\\EditableFormField\\EditableFormStep.TITLE_FIRST', 'First Page');
            $step->Sort = 1;
            $step->write();
            $fields->add($step);
        }
    }

    public function initCatFormFields() {
        $fields = $this->owner->Fields();
        $sort = 2;

        // status dropdown
        $statusDrop = new CatLostFoundStatusDropdownField();
        $statusDrop->Title = "Status";
        $statusDrop->Sort = $sort++;
        $statusDrop->Required = true;
        $statusDrop->write();
        $fields->add($statusDrop);
        $statusDrop->publish("Stage", "Live");
        // name textfield
        $nameText = new CatTitleTextField();
        $nameText->Title = "Name";
        $nameText->Sort = $sort++;
        $nameText->Required = true;
        $nameText->write();
        $fields->add($nameText);
        $nameText->publish("Stage", "Live");
        // breed textfield
        $breedText = new CatBreedTextField();
        $breedText->Title = "Rasse";
        $breedText->Sort = $sort++;
        $breedText->write();
        $fields->add($breedText);
        $breedText->publish("Stage", "Live");
        // age textfield
        $ageText = new CatAgeTextField();
        $ageText->Title = "Alter";
        $ageText->Sort = $sort++;
        $ageText->write();
        $fields->add($ageText);
        $ageText->publish("Stage", "Live");
        // gender dropdown
        $genderDrop = new CatGenderDropdownField();
        $genderDrop->Title = "Geschlecht";
        $genderDrop->Sort = $sort++;
        $genderDrop->write();
        $fields->add($genderDrop);
        $genderDrop->publish("Stage", "Live");
        // castrated dropdown
        $castratedDrop = new CatIsCastratedDropdownField();
        $castratedDrop->Title = "Kastriert?";
        $castratedDrop->Sort = $sort++;
        $castratedDrop->write();
        $fields->add($castratedDrop);
        $castratedDrop->publish("Stage", "Live");
        // chipped dropdown
        $chippedDrop = new CatIsChippedDropdownField();
        $chippedDrop->Title = "Gechippt?";
        $chippedDrop->Sort = $sort++;
        $chippedDrop->write();
        $fields->add($chippedDrop);
        $chippedDrop->publish("Stage", "Live");
        // chipnumber textfield
        $chipnumberText = new CatChipNumberTextField();
        $chipnumberText->Title = "Chipnummer";
        $chipnumberText->Sort = $sort++;
        $chipnumberText->ShowOnLoad = false;
        $chipnumberText->write();
        $fields->add($chipnumberText);
        $chipnumberText->publish("Stage", "Live");
        $chipnumberRule = new EditableCustomRule();
        $chipnumberRule->Display = "Show";
        $chipnumberRule->ConditionOption = "HasValue";
        $chipnumberRule->FieldValue = "ja";
        $chipnumberRule->ParentID = $chipnumberText->ID;
        $chipnumberRule->ConditionFieldID = $chippedDrop->ID;
        $chipnumberRule->write();
        $chipnumberRule->publish("Stage", "Live");

        // step 2
        $step2 = EditableFormStep::create();
        $step2->Title = "Aussehen";
        $step2->Sort = $sort++;
        $step2->write();
        $fields->add($step2);
        $step2->publish("Stage", "Live");
        // fur color dropdown
        $furColorDrop = new CatFurColorMultiSelectField();
        $furColorDrop->Title = "Fellfarbe";
        $furColorDrop->Sort = $sort++;
        $furColorDrop->write();
        $fields->add($furColorDrop);
        $furColorDrop->publish("Stage", "Live");
        // fur length dropdown
        $furLengthDrop = new CatFurLengthDropdownField();
        $furLengthDrop->Title = "Haarlänge";
        $furLengthDrop->Sort = $sort++;
        $furLengthDrop->write();
        $fields->add($furLengthDrop);
        $furLengthDrop->publish("Stage", "Live");
        // characteristics textfield
        $characteristicsText = new CatCharacteristicsTextField();
        $characteristicsText->Title = "Besonderheiten";
        $characteristicsText->Sort = $sort++;
        $characteristicsText->Rows = 3;
        $characteristicsText->write();
        $fields->add($characteristicsText);
        $characteristicsText->publish("Stage", "Live");
        // color characteristics textfield
        $colorCharacteristicsText = new CatColorCharacteristicsTextField();
        $colorCharacteristicsText->Title = "Farbliche Besonderheiten";
        $colorCharacteristicsText->Sort = $sort++;
        $colorCharacteristicsText->Rows = 3;
        $colorCharacteristicsText->write();
        $fields->add($colorCharacteristicsText);
        $colorCharacteristicsText->publish("Stage", "Live");
        // eye color textfield
        $eyeColorText = new CatEyeColorTextField();
        $eyeColorText->Title = "Augenfarbe";
        $eyeColorText->Sort = $sort++;
        $eyeColorText->write();
        $fields->add($eyeColorText);
        $eyeColorText->publish("Stage", "Live");
        // tattoo textfield
        $tattooText = new CatTattooTextField();
        $tattooText->Title = "Tattoo";
        $tattooText->Sort = $sort++;
        $tattooText->write();
        $fields->add($tattooText);
        $tattooText->publish("Stage", "Live");
        // pet collar dropdown
        $petCollarDrop = new CatHasPetCollarDropdownField();
        $petCollarDrop->Title = "Halsband?";
        $petCollarDrop->Sort = $sort++;
        $petCollarDrop->write();
        $fields->add($petCollarDrop);
        $petCollarDrop->publish("Stage", "Live");
        // pet collar description textfield
        $petCollarDescriptionText = new CatPetCollarDescriptionTextField();
        $petCollarDescriptionText->Title = "Beschreibung des Halsbands";
        $petCollarDescriptionText->Sort = $sort++;
        $petCollarDescriptionText->ShowOnLoad = false;
        $petCollarDescriptionText->write();
        $fields->add($petCollarDescriptionText);
        $petCollarDescriptionText->publish("Stage", "Live");
        $petCollarDescriptionRule = new EditableCustomRule();
        $petCollarDescriptionRule->Display = "Show";
        $petCollarDescriptionRule->ConditionOption = "HasValue";
        $petCollarDescriptionRule->FieldValue = "ja";
        $petCollarDescriptionRule->ParentID = $petCollarDescriptionText->ID;
        $petCollarDescriptionRule->ConditionFieldID = $petCollarDrop->ID;
        $petCollarDescriptionRule->write();
        $petCollarDescriptionRule->publish("Stage", "Live");

        // step 3
        $step3 = EditableFormStep::create();
        $step3->Title = "Verhalten";
        $step3->Sort = $sort++;
        $step3->write();
        $fields->add($step3);
        $step3->publish("Stage", "Live");
        // house cat dropdown
        $houseCatDrop = new CatIsHouseCatDropdownField();
        $houseCatDrop->Title = "Hauskatze?";
        $houseCatDrop->Sort = $sort++;
        $houseCatDrop->write();
        $fields->add($houseCatDrop);
        $houseCatDrop->publish("Stage", "Live");
        // behaviour owner textfield
        $behaviourOwnerText = new CatBehaviourOwnerTextField();
        $behaviourOwnerText->Title = "Verhalten gegenüber Besitzer";
        $behaviourOwnerText->Sort = $sort++;
        $behaviourOwnerText->Rows = 3;
        $behaviourOwnerText->write();
        $fields->add($behaviourOwnerText);
        $behaviourOwnerText->publish("Stage", "Live");
        // behaviour stranger textfield
        $behaviourStrangerText = new CatBehaviourStrangerTextField();
        $behaviourStrangerText->Title = "Verhalten gegenüber Fremden";
        $behaviourStrangerText->Sort = $sort++;
        $behaviourStrangerText->Rows = 3;
        $behaviourStrangerText->write();
        $fields->add($behaviourStrangerText);
        $behaviourStrangerText->publish("Stage", "Live");

        // step 4
        $step4 = EditableFormStep::create();
        $step4->Title = "Ort";
        $step4->Sort = $sort++;
        $step4->write();
        $fields->add($step4);
        $step4->publish("Stage", "Live");
        // street textfield
        $streetText = new CatStreetTextField();
        $streetText->Title = "Straße";
        $streetText->Sort = $sort++;
        $streetText->write();
        $fields->add($streetText);
        $streetText->publish("Stage", "Live");
        // town textfield
        $townText = new CatTownTextField();
        $townText->Title = "Ort";
        $townText->Sort = $sort++;
        $townText->Required = true;
        $townText->write();
        $fields->add($townText);
        $townText->publish("Stage", "Live");
        // zip code number field
        $zipNumber = new CatZipcodeNumericField();
        $zipNumber->Title = "PLZ";
        $zipNumber->Sort = $sort++;
        $zipNumber->write();
        $fields->add($zipNumber);
        $zipNumber->publish("Stage", "Live");
        // fed state dropdown
        $fedStateDrop = new EditableFedStateField();
        $fedStateDrop->Title = "Bundesland";
        $fedStateDrop->Sort = $sort++;
        $fedStateDrop->write();
        $fields->add($fedStateDrop);
        $fedStateDrop->publish("Stage", "Live");

        // step 5
        $step5 = EditableFormStep::create();
        $step5->Title = "Details";
        $step5->Sort = $sort++;
        $step5->write();
        $fields->add($step5);
        $step5->publish("Stage", "Live");
        // date field
        $date = new CatLostFoundDateField();
        $date->Title = "Datum";
        $date->Sort = $sort++;
        $date->Required = true;
        $date->write();
        $fields->add($date);
        $date->publish("Stage", "Live");
        // lost found time dropdown
        $lostFoundDrop = new CatLostFoundTimeDropdownField();
        $lostFoundDrop->Title = "Zeit";
        $lostFoundDrop->Sort = $sort++;
        $lostFoundDrop->write();
        $fields->add($lostFoundDrop);
        $lostFoundDrop->publish("Stage", "Live");
        // situation description textfield
        $situationDescriptionText = new CatLostFoundDescriptionTextField();
        $situationDescriptionText->Title = "Beschreibung der Situation";
        $situationDescriptionText->Sort = $sort++;
        $situationDescriptionText->Rows = 3;
        $situationDescriptionText->write();
        $fields->add($situationDescriptionText);
        $situationDescriptionText->publish("Stage", "Live");
        // details textfield
        $detailsText = new CatMoreInfoTextField();
        $detailsText->Title = "Details";
        $detailsText->Sort = $sort++;
        $detailsText->Rows = 3;
        $detailsText->write();
        $fields->add($detailsText);
        $detailsText->publish("Stage", "Live");
        // file upload field
        $fileFolder = UserDefinedFormAdmin::getFormSubmissionFolder();
        $file1 = new EditableFileField();
        $file1->Title = "Anhang";
        $file1->Sort = $sort++;
        $file1->MaxFileSizeMB = 10;
        $file1->FolderID = $fileFolder->ID;
        $file1->FolderConfirmed = true;
        $file1->write();
        $fields->add($file1);
        $file1->publish("Stage", "Live");
        // file upload field 2
        $file2 = new EditableFileField();
        $file2->Title = "Anhang 2";
        $file2->Sort = $sort++;
        $file2->MaxFileSizeMB = 10;
        $file2->FolderID = $fileFolder->ID;
        $file2->FolderConfirmed = true;
        $file2->ShowOnLoad = false;
        $file2->write();
        $fields->add($file2);
        $file2->publish("Stage", "Live");
        $file2Rule = new EditableCustomRule();
        $file2Rule->Display = "Show";
        $file2Rule->ConditionOption = "IsNotBlank";
        $file2Rule->ParentID = $file2->ID;
        $file2Rule->ConditionFieldID = $file1->ID;
        $file2Rule->write();
        $file2Rule->publish("Stage", "Live");
        // file upload field 3
        $file3 = new EditableFileField();
        $file3->Title = "Anhang 3";
        $file3->Sort = $sort++;
        $file3->MaxFileSizeMB = 10;
        $file3->FolderID = $fileFolder->ID;
        $file3->FolderConfirmed = true;
        $file3->ShowOnLoad = false;
        $file3->write();
        $fields->add($file3);
        $file3->publish("Stage", "Live");
        $file3Rule = new EditableCustomRule();
        $file3Rule->Display = "Show";
        $file3Rule->ConditionOption = "IsNotBlank";
        $file3Rule->ParentID = $file3->ID;
        $file3Rule->ConditionFieldID = $file2->ID;
        $file3Rule->write();
        $file3Rule->publish("Stage", "Live");
        // e-mail field
        $email = new CatContactField();
        $email->Title = "Kontakt E-Mail-Adresse";
        $email->Sort = $sort++;
        $email->Required = true;
        $email->write();
        $fields->add($email);
        $email->publish("Stage", "Live");
    }
}
