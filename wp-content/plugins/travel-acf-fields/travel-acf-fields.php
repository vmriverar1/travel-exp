<?php
/**
 * Plugin Name: Travel ACF Fields
 * Plugin URI:  https://example.com/
 * Description: Professional, OOP-first scaffolding to register ACF field groups programmatically with JSON sync and scalable architecture.
 * Version:     1.1.0
 * Author:      Rogger Palomino Gamboa
 * Author URI:  https://example.com/
 * Text Domain: travel-acf-fields
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

if (!defined('TRAVEL_ACF_FIELDS_PATH')) {
    define('TRAVEL_ACF_FIELDS_PATH', plugin_dir_path(__FILE__));
}
if (!defined('TRAVEL_ACF_FIELDS_URL')) {
    define('TRAVEL_ACF_FIELDS_URL', plugin_dir_url(__FILE__));
}
if (!defined('TRAVEL_ACF_FIELDS_VERSION')) {
    define('TRAVEL_ACF_FIELDS_VERSION', '1.1.0');
}

// Simple PSR-4 autoloader for Aurora\ACFKit
spl_autoload_register(function ($class) {
    $prefix = 'Aurora\\ACFKit\\';
    $base_dir = TRAVEL_ACF_FIELDS_PATH . 'src/';
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

// PSR-4 autoloader for Travel\ACFFields
spl_autoload_register(function ($class) {
    $prefix = 'Travel\\ACFFields\\';
    $base_dir = TRAVEL_ACF_FIELDS_PATH . 'src/';
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

// Load i18n
add_action('plugins_loaded', function () {
    load_plugin_textdomain('travel-acf-fields', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Ensure ACF is active
function aurora_acf_kit_acf_active(): bool {
    return function_exists('acf_add_local_field_group');
}

// Bootstrap services
add_action('init', function () {
    if (!aurora_acf_kit_acf_active()) {
        return;
    }

    $services = [
        new \Aurora\ACFKit\Integration\JsonSync(),
        new \Aurora\ACFKit\Setup\Settings(),

        // Field Groups - Pages
        new \Aurora\ACFKit\FieldGroups\GlobalOptions(),
        new \Aurora\ACFKit\FieldGroups\HomePage(),
        new \Aurora\ACFKit\FieldGroups\TourSingle(),
        new \Aurora\ACFKit\FieldGroups\AboutPage(),
        new \Aurora\ACFKit\FieldGroups\HomeHero(),

        // Field Groups - Package CPT
        new \Aurora\ACFKit\FieldGroups\PackageGeneral(),
        new \Aurora\ACFKit\FieldGroups\PackageBaseInfo(),
        new \Aurora\ACFKit\FieldGroups\PackagePricing(),
        new \Aurora\ACFKit\FieldGroups\PackageMedia(),
        new \Aurora\ACFKit\FieldGroups\PackageItinerary(),
        new \Aurora\ACFKit\FieldGroups\PackageAvailability(),
        new \Aurora\ACFKit\FieldGroups\PackageAdditionalContent(),
        new \Aurora\ACFKit\FieldGroups\PackageSEO(),

        // Field Groups - Deal CPT
        new \Travel\ACFFields\FieldGroups\Deal(),

        // Field Groups - Post Featured (for Posts and Packages)
        new \Travel\ACFFields\FieldGroups\PostFeatured(),

        // Field Groups - Location CPT (NUEVO)
        new \Aurora\ACFKit\FieldGroups\LocationsGeneral(),

        // Field Groups - Collaborators CPT (NUEVO)
        new \Aurora\ACFKit\FieldGroups\CollaboratorsGeneral(),

        // Field Groups - Taxonomies (NUEVO)
        new \Aurora\ACFKit\FieldGroups\TaxonomyCountries(),
        new \Aurora\ACFKit\FieldGroups\TaxonomyDestinations(),
        new \Aurora\ACFKit\FieldGroups\TaxonomyFlights(),
        new \Aurora\ACFKit\FieldGroups\TaxonomyRoles(),
        new \Aurora\ACFKit\FieldGroups\TaxonomySpotCalendar(),
        new \Aurora\ACFKit\FieldGroups\TaxonomySpecialists(),
        new \Aurora\ACFKit\FieldGroups\TaxonomyOptionalRenting(),
        new \Aurora\ACFKit\FieldGroups\TaxonomyFAQ(),
    ];

    foreach ($services as $service) {
        if (method_exists($service, 'register')) {
            $service->register();
        }
    }

    // Initialize PackageHelper for performance optimization
    if (class_exists('\Aurora\ACFKit\Helpers\PackageHelper')) {
        \Aurora\ACFKit\Helpers\PackageHelper::register();
    }
}, 5);

/**
 * Hide native Description field for FAQ taxonomy
 */
add_action('admin_enqueue_scripts', function($hook) {
    // Only load on taxonomy edit pages
    if ($hook === 'edit-tags.php' || $hook === 'term.php') {
        $screen = get_current_screen();
        if ($screen && $screen->taxonomy === 'faq') {
            wp_enqueue_style(
                'travel-acf-fields-faq-admin',
                TRAVEL_ACF_FIELDS_URL . 'assets/css/admin-taxonomy-faq.css',
                [],
                TRAVEL_ACF_FIELDS_VERSION
            );

            wp_enqueue_script(
                'travel-acf-fields-faq-admin',
                TRAVEL_ACF_FIELDS_URL . 'assets/js/admin-taxonomy-faq.js',
                ['jquery'],
                TRAVEL_ACF_FIELDS_VERSION,
                true
            );
        }
    }
});

/**
 * Customize FAQ taxonomy table columns
 */
// Remove description and slug columns
add_filter('manage_edit-faq_columns', function($columns) {
    unset($columns['description']);
    unset($columns['slug']);
    return $columns;
});

// Add custom Respuesta column
add_filter('manage_edit-faq_columns', function($columns) {
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        // Add after name column
        if ($key === 'name') {
            $new_columns['pregunta'] = 'Pregunta';
            $new_columns['respuesta'] = 'Respuesta';
        }
    }
    return $new_columns;
});

// Display content for custom columns
add_filter('manage_faq_custom_column', function($content, $column_name, $term_id) {
    if ($column_name === 'pregunta') {
        $pregunta = get_field('pregunta', 'faq_' . $term_id);
        return $pregunta ? esc_html(wp_trim_words($pregunta, 10)) : '—';
    }

    if ($column_name === 'respuesta') {
        $respuesta = get_field('respuesta', 'faq_' . $term_id);
        if ($respuesta) {
            // Strip HTML and limit to 15 words
            return esc_html(wp_trim_words(strip_tags($respuesta), 15));
        }
        return '—';
    }

    return $content;
}, 10, 3);

/**
 * Filter packages in Deal relationship field to only show packages with active promotions
 */
add_filter('acf/fields/relationship/query/key=field_deal_packages', function($args, $field, $post_id) {
    // Add meta query to only show packages with active_promotion = 1
    $args['meta_query'] = [
        [
            'key' => 'active_promotion',
            'value' => '1',
            'compare' => '='
        ]
    ];

    return $args;
}, 10, 3);
