<?php
/**
 * Reviews Syncer
 *
 * Synchronizes reviews from external platforms (TripAdvisor, Google, Facebook).
 *
 * @package Travel\Integrations\Reviews
 * @since 1.0.0
 */

namespace Travel\Integrations\Reviews;

class ReviewsSyncer
{
    /**
     * Sync all reviews from enabled platforms.
     *
     * @return array Results of sync operations
     */
    public function sync_all_reviews(): array
    {
        $results = [];

        // Sync TripAdvisor reviews
        if (get_option('travel_tripadvisor_enabled', false)) {
            $tripadvisor = new TripAdvisorAPI();
            $results['tripadvisor'] = $tripadvisor->sync_reviews();
        }

        // Sync Google reviews
        if (get_option('travel_google_enabled', false)) {
            $google = new GoogleReviewsAPI();
            $results['google'] = $google->sync_reviews();
        }

        // Sync Facebook reviews
        if (get_option('travel_facebook_enabled', false)) {
            $facebook = new FacebookReviewsAPI();
            $results['facebook'] = $facebook->sync_reviews();
        }

        // Log results
        error_log('Reviews sync completed: ' . wp_json_encode($results));

        return $results;
    }

    /**
     * Create or update a review post in WordPress.
     *
     * @param array  $review_data Review data
     * @param string $platform    Platform identifier
     *
     * @return int|false Post ID on success, false on failure
     */
    public static function create_or_update_review(array $review_data, string $platform)
    {
        // Check if review already exists
        $existing_review = self::find_existing_review($review_data['external_id'], $platform);

        $post_data = [
            'post_type' => 'review',
            'post_status' => 'publish',
            'post_content' => sanitize_textarea_field($review_data['content'] ?? ''),
            'meta_input' => [
                'platform' => $platform,
                'external_id' => $review_data['external_id'],
                'rating' => absint($review_data['rating'] ?? 0),
                'client_name' => sanitize_text_field($review_data['client_name'] ?? ''),
                'client_country' => sanitize_text_field($review_data['client_country'] ?? ''),
                'review_date' => $review_data['review_date'] ?? current_time('Y-m-d'),
                'review_url' => esc_url_raw($review_data['review_url'] ?? ''),
                'featured' => $review_data['featured'] ?? false,
            ],
        ];

        if ($existing_review) {
            $post_data['ID'] = $existing_review;
            return wp_update_post($post_data);
        } else {
            return wp_insert_post($post_data);
        }
    }

    /**
     * Find existing review by external ID and platform.
     *
     * @param string $external_id External review ID
     * @param string $platform    Platform identifier
     *
     * @return int|false Post ID if found, false otherwise
     */
    private static function find_existing_review(string $external_id, string $platform)
    {
        $query = new \WP_Query([
            'post_type' => 'review',
            'posts_per_page' => 1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'external_id',
                    'value' => $external_id,
                ],
                [
                    'key' => 'platform',
                    'value' => $platform,
                ],
            ],
            'fields' => 'ids',
        ]);

        return $query->posts[0] ?? false;
    }
}
