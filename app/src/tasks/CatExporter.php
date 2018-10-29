<?php
namespace Streunerkatzen;

use PDO;

class CatExporter
{
    private $db;
    private $categoryData = array();
    public function __construct() {
        $this->connect();
    }

    public function connect() {
        $servername = 'localhost';
        $dbName     = 'streunerkatzenorgdb1';
        $username   = 'root';
        $password   = '';
        $prefix     = '';

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbName;charset=utf8", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db = $conn;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    private function query($sql, $mode = PDO::FETCH_OBJ) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        // set the resulting array to object
        $stmt->setFetchMode($mode);
        return $stmt;
    }

    public function getEntries($limit = 0) {
        $sql = "SELECT
                    `items`.*
                FROM
                    `jos_sobi2_item` `items`";

        if ($limit > 0) {
            $sql .= " LIMIT $limit";
        }

        $csvArr = array();

        $entries = $this->query($sql)->fetchAll();

        $cnt = 0;
        for ($i = 0; $i < count($entries); $i++) {
            $entry = array();
            foreach ($entries[$i] as $entryKey => $entryValue) {
                $entry[$entryKey] = $entryValue;
            }
            $itemId = $entries[$i]->itemid;
            $categories = $this->getCategories($itemId);
            if (count($categories) > 0) {
                $entry['categories'] = $categories;
            }

            $images = $this->getImages($itemId);
            if (count($images) > 0) {
                $entry['images'] = $images;
            }

            $resources = $this->getResources($itemId);
            if (count($resources) > 0) {
                $entry['resources'] = $resources;
            }

            $fields = $this->getFieldsById($itemId);
            if (count($fields) > 0) {
                $entry['fields'] = array();
            }

            for ($j = 0; $j < count($fields); $j++) {
                $field = $fields[$j];
                $description = trim(strip_tags($field->fieldDescription));
                $hasDescription = strlen($description) > 0;
                if (strpos($field->value, "_opt_") !== false) {
                    $value = $hasDescription ? $description : $field->name;
                    $key = preg_replace("/field_([^_]*).*/", "$1", $field->value);
                } else {
                    $key = $hasDescription ? $description : $field->name;
                    $value = $field->value;
                }
                $entry['fields'][$key] = $value;
            }
            $csvArr[$i] = $entry;
        }
        $result = [
            'cats' => $csvArr
        ];
        return $result;
    }

    private function getFieldsById($id) {
        $sql = "SELECT
                    f.fieldid,
                    l.langKey,
                    f.fieldDescription,
                    l.langValue as name,
                    fd.data_txt as value,
                    f.fieldType
                FROM
                    jos_sobi2_fields_data fd
                        JOIN
                    jos_sobi2_fields f
                        ON f.fieldid = fd.fieldid
                        JOIN
                    jos_sobi2_language l
                        ON l.fieldid = f.fieldid
                WHERE
                    fd.itemid = {$id}
                AND
                    (l.langKey = fd.data_txt OR f.fieldType != 5)
                AND
                    l.sobi2Lang = 'german'
                AND
                    fd.data_txt != ''
                ORDER BY f.fieldid ASC";

        return $this->query($sql)->fetchAll();
    }

    private function getParentDataOfCatId($catid) {
        if (array_key_exists($catid, $this->categoryData)) {
            return $this->categoryData[$catid];
        }
        $sql = "SELECT cr.parentid, c.catid, c.name
            FROM jos_sobi2_cats_relations cr
            JOIN jos_sobi2_categories c
                ON c.catid = cr.catid
            WHERE cr.catid = {$catid}";
        $parents = $this->query($sql)->fetchAll();
        if (count($parents) > 1) {
            echo "that's weird... cat ".$catid." has ".count($parents)." parents";
            return null;
        }
        $curData = $parents[0];
        $res = array();
        $res["name"] = $curData->name;
        if ($curData->parentid > 1) {
            $parentData = $this->getParentDataOfCatId($curData->parentid);
            $res["parent"] = $parentData;
        }
        $this->categoryData[$curData->catid] = $res;
        return $res;
    }

    private function getCategories($itemid) {
        $sql = "SELECT c.catid, c.name
            FROM jos_sobi2_cat_items_relations ir
            JOIN jos_sobi2_categories c
            ON c.catid = ir.catid
            WHERE itemid = {$itemid}";
        $catIds = $this->query($sql)->fetchAll();
        $result = array();
        for ($i = 0; $i < count($catIds); $i++) {
            $curCatId = $catIds[$i]->catid;
            $result = array_merge($result, $this->getParentDataOfCatId($curCatId));
        }
        return $result;
    }

    private function getImages($itemid) {
        $sql = "SELECT filename, thumb, title, added, imgid
            FROM jos_sobi2_plugin_gallery
            WHERE itemid = {$itemid}";
        $images = $this->query($sql)->fetchAll();
        $result = array();
        for ($i = 0; $i < count($images); $i++) {
            $cur = $images[$i];
            $image = [
                "filename" => "images/com_sobi2/gallery/".$cur->filename,
                "thumb" => "images/com_sobi2/gallery/".$cur->thumb,
                "title" => $cur->title,
                "added" => $cur->added
            ];
            array_push($result, $image);
        }
        return $result;
    }

    private function getResources($itemid) {
        $sql = "SELECT filename, filetype, fileext, filesize, title, added
            FROM jos_sobi2_plugin_download
            WHERE itemid = {$itemid}";
        $resources = $this->query($sql)->fetchAll();
        $result = array();
        for ($i = 0; $i < count($resources); $i++) {
            $cur = $resources[$i];
            $resource = [
                "filename" => "sobi2_downloads/".$cur->filename,
                "filetype" => $cur->filetype,
                "extension" => $cur->fileext,
                "size" => $cur->filesize,
                "title" => $cur->title,
                "added" => $cur->added
            ];
            array_push($result, $resource);
        }
        return $result;
    }

    private function export($results = array())
    {
        $fileName = 'sobi_entries-' . date('Y-m-d-h:i:s') . '.csv';

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Expires: 0");
        header("Pragma: public");

        $fh = @fopen('php://output', 'w');

        $headerDisplayed = false;

        foreach ($results as $data) {
            // Add a header row if it hasn't been added yet
            if (!$headerDisplayed) {
                // Use the keys from $data as the titles
                fputcsv($fh, array_keys($data));
                $headerDisplayed = true;
            }

            // Put the data into the stream
            fputcsv($fh, $data);
        }
        // Close the file
        fclose($fh);
        // Make sure nothing else is sent, our file is done
        exit;

    }
}
