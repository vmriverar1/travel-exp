<?php
/**
 * Cache Warmer
 *
 * Pre-warms cache for critical pages and data.
 *
 * @package Travel\Performance\Performance
 * @since 1.0.0
 */

namespace Travel\Performance\Performance;

class CacheWarmer
{
    /**
     * Cache TTLs by data type (in seconds).
     *
     * @var array
     */
    private $ttls = [
        'tours_list'       => 3600,    // 1 hour
        'tour_single'      => 7200,    // 2 hours
        'destinations'     => 10800,   // 3 hours
        'deals'            => 1800,    // 30 minutes (change frequently)
        'reviews'          => 21600,   // 6 hours
        'home_featured'    => 3600,    // 1 hour
        'taxonomies'       => 86400,   // 24 hours
        'acf_fields'       => 43200,   // 12 hours
        'availability'     => 7200,    // 2 hours
    ];

    /**
     * Initialize cache warmer.
     */
    public function __construct()
    {
        // Schedule cache warming
        add_action('travel_warm_cache', [$this, 'warm_critical_cache']);

        // Schedule daily if not already scheduled
        if (!wp_next_scheduled('travel_warm_cache')) {
            wp_schedule_event(time(), 'daily', 'travel_warm_cache');
        }

        // Warm cache on plugin activation
        register_activation_hook(__FILE__, [$this, 'warm_critical_cache']);
    }

    /**
     * Warm critical cache.
     *
     * @return void
     */
    public function warm_critical_cache(): void
    {
        // Warm tours cache
        $this->warm_tours_cache();

        // Warm destinations cache
        $this->warm_destinations_cache();

        // Warm active deals cache
        $this->warm_deals_cache();

        // Warm featured reviews cache
        $this->warm_reviews_cache();

        // Warm taxonomies cache
        $this->warm_taxonomies_cache();

        // Log completion
        error_log('[Travel Performance] Cache warming completed at ' . current_time('mysql'));
    }

    /**
     * Warm tours cache.
     *
     * @return void
     */
    private function warm_tours_cache(): void
    {
        $cache_key = 'tours_list_all';

        // Check if already cached
        if (wp_cache_get($cache_key, 'travel')) {
            return;
        }

        // Query tours
        $tours = new \WP_Query([
            'post_type'      => 'tour',
            'posts_per_page' => 100,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => false,
        ]);

        $tours_data = [];
        foreach ($tours->posts as $post) {
            $tours_data[] = [
                'id'       => $post->ID,
                'title'    => $post->post_title,
                'slug'     => $post->post_name,
                'price'    => get_field('price', $post->ID),
                'duration' => get_field('tour_duration_days', $post->ID),
            ];

            // Also cache individual tour
            $single_cache_key = 'tour_single_' . $post->ID;
            wp_cache_set($single_cache_key, $post, 'travel', $this->ttls['tour_single']);
        }

        // Cache tours list
        wp_cache_set($cache_key, $tours_data, 'travel', $this->ttls['tours_list']);
    }

    /**
     * Warm destinations cache.
     *
     * @return void
     */
    private function warm_destinations_cache(): void
    {
        $cache_key = 'destinations_list_all';

        if (wp_cache_get($cache_key, 'travel')) {
            return;
        }

        $destinations = new \WP_Query([
            'post_type'      => 'destination',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);

        $destinations_data = [];
        foreach ($destinations->posts as $post) {
            $destinations_data[] = [
                'id'    => $post->ID,
                'title' => $post->post_title,
                'slug'  => $post->post_name,
            ];
        }

        wp_cache_set($cache_key, $destinations_data, 'travel', $this->ttls['destinations']);
    }

    /**
     * Warm deals cache.
     *
     * @return void
     */
    private function warm_deals_cache(): void
    {
        $cache_key = 'deals_active';

        if (wp_cache_get($cache_key, 'travel')) {
            return;
        }

        $deals = new \WP_Query([
            'post_type'      => 'deal',
            'posts_per_page' => 20,
            'post_status'    => 'publish',
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => 'end_date',
                    'value'   => current_time('Y-m-d'),
                    'compare' => '>=',
                    'type'    => 'DATE',
                ],
            ],
            'orderby'        => 'meta_value',
            'meta_key'       => 'end_date',
            'order'          => 'ASC',
        ]);

