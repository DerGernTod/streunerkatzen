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
        'Link' => 'Text',
        'IsNewWindow' => 'Boolean'
    ];

    private static $has_one = [
        'Page' => SiteTree::class,
    ];

    public function getCMSfields() {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Label', 'Buttontext'),
            TreeDropdownField::create(
                'PageID',
                'Interne Seite',
                SiteTree::class
            )->setDescription('Leer lassen falls eine externe URL verwendet werden soll.'),
            TextField::create('Link', 'Externe URL'),
            CheckboxField::create('IsNewWindow', 'In neuem Fenster öffnen?')
        ]);

        return $fields;
    }

    public function onBeforeWrite() {
        parent::onBeforeWrite();

        if ($this->Page != null) {
            $this->Link = null;
        }
    }
}
