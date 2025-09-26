<?php

use Larapress\Console\Please;

require_once __DIR__.'/vendor/autoload.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

$please = new Please();

$command = $argv[1] ?? null;
if (!$command) {
    echo "Usage: php please.php <command>\n";
    exit(-1);
}

$please->run($command);
