<?php
namespace Streunerkatzen\Utils;

class Utils {
    public static function generateURLPath($string, $id) {
        // replace spaces with hyphens
        $urlPath = str_replace(' ', '-', $string);

        // replace umlaute
        $search = array('Ä', 'Ö', 'Ü', 'ä', 'ö', 'ü', 'ß', '´');
        $replace = array('Ae', 'Oe', 'Ue', 'ae', 'oe', 'ue', 'ss', '');
        $urlPath = str_replace($search, $replace, $urlPath);

        // remove special chars
        $urlPath = preg_replace('/[^A-Za-z0-9\-]/', '', $urlPath);

        // replace multiple hyphens with single one
        $urlPath = preg_replace('/-+/', '-', $urlPath);

        // string to lowercase
        $urlPath = mb_strtolower($urlPath);

        // add id
        $urlPath = $urlPath.'-'.$id;

        return $urlPath;
    }
}
