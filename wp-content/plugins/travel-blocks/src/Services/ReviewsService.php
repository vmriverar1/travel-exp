<?php

namespace Travel\Blocks\Services;

/**
 * Reviews Service
 *
 * Handles connections to the external Valencia Travel CMS API for social media reviews.
 * Provides methods for fetching reviews from different suppliers (Google, TripAdvisor, etc).
 *
 * @package Travel\Blocks\Services
 * @since 1.0.0
 */
class ReviewsService
{
    /**
     * Default base URL for the API (fallback)
     *
     * @var string
     */
    private const DEFAULT_API_BASE_URL = 'https://cms.valenciatravelcusco.com';

    /**
     * Default timeout for API requests in seconds
     *
     * @var int
     */
    private const DEFAULT_TIMEOUT = 15;

    /**
     * Supported review suppliers
     *
     * @var array
     */
    private const SUPPORTED_SUPPLIERS = [
        'google',
        'trip-advisor',
        'facebook',
        'yelp',
    ];

    /**
     * Cached API base URL
     *
     * @var string|null
     */
    private ?string $cached_base_url = null;

    /**
     * Fetch reviews from social media for a specific supplier
     *
     * This method attempts to fetch data using wp_remote_get first, and falls back
     * to file_get_contents if that fails. Both methods have SSL verification disabled
     * to handle potential certificate issues.
     *
     * @param string $supplier Supplier name (google, trip-advisor, etc)
     * @param array $params Optional query parameters (limit, offset, rating, etc)
     * @return array API response data or empty array on error
     */
    public function fetch_reviews(string $supplier, array $params = []): array
    {
        // Validate supplier
        if (!$this->is_valid_supplier($supplier)) {
            $this->log_error("Invalid supplier: {$supplier}");
            return [];
        }

        $url = $this->build_reviews_url($supplier, $params);

        // Try wp_remote_get first (WordPress HTTP API)
        $data = $this->fetch_with_wp_remote_get($url);

        // Fallback to file_get_contents if wp_remote_get fails
        if (empty($data)) {
            $data = $this->fetch_with_file_get_contents($url);
        }

        return $data;
    }

    /**
     * Fetch reviews from Google
     *
     * Convenience method for fetching Google reviews specifically
     *
     * @param array $params Optional query parameters (limit, offset, rating, etc)
     * @return array API response data or empty array on error
     */
    public function fetch_google_reviews(array $params = []): array
    {
        return $this->fetch_reviews('google', $params);
    }

    /**
     * Fetch reviews from TripAdvisor
     *
     * Convenience method for fetching TripAdvisor reviews specifically
     *
     * @param array $params Optional query parameters (limit, offset, rating, etc)
     * @return array API response data or empty array on error
     */
    public function fetch_tripadvisor_reviews(array $params = []): array
    {
        return $this->fetch_reviews('trip-advisor', $params);
    }

    /**
     * Fetch reviews from multiple suppliers
     *
     * Useful for displaying aggregated reviews from different sources
     *
     * @param array $suppliers Array of supplier names
     * @param array $params Optional query parameters applied to all requests
     * @return array Array of reviews indexed by supplier name
     */
    public function fetch_multi_supplier_reviews(array $suppliers, array $params = []): array
    {
        $all_reviews = [];

        foreach ($suppliers as $supplier) {
            if ($this->is_valid_supplier($supplier)) {
                $reviews = $this->fetch_reviews($supplier, $params);
                if (!empty($reviews)) {
                    $all_reviews[$supplier] = $reviews;
                }
            } else {
                $this->log_error("Skipping invalid supplier: {$supplier}");
            }
        }

        return $all_reviews;
    }

    /**
     * Fetch aggregated statistics for a supplier
     *
     * Gets total count, average rating, and rating distribution
     *
     * @param string $supplier Supplier name
     * @return array Statistics data or empty array on error
     */
    public function fetch_reviews_stats(string $supplier): array
    {
        if (!$this->is_valid_supplier($supplier)) {
            $this->log_error("Invalid supplier: {$supplier}");
            return [];
        }

        $url = $this->build_stats_url($supplier);

        // Try wp_remote_get first
        $data = $this->fetch_with_wp_remote_get($url);

        // Fallback to file_get_contents if wp_remote_get fails
        if (empty($data)) {
            $data = $this->fetch_with_file_get_contents($url);
        }

        return $data;
    }

