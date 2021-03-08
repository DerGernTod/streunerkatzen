<?php

namespace Streunerkatzen\Controllers;

use PageController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextareaField;
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
    }
}
