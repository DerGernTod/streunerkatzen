<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;

class Cat extends DataObject {

    private static $db = [
        'Title' => 'Varchar(250)',
        // 'PublishStatus' => 'Varchar(20)',
        'PublishTime' => 'Datetime',
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
        // 'LostFoundTime' => dropdown
        'Street' => 'Varchar(250)',
        'Town' => 'Varchar(250)',
        'Zipcode' => 'Int',
        'Country' => 'Varchar(250)',
        'LostFoundDescription' => 'Varchar(1000)',
        // owner/finder

        'MoreInfo' => 'Varchar(1000)'
    ];

}
