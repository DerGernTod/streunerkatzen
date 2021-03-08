<?php

namespace Streunerkatzen\Controllers;

use PageController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Email\Email;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextareaField;
use SilverStripe\View\ArrayData;
use Streunerkatzen\Cats\Cat;

class CatController extends PageController {
    private static $allowed_actions = [
        'view',
        'ContactForm'
    ];

    public function view(HTTPRequest $request) {
        $cat = Cat::get()->byID($request->param('ID'));
        if (!$cat) {
            return $this->owner->httpError(404, 'Diese Katze hat sich versteckt!');
        }
        if ($request->isAjax()) {
            return $cat->renderWith('Streunerkatzen/Includes/CatSingle');
        } else {
            return [ 'Cat' => $cat ];
        }
    }

    public function ContactForm($catID) {
        $fields = new FieldList(
            TextareaField::create('Message', 'Nachricht'),
            HiddenField::create('CatID', 'Katzen ID')->setValue($catID)
        );

        $actions = new FieldList(
            FormAction::create('handleContactForm')
                ->addExtraClass('button')
                ->setTitle('Nachricht absenden')
        );

        $required = new RequiredFields('Message');

        $form = new Form($this, 'ContactForm', $fields, $actions, $required);
        $form->setFormAction(
            Controller::join_links(
                'cats',
                'ContactForm'
            )
        );
        $form->enableSpamProtection();
        $form->addExtraClass('ajax-form');

        return $form;
    }

    public function handleContactForm($data, Form $form) {
        $catID = $data['CatID'];
        $msg = $data['Message'];

        $cat = Cat::get()->byID($catID);
        if (!$cat) {
            return $this->httpError(404, 'Diese Katze existiert nicht.');
        }

        $templateData = new ArrayData([
            'Cat' => $cat,
            'Message' => $msg,
            'AdminMail' => array_key_first(Email::config()->get('admin_email'))
        ]);
        $emailContent = "";
        $emailTo = "";

        $contactData = $cat->Contact;
        if ($this->checkIfEmail($contactData)) {
            // send mail to contact
            $emailContent = $templateData->renderWith('Streunerkatzen/Controllers/Includes/CatMessageEmail_Contact');
            $emailTo = $contactData;
        } else {
            // send mail to admin
            $emailContent = $templateData->renderWith('Streunerkatzen/Controllers/Includes/CatMessageEmail_Admin');
            $emailTo = Email::config()->get('admin_email');
        }

        $email = new Email();
        $email->setTo($emailTo);
        $email->setSubject('Streunerkatzen Nachricht zum Eintrag "' . $cat->Title . '"');
        $email->setBody($emailContent);
        if (!$email->send()) {
            return $this->httpError(500, 'Fehler beim E-Mail senden!');
        }

        return $this->renderWith('Streunerkatzen/Controllers/Includes/CatMessageForm_Complete');
    }

    private function checkIfEmail($string) {
        $find1 = strpos($string, '@');
        $find2 = strpos($string, '.');

        return ($find1 !== false &&
                $find2 !== false &&
                $find2 > $find1);
    }
}
