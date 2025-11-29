<?php
/**
 * Plugin Name: Travel Performance & SEO
 * Plugin URI:  https://example.com/
 * Description: Performance optimizations and SEO enhancements for travel website.
 * Version:     1.0.0
 * Author:      Rogger Palomino Gamboa
 * Author URI:  https://example.com/
 * Text Domain: travel-performance
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

if (!defined('TRAVEL_PERFORMANCE_PATH')) {
    define('TRAVEL_PERFORMANCE_PATH', plugin_dir_path(__FILE__));
}
if (!defined('TRAVEL_PERFORMANCE_URL')) {
    define('TRAVEL_PERFORMANCE_URL', plugin_dir_url(__FILE__));
}
if (!defined('TRAVEL_PERFORMANCE_VERSION')) {
    define('TRAVEL_PERFORMANCE_VERSION', '1.0.0');
}

/**
 * Simple PSR-4 autoloader for the plugin (no Composer needed).
 */
spl_autoload_register(function ($class) {
    $prefix = 'Travel\\Performance\\';
    $base_dir = TRAVEL_PERFORMANCE_PATH . 'src/';
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
    load_plugin_textdomain('travel-performance', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

/**
 * Bootstrap plugin - register optimization services.
 */
add_action('init', function () {
    $services = [
        // Performance optimizations
        new \Travel\Performance\Performance\QueryOptimizer(),
        new \Travel\Performance\Performance\LazyLoadImages(),
        new \Travel\Performance\Performance\AssetOptimizer(),
        new \Travel\Performance\Performance\CacheWarmer(),

        // SEO enhancements
        new \Travel\Performance\SEO\SchemaMarkup(),
        new \Travel\Performance\SEO\MetaTags(),
    ];

    foreach ($services as $service) {
        if (method_exists($service, 'register')) {
            $service->register();
        }
    }
}, 10);

/**
 * Cache invalidation hooks.
 */
add_action('save_post', function ($post_id, $post) {
    // Skip revisions and autosaves
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    $cache_warmer = new \Travel\Performance\Performance\CacheWarmer();

    // Invalidate cache based on post type
    if (in_array($post->post_type, ['tour', 'destination', 'deal', 'review'])) {
        $cache_warmer->invalidate_cache_for_post_type($post->post_type);
    }

    // Specific tour cache invalidation
    if ($post->post_type === 'tour') {
        $cache_warmer->invalidate_tour_cache($post_id);
    }
}, 10, 2);

/**
 * Flush object cache on plugin activation.
 */
register_activation_hook(__FILE__, function () {
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
});
