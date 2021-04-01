<?php
namespace Streunerkatzen\Cats;

use SilverStripe\Assets\File;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Versioned\Versioned;

class Cat extends DataObject {
    private static $singular_name = 'Katze';
    private static $plural_name = 'Katzen';
    private static $table_name = 'Streunerkatzen_Cats';

    private static $db = [
        'Title' => 'Varchar(250)',
        'PublishTime' => 'Datetime',

        'Age' => 'Varchar(250)',
        'Gender' => 'Enum("nicht bekannt,männlich,weiblich")',
        'HasPetCollar' => 'Enum("nicht bekannt,ja,nein")',
        'PetCollarDescription' => 'Varchar(250)',
        'Characteristics' => 'Text',
        'ColorCharacteristics' => 'Text',
        'EyeColor' => 'Varchar(50)',
        'ChipNumber' => 'Varchar(100)',
        'Tattoo' => 'Varchar(250)',
        'Breed' => 'Varchar(250)',
        'IsCastrated' => 'Enum("nicht bekannt,ja,nein")',
        'IsHouseCat' => 'Enum("nicht bekannt,ja,nein")',
        'IsChipped' => 'Enum("nicht bekannt,ja,nein")',

        'BehaviourOwner' => 'Text',
        'BehaviourStranger' => 'Text',

        'LostFoundDate' => 'Date',

        'Street' => 'Varchar(250)',
        'Town' => 'Varchar(250)',
        'Zipcode' => 'Int',
        'Country' => 'Enum("Wien,Niederösterreich,Oberösterreich,Salzburg,Steiermark,Burgenland,Kärnten,Tirol,Vorarlberg")',
        'LostFoundDescription' => 'Text',

        'MoreInfo' => 'Text',

        'LostFoundTime' => 'Enum("nicht bekannt,morgens/vormittags,mittags/nachmittags,abends/nachts")',
        'LostFoundStatus' => 'Enum("Vermisst,Gefunden,Tot gefunden,Pflegekatze")',
        'FurLength' => 'Enum("kurz,mittel,lang,sonstiges")',
        'Contact' => 'Varchar(250)'
    ];

    private static $has_one = [
        'Notifier' => Notifier::class
    ];

    private static $many_many = [
        'Attachments' => File::class,
        'FurColors' => FurColor::class
    ];

    private static $owns = [
        'Attachments'
    ];

    private static $extensions = [
        Versioned::class,
    ];

    public function getCMSFields() {
        $fields = FieldList::create(
            ReadonlyField::create(
                'LinkPreview',
                'Link',
                Director::absoluteBaseURL().$this->Link()
            ),
            DatetimeField::create('PublishTime', 'Datum der Veröffentlichung'),
            TextField::create('Title', 'Name der Katze'),
            TextField::create('Breed', 'Rasse'),
            TextField::create('Age', 'Alter'),
            DropdownField::create(
                'Gender',
                'Geschlecht',
                singleton(Cat::class)->dbObject('Gender')->enumValues()
            ),
            ListboxField::create(
                'FurColors',
                'Fellfarben',
                FurColor::get()->map('ID', 'Title')
            ),
            DropdownField::create(
                'FurLength',
                'Haarlänge',
                singleton(Cat::class)->dbObject('FurLength')->enumValues()
            ),
            TextareaField::create('Characteristics', 'Besonderheiten'),
            TextareaField::create('ColorCharacteristics', 'Farbliche Besonderheiten'),
            TextField::create('EyeColor', 'Augenfarbe'),
            TextField::create('Tattoo', 'Tattoo'),
            DropdownField::create(
                'HasPetCollar',
                'Halsband?',
                singleton(Cat::class)->dbObject('HasPetCollar')->enumValues()
            ),
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
            TextareaField::create('BehaviourOwner', 'Verhalten gegenüber Besitzer'),
            TextareaField::create('BehaviourStranger', 'Verhalten gegenüber Fremden'),
            DateField::create('LostFoundDate', 'Datum'),
            DropdownField::create(
                'LostFoundTime',
                'Zeitpunkt',
                singleton(Cat::class)->dbObject('LostFoundTime')->enumValues()
            ),
            DropdownField::create(
                'LostFoundStatus',
                'Status',
                singleton(Cat::class)->dbObject('LostFoundStatus')->enumValues()
            ),
            TextField::create('Street', 'Straße'),
            TextField::create('Town', 'Ort'),
            TextField::create('Zipcode', 'PLZ'),
            DropdownField::create(
                'Country',
                'Bundesland',
                singleton(Cat::class)->dbObject('Country')->enumValues()
            ),
            TextareaField::create('LostFoundDescription', 'Beschreibung der Situation'),
            TextareaField::create('MoreInfo', 'Details'),
            $upload = UploadField::create('Attachments', 'Anhänge'),
            TextField::create('Contact', 'Kontakt')
        );

        $upload->setFolderName("Katzen");

        return $fields;
    }

    public function Link() {
        return 'cats/view/'.$this->ID;
    }

    public function AbsoluteLink() {
        return Director::absoluteBaseURL().$this->Link();
    }

    public function CMSEditLink() {
        $admin = Injector::inst()->get(CatAdmin::class);
        $className = str_replace('\\', '-', $this->ClassName);

        return Controller::join_links(
            $admin->Link($className),
            "EditForm",
            "field",
            $className,
            "item",
            $this->ID,
            "edit"
        );
    }

    /**
     * used to normalize unknown fields
     */
    public function Normalized(string $field) {
        if ($this->$field) {
            return $this->$field;
        }
        return 'k. A.';
    }

    /**
     * used to normalize simple yes/no/unknown dropdowns
     */
    public function Check(string $field) {
        if (strcmp($this->$field, 'nicht bekannt') === 0) {
            return '?';
        } elseif (strcmp($this->$field, 'ja') === 0) {
            return '✔';
        }
        return '✗';
    }

    public function Controller() {
        return Controller::curr();
    }

    public function getShortcodeView() {
        return $this->renderWith('Streunerkatzen/Includes/CatShortcodeView');
    }

    public static function CatShortcode($arguments) {
        if (!isset($arguments['id'])) {
            return '';
        }
        $cat = Cat::get_by_id($arguments['id']);
        if (!$cat) {
            return "Katze mit der ID ".$arguments['id']." nicht gefunden!";
        }
        return $cat->getShortcodeView($arguments);
    }

    public function getFirstImage() {
        $attachments = $this->Attachments();
        foreach ($attachments as $file) {
            if ($file->IsImage) {
                return $file;
            }
        }
        return null;
    }

    public static function getFilterOptions() {
        $options = [];

        $filterOptions = [
            'Gender',
            'IsCastrated',
            'IsHouseCat',
            'IsChipped',
            'Country',
            'LostFoundTime',
            'LostFoundStatus',
            'FurLength'
        ];

        foreach ($filterOptions as $option) {
            foreach (singleton(Cat::class)->dbObject($option)->enumValues() as $item) {
                $options[$option][$item] = $item;
            }
        }

        foreach (FurColor::get() as $furColor) {
            $options['FurColor'][$furColor->Title] = $furColor->Title;
        }

        return $options;
    }
}
