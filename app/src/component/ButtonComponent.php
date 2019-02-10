<?php

namespace Streunerkatzen;

use Streunerkatzen\Component;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TreeDropdownField;

class ButtonComponent extends Component {

    private static $singular_name = 'Button';
    private static $plural_name = 'Buttons';
    private static $table_name = 'Streunerkatzen_ButtonComponent';

    public function canCreate($member = null, $context = []) {
        return true;
    }

    private static $db = [
        'Label' => 'Varchar(250)',
        'PageID' => 'Int',
        'Link' => 'Text',
        'IsNewWindow' => 'Boolean'
    ];

    public function getCMSfields() {
        $fields = parent::getCMSFields();

        $fields->push(
            TextField::create('Label', 'Buttontext')
        );

        $fields->push(
            TreeDropdownField::create(
                'PageID',
                'Interne Seite',
                SiteTree::class
            )->setDescription('Leer lassen falls eine externe URL verwendet werden soll.')
        );

        $fields->push(TextField::create('Link', 'Externe URL'));
        $fields->push(CheckboxField::create('IsNewWindow', 'In neuem Fenster öffnen?'));

        return $fields;
    }

    public function onBeforeWrite() {
        parent::onBeforeWrite();

        if ($this->PageID != 0) {
            $this->Link = null;
        }
    }

    public function getResultUrl() {
        if ($this->PageID != 0) {
            return DataObject::get_by_id(SiteTree::class, $this->PageID)->Link();
        }
        return $this->Link;
    }
}
