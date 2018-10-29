<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;

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

        'MoreInfo' => 'Varchar(1000)'
    ];
        // owner/finder/contact
        // 'PublishStatus' => 'Varchar(20)',
        // icon
        // images
        // attachments
        // creator
    private static $has_one = [
        'LostFoundTime' => LostFoundTime::class,
        'LostFoundStatus' => LostFoundStatus::class,
        'HairLength' => HairLength::class,
        'HairColor' => HairColor::class,
        'Reporter' => User::class,
        'Owner' => User::class
    ];

    private static $has_many = [
        'Attachments' => File::class
    ];

    public function getCMSFields() {
        $fields = FieldList::create(
            DateField::create('PublishTime', 'Datum der Veröffentlichung'),
            TextField::create('Title', 'Name der Katze'),
            TextField::create('Breed', 'Rasse'),
            TextField::create('Age', 'Alter'),
            DropdownField::create(
                'Gender',
                'Geschlecht',
                singleton(Cat::class)->dbObject('Gender')->enumValues()
            ),
            DropdownField::create('HairColor', 'Fellfarbe', HairColor::get()->map('ID', 'Name')),
            DropdownField::create('HairLength', 'Haarlänge', HairLength::get()->map('ID', 'Name')),
            TextField::create('Characteristics', 'Besonderheiten'),
            TextField::create('ColorCharacteristics', 'Farbliche Besonderheiten'),
            TextField::create('EyeColor', 'Augenfarbe'),
            TextField::create('Tattoo', 'Tattoo'),
            CheckboxField::create('HasPetCollar', 'Halsband?'),
            TextField::create('PetCollarDescription', 'Beschreibung des Halsbands'),
            DropdownField::create(
                'IsCastrated',
                'Kastriert?',
                singleton(Cat::class)->dbObject('IsCastrated')->enumValues()
            ),
            DropdownField::create(
                'IsHouseCat',
                'Hauskatze?',
                singleton(Cat::class)->dbObject('IsHouseCat')->enumValues()
            ),
            DropdownField::create(
                'IsChipped',
                'Gechippt?',
                singleton(Cat::class)->dbObject('IsChipped')->enumValues()
            ),
            TextField::create('ChipNumber', 'Chipnummer'),
            TextField::create('BehaviourOwner', 'Verhalten gegenüber Besitzer'),
            TextField::create('BehaviourStranger', 'Verhalten gegenüber Fremden'),
            DateField::create('LostFoundDate', 'Datum'),
            DropdownField::create('LostFoundTime', 'Zeitpunkt', LostFoundTime::get()->map('ID', 'Name')),
            DropdownField::create('LostFoundStatus', 'Status', LostFoundStatus::get()->map('ID', 'Name')),
            TextField::create('Street', 'Straße'),
            TextField::create('Town', 'Ort'),
            TextField::create('ZipCode', 'PLZ'),
            TextField::create('Country', 'Land'),
            TextField::create('LostFoundDescription', 'Beschreibung der Situation'),
            TextField::create('MoreInfo', 'Details'),
            UploadField::create('Attachments', 'Anhänge')
        );
        return $fields;
    }
}
