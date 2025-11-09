<?php
/**
 * Plugin Name: Travel Post Types
 * Plugin URI:  https://example.com/
 * Description: Professional, OOP-first scaffolding for scalable Custom Post Types and Taxonomies (Gutenberg/ACF ready).
 * Version:     1.0.0
 * Author:      Rogger Palomino Gamboa
 * Author URI:  https://example.com/
 * Text Domain: travel-post-types
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

if (!defined('TRAVEL_POST_TYPES_PATH')) {
    define('TRAVEL_POST_TYPES_PATH', plugin_dir_path(__FILE__));
}
if (!defined('TRAVEL_POST_TYPES_URL')) {
    define('TRAVEL_POST_TYPES_URL', plugin_dir_url(__FILE__));
}
if (!defined('TRAVEL_POST_TYPES_VERSION')) {
    define('TRAVEL_POST_TYPES_VERSION', '1.0.0');
}

/**
 * Simple PSR-4 autoloader for the plugin (no Composer needed).
 */
spl_autoload_register(function ($class) {
    $prefix = 'Aurora\\ContentKit\\';
    $base_dir = TRAVEL_POST_TYPES_PATH . 'src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load text domain for i18n.
add_action('plugins_loaded', function () {
    load_plugin_textdomain('travel-post-types', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Bootstrap plugin.
add_action('init', function () {
    $services = [
        // Custom Post Types
        new \Aurora\ContentKit\PostTypes\Package(),
        new \Aurora\ContentKit\PostTypes\Location(),
        new \Aurora\ContentKit\PostTypes\Deal(),
        new \Aurora\ContentKit\PostTypes\Review(),
        new \Aurora\ContentKit\PostTypes\Guide(),

        // Taxonomies
        new \Aurora\ContentKit\Taxonomies\PackageType(),
        new \Aurora\ContentKit\Taxonomies\Interest(),
        // new \Aurora\ContentKit\Taxonomies\Locations(), // LEGACY - Desactivado
        new \Aurora\ContentKit\Taxonomies\OptionalRenting(),
        new \Aurora\ContentKit\Taxonomies\IncludedServices(),
        new \Aurora\ContentKit\Taxonomies\Days(),
        new \Aurora\ContentKit\Taxonomies\AdditionalInfo(),
        new \Aurora\ContentKit\Taxonomies\TagLocations(),
        new \Aurora\ContentKit\Taxonomies\Activities(),
        new \Aurora\ContentKit\Taxonomies\TypeService(),
        new \Aurora\ContentKit\Taxonomies\Hotels(),
        new \Aurora\ContentKit\Taxonomies\SpotCalendar(),
        new \Aurora\ContentKit\Taxonomies\Specialists(),
        new \Aurora\ContentKit\Taxonomies\Countries(),
        new \Aurora\ContentKit\Taxonomies\Destinations(),
        new \Aurora\ContentKit\Taxonomies\Flights(),
        new \Aurora\ContentKit\Taxonomies\LandingPackages(),
        new \Aurora\ContentKit\Taxonomies\Roles(),
        new \Aurora\ContentKit\Taxonomies\FAQ(),
    ];

    foreach ($services as $service) {
        if (method_exists($service, 'register')) {
            $service->register();
        }
    }
}, 5);

/**
 * Flush rewrite rules on activation/deactivation.
 */
register_activation_hook(__FILE__, function () {
    // Ensure CPTs/Taxonomies are registered before flushing.
    do_action('init');
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});
