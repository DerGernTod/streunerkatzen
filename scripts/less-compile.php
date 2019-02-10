<?php
require "vendor/leafo/lessphp/lessc.inc.php";

function compileLess() {
    $lessFiles = glob('./app/src/styles/main.less');
    $less = new lessc;
    echo "starting less compilation...\n";
    foreach ($lessFiles as $filename) {
        try {
            $targetName = str_replace('.less', '.css', $filename);
            unlink($targetName);
            echo 'compiling '.$filename.' => '.$targetName."\n";
            $less->compileFile($filename, $targetName);
        } catch (Exception $e) {
            echo 'Error during less compilation: '.$e->getMessage().' at '.$e->getFile().':'.$e->getLine();
            return;
        }
    }
    echo 'successfully compiled '.count($lessFiles)." less files\n";
}

compileLess();
