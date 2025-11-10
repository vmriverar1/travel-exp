<?php

namespace Travel\Blocks\Services;

/**
 * Package Lookup Service
 *
 * Handles searching for packages in the local WordPress database by tour_id.
 * Provides methods to check if a package exists and retrieve package post IDs.
 *
 * @package Travel\Blocks\Services
 * @since 1.0.0
 */
class PackageLookupService
{
    /**
     * Cache for tour_id lookups to avoid repeated queries
     * Format: ['tour_id' => post_id|null]
     *
     * @var array
     */
    private array $lookup_cache = [];

    /**
     * Post type for packages
     *
     * @var string
     */
    private const POST_TYPE = 'package';

    /**
     * Meta key for tour ID
     *
     * @var string
     */
    private const META_KEY = 'tour_id';

    /**
     * Find package post ID by tour_id
     *
     * Searches for a package with the given tour_id in the database.
     * Results are cached to improve performance during batch operations.
     *
     * @param int $tour_id The tour ID from the external API
     * @return int|null Post ID if found, null otherwise
     */
    public function find_by_tour_id(int $tour_id): ?int
    {
        // Check cache first
        if (array_key_exists($tour_id, $this->lookup_cache)) {
            return $this->lookup_cache[$tour_id];
        }

        // Query WordPress
        $args = [
            'post_type' => self::POST_TYPE,
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => 1,
            'fields' => 'ids',
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'meta_query' => [
                [
                    'key' => self::META_KEY,
                    'value' => $tour_id,
                    'compare' => '=',
                    'type' => 'NUMERIC'
                ]
            ]
        ];

        $query = new \WP_Query($args);
        $post_id = !empty($query->posts) ? (int) $query->posts[0] : null;

        // Cache the result
        $this->lookup_cache[$tour_id] = $post_id;

        $this->log_debug(
            $post_id
                ? "Found package post_id={$post_id} for tour_id={$tour_id}"
                : "No package found for tour_id={$tour_id}"
        );

        return $post_id;
    }

    /**
     * Check if a package exists with the given tour_id
     *
     * @param int $tour_id The tour ID from the external API
     * @return bool True if package exists, false otherwise
     */
    public function exists(int $tour_id): bool
    {
        return $this->find_by_tour_id($tour_id) !== null;
    }

    /**
     * Get package post object by tour_id
     *
     * @param int $tour_id The tour ID from the external API
     * @return \WP_Post|null Post object if found, null otherwise
     */
    public function get_post_by_tour_id(int $tour_id): ?\WP_Post
    {
        $post_id = $this->find_by_tour_id($tour_id);

        if ($post_id === null) {
            return null;
        }

        $post = get_post($post_id);
        return $post instanceof \WP_Post ? $post : null;
    }

    /**
     * Batch check if multiple tour_ids exist
     *
     * More efficient than calling exists() multiple times as it queries
     * all packages in a single database query.
     *
     * @param array $tour_ids Array of tour IDs to check
     * @return array Associative array ['tour_id' => post_id|null]
     */
    public function batch_find(array $tour_ids): array
    {
        if (empty($tour_ids)) {
            return [];
        }

        // Filter out non-numeric and already cached IDs
        $to_query = [];
        $results = [];

        foreach ($tour_ids as $tour_id) {
            if (!is_numeric($tour_id) || $tour_id <= 0) {
                continue;
            }

            $tour_id = (int) $tour_id;

            if (array_key_exists($tour_id, $this->lookup_cache)) {
                $results[$tour_id] = $this->lookup_cache[$tour_id];
            } else {
                $to_query[] = $tour_id;
            }
        }

        // If all IDs were cached, return early
        if (empty($to_query)) {
            return $results;
        }

        // Query WordPress for uncached IDs
        $args = [
            'post_type' => self::POST_TYPE,
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
            'update_post_meta_cache' => true, // Need meta for mapping
            'update_post_term_cache' => false,
            'meta_query' => [
                [
                    'key' => self::META_KEY,
                    'value' => $to_query,
                    'compare' => 'IN',
                    'type' => 'NUMERIC'
                ]
            ]
        ];

        $query = new \WP_Query($args);

        // Map tour_ids to post_ids
        if (!empty($query->posts)) {
            foreach ($query->posts as $post_id) {
                $tour_id = get_post_meta($post_id, self::META_KEY, true);
                if ($tour_id) {
                    $tour_id = (int) $tour_id;
                    $results[$tour_id] = (int) $post_id;
                    $this->lookup_cache[$tour_id] = (int) $post_id;
                }
            }
        }

        // Mark non-found IDs as null in cache
        foreach ($to_query as $tour_id) {
            if (!isset($results[$tour_id])) {
                $results[$tour_id] = null;
                $this->lookup_cache[$tour_id] = null;
            }
        }

        $found_count = count(array_filter($results));
        $total_count = count($to_query);
        $this->log_debug("Batch lookup: found {$found_count}/{$total_count} packages");

        return $results;
    }

    /**
     * Check which tour_ids exist and which don't
     *
     * Useful for determining which packages need to be created vs updated.
     *
     * @param array $tour_ids Array of tour IDs to check
     * @return array ['existing' => [tour_id => post_id], 'missing' => [tour_id]]
     */
    public function categorize_tour_ids(array $tour_ids): array
    {
        $lookup_results = $this->batch_find($tour_ids);

        $existing = [];
        $missing = [];

        foreach ($lookup_results as $tour_id => $post_id) {
            if ($post_id !== null) {
                $existing[$tour_id] = $post_id;
            } else {
                $missing[] = $tour_id;
            }
        }

        return [
            'existing' => $existing,
            'missing' => $missing
        ];
    }

    /**
     * Get all packages with tour_id meta
     *
     * Useful for finding packages that have been imported from the API.
     *
     * @param int $limit Maximum number of results (default -1 for all)
     * @return array Array of ['post_id' => tour_id]
     */
    public function get_all_with_tour_id(int $limit = -1): array
    {
        $args = [
            'post_type' => self::POST_TYPE,
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => $limit,
            'fields' => 'ids',
            'no_found_rows' => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => false,
            'meta_query' => [
                [
                    'key' => self::META_KEY,
                    'compare' => 'EXISTS'
                ]
            ]
        ];

        $query = new \WP_Query($args);
        $results = [];

        if (!empty($query->posts)) {
            foreach ($query->posts as $post_id) {
                $tour_id = get_post_meta($post_id, self::META_KEY, true);
                if ($tour_id) {
                    $results[(int) $post_id] = (int) $tour_id;
                }
            }
        }

        return $results;
    }

    /**
     * Clear the internal cache
     *
     * Useful when packages have been created/updated during the same request.
     *
     * @return void
     */
    public function clear_cache(): void
    {
        $this->lookup_cache = [];
        $this->log_debug('Lookup cache cleared');
    }

    /**
     * Get cache statistics for debugging
     *
     * @return array ['size' => int, 'entries' => array]
     */
    public function get_cache_stats(): array
    {
        return [
            'size' => count($this->lookup_cache),
            'entries' => $this->lookup_cache
        ];
    }

    /**
     * Log debug message if WP_DEBUG is enabled
     *
     * @param string $message Debug message
     * @return void
     */
    private function log_debug(string $message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('PackageLookupService: ' . $message);
        }
    }
}
