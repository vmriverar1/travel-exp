<?php
/**
 * Facebook Reviews API Integration
 *
 * Fetches and syncs reviews/ratings from Facebook Page.
 *
 * @package Travel\Integrations\Reviews
 * @since 1.0.0
 */

namespace Travel\Integrations\Reviews;

class FacebookReviewsAPI
{
    private string $access_token;
    private string $page_id;

    public function __construct()
    {
        $this->access_token = get_option('travel_facebook_access_token', '');
        $this->page_id = get_option('travel_facebook_page_id', '');
    }

    /**
     * Check if API is configured.
     *
     * @return bool
     */
    public function is_configured(): bool
    {
        return !empty($this->access_token) && !empty($this->page_id);
    }

    /**
     * Sync reviews from Facebook.
     *
     * @return array Sync results
     */
    public function sync_reviews(): array
    {
        if (!$this->is_configured()) {
            return ['success' => false, 'message' => 'Facebook not configured'];
        }

        $reviews = $this->fetch_reviews();

        if (!$reviews) {
            return ['success' => false, 'message' => 'Failed to fetch reviews'];
        }

        $synced = 0;
        foreach ($reviews as $review) {
            $result = ReviewsSyncer::create_or_update_review($review, 'facebook');
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
     * Fetch reviews from Facebook Graph API.
     *
     * @return array|false Reviews data or false on failure
     */
    private function fetch_reviews()
    {
        // Facebook Graph API endpoint for ratings/reviews
        $url = 'https://graph.facebook.com/v18.0/' . $this->page_id . '/ratings';

        $response = wp_remote_get($url, [
            'timeout' => 30,
            'body' => [
                'access_token' => $this->access_token,
                'fields' => 'created_time,rating,review_text,reviewer,recommendation_type',
                'limit' => 50,
            ],
        ]);

        if (is_wp_error($response)) {
            error_log('Facebook API Error: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!isset($data['data'])) {
            error_log('Facebook API Error: ' . ($data['error']['message'] ?? 'Unknown error'));
            return false;
        }

        // Transform to our format
        $reviews = [];
        foreach ($data['data'] as $review) {
            $reviews[] = [
                'external_id' => $review['id'] ?? '',
                'content' => $review['review_text'] ?? $review['recommendation_type'] ?? '',
                'rating' => $review['rating'] ?? 0,
                'client_name' => $review['reviewer']['name'] ?? 'Anonymous',
                'client_country' => '',
                'review_date' => isset($review['created_time']) ? date('Y-m-d', strtotime($review['created_time'])) : '',
                'review_url' => 'https://www.facebook.com/' . $this->page_id . '/reviews',
                'featured' => false,
            ];
        }

        return $reviews;
    }
}
