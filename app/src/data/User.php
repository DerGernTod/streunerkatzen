<?php
namespace Streunerkatzen;

use SilverStripe\ORM\DataObject;
use Streunerkatzen\Cat;

class User extends DataObject {
    private static $singular_name = 'Benutzer';
    private static $plural_name = 'Benutzer';
    private static $table_name = 'data_Users';

    private static $db = [
        'FirstName' => 'Varchar(250)',
        'LastName' => 'Varchar(250)',
        'Street' => 'Varchar(250)',
        'HouseNumber' => 'Varchar(25)',
        'Town' => 'Varchar(250)',
        'Zipcode' => 'Int',
        'Country' => 'Varchar(250)',
        'PhoneNumber' => 'Varchar(250)'
    ];

    private static $has_many = [
        'ReportedCats' => Cat::class,
        'OwnedCats' => Cat::class
    ];
}
