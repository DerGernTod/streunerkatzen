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
use SilverStripe\UserForms\Extension\UserFormFieldEditorExtension;
use SilverStripe\UserForms\Form\GridFieldAddClassesButton;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\UserForms\Model\EditableFormField\EditableFieldGroup;
use SilverStripe\UserForms\Model\EditableFormField\EditableFieldGroupEnd;
use SilverStripe\UserForms\Model\EditableFormField\EditableFileField;
use SilverStripe\UserForms\Model\EditableFormField\EditableFormStep;
use SilverStripe\UserForms\Model\EditableFormField\EditableTextField;
use SilverStripe\View\Requirements;
use Streunerkatzen\Elements\CatElementForm;
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

        $isCatField = strcmp(get_class($this->owner), CatElementForm::class) == 0;
        $fieldClasses = [];
        if ($isCatField) {
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
}
