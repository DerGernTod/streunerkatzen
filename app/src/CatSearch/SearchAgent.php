<?php

namespace Streunerkatzen\CatSearch;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

class SearchAgent extends DataObject {
    private static $singular_name = 'Suchagent';
    private static $plural_name = 'Suchagenten';
    private static $table_name = 'Streunerkatzen_SearchAgents';

    // this is basically just to store the filter for cat, so it's just strings for the query
    private static $db = array(
        "Token" => "Varchar",
        "Filter" => "Varchar",
        "Email" => "Text"
    );

    private static $casting = [
        'ReadableSearch' => 'HTMLText'
    ];

    private static $niceName = [
        "Gender" => "Geschlecht",
        "IsCastrated" => "Kastriert",
        "FurColors.Title" => "Fellfarben",
        "LostFoundDate:GreaterThanOrEqual" => "Funddatum später als",
        "LostFoundDate:LessThanOrEqual" => "Funddatum früher als",
        "Title:PartialMatch" => "Katzen Name"
    ];

    public function getCMSFields() {
        $fields = FieldList::create(
            TextField::create('Token', 'Token')->isReadonly(),
            TextareaField::create('Filter', 'Filter')->isReadonly(),
            TextField::create('Email', 'Email')->isReadonly()
        );
        return $fields;
    }

    private static function getNiceName($filterName) {
        if (array_key_exists($filterName, self::$niceName)) {
            return (self::$niceName[$filterName]);
        } else {
            return $filterName;
        }
    }

    /**
     * returns a readable, formatted html of the filter
     */
    public function getReadableSearch() {
        $arrFilter = json_decode($this->Filter, true);

        $filterList = new ArrayList();
        foreach ($arrFilter as $filterName => $filterValue) {
            if (!is_array($filterValue)) {
                $arrData = new ArrayData([
                    'Name' => self::getNiceName($filterName),
                    'Value' => $filterValue
                ]);
            } else {
                $arrData = new ArrayData([
                    'Name' => self::getNiceName($filterName),
                    'Value' => join(', ', $filterValue)
                ]);
            }
            $filterList->push($arrData);
        }

        $templateData = new ArrayData([
            'Filters' => $filterList
        ]);

        $html = $templateData->renderWith('Streunerkatzen/CatSearch/Includes/SearchAgentFilter');
        return $html;
    }
}