    /**
     * Check if supplier is valid
     *
     * @param string $supplier Supplier name to validate
     * @return bool True if valid, false otherwise
     */
    private function is_valid_supplier(string $supplier): bool
    {
        return in_array(strtolower($supplier), self::SUPPORTED_SUPPLIERS, true);
    }

    /**
     * Get list of supported suppliers
     *
     * @return array Array of supported supplier names
     */
    public function get_supported_suppliers(): array
    {
        return self::SUPPORTED_SUPPLIERS;
    }

    /**
     * Get API base URL from ACF Global Options
     *
     * @return string API base URL
     */
    private function get_api_base_url(): string
    {
        // Return cached value if available
        if ($this->cached_base_url !== null) {
            return $this->cached_base_url;
        }

        // Try to get from ACF Global Options
        $api_url = function_exists('get_field') ? get_field('package_api_base_url', 'option') : '';

        // Validate and sanitize URL
        if (!empty($api_url) && filter_var($api_url, FILTER_VALIDATE_URL)) {
            // Remove trailing slash
            $api_url = rtrim($api_url, '/');
            $this->cached_base_url = $api_url;
            return $api_url;
        }

        // Fallback to default
        $this->cached_base_url = self::DEFAULT_API_BASE_URL;
        return self::DEFAULT_API_BASE_URL;
    }

    /**
     * Build the reviews API URL
     *
     * @param string $supplier Supplier name
     * @param array $params Query parameters
     * @return string Complete API URL
     */
    private function build_reviews_url(string $supplier, array $params = []): string
    {
        $url = sprintf(
            '%s/reviews/social-media?supplier=%s',
            $this->get_api_base_url(),
            urlencode($supplier)
        );

        // Add additional query parameters
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url .= '&' . urlencode($key) . '=' . urlencode($value);
            }
        }

        return $url;
    }

    /**
     * Build the reviews statistics API URL
     *
     * @param string $supplier Supplier name
     * @return string Complete API URL
     */
    private function build_stats_url(string $supplier): string
    {
        return sprintf(
            '%s/reviews/social-media/stats?supplier=%s',
            $this->get_api_base_url(),
            urlencode($supplier)
        );
    }

    /**
     * Fetch data using WordPress HTTP API (wp_remote_get)
     *
     * @param string $url API endpoint URL
     * @return array Response data or empty array on error
     */
    private function fetch_with_wp_remote_get(string $url): array
    {
        $response = wp_remote_get($url, [
            'timeout' => self::DEFAULT_TIMEOUT,
            'sslverify' => false,
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
            ],
        ]);

        if (is_wp_error($response)) {
            $this->log_error('wp_remote_get failed: ' . $response->get_error_message());
            return [];
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            $this->log_error("HTTP {$status_code} response from API for URL: {$url}");
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        return $this->decode_json($body);
    }

    /**
     * Fetch data using file_get_contents (fallback method)
     *
     * @param string $url API endpoint URL
     * @return array Response data or empty array on error
     */
    private function fetch_with_file_get_contents(string $url): array
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => self::DEFAULT_TIMEOUT,
                'header' => "Accept: application/json\r\n",
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);

        $body = @file_get_contents($url, false, $context);

        if ($body === false) {
            $this->log_error('file_get_contents failed for ' . $url);
            return [];
        }

        return $this->decode_json($body);
    }

    /**
     * Decode JSON response and validate
     *
     * @param string $json JSON string
     * @return array Decoded data or empty array on error
     */
    private function decode_json(string $json): array
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log_error('Invalid JSON response: ' . json_last_error_msg());
            return [];
        }

        return is_array($data) ? $data : [];
    }

    /**
     * Log error message if WP_DEBUG is enabled
     *
     * @param string $message Error message
     * @return void
     */
    private function log_error(string $message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ReviewsService: ' . $message);
        }
    }

    /**
     * Get API base URL
     *
     * Public method to access the configured API base URL
     *
     * @return string API base URL from ACF Global Options or default
     */
    public function get_base_url(): string
    {
        return $this->get_api_base_url();
    }
}
