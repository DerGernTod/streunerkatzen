<?php
namespace Streunerkatzen;

use SilverStripe\Control\Controller;
use SilverStripe\Dev\Debug;
use SilverStripe\ORM\DataExtension;
use SilverStripe\UserForms\Model\Submission\SubmittedForm;
use SilverStripe\UserForms\Model\Submission\SubmittedFormField;

class UserFormExtension extends DataExtension {
    public function updateForm() {
        $token = Controller::curr()->getRequest()->getVar('token');
        if (!$token) {
            return;
        }

        $form = $this->owner;
        $formFields = $form->Fields();

        $submittedForm = SubmittedForm::get()
            ->filter(['EditToken' => $token])
            ->first();
        $submittedFormFields = SubmittedFormField::get()
            ->filter(['ParentID' => $submittedForm->ID])
            ->map('Name', 'Value')
            ->toArray();

        foreach ($formFields as $field) {
            if (array_key_exists($field->Name, $submittedFormFields)) {
                Debug::message('Setting Form field '.
                ($field->Name).
                ' to '.
                $submittedFormFields[$field->Name]);
                $field->setValue($submittedFormFields[$field->Name]);
            } else {    // form is divided in form steps --> children contain the actual form fields
                if (property_exists($field, 'children')) {
                    foreach ($field->children as $childField) {
                        if (array_key_exists($childField->Name, $submittedFormFields)) {
                            Debug::message(
                                'Setting child form field '.
                                ($childField->Name) .
                                get_class($childField) .
                                ' to ' .
                                $submittedFormFields[$childField->Name]
                            );
                            $childField->setValue($submittedFormFields[$childField->Name]);
                        }
                    }
                }
                Debug::message('Not setting Form field '.($field->Name) . get_class($field));
            }
        }
    }
}
