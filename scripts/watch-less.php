<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once 'scripts/less-compile.php';
use Illuminate\Filesystem\Filesystem;
use JasonLewis\ResourceWatcher\Tracker;
use JasonLewis\ResourceWatcher\Watcher;

$tracker = new Tracker;
$files = new Filesystem;
$watcher = new Watcher($tracker, $files);

$listener = $watcher->watch('./app/src/styles');
$listener->onModify(function ($resource, $path) {
    if (str_contains($path, '.less')) {
        echo "{$path} changed...";
        compileLess();
    }
});

$watcher->start();
