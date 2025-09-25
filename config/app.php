<?php
$plugin_base_file = str_replace('/config', '', __FILE__);

return [
    'views' => __DIR__ . '/views',
    'slug' => 'larapress',
    'resources_url' => plugin_dir_url( $plugin_base_file ) . 'resources',
];