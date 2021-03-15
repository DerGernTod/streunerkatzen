<?php

namespace Streunerkatzen\CatSearch;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

class SearchAgent extends DataObject {
    private static $singular_name = 'Suchagent';
    private static $plural_name = 'Suchagenten';
    private static $table_name = 'Streunerkatzen_SearchAgents';

    // this is basically just to store the filter for cat, so it's just strings for the query
    private static $db = array(
        "Token" => "Varchar",
        "Filter" => "Text",
        "Email" => "Varchar"
    );

    private static $casting = [
        'ReadableSearch' => 'HTMLText'
    ];

    private static $summary_fields = [
        "Email" => "E-Mail Adresse",
        "Created" => "Erstellungszeit",
        "ReadableSearch" => "Suchfilter"
    ];

    private static $niceName = [
        "Gender" => "Geschlecht",
        "IsCastrated" => "Kastriert",
        "IsChipped" => "Gechippt",
        "FurColors.Title" => "Fellfarben",
        "LostFoundDate:GreaterThanOrEqual" => "Funddatum später als",
        "LostFoundDate:LessThanOrEqual" => "Funddatum früher als",
        "Title:PartialMatch" => "Katzen Name"
    ];

    public function getCMSFields() {
        $fields = FieldList::create(
            ReadonlyField::create('Created', 'Erstellungszeit'),
            ReadonlyField::create('Email', 'Email'),
            LiteralField::create('Filter', '<div class="form-group"><p>Filter</p>'.$this->getReadableSearch().'</div>'),
            ReadonlyField::create('Token', 'Token')
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
        if ($this->Filter) {
            $arrFilter = json_decode($this->Filter, true);

            $filterList = new ArrayList();

            foreach ($arrFilter as $filterName => $filterValue) {
                if (strpos($filterName, 'LostFoundDate') !== false) {
                    $filterValue = date('d.m.Y', strtotime($filterValue));
                }
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

        return "(keine Filter)";
    }
}
