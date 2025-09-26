<?php

/**
 * @description App Initialization
 */

use Larapress\App\Application;

if ( ! defined( 'ABSPATH' ) ) exit;

Application::make()
    ->loadConfig()
    ->loadDatabase()
    ->loadSession()
    ->loadAdmin()
    ->loadResources();