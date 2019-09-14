<?php

use SilverStripe\Dev\BuildTask;
use SilverStripe\Security\Member;
use Streunerkatzen\Cat;
use Streunerkatzen\CatExporter;

class CatImportTask extends BuildTask {

    public function run($request) {
        $catExporter = new CatExporter();
        $exportResult = $catExporter->getEntries();
        echo "<h2>importing cats</h2>";
        echo "<ul>";
        for ($i = 0; $i < count($exportResult["cats"]); $i++) {
            echo "<li>";
            $catEntry = $exportResult["cats"][$i];
            $fields = $catEntry["fields"];
            $cat = Cat::create();
            $cat->Title = $catEntry["title"] ? $catEntry["title"] : "unbekannt";
            echo "<div>".$cat->Title."</div>";
            assignIfDate($cat, 'PublishTime', $catEntry["publish_up"]);
            assignIfDate($cat, 'LostFoundDate', $fields["Datum"]);
            $cat->Gender = $fields["geschlecht"];
            $cat->IsCastrated = $fields["kastriert"];
            $cat->isHouseCat = $fields["hauskatze"];
            $cat->Breed = $fields["Rasse"];
            $cat->EyeColor = $fields["augenfarbe"];
            $cat->BehaviourOwner = $fields["Besitzer"];
            $cat->BehaviourStranger = $fields["Fremden"];
            $cat->Street = $fields['Straße'];
            $cat->Country = $fields["bundesland"];
            $cat->IsChipped = $fields["gechipt"];
            $cat->HasPetCollar = $fields["halsband"];
            $lostFoundStatus = $fields["gesuchtgefunden"];
            $cat->LostFoundStatus = $this->mapLostFoundStatus($lostFoundStatus);
            $cat->HairColor = $this->mapHairColor($catEntry["categories"]["name"]);
            $cat->HairLength = $this->mapHairLength($fields["haarlnge"]);
            $cat->LostFoundTime = $fields["tageszeit"];
            $cat->Attachments = createAttachments($catEntry["resources"], $catEntry["images"]);
            $userId = createUser($fields);
            if ($lostFoundStatus == "vermisst") {
                $cat->OwnerID = $userId;
            }
            $cat->ReporterID = $userId;
            $newCatID = $cat->write();
            echo "</li>";
        }
        echo "</ul>";
        echo "wrote ".count($exportResult["cats"])." cats into db!";
    }

    /**
     * translates imported hair colors to dropdown fields
     */
    private function mapHairColor($importedHairColor) {

        if (strpos($importedHairColor, "zur Farbauswahl") !== false) {
            $importedHairColor = "schwarz";
        }
        $importedHairColor = str_ireplace("m. ", "mit ", $importedHairColor);
        return $importedHairColor;
    }

    /**
     * translates imported hair length to dropdown fields
     */
    private function mapHairLength($importedHairLength) {
        if (!$importedHairLength) {
            $importedHairLength = "sonstiges";
        }
        return $importedHairLength;
    }

    /**
     * translates imported lostfoundstatus to dropdown fields
     */
    private function mapLostFoundStatus($importedLostFoundStatus) {
        return str_ireplace(
            "tot aufgefunden",
            "Tot gefunden",
            ucFirst($importedLostFoundStatus)
        );
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
function createUser($importedCatFields) {
    $firstName = $importedCatFields["Vorname"];
    $lastName = $importedCatFields["Nachname"];
    $matchingMembers = Member::get()->filter(array(
        "FirstName" => $firstName,
        "Surname" => $lastName
    ));
    $id = -1;
    if (count($matchingMembers) == 0) {
        $member = Member::create();
        $member->FirstName = $firstName;
        $member->Surname = $lastName;
        $member->PhoneNumber = $importedCatFields["Kontakt"];
        // i'll just assume the reporter lives in the same country
        // as they found/are missing the cat...
        $member->Country = $importedCatFields["bundesland"];
        $id = $member->write();
    } else {
        $id = $matchingMembers[0]->ID;
    }


    return $id;
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
