<?php

use SilverStripe\Dev\BuildTask;
use Streunerkatzen\Cat;
use Streunerkatzen\LostFoundTime;
use Streunerkatzen\CatExporter;
use Streunerkatzen\User;

class CatImportTask extends BuildTask {

    public function run($request) {
        $catExporter = new CatExporter();
        $exportResult = $catExporter->getEntries();
        echo "working...";
        for ($i = 0; $i < count($exportResult["cats"]); $i++) {
            $catEntry = $exportResult["cats"][$i];
            $fields = $catEntry["fields"];
            $cat = Cat::create();
            $cat->Title = $catEntry["title"];
            assignIfDate($cat, 'PublishTime', $catEntry["publish_up"]);
            $cat->HairColor = mapHairColor($catEntry["categories"]["name"]);
            $cat->Attachments = createAttachments($catEntry["resources"], $catEntry["images"]);
            $cat->Gender = $fields["geschlecht"];
            $cat->IsCastrated = $fields["kastriert"];
            $cat->isHouseCat = $fields["hauskatze"];
            $cat->Breed = $fields["Rasse"];
            $cat->EyeColor = $fields["augenfarbe"];
            $cat->HairLength = mapHairLength($fields["haarlnge"]);
            $cat->BehaviourOwner = $fields["Besitzer"];
            $cat->BehaviourStranger = $fields["Fremden"];
            assignIfDate($cat, 'LostFoundDate', $fields["Datum"]);
            $cat->Street = $fields['Straße'];
            $cat->LostFoundTime = mapLostFoundTime($fields["tageszeit"]);
            $cat->Country = $fields["bundesland"];
            $cat->LostFoundStatus = mapLostFoundStatus($fields["gesuchtgefunden"]);
            $cat->IsChipped = $fields["gechipt"];
            $cat->HasPetCollar = $fields["halsband"];
            $reporter = createReporter($fields);
            $reporterId = $reporter->write();
            $cat->ReporterID = $reporterId;
            //$lostFoundTime = LostFoundTime::;
            //$cat->LostFoundTimeID = ;
            $newCatID = $cat->write();
            echo ".";
            if ($i > 10) {
                break;
            }
        }
        echo "wrote ".count($exportResult["cats"])." cats into db!";
    }
}

function assignIfDate($newCat, $index, $value) {
    if (DateTime::createFromFormat('Y-m-d', $value) !== false) {
        $newCat->{$index} = $value;
    }
}

/**
 * creates a user for the reporter
 */
function createReporter($importedCatFields) {
    $reporter = User::create();
    $reporter->FirstName = $importedCatFields["Vorname"];
    $reporter->LastName = $importedCatFields["Nachname"];
    $reporter->PhoneNumber = $importedCatFields["Kontakt"];
    // i'll just assume the reporter lives in the same country
    // as they found/are missing the cat...
    $reporter->Country = $importedCatFields["bundesland"];

    return $reporter;
}

/**
 * TODO
 * translates imported attachments to a list of silverstripe files
 */
function createAttachments($importedAttachments, $importedImages) {
    if ($importedImages) {
        foreach ($importedImages as $image) {
            $filename = $image["filename"];
            $thumb = $image["thumb"];
            $title = $image["title"];
            $added = $image["added"];
        }
    }
    if ($importedAttachments) {
        foreach ($importedAttachments as $attachment) {
            $filename = $attachment["filename"];
            $filetype = $attachment["filetype"];
            $extension = $attachment["extension"];
            $size = $attachment["size"];
            $title = $attachment["title"];
            $added = $attachment["added"];
            // TODO ...write, return list?
        }
    }
}

/**
 * TODO
 * translates imported hair colors to dropdown fields
 */
function mapHairColor($importedHairColor) {
    return $importedHairColor;
}

/**
 * TODO
 * translates imported hair length to dropdown fields
 */
function mapHairLength($importedHairLength) {
    return $importedHairLength;
}

/**
 * TODO
 * translates imported lostfoundtime to dropdown fields
 */
function mapLostFoundTime($importedLostFoundTime) {
    return $importedLostFoundTime;
}

/**
 * TODO
 * translates imported lostfoundstatus to dropdown fields
 */
function mapLostFoundStatus($importedLostFoundStatus) {
    return $importedLostFoundStatus;
}
