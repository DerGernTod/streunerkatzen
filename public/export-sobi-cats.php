<?php

use Streunerkatzen\CatExporter;

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
$exporter = new CatExporter();
echo json_encode($exporter->getEntries());
