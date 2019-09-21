<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataExtension;
use SilverStripe\UserForms\Model\Submission\SubmittedForm;
use SilverStripe\UserForms\Model\Submission\SubmittedFormField;
use SilverStripe\View\Requirements;

class UserDefinedFormControllerExtension extends DataExtension {
    public function onAfterInit() {
        $token = $this->owner->getRequest()->getVar('token');
        if (!$token) {
            return;
        }
        /** @var \SilverStripe\Control\Session $sessionData  */
        $sessionData = $this->owner->getRequest()->getSession();
        $sessionData->getAll();
        $submittedForm = SubmittedForm::get()->filter(array('EditToken' => $token))->first();
        $submittedFormFields = SubmittedFormField::get()->filter(array('ParentID' => $submittedForm->ID))->map('Name', 'Value')->toArray();
        //var_dump($submittedFormFields);
        /** @var \SilverStripe\UserForms\Model\UserDefinedForm */
        $form = $this->owner->data();
        $formFields = $form->Fields();
        /** @var \SilverStripe\UserForms\Model\EditableFormField $field */
        foreach ($formFields as $field) {
            if ($submittedFormFields[$field->Name]) {
                $field
                    ->getFormField()
                    ->setAttribute('data-val', $submittedFormFields[$field->Name]);
            }
        }

        Requirements::themedJavascript("form.js");
    }
    public function Form() {
        echo 'fooo';
    }
}
