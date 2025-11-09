<?php
/**
 * TripAdvisor API Integration
 *
 * Fetches and syncs reviews from TripAdvisor.
 *
 * @package Travel\Integrations\Reviews
 * @since 1.0.0
 */

namespace Travel\Integrations\Reviews;

class TripAdvisorAPI
{
    private string $api_key;
    private string $location_id;

    public function __construct()
    {
        $this->api_key = get_option('travel_tripadvisor_api_key', '');
        $this->location_id = get_option('travel_tripadvisor_location_id', '');
    }

    /**
     * Check if API is configured.
     *
     * @return bool
     */
    public function is_configured(): bool
    {
        return !empty($this->api_key) && !empty($this->location_id);
    }

    /**
     * Sync reviews from TripAdvisor.
     *
     * @return array Sync results
     */
    public function sync_reviews(): array
    {
        if (!$this->is_configured()) {
            return ['success' => false, 'message' => 'TripAdvisor not configured'];
        }

        $reviews = $this->fetch_reviews();

        if (!$reviews) {
            return ['success' => false, 'message' => 'Failed to fetch reviews'];
        }

        $synced = 0;
        foreach ($reviews as $review) {
            $result = ReviewsSyncer::create_or_update_review($review, 'tripadvisor');
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
     * Fetch reviews from TripAdvisor API.
     *
     * @return array|false Reviews data or false on failure
     */
    private function fetch_reviews()
    {
        // Note: TripAdvisor Content API endpoint
        $url = 'https://api.tripadvisor.com/api/partner/2.0/location/' . $this->location_id . '/reviews';

        $response = wp_remote_get($url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'timeout' => 30,
            'body' => [
                'key' => $this->api_key,
                'language' => 'en',
            ],
        ]);

        if (is_wp_error($response)) {
            error_log('TripAdvisor API Error: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!isset($data['data'])) {
            return false;
        }

        // Transform to our format
        $reviews = [];
        foreach ($data['data'] as $review) {
            $reviews[] = [
                'external_id' => $review['id'] ?? '',
                'content' => $review['text'] ?? '',
                'rating' => $review['rating'] ?? 0,
                'client_name' => $review['user']['username'] ?? 'Anonymous',
                'client_country' => $review['user']['user_location']['name'] ?? '',
                'review_date' => $review['published_date'] ?? '',
                'review_url' => $review['url'] ?? '',
                'featured' => false,
            ];
        }

        return $reviews;
    }
}
