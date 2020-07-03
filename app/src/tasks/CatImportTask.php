<?php

use SilverStripe\Assets\File;
use SilverStripe\Assets\Storage\AssetStore;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Dev\Debug;
use SilverStripe\Security\Member;
use SilverStripe\UserForms\Model\EditableFormField\EditableOption;
use Streunerkatzen\Cat;
use Streunerkatzen\CatExporter;

function param(Array $arr, string $prop) {
    if (array_has($arr, $prop)) {
        return $arr[$prop];
    }
    return null;
}

class CatImportTask extends BuildTask {


    public function run($request) {
        $catExporter = new CatExporter();
        $exportResult = $catExporter->getEntries();
        $entryCount = 10; //count($exportResult["cats"]);
        echo "<h2>importing cats</h2>";
        echo "<ul>";
        for ($i = 0; $i < $entryCount; $i++) {
            echo "<li>";
            $catEntry = $exportResult["cats"][$i];
            $fields = $catEntry["fields"];
            $cat = Cat::create();
            $cat->Title = $catEntry["title"] ? $catEntry["title"] : "unbekannt";
            echo "<div>".$cat->Title."</div>";
            assignIfDate($cat, 'PublishTime', $catEntry["publish_up"]);
            assignIfDate($cat, 'LostFoundDate', $fields["Datum"]);
            $cat->Gender = param($fields, "geschlecht");
            $cat->IsCastrated = param($fields, "kastriert");
            $cat->isHouseCat = param($fields, "hauskatze");
            $cat->Breed = param($fields, "Rasse");
            $cat->EyeColor = param($fields, "augenfarbe");
            $cat->BehaviourOwner = param($fields, "Besitzer");
            $cat->BehaviourStranger = param($fields, "Fremden");
            $cat->Street = param($fields, 'Straße');
            $cat->Country = param($fields, "bundesland");
            $cat->IsChipped = param($fields, "gechipt");
            $cat->HasPetCollar = param($fields, "halsband");
            $lostFoundStatus = param($fields, "gesuchtgefunden");
            $cat->LostFoundStatus = $this->mapLostFoundStatus($lostFoundStatus);
            $hairColor = $this->mapHairColor($catEntry["categories"]["name"]);
            $mappedColor = EditableOption::get()->filter(['Title' => $hairColor])->first();
            if ($mappedColor) {
                $cat->HairColors()->Add($mappedColor);
            }
            $cat->HairLength = $this->mapHairLength(param($fields, "haarlnge"));
            $cat->LostFoundTime = param($fields, "tageszeit");
            createAttachments(param($catEntry, "resources"), param($catEntry, "images"), $cat);
            $cat->write();
            echo "</li>";
        }
        echo "</ul>";
        echo "wrote $entryCount cats into db!";
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
 * TODO
 * translates imported attachments to a list of silverstripe files
 * @return File[]
 */
function createAttachments($importedAttachments, $importedImages, $cat) {
    $files = [];
    if ($importedImages) {
        foreach ($importedImages as $image) {
            // real url is images/com_sobi2/gallery/74/74_image_1.jpg
            // but stored as images/com_sobi2/gallery/74_image_1.jpg
            $origFileName = $image["filename"];
            $url = "http://katzensuche.streunerkatzen.org/".preg_replace('/\/([0-9]*)_i/', '/$1/$1_i', $origFileName);
            $title = $image["title"];
            $added = $image["added"];

            $file = createFile($title, $url);

            $cat->Attachments()->Add($file);
            $file->publishRecursive();
        }
    }
    if ($importedAttachments) {
        foreach ($importedAttachments as $attachment) {
            // it's a bit weird but that's what it is...
            $url = "http://katzensuche.streunerkatzen.org/index2.php?option=com_sobi2&sobi2Task=dd_download&format=html&Itemid=53&fid=".$attachment['fid'];
            $title = $attachment["title"];
            $filename = $attachment["filename"];
            $filetype = $attachment["filetype"];
            $extension = $attachment["extension"];
            $size = $attachment["size"];
            $title = $attachment["title"];
            $added = $attachment["added"];
            $fid = $attachment["fid"];
            Debug::message("got attachment: $filename $filetype $extension $size $title $added $fid");

            $file = createFile($title, $url, basename($filename));
            $cat->Attachments()->Add($file);
            $file->publishRecursive();
        }
    }
    return $files;
}

/**
 * @return File
 */
function createFile(string $title, string $url, string $realFile = null) {
    $tempDelimiter = "%dot%";
    // Use basename() function to return the base name of file
    // replace dots, silverstripe is retarded...
    $baseName = preg_replace("/\./", $tempDelimiter, basename($realFile ?? $url));
    $lastDotPos = strrpos($baseName, $tempDelimiter);
    $baseName = substr_replace($baseName, ".", $lastDotPos, strlen($tempDelimiter));
    $baseName = preg_replace("/$tempDelimiter/", "_", $baseName);

    $filename = "assets/imported/$baseName";
    // Use file_get_contents() function to get the file
    // from url and use file_put_contents() function to
    // save the file by using base name
    if(file_put_contents($filename, file_get_contents($url))) {
        echo "$url downloaded successfully to $filename";
    } else {
        echo "$url downloading failed.";
    }
    // this here is mostly copied from SilverStripe\AssetAdmin\Controller\AssetAdmin
    // it basically reads the extension and finds the correct file-type class (get_class_for_file_extension)
    $file = File::create($title);
    $file->setFromLocalFile($filename, "imported/managed/$baseName", null, null, ['visibility' => AssetStore::VISIBILITY_PUBLIC]);
    $extension = File::get_file_extension($baseName);
    $currentClass = $file->getClassName();
    $newClass = File::get_class_for_file_extension($extension);
    if (!is_a($currentClass, $newClass, true) || ($currentClass !== $newClass && $newClass === File::class)) {
        $file = $file->newClassInstance($newClass);

        // update the allowed category for the new file extension
        $category = File::get_app_category($extension);
        $file->File->setAllowedCategories($category);
        $file->grantFile();
    }

    $file->write();
    return $file;
}
