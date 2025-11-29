<?php
/*
Plugin Name: Travel Search (Packages & Posts)
Description: Advanced search for travel packages and blog posts with ACF-powered filters and REST SPA behavior.
Version: 1.0.0
Author: ChatGPT & Estimado
*/

if (!defined('ABSPATH')) {
    exit;
}

// Simple PSR-4 autoloader for the TravelSearch namespace.
spl_autoload_register(function ($class) {
    if (strpos($class, 'TravelSearch\\') !== 0) {
        return;
    }
    $relative = substr($class, strlen('TravelSearch\\'));
    $relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative);
    $file = plugin_dir_path(__FILE__) . 'src/' . $relative . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

add_action('plugins_loaded', function () {
    if (!class_exists('TravelSearch\\Plugin')) {
        return;
    }

    $plugin = new TravelSearch\Plugin();
    $plugin->init();
});
