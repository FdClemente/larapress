<?php

include_once(ABSPATH . 'wp-includes/pluggable.php');

function loadFiles($path): void
{
    $files = glob(__DIR__ . '/../../' .$path . '/*.php');

    foreach ($files as $file) {
        require_once $file;
    }
}

function config_loader(){
    $path = __DIR__ . '/../../config';

    $files = glob($path.'/*.php');

    global $configs;

    foreach ($files as $file) {
        $filename = basename($file, '.php');
        $configs[$filename] = require $file;
    }
}

function loadResources()
{
    $path = __DIR__ . '/../Resources';
    if (!is_dir($path)) {
        return;
    }
    $files = scandir($path);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $class = "App\\Resources" . '\\' . pathinfo($file, PATHINFO_FILENAME);
        if (class_exists($class) && method_exists($class, '__construct')) {
            new $class();
        }
    }
}
function loadShortCodes()
{
    $path = __DIR__ . '/../ShortCodes';
    if (!is_dir($path)) {
        return;
    }
    $files = scandir($path);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $class = "App\\ShortCodes" . '\\' . pathinfo($file, PATHINFO_FILENAME);
        if (class_exists($class) && method_exists($class, '__construct')) {
            new $class();
        }
    }
}