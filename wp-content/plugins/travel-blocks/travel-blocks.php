<?php
/**
 * Plugin Name: Travel Blocks
 * Plugin URI:  https://example.com/
 * Description: Custom Gutenberg blocks powered by Advanced Custom Fields for travel website. Consolidated plugin with 10 blocks.
 * Version:     1.3.8
 * Author:      Rogger Palomino Gamboa
 * Author URI:  https://example.com/
 * Text Domain: travel-blocks
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

if (!defined('TRAVEL_BLOCKS_PATH')) {
    define('TRAVEL_BLOCKS_PATH', plugin_dir_path(__FILE__));
}
if (!defined('TRAVEL_BLOCKS_URL')) {
    define('TRAVEL_BLOCKS_URL', plugin_dir_url(__FILE__));
}
if (!defined('TRAVEL_BLOCKS_VERSION')) {
    define('TRAVEL_BLOCKS_VERSION', '1.3.8');
}

/**
 * Simple PSR-4 autoloader for the plugin (no Composer needed).
 */
spl_autoload_register(function ($class) {
    $prefix = 'Travel\\Blocks\\';
    $base_dir = TRAVEL_BLOCKS_PATH . 'src/';
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
    load_plugin_textdomain('travel-blocks', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

/**
 * Initialize Admin Pages
 */
if (is_admin()) {
    \Travel\Blocks\Admin\ApiImportAdmin::init();
}

/**
 * Check if ACF Pro is active and supports blocks.
 */
function acf_blocks_is_acf_active(): bool {
    return function_exists('acf_register_block_type');
}

/**
 * Register custom block category for travel blocks.
 */
add_filter('block_categories_all', function ($categories) {
    return array_merge(
        $categories,
        [
            [
                'slug'  => 'travel',
                'title' => __('Travel Blocks', 'travel-blocks'),
                'icon'  => 'palmtree',
            ],
            [
                'slug'  => 'template-blocks',
                'title' => __('Template Blocks', 'travel-blocks'),
                'icon'  => 'layout',
            ],
        ]
    );
});

/**
 * Bootstrap plugin - register ACF blocks.
 */
add_action('acf/init', function () {
    if (!acf_blocks_is_acf_active()) {
        return;
    }

    $acf_blocks = [
        // ==========================================
        // Bloques ACF (Antiguos - NO modificar)
        // ==========================================

        // Bloques originales de acf-gutenberg-blocks
        new \Travel\Blocks\Blocks\ACF\HeroSection(),
        new \Travel\Blocks\Blocks\ACF\StaticCTA(),
        new \Travel\Blocks\Blocks\ACF\FAQAccordion(),
        new \Travel\Blocks\Blocks\ACF\PostsCarousel(), // PostsCarousel Material
        new \Travel\Blocks\Blocks\ACF\SideBySideCards(), // Side by Side Cards (Horizontal)
        new \Travel\Blocks\Blocks\ACF\Breadcrumb(), // Breadcrumb (Migas de Pan)
        new \Travel\Blocks\Blocks\ACF\StickySideMenu(), // Sticky Side Menu (lateral derecho)

        // Bloques migrados desde acf-gutenberg-rest-blocks-v5
        new \Travel\Blocks\Blocks\ACF\FlexibleGridCarousel(),
        new \Travel\Blocks\Blocks\ACF\HeroCarousel(),
        new \Travel\Blocks\Blocks\ACF\PostsCarouselNative(), // PostsCarousel Native CSS
        new \Travel\Blocks\Blocks\ACF\TeamCarousel(),
        new \Travel\Blocks\Blocks\ACF\PostsListAdvanced(),
        new \Travel\Blocks\Blocks\ACF\StaticHero(),
        new \Travel\Blocks\Blocks\ACF\TaxonomyTabs(), // Taxonomy Tabs - Cards organizadas por categorías

        // Bloques Template con ACF
        new \Travel\Blocks\Blocks\Template\PromoCards(), // Two image cards with editable heights
        new \Travel\Blocks\Blocks\Template\TaxonomyArchiveHero(), // Taxonomy Archive Hero with image fallback
        new \Travel\Blocks\Blocks\Package\RelatedPackages(), // Related packages with ACF configuration
        new \Travel\Blocks\Blocks\Package\PackagesByLocation(), // Filter packages by location/destination
        new \Travel\Blocks\Blocks\ACF\ContactForm(), // Hero contact form with background image and ACF fields
    ];

    foreach ($acf_blocks as $block) {
        if (method_exists($block, 'register')) {
            $block->register();
        }
    }
});

/**
 * Register Package blocks (Native WordPress blocks - NO ACF).
 * These blocks get data from Package post meta fields.
 *
 * IMPORTANTE: Estos bloques están diseñados para funcionar SIN ACF
 * y pueden ser usados en single-package.html (FSE template).
 */
add_action('init', function () {
    // ==========================================
    // Bloques Package - BLOQUES NATIVOS WORDPRESS
    // ==========================================
    // ✅ COMPLETAMENTE CONVERTIDOS (6/15) - Listos para producción
    // ⏳ EN PROCESO (9/15) - Requieren conversión final

    $package_blocks = [
        // ====== BLOQUES PACKAGE - QUEDAN EN CATEGORÍA "TRAVEL" ======
        // Estos bloques permanecen en la categoría "Travel Blocks"
        // Los demás se movieron a "Template Blocks" (categoría template-blocks)

        new \Travel\Blocks\Blocks\Package\ProductMetadata(), // TripAdvisor badge + package title
        new \Travel\Blocks\Blocks\Package\QuickFacts(), // Quick facts list with icons
        new \Travel\Blocks\Blocks\Package\CTABanner(), // Call-to-action banner with background
        new \Travel\Blocks\Blocks\Package\PromoCard(), // Promotional card with circular image
        new \Travel\Blocks\Blocks\Package\FAQAccordion(), // FAQ accordion with schema.org markup (Package version)
    ];

    foreach ($package_blocks as $block) {
        if (method_exists($block, 'register')) {
            $block->register();
        }
    }

    // ==========================================
    // Bloques Deal - BLOQUES NATIVOS WORDPRESS
    // ==========================================
    // Bloques para mostrar deals/ofertas con paquetes incluidos
    $deal_blocks = [
        new \Travel\Blocks\Blocks\Deal\DealInfoCard(), // Deal discount, dates, and CTA sidebar card
        new \Travel\Blocks\Blocks\Deal\DealPackagesGrid(), // Grid of packages included in deal
    ];

    // ==========================================
    // Bloques Deal ACF - BLOQUES CON ACF
    // ==========================================
    // Bloques de deals con configuración ACF avanzada
    $deal_acf_blocks = [
        new \Travel\Blocks\Blocks\Deal\DealsSlider(), // Deals slider with countdown timer and packages
    ];

    foreach ($deal_blocks as $block) {
        if (method_exists($block, 'register')) {
            $block->register();
        }
    }

    foreach ($deal_acf_blocks as $block) {
        if (method_exists($block, 'register')) {
            $block->register();
        }
    }

    // ==========================================
    // Bloques Template - BLOQUES NATIVOS WORDPRESS
    // ==========================================
    // Bloques diseñados para templates FSE (single-package.html)
    // Con preview data automático en editor y datos reales en frontend
    $template_blocks = [
        // ====== NATIVE TEMPLATE BLOCKS (15 total) ======
        // Template-specific blocks (created in Template namespace)
        new \Travel\Blocks\Blocks\Template\Breadcrumb(), // Breadcrumb navigation
        new \Travel\Blocks\Blocks\Template\HeroMediaGrid(), // Gallery + Map + Video grid
        new \Travel\Blocks\Blocks\Template\PackageHeader(), // Title + Overview + Metadata

        // Package blocks moved to Template category (still in Package namespace)
        new \Travel\Blocks\Blocks\Package\ProductGalleryHero(), // Gallery carousel with discount badge
        new \Travel\Blocks\Blocks\Package\PackageVideo(), // YouTube video embed
        new \Travel\Blocks\Blocks\Package\PackageMap(), // Route map image
        new \Travel\Blocks\Blocks\Package\MetadataLine(), // Metadata line with icons
        new \Travel\Blocks\Blocks\Package\ItineraryDayByDay(), // Accordion-style day-by-day itinerary
        new \Travel\Blocks\Blocks\Package\DatesAndPrices(), // Departures calendar with pricing
        new \Travel\Blocks\Blocks\Package\InclusionsExclusions(), // What's included/not included
        // ContactForm moved to ACF blocks section
        new \Travel\Blocks\Blocks\Package\PricingCard(), // Sticky sidebar conversion card
        new \Travel\Blocks\Blocks\Package\ReviewsCarousel(), // Customer reviews mini-carousel
        // RelatedPackages moved to ACF blocks section
        new \Travel\Blocks\Blocks\Package\ContactPlannerForm(), // Contact form with background image
        new \Travel\Blocks\Blocks\Package\TravelerReviews(), // Large grid of reviews with filters
        new \Travel\Blocks\Blocks\Package\RelatedPostsGrid(), // Related blog posts grid
        new \Travel\Blocks\Blocks\Package\ImpactSection(), // Social responsibility section
        new \Travel\Blocks\Blocks\Package\TrustBadges(), // Trust badges and certifications

        // ====== ACF TEMPLATE BLOCKS (1 total) ======
        new \Travel\Blocks\Blocks\Template\FAQAccordion(), // FAQ accordion with schema markup
    ];

    foreach ($template_blocks as $block) {
        if (method_exists($block, 'register')) {
            $block->register();
        }
    }
});

/**
 * Configure ACF JSON paths for field synchronization.
 */
add_filter('acf/settings/save_json', function ($path) {
    return TRAVEL_BLOCKS_PATH . 'acf-json';
});

add_filter('acf/settings/load_json', function ($paths) {
    $paths[] = TRAVEL_BLOCKS_PATH . 'acf-json';
    return $paths;
});

/**
 * Initialize ContentQueryHelper ACF filters for dynamic field population
 */
\Travel\Blocks\Helpers\ContentQueryHelper::init_acf_filters();

/**
 * Clear content cache when a package, post, or deal is saved/updated.
 * This ensures dynamic content blocks show the latest data.
 */
add_action('save_post_package', function ($post_id) {
    \Travel\Blocks\Helpers\ContentQueryHelper::clear_cache();
});

add_action('save_post_post', function ($post_id) {
    \Travel\Blocks\Helpers\ContentQueryHelper::clear_cache();
});

add_action('save_post_deal', function ($post_id) {
    \Travel\Blocks\Helpers\ContentQueryHelper::clear_cache();
});

/**
 * Enqueue block assets.
 */
add_action('enqueue_block_assets', function () {
    // Common styles for all blocks
    if (file_exists(TRAVEL_BLOCKS_PATH . 'assets/blocks/common.css')) {
        wp_enqueue_style(
            'travel-blocks-common',
            TRAVEL_BLOCKS_URL . 'assets/blocks/common.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );
    }
});

/**
 * Enqueue editor assets for Template Blocks
 */
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_script(
        'travel-template-blocks-editor',
        TRAVEL_BLOCKS_URL . 'assets/js/template-blocks-editor.js',
        ['wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-server-side-render'],
        TRAVEL_BLOCKS_VERSION,
        true
    );
});
