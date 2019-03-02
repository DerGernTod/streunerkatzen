<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DB;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Member;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\UserForms\Model\EditableFormField\EditableOption;
use SilverStripe\UserForms\Model\EditableFormField\EditableDropdown;

class Cat extends DataObject {
    private static $singular_name = 'Katze';
    private static $plural_name = 'Katzen';
    private static $table_name = 'data_Cats';

    private static $db = [
        'Title' => 'Varchar(250)',
        'PublishTime' => 'Datetime',

        'Age' => 'Varchar(250)',
        'Gender' => 'Enum("nicht bekannt,männlich,weiblich")',
        'HasPetCollar' => 'Boolean',
        'PetCollarDescription' => 'Varchar(250)',
        'Characteristics' => 'Varchar(500)',
        'ColorCharacteristics' => 'Varchar(500)',
        'EyeColor' => 'Varchar(50)',
        'ChipNumber' => 'Varchar(100)',
        'Tattoo' => 'Varchar(250)',
        'Breed' => 'Varchar(250)',
        'IsCastrated' => 'Enum("nicht bekannt,ja,nein")',
        'IsHouseCat' => 'Enum("nicht bekannt,ja,nein")',
        'IsChipped' => 'Enum("nicht bekannt,ja,nein")',

        'BehaviourOwner' => 'Varchar(500)',
        'BehaviourStranger' => 'Varchar(500)',

        'LostFoundDate' => 'Date',

        'Street' => 'Varchar(250)',
        'Town' => 'Varchar(250)',
        'Zipcode' => 'Int',
        'Country' => 'Varchar(250)',
        'LostFoundDescription' => 'Varchar(1000)',

        'MoreInfo' => 'Varchar(1000)',

        'LostFoundTime' => 'Varchar(250)',
        'LostFoundStatus' => 'Varchar(250)',
        'HairLength' => 'Varchar(250)',
        'HairColor' => 'Varchar(250)',
    ];
        // owner/finder/contact
        // 'PublishStatus' => 'Varchar(20)',
        // icon
        // images
        // attachments
        // creator
    private static $has_one = [
        'Reporter' => Member::class,
        'Owner' => Member::class
    ];

    private static $many_many = [
        'Attachments' => File::class
    ];

    public static function getCatDropdownsWithOptions() {
        $results = DB::Query("
            SELECT
                options.Title AS optionname,
                dropdownnames.Name AS dropdownname
            FROM
                editableoption AS options
            LEFT JOIN
                editabledropdown AS dropdowns
            ON
                options.ParentID = dropdowns.ID
            LEFT JOIN
                editableformfield AS dropdownnames
            ON
                dropdowns.ID = dropdownnames.ID
            WHERE
                dropdownnames.Name LIKE 'CatField_%'
            ORDER BY
                dropdownnames.Name, options.Sort ASC
        ");
        $result = [];
        foreach ($results as $item) {
            $dropdownname = $item['dropdownname'];
            if (!$result[$dropdownname]) {
                $result[$dropdownname] = [];
            }
            array_push($result[$dropdownname], $item['optionname']);
        }
        return $result;
    }

    public function getCMSFields() {
        $result = Cat::getCatDropdownsWithOptions();
        $fields = FieldList::create(
            DateField::create('PublishTime', 'Datum der Veröffentlichung'),
            TextField::create('Title', 'Name der Katze'),
            TextField::create('Breed', 'Rasse'),
            TextField::create('Age', 'Alter'),
            DropdownField::create(
                'Gender',
                'Geschlecht',
                $result['CatField_Gender']
            ),
            DropdownField::create('HairColor', 'Fellfarbe', $result['CatField_HairColor']),
            DropdownField::create('HairLength', 'Haarlänge', $result['CatField_HairLength']),
            TextField::create('Characteristics', 'Besonderheiten'),
            TextField::create('ColorCharacteristics', 'Farbliche Besonderheiten'),
            TextField::create('EyeColor', 'Augenfarbe'),
            TextField::create('Tattoo', 'Tattoo'),
            CheckboxField::create('HasPetCollar', 'Halsband?'),
            TextField::create('PetCollarDescription', 'Beschreibung des Halsbands'),
            DropdownField::create(
                'IsCastrated',
                'Kastriert?',
                $result['CatField_IsCastrated']
            ),
            DropdownField::create(
                'IsHouseCat',
                'Hauskatze?',
                $result['CatField_IsHouseCat']
            ),
            DropdownField::create(
                'IsChipped',
                'Gechippt?',
                $result['CatField_IsChipped']
            ),
            TextField::create('ChipNumber', 'Chipnummer'),
            TextField::create('BehaviourOwner', 'Verhalten gegenüber Besitzer'),
            TextField::create('BehaviourStranger', 'Verhalten gegenüber Fremden'),
            DateField::create('LostFoundDate', 'Datum'),
            DropdownField::create('LostFoundTime', 'Zeitpunkt', $result['CatField_LostFoundTime']),
            DropdownField::create('LostFoundStatus', 'Status', $result['CatField_LostFoundStatus']),
            TextField::create('Street', 'Straße'),
            TextField::create('Town', 'Ort'),
            TextField::create('ZipCode', 'PLZ'),
            TextField::create('Country', 'Land'),
            TextField::create('LostFoundDescription', 'Beschreibung der Situation'),
            TextField::create('MoreInfo', 'Details'),
            UploadField::create('Attachments', 'Anhänge'),
            DropdownField::create(
                'ReporterID',
                'Meldende Person',
                Member::get()->map('ID', 'FullName')
            )->setEmptyString('Auswählen...'),
            DropdownField::create(
                'OwnerID',
                'Besitzer',
                Member::get()->map('ID', 'FullName')
            )->setEmptyString('Auswählen...')
        );
        return $fields;
    }
}
