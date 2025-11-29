<?php
namespace Aurora\ACFKit\Helpers;

/**
 * PackageHelper
 *
 * Utility class for Package CPT optimization and eager loading.
 * Prevents N+1 queries and improves performance when displaying multiple packages.
 */
class PackageHelper
{
    /**
     * Eager load all ACF fields for an array of package posts
     *
     * @param array $posts Array of WP_Post objects
     * @return array Same posts with ACF fields preloaded
     */
    public static function eager_load_fields(array $posts): array
    {
        if (empty($posts)) {
            return $posts;
        }

        $post_ids = wp_list_pluck($posts, 'ID');

        // Preload all ACF fields for these posts
        if (function_exists('acf_get_meta')) {
            foreach ($post_ids as $post_id) {
                acf_get_meta($post_id);
            }
        }

        // Preload featured images
        self::preload_thumbnails($post_ids);

        // Preload taxonomies
        self::preload_taxonomies($post_ids);

        return $posts;
    }

    /**
     * Preload featured images for multiple posts
     *
     * @param array $post_ids Array of post IDs
     */
    public static function preload_thumbnails(array $post_ids): void
    {
        $thumbnail_ids = [];

        foreach ($post_ids as $post_id) {
            $thumbnail_id = get_post_thumbnail_id($post_id);
            if ($thumbnail_id) {
                $thumbnail_ids[] = $thumbnail_id;
            }
        }

        if (!empty($thumbnail_ids)) {
            // Preload attachment posts
            get_posts([
                'post_type' => 'attachment',
                'post__in' => $thumbnail_ids,
                'posts_per_page' => -1,
            ]);
        }
    }

    /**
     * Preload all package taxonomies
     *
     * @param array $post_ids Array of post IDs
     */
    public static function preload_taxonomies(array $post_ids): void
    {
        $taxonomies = [
            'package_type',
            'interest',
            'locations',
            'optional_renting',
            'included_services',
            'additional_info',
            'tag_locations',
        ];

        foreach ($taxonomies as $taxonomy) {
            update_object_term_cache($post_ids, ['package']);
        }
    }

    /**
     * Get package price (handles offer/normal/from logic)
     *
     * @param int $post_id Package post ID
     * @return array ['current' => float, 'original' => float|null, 'has_offer' => bool]
     */
    public static function get_package_price(int $post_id): array
    {
        $price_offer = get_field('price_offer', $post_id);
        $price_normal = get_field('price_normal', $post_id);
        $price_from = get_field('price_from', $post_id);

        $has_offer = !empty($price_offer) && $price_offer > 0;

        return [
            'current' => $has_offer ? floatval($price_offer) : floatval($price_normal),
            'original' => $has_offer ? floatval($price_normal) : null,
            'from' => floatval($price_from),
            'has_offer' => $has_offer,
        ];
    }

    /**
     * Get available departure dates (sorted by date)
     *
     * @param int $post_id Package post ID
     * @param string $status Filter by status (available, few_spots, sold_out, guaranteed)
     * @return array Array of departure dates
     */
    public static function get_departures(int $post_id, string $status = ''): array
    {
        $departures = get_field('fixed_departures', $post_id) ?: [];

        if (!empty($status)) {
            $departures = array_filter($departures, function($dep) use ($status) {
                return isset($dep['status']) && $dep['status'] === $status;
            });
        }

        // Sort by date
        usort($departures, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $departures;
    }

    /**
     * Check if package is available in a specific month
     *
     * @param int $post_id Package post ID
     * @param string $month Month name (lowercase)
     * @return bool
     */
    public static function is_available_in_month(int $post_id, string $month): bool
    {
        $available_months = get_field('available_months', $post_id) ?: [];
        return in_array(strtolower($month), $available_months);
    }

    /**
     * Get next available departure date
     *
     * @param int $post_id Package post ID
     * @return string|null Date string or null if no departures
     */
    public static function get_next_departure(int $post_id): ?string
    {
        $departures = self::get_departures($post_id);
        $today = date('Y-m-d');

        foreach ($departures as $departure) {
            if ($departure['date'] >= $today && $departure['status'] !== 'sold_out') {
                return $departure['date'];
            }
        }

        return null;
    }

    /**
     * Get package rating as stars HTML
     *
     * @param int $post_id Package post ID
     * @return string HTML for star rating
     */
    public static function get_rating_stars(int $post_id): string
    {
        $rating = floatval(get_field('rating', $post_id));

        if ($rating <= 0) {
            return '';
        }

        $full_stars = floor($rating);
        $half_star = ($rating - $full_stars) >= 0.5;
        $empty_stars = 5 - ceil($rating);

        $html = '<div class="package-rating" data-rating="' . esc_attr($rating) . '">';

        // Full stars
        for ($i = 0; $i < $full_stars; $i++) {
            $html .= '<span class="star star-full">⭐</span>';
        }

        // Half star
        if ($half_star) {
            $html .= '<span class="star star-half">⭐</span>';
        }

        // Empty stars
        for ($i = 0; $i < $empty_stars; $i++) {
            $html .= '<span class="star star-empty">☆</span>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Get itinerary days (only active ones)
     *
     * @param int $post_id Package post ID
     * @return array Filtered and sorted itinerary days
     */
    public static function get_active_itinerary(int $post_id): array
    {
        $itinerary = get_field('itinerary', $post_id) ?: [];

        // Filter only active days
        $active = array_filter($itinerary, function($day) {
            return isset($day['active']) && $day['active'] == true;
        });

        // Sort by order
        usort($active, function($a, $b) {
            return intval($a['order']) - intval($b['order']);
        });

        return $active;
    }

    /**
     * Register helper functions
     * This method should be called on plugin init
     */
    public static function register(): void
    {
        // Hook into pre_get_posts to optimize package queries
        add_action('pre_get_posts', [__CLASS__, 'optimize_package_query']);
    }

    /**
     * Optimize package queries
     *
     * @param \WP_Query $query
     */
    public static function optimize_package_query(\WP_Query $query): void
    {
        if (!is_admin() && $query->is_main_query() && $query->get('post_type') === 'package') {
            // Set reasonable posts per page
            if (!$query->get('posts_per_page')) {
                $query->set('posts_per_page', 12);
            }

            // Optimize query
            $query->set('no_found_rows', false); // We need pagination
            $query->set('update_post_meta_cache', true);
            $query->set('update_post_term_cache', true);
        }
    }
}
