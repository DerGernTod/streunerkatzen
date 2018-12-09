<?php

use SilverStripe\Dev\BuildTask;
use Streunerkatzen\Cat;
use Streunerkatzen\LostFoundTime;
use Streunerkatzen\CatExporter;
use Streunerkatzen\User;
use Streunerkatzen\HairColor;
use Streunerkatzen\HairLength;
use Streunerkatzen\LostFoundStatus;

class CatImportTask extends BuildTask {
    private $hairColors;
    private $hairLengths;
    private $lostFoundTimes;
    private $lostFoundStates;

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
            $cat->LostFoundStatusID = $this->mapLostFoundStatus($lostFoundStatus);
            $cat->HairColorID = $this->mapHairColor($catEntry["categories"]["name"]);
            $cat->HairLengthID = $this->mapHairLength($fields["haarlnge"]);
            $cat->LostFoundTimeID = $this->mapLostFoundTime($fields["tageszeit"]);
            $cat->Attachments = createAttachments($catEntry["resources"], $catEntry["images"]);
            $user = createUser($fields);
            $userId = $user->write();
            if ($lostFoundStatus == "vermisst") {
                $cat->OwnerID = $userId;
            } else {
                $cat->ReporterID = $userId;
            }
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
        if (!$this->hairColors) {
            $this->hairColors = HairColor::get();
        }
        $id = $this->hairColors->find('Name', str_ireplace("m. ", "mit ", $importedHairColor))->ID;
        if (!$id) {
            echo "<div>no dropdown entry found for hairColor: ".$importedHairColor."!</div>";
        }
        return $id;
    }

    /**
     * translates imported hair length to dropdown fields
     */
    private function mapHairLength($importedHairLength) {
        if (!$importedHairLength) {
            $importedHairLength = "sonstiges";
        }
        if (!$this->hairLengths) {
            $this->hairLengths = HairLength::get();
        }
        $id = $this->hairLengths->find('Name', $importedHairLength)->ID;
        if (!$id) {
            echo "<div>no dropdown entry found for hairLength: ".$importedHairLength."!</div>";
        }
        return $id;
    }

    /**
     * translates imported lostfoundtime to dropdown fields
     */
    private function mapLostFoundTime($importedLostFoundTime) {
        if (!$importedLostFoundTime) {
            $importedLostFoundTime = "nicht bekannt";
        }
        if (!$this->lostFoundTimes) {
            $this->lostFoundTimes = LostFoundTime::get();
        }
        $id = $this->lostFoundTimes->find('Name', $importedLostFoundTime)->ID;
        if (!$id) {
            echo "<div>no dropdown entry found for lostFoundTime: ".$importedLostFoundTime."!</div>";
        }
        return $id;
    }

    /**
     * translates imported lostfoundstatus to dropdown fields
     */
    private function mapLostFoundStatus($importedLostFoundStatus) {
        if (!$this->lostFoundStates) {
            $this->lostFoundStates = LostFoundStatus::get();
        }
        $id = $this->lostFoundStates->find('Name', $importedLostFoundStatus)->ID;
        if (!$id) {
            echo "<div>no dropdown entry found for lostFoundState: ".$importedLostFoundStatus."!</div>";
        }
        return $id;
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
    $user = User::create();
    $user->FirstName = $importedCatFields["Vorname"];
    $user->LastName = $importedCatFields["Nachname"];
    $user->PhoneNumber = $importedCatFields["Kontakt"];
    // i'll just assume the reporter lives in the same country
    // as they found/are missing the cat...
    $user->Country = $importedCatFields["bundesland"];

    return $user;
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
