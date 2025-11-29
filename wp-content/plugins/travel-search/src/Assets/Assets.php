<?php

namespace TravelSearch\Assets;

if (!defined('ABSPATH')) {
    exit;
}

class Assets
{
    public function init(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_front_assets']);
    }

    public function enqueue_front_assets(): void
    {
        $plugin_url = plugin_dir_url(dirname(__DIR__)); // /travel-search/
        $version = '1.0.2'; // Increment version for cache busting

        // CSS Files
        $css_front = $plugin_url . 'assets/css/front.css';
        $css_filters = $plugin_url . 'assets/css/filters.css';

        // JS Files
        $js_filters = $plugin_url . 'assets/js/packages-filters.js';
        $js_favorites = $plugin_url . 'assets/js/packages-favorites.js';

        // Register styles
        wp_register_style('travel-search-front', $css_front, [], $version);
        wp_register_style('travel-search-filters', $css_filters, [], $version);

        // Register scripts
        wp_register_script('travel-search-filters', $js_filters, ['jquery'], $version, true);
        wp_register_script('travel-search-favorites', $js_favorites, [], $version, true);
    }
}
