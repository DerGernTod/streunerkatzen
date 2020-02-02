<?php

namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;

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

    private static $translation = [
        "Gender" => "Geschlecht",
        "IsCastrated" => "Kastriert",
        "HairColors.Title" => "Fellfarben",
        "LostFoundDate:GreaterThanOrEqual" => "Funddatum später als",
        "LostFoundDate:LessThanOrEqual" => "Funddatum früher als"
    ];

    public function getCMSFields() {
        $fields = FieldList::create(
            TextField::create('Token', 'Token')->isReadonly(),
            TextareaField::create('Filter', 'Filter')->isReadonly(),
            TextField::create('Email', 'Email')->isReadonly()
        );
        return $fields;
    }

    /**
     * returns a readable, formatted html of the filter
     */
    public function getReadableSearch() {
        $arrFilter = json_decode($this->Filter);
        $res = "<ul>";
        foreach($arrFilter as $key => $val) {
            $res .= "<li><strong>".SearchAgent::$translation[$key].":</strong> ";
            if (!is_array($val)) {
                $res .= $val;
            } else {
                $res .= join(', ', $val);
            }
            $res .= "</li>";
        }
        $res .= "</ul>";
        return $res;
    }
}
