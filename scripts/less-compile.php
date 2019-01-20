<?php
require "vendor/leafo/lessphp/lessc.inc.php";

function compileLess() {
    $lessFiles = glob('./app/src/styles/main.less');
    $less = new lessc;
    echo "starting less compilation...\n";
    foreach ($lessFiles as $filename) {
        try {
            $targetName = str_replace('.less', '.css', $filename);
            echo 'compiling '.$filename.' => '.$targetName."\n";
            $less->checkedCompile($filename, $targetName);
        } catch (Exception $e) {
            die($e);
        }
    }
    echo 'successfully compiled '.count($lessFiles)." less files\n";
}

compileLess();
