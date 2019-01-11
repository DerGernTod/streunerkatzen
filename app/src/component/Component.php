<?php

namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\CheckboxField;

class Component extends DataObject {
    private static $table_name = 'Streunerkatzen_Component';

    public function canCreate($member = null, $context = []) {
        return false;
    }

    private static $db = [
        'Title' => 'Varchar',
        'Sort' => 'Int',
        'GridSizeLarge' => 'Varchar',
        'GridSizeMedium' => 'Varchar',
        'GridSizeSmall' => 'Varchar'
    ];

    private static $has_one = [
        'ComponentContainerPage' => ComponentContainerPage::class
    ];

    private static $summary_fields = [
        'Title' => 'Titel',
        'SingularName' => 'Typ'
    ];

    public function getSingularName() {
        return $this->singular_name();
    }

    protected function onBeforeWrite() {
        if (!$this->Sort) {
            $this->Sort = Component::get()->max('Sort') + 1;
        }

        parent::onBeforeWrite();
    }

    public function getCMSfields() {
        $fields = FieldList::create(TabSet::create('Root'));

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Title', 'Titel'),
            DropdownField::create('GridSizeLarge', 'Größe Desktop')
                ->setSource([
                    'size-l-12' => 'Volle Breite',
                    'size-l-11' => '11/12',
                    'size-l-10' => '5/6',
                    'size-l-9' => '3/4',
                    'size-l-8' => '2/3',
                    'size-l-7' => '7/12',
                    'size-l-6' => '1/2',
                    'size-l-5' => '5/12',
                    'size-l-4' => '1/3',
                    'size-l-3' => '1/4',
                    'size-l-2' => '1/6',
                    'size-l-1' => '1/12']),
            DropdownField::create('GridSizeMedium', 'Größe Tablet')
                ->setSource([
                    'size-m-12' => 'Volle Breite',
                    'size-m-11' => '11/12',
                    'size-m-10' => '5/6',
                    'size-m-9' => '3/4',
                    'size-m-8' => '2/3',
                    'size-m-7' => '7/12',
                    'size-m-6' => '1/2',
                    'size-m-5' => '5/12',
                    'size-m-4' => '1/3',
                    'size-m-3' => '1/4',
                    'size-m-2' => '1/6',
                    'size-m-1' => '1/12']),
            DropdownField::create('GridSizeSmall', 'Größe Mobil')
                ->setSource([
                    'size-s-12' => 'Volle Breite',
                    'size-s-11' => '11/12',
                    'size-s-10' => '5/6',
                    'size-s-9' => '3/4',
                    'size-s-8' => '2/3',
                    'size-s-7' => '7/12',
                    'size-s-6' => '1/2',
                    'size-s-5' => '5/12',
                    'size-s-4' => '1/3',
                    'size-s-3' => '1/4',
                    'size-s-2' => '1/6',
                    'size-s-1' => '1/12'])
        ]);

        return $fields;
    }
}
