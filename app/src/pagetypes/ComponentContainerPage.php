<?php

namespace Streunerkatzen;

use Page;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Silverstripe\Forms\GridField\GridFieldPaginator;

class ComponentContainerPage extends Page {

    public function canCreate($member = null, $context = []) {
        return false;
    }

    private static $has_many = [
        'PageComponents' => Component::class,
    ];

    public function getCMSfields() {
        $fields = parent::getCMSFields();
        $fields->removeFieldFromTab("Root.Content.Main", "Content");

        $fields->addFieldsToTab('Root.Main', [
            $gridField = GridField::create(
                'PageComponents',
                'Elemente',
                $this->PageComponents(),
                GridFieldConfig_RecordEditor::create()
                    ->removeComponentsByType(GridFieldAddNewButton::class)
                    ->addComponent(new GridFieldAddNewMultiClass())
                    ->addComponent(new GridFieldOrderableRows())
            )
        ]);

        $gridField->getConfig()->getComponentByType(GridFieldPaginator::class)->setItemsPerPage(50);

        return $fields;
    }

    public function SortedComponents() {
        return $this->PageComponents()
            ->sort('Sort', 'ASC');
    }
}
