<?php
/**
 * Travel Child Theme Functions
 *
 * Add your custom functions, hooks, and filters here.
 * All parent theme functionality is automatically inherited.
 *
 * @package Travel Child Theme
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles
 */
function travel_child_enqueue_styles() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style(
        'travel-parent-style',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme()->parent()->get('Version')
    );

    // Enqueue child theme stylesheet
    wp_enqueue_style(
        'travel-child-style',
        get_stylesheet_uri(),
        ['travel-parent-style'],
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'travel_child_enqueue_styles', 20);

/**
 * Add your custom functions below
 *
 * Examples:
 *
 * // Override parent theme function
 * function travel_child_custom_function() {
 *     // Your code here
 * }
 *
 * // Add custom hook
 * add_action('wp_footer', 'travel_child_footer_content');
 * function travel_child_footer_content() {
 *     // Your code here
 * }
 *
 * // Modify parent theme behavior
 * add_filter('some_parent_filter', 'travel_child_modify_filter');
 * function travel_child_modify_filter($value) {
 *     // Modify $value
 *     return $value;
 * }
 */