        $deals_data = [];
        foreach ($deals->posts as $post) {
            $deals_data[] = [
                'id'              => $post->ID,
                'title'           => $post->post_title,
                'discount'        => get_field('discount_percentage', $post->ID),
                'original_price'  => get_field('original_price', $post->ID),
                'discounted_price'=> get_field('discounted_price', $post->ID),
                'end_date'        => get_field('end_date', $post->ID),
            ];
        }

        wp_cache_set($cache_key, $deals_data, 'travel', $this->ttls['deals']);
    }

    /**
     * Warm reviews cache.
     *
     * @return void
     */
    private function warm_reviews_cache(): void
    {
        $cache_key = 'reviews_featured';

        if (wp_cache_get($cache_key, 'travel')) {
            return;
        }

        $reviews = new \WP_Query([
            'post_type'      => 'review',
            'posts_per_page' => 20,
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'   => 'featured',
                    'value' => '1',
                ],
            ],
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        $reviews_data = [];
        foreach ($reviews->posts as $post) {
            $reviews_data[] = [
                'id'            => $post->ID,
                'content'       => $post->post_content,
                'rating'        => get_field('rating', $post->ID),
                'client_name'   => get_field('client_name', $post->ID),
                'client_country'=> get_field('client_country', $post->ID),
                'platform'      => get_field('platform', $post->ID),
            ];
        }

        wp_cache_set($cache_key, $reviews_data, 'travel', $this->ttls['reviews']);
    }

    /**
     * Warm taxonomies cache.
     *
     * @return void
     */
    private function warm_taxonomies_cache(): void
    {
        $taxonomies = [
            'tour_category',
            'difficulty',
            'duration',
            'region',
            'tour_type',
        ];

        foreach ($taxonomies as $taxonomy) {
            $cache_key = 'taxonomy_' . $taxonomy;

            if (wp_cache_get($cache_key, 'travel')) {
                continue;
            }

            $terms = get_terms([
                'taxonomy'   => $taxonomy,
                'hide_empty' => true,
            ]);

            if (!is_wp_error($terms)) {
                wp_cache_set($cache_key, $terms, 'travel', $this->ttls['taxonomies']);
            }
        }
    }

    /**
     * Invalidate cache for specific post type.
     *
     * @param string $post_type Post type.
     * @return void
     */
    public function invalidate_cache_for_post_type(string $post_type): void
    {
        switch ($post_type) {
            case 'tour':
                wp_cache_delete('tours_list_all', 'travel');
                wp_cache_delete('home_featured', 'travel');
                break;

            case 'destination':
                wp_cache_delete('destinations_list_all', 'travel');
                break;

            case 'deal':
                wp_cache_delete('deals_active', 'travel');
                break;

            case 'review':
                wp_cache_delete('reviews_featured', 'travel');
                break;
        }
    }

    /**
     * Invalidate cache for specific tour.
     *
     * @param int $tour_id Tour post ID.
     * @return void
     */
    public function invalidate_tour_cache(int $tour_id): void
    {
        wp_cache_delete('tour_single_' . $tour_id, 'travel');
        wp_cache_delete('tours_list_all', 'travel');
        wp_cache_delete('availability_' . $tour_id, 'travel');
    }

    /**
     * Get TTL for cache type.
     *
     * @param string $type Cache type.
     * @return int TTL in seconds.
     */
    public function get_ttl(string $type): int
    {
        return $this->ttls[$type] ?? 3600; // Default 1 hour
    }
}
