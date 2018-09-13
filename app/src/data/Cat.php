<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;

class Cat extends DataObject {
    private static $table_name = 'data_Cats';

    private static $db = [
        'Title' => 'Varchar(250)',
        'PublishTime' => 'Datetime',

        'Age' => 'Varchar(250)',
        'HasPetCollar' => 'Boolean',
        'PetCollarDescription' => 'Varchar(250)',
        'Characteristics' => 'Varchar(500)',
        'ColorCharacteristics' => 'Varchar(500)',
        'EyeColor' => 'Varchar(50)',
        'ChipNumber' => 'Varchar(100)',
        'Tattoo' => 'Varchar(250)',
        'Breed' => 'Varchar(250)',

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
        // 'LostFoundTime' => dropdown (morgens/vormittags, mittags/nachmittags, abends/nachts, nicht bekannt)
        // 'PublishStatus' => 'Varchar(20)',
        // icon
        // images
        // attachments
        // creator
        // farbe
        // fundstatus => dropdown status, (vermisst, gefunden, tot gefunden)
        // 'Gender' => dropdown status,
        // 'IsCastrated' => dropdown status,
        // 'IsHouseCat' => dropdown status,
        // 'IsChipped' => ,
        // 'HairLength' => ,
    private static $has_one = [
        'LostFoundTime' => LostFoundTime::class
    ];

}
