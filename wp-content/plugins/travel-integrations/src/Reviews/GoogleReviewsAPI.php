<?php
/**
 * Google Reviews API Integration
 *
 * Fetches and syncs reviews from Google My Business.
 *
 * @package Travel\Integrations\Reviews
 * @since 1.0.0
 */

namespace Travel\Integrations\Reviews;

class GoogleReviewsAPI
{
    private string $api_key;
    private string $place_id;

    public function __construct()
    {
        $this->api_key = get_option('travel_google_api_key', '');
        $this->place_id = get_option('travel_google_place_id', '');
    }

    /**
     * Check if API is configured.
     *
     * @return bool
     */
    public function is_configured(): bool
    {
        return !empty($this->api_key) && !empty($this->place_id);
    }

    /**
     * Sync reviews from Google.
     *
     * @return array Sync results
     */
    public function sync_reviews(): array
    {
        if (!$this->is_configured()) {
            return ['success' => false, 'message' => 'Google Reviews not configured'];
        }

        $reviews = $this->fetch_reviews();

        if (!$reviews) {
            return ['success' => false, 'message' => 'Failed to fetch reviews'];
        }

        $synced = 0;
        foreach ($reviews as $review) {
            $result = ReviewsSyncer::create_or_update_review($review, 'google');
            if ($result) {
                $synced++;
            }
        }

        return [
            'success' => true,
            'synced' => $synced,
            'total' => count($reviews),
        ];
    }

    /**
     * Fetch reviews from Google Places API.
     *
     * @return array|false Reviews data or false on failure
     */
    private function fetch_reviews()
    {
        $url = 'https://maps.googleapis.com/maps/api/place/details/json';

        $response = wp_remote_get($url, [
            'timeout' => 30,
            'body' => [
                'place_id' => $this->place_id,
                'key' => $this->api_key,
                'fields' => 'reviews',
            ],
        ]);

        if (is_wp_error($response)) {
            error_log('Google API Error: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!isset($data['result']['reviews'])) {
            return false;
        }

        // Transform to our format
        $reviews = [];
        foreach ($data['result']['reviews'] as $review) {
            $reviews[] = [
                'external_id' => 'google_' . $review['time'], // Google doesn't provide unique ID
                'content' => $review['text'] ?? '',
                'rating' => $review['rating'] ?? 0,
                'client_name' => $review['author_name'] ?? 'Anonymous',
                'client_country' => '',
                'review_date' => date('Y-m-d', $review['time']),
                'review_url' => $review['author_url'] ?? '',
                'featured' => false,
            ];
        }

        return $reviews;
    }
}
