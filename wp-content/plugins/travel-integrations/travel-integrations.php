<?php
/**
 * Plugin Name: Travel Integrations
 * Plugin URI:  https://example.com/
 * Description: External service integrations for travel website (Reviews, Payments, Analytics).
 * Version:     1.0.0
 * Author:      Rogger Palomino Gamboa
 * Author URI:  https://example.com/
 * Text Domain: travel-integrations
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

if (!defined('TRAVEL_INTEGRATIONS_PATH')) {
    define('TRAVEL_INTEGRATIONS_PATH', plugin_dir_path(__FILE__));
}
if (!defined('TRAVEL_INTEGRATIONS_URL')) {
    define('TRAVEL_INTEGRATIONS_URL', plugin_dir_url(__FILE__));
}
if (!defined('TRAVEL_INTEGRATIONS_VERSION')) {
    define('TRAVEL_INTEGRATIONS_VERSION', '1.0.0');
}

/**
 * Simple PSR-4 autoloader for the plugin (no Composer needed).
 */
spl_autoload_register(function ($class) {
    $prefix = 'Travel\\Integrations\\';
    $base_dir = TRAVEL_INTEGRATIONS_PATH . 'src/';
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
    load_plugin_textdomain('travel-integrations', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

/**
 * Schedule cron job for reviews sync on activation.
 */
register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('travel_sync_reviews_daily')) {
        wp_schedule_event(time(), 'daily', 'travel_sync_reviews_daily');
    }
});

/**
 * Clear cron job on deactivation.
 */
register_deactivation_hook(__FILE__, function () {
    wp_clear_scheduled_hook('travel_sync_reviews_daily');
});

/**
 * Bootstrap plugin - register services.
 */
add_action('init', function () {
    // Reviews Syncer (only initialize if class exists)
    if (class_exists('\Travel\Integrations\Reviews\ReviewsSyncer')) {
        $syncer = new \Travel\Integrations\Reviews\ReviewsSyncer();
        add_action('travel_sync_reviews_daily', [$syncer, 'sync_all_reviews']);
    }
}, 10);

/**
 * Add admin menu for settings.
 */
add_action('admin_menu', function () {
    add_options_page(
        __('Travel Integrations', 'travel-integrations'),
        __('Travel Integrations', 'travel-integrations'),
        'manage_options',
        'travel-integrations',
        function () {
            include TRAVEL_INTEGRATIONS_PATH . 'admin/settings-page.php';
        }
    );
});

/**
 * Register settings.
 */
add_action('admin_init', function () {
    // TripAdvisor Settings
    register_setting('travel_integrations', 'travel_tripadvisor_api_key');
    register_setting('travel_integrations', 'travel_tripadvisor_location_id');
    register_setting('travel_integrations', 'travel_tripadvisor_enabled', ['type' => 'boolean', 'default' => false]);

    // Google Reviews Settings
    register_setting('travel_integrations', 'travel_google_api_key');
    register_setting('travel_integrations', 'travel_google_place_id');
    register_setting('travel_integrations', 'travel_google_enabled', ['type' => 'boolean', 'default' => false]);

    // Facebook Settings
    register_setting('travel_integrations', 'travel_facebook_access_token');
    register_setting('travel_integrations', 'travel_facebook_page_id');
    register_setting('travel_integrations', 'travel_facebook_enabled', ['type' => 'boolean', 'default' => false]);

    // Stripe Settings
    register_setting('travel_integrations', 'travel_stripe_publishable_key');
    register_setting('travel_integrations', 'travel_stripe_secret_key');
    register_setting('travel_integrations', 'travel_stripe_webhook_secret');
    register_setting('travel_integrations', 'travel_stripe_enabled', ['type' => 'boolean', 'default' => false]);
    register_setting('travel_integrations', 'travel_stripe_test_mode', ['type' => 'boolean', 'default' => true]);

    // Add settings sections
    add_settings_section(
        'travel_integrations_reviews',
        __('Reviews Integrations', 'travel-integrations'),
        function () {
            echo '<p>' . esc_html__('Configure integrations with review platforms.', 'travel-integrations') . '</p>';
        },
        'travel_integrations'
    );

    add_settings_section(
        'travel_integrations_payments',
        __('Payment Integrations', 'travel-integrations'),
        function () {
            echo '<p>' . esc_html__('Configure payment gateway integrations.', 'travel-integrations') . '</p>';
        },
        'travel_integrations'
    );
});

/**
 * Handle Stripe webhooks.
 */
add_action('rest_api_init', function () {
    if (class_exists('\Travel\Integrations\Payments\WebhookHandler')) {
        register_rest_route('travel/v1', '/stripe/webhook', [
            'methods' => 'POST',
            'callback' => function ($request) {
                $handler = new \Travel\Integrations\Payments\WebhookHandler();
                return $handler->handle($request);
            },
            'permission_callback' => '__return_true', // Stripe will verify via webhook secret
        ]);
    }
});
