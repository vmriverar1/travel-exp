<?php
/**
 * Plugin Name: Travel Forms
 * Plugin URI:  https://example.com/
 * Description: Custom forms for travel website with HubSpot integration (Contact, Booking, Brochure).
 * Version:     1.0.0
 * Author:      Rogger Palomino Gamboa
 * Author URI:  https://example.com/
 * Text Domain: travel-forms
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

if (!defined('TRAVEL_FORMS_PATH')) {
    define('TRAVEL_FORMS_PATH', plugin_dir_path(__FILE__));
}
if (!defined('TRAVEL_FORMS_URL')) {
    define('TRAVEL_FORMS_URL', plugin_dir_url(__FILE__));
}
if (!defined('TRAVEL_FORMS_VERSION')) {
    define('TRAVEL_FORMS_VERSION', '1.0.3');
}

/**
 * Simple PSR-4 autoloader for the plugin (no Composer needed).
 */
spl_autoload_register(function ($class) {
    $prefix = 'Travel\\Forms\\';
    $base_dir = TRAVEL_FORMS_PATH . 'src/';
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
    load_plugin_textdomain('travel-forms', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

/**
 * Create database table on plugin activation.
 */
register_activation_hook(__FILE__, function () {
    \Travel\Forms\Core\Database::create_table();
});

/**
 * Bootstrap plugin - register forms.
 */
add_action('init', function () {
    error_log('travel-forms: init action triggered');

    try {
        $forms = [
            new \Travel\Forms\Forms\ContactForm(),
            new \Travel\Forms\Forms\BookingForm(),
            new \Travel\Forms\Forms\BrochureForm(),
            new \Travel\Forms\Forms\BookingWizard(),
        ];

        error_log('travel-forms: All form instances created successfully');

        foreach ($forms as $index => $form) {
            $className = get_class($form);
            error_log("travel-forms: Processing form #{$index}: {$className}");

            if (method_exists($form, 'register')) {
                $form->register();
                error_log("travel-forms: Registered {$className}");
            } else {
                error_log("travel-forms: {$className} does not have register() method");
            }
        }
    } catch (\Exception $e) {
        error_log('travel-forms: ERROR - ' . $e->getMessage());
    }
}, 10);

/**
 * Enqueue form assets.
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'travel-forms',
        TRAVEL_FORMS_URL . 'assets/css/forms.css',
        [],
        TRAVEL_FORMS_VERSION
    );

    wp_enqueue_script(
        'travel-forms-validation',
        TRAVEL_FORMS_URL . 'assets/js/validation.js',
        [],
        TRAVEL_FORMS_VERSION,
        true
    );

    wp_localize_script('travel-forms-validation', 'travelFormsConfig', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('travel_forms_nonce'),
        'messages' => [
            'required' => __('This field is required', 'travel-forms'),
            'email' => __('Please enter a valid email address', 'travel-forms'),
            'phone' => __('Please enter a valid phone number', 'travel-forms'),
            'success' => __('Form submitted successfully!', 'travel-forms'),
            'error' => __('An error occurred. Please try again.', 'travel-forms'),
        ],
    ]);
});

/**
 * Add settings page for HubSpot API configuration.
 */
add_action('admin_menu', function () {
    add_options_page(
        __('Travel Forms Settings', 'travel-forms'),
        __('Travel Forms', 'travel-forms'),
        'manage_options',
        'travel-forms-settings',
        function () {
            include TRAVEL_FORMS_PATH . 'admin/settings-page.php';
        }
    );
});

/**
 * Register settings.
 */
add_action('admin_init', function () {
    register_setting('travel_forms', 'travel_forms_hubspot_api_key', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    register_setting('travel_forms', 'travel_forms_hubspot_enabled', [
        'type' => 'boolean',
        'default' => false,
    ]);

    add_settings_section(
        'travel_forms_hubspot_section',
        __('HubSpot Integration', 'travel-forms'),
        function () {
            echo '<p>' . esc_html__('Configure HubSpot API integration for form submissions.', 'travel-forms') . '</p>';
        },
        'travel_forms'
    );

    add_settings_field(
        'travel_forms_hubspot_enabled',
        __('Enable HubSpot', 'travel-forms'),
        function () {
            $enabled = get_option('travel_forms_hubspot_enabled', false);
            echo '<input type="checkbox" name="travel_forms_hubspot_enabled" value="1" ' . checked(1, $enabled, false) . ' />';
            echo '<p class="description">' . esc_html__('Enable sending form submissions to HubSpot', 'travel-forms') . '</p>';
        },
        'travel_forms',
        'travel_forms_hubspot_section'
    );

    add_settings_field(
        'travel_forms_hubspot_api_key',
        __('HubSpot API Key', 'travel-forms'),
        function () {
            $api_key = get_option('travel_forms_hubspot_api_key', '');
            echo '<input type="text" class="regular-text" name="travel_forms_hubspot_api_key" value="' . esc_attr($api_key) . '" />';
            echo '<p class="description">' . esc_html__('Enter your HubSpot Private App Access Token', 'travel-forms') . '</p>';
        },
        'travel_forms',
        'travel_forms_hubspot_section'
    );
});
