<?php

function loadFiles($path): void
{
    $files = glob(__DIR__ . '/'.config('app.slug').'/' .$path . '/*.php');

    foreach ($files as $file) {
        require_once $file;
    }
}

function config_loader(): void
{
    $path = __DIR__ . '/../config';

    $files = glob($path.'/*.php');

    global $configs;

    foreach ($files as $file) {
        $filename = basename($file, '.php');
        $configs[$filename] = require $file;
    }
}

function loadResources(): void
{
    $path = __DIR__ . '/../App/Resources';

    if (!is_dir($path)) {
        return;
    }

    $files = scandir($path);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === 'Dependencies') {
            continue;
        }

        $class = "App\\Resources" . '\\' . pathinfo($file, PATHINFO_FILENAME);

        if (class_exists($class) && method_exists($class, '__construct')) {
            new $class();
        }
    }
}

