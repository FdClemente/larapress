<?php

/**
 * @description App Initialization
 */

use As247\WpEloquent\Application;

if ( ! defined( 'ABSPATH' ) ) exit;

Application::bootWp();

session_start();

loadFiles('helpers');

config_loader();

//admin options
if(is_admin()) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
    loadResources();
}
loadShortCodes();
