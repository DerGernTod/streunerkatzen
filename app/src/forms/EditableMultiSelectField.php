<?php

namespace Streunerkatzen;

use SilverStripe\UserForms\Model\EditableFormField\EditableMultipleOptionField;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\Forms\ListboxField;

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
}
