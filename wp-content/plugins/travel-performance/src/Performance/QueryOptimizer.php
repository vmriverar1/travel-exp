<?php
/**
 * Query Optimizer
 *
 * Optimizes WordPress queries to prevent N+1 problems and improve performance.
 *
 * @package Travel\Performance\Performance
 * @since 1.0.0
 */

namespace Travel\Performance\Performance;

class QueryOptimizer
{
    /**
     * Register query optimization hooks.
     *
     * @return void
     */
    public function register(): void
    {
        // Pre-fetch ACF fields for post lists
        add_action('pre_get_posts', [$this, 'eager_load_acf_fields']);

        // Pre-fetch taxonomies for tours
        add_action('pre_get_posts', [$this, 'eager_load_taxonomies']);

        // Optimize featured images queries
        add_filter('posts_results', [$this, 'eager_load_featured_images'], 10, 2);

        // Remove unnecessary queries
        add_action('init', [$this, 'remove_unnecessary_queries']);
    }

    /**
     * Eager load ACF fields for post queries.
     *
     * @param \WP_Query $query Query object
     *
     * @return void
     */
    public function eager_load_acf_fields(\WP_Query $query): void
    {
        // Only for main queries on frontend
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        // Only for post types that use ACF
        $post_type = $query->get('post_type');
        $acf_post_types = ['tour', 'destination', 'deal', 'review', 'guide', 'page'];

        if (!in_array($post_type, $acf_post_types)) {
            return;
        }

        // Pre-load ACF fields after posts are retrieved
        add_action('the_post', function ($post) {
            if (function_exists('acf_get_values')) {
                // This caches ACF values for the post
                acf_get_values($post->ID);
            }
        }, 1);
    }

    /**
     * Eager load taxonomies for tour queries.
     *
     * @param \WP_Query $query Query object
     *
     * @return void
     */
    public function eager_load_taxonomies(\WP_Query $query): void
    {
        // Only for tour archives and searches
        if (is_admin() || $query->get('post_type') !== 'tour') {
            return;
        }

        // Update term cache for tours
        add_filter('posts_results', function ($posts) {
            if (empty($posts)) {
                return $posts;
            }

            $post_ids = wp_list_pluck($posts, 'ID');

            // Pre-load all tour taxonomies
            $taxonomies = ['tour_category', 'difficulty', 'duration', 'region', 'tour_type'];

            foreach ($taxonomies as $taxonomy) {
                $terms = wp_get_object_terms($post_ids, $taxonomy, ['fields' => 'all_with_object_id']);
                // This populates the cache
            }

            return $posts;
        }, 10);
    }

    /**
     * Eager load featured images.
     *
     * @param array     $posts Posts array
     * @param \WP_Query $query Query object
     *
     * @return array
     */
    public function eager_load_featured_images(array $posts, \WP_Query $query): array
    {
        if (empty($posts) || is_admin()) {
            return $posts;
        }

        // Get all thumbnail IDs
        $thumbnail_ids = [];
        foreach ($posts as $post) {
            if (has_post_thumbnail($post->ID)) {
                $thumbnail_ids[] = get_post_thumbnail_id($post->ID);
            }
        }

        if (empty($thumbnail_ids)) {
            return $posts;
        }

        // Prime the cache with a single query
        _prime_post_caches($thumbnail_ids, false, true);

        return $posts;
    }

    /**
     * Remove unnecessary queries.
     *
     * @return void
     */
    public function remove_unnecessary_queries(): void
    {
        // Remove emoji scripts (not needed for modern browsers)
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');

        // Remove WordPress embed script (if not needed)
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');

        // Disable XML-RPC (if not using it)
        add_filter('xmlrpc_enabled', '__return_false');

        // Remove unnecessary REST API links from header
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');

        // Remove shortlink from header
        remove_action('wp_head', 'wp_shortlink_wp_head');

        // Remove generator meta tag
        remove_action('wp_head', 'wp_generator');

        // Remove Windows Live Writer manifest link
        remove_action('wp_head', 'wlwmanifest_link');

        // Remove RSD link
        remove_action('wp_head', 'rsd_link');
    }
}
