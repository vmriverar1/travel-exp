<?php

namespace Travel\Blocks\Services;

/**
 * Calendar API Service
 *
 * Handles connections to the external Valencia Travel CMS calendar API.
 * Provides methods for fetching tour availability and pricing data.
 *
 * @package Travel\Blocks\Services
 * @since 1.0.0
 */
class CalendarApiService
{
    /**
     * Default base URL for the calendar API (fallback)
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
     * Cached API base URL
     *
     * @var string|null
     */
    private ?string $cached_base_url = null;

    /**
     * Fetch calendar data from the external API for a specific tour, year, and month
     *
     * This method attempts to fetch data using wp_remote_get first, and falls back
     * to file_get_contents if that fails. Both methods have SSL verification disabled
     * to handle potential certificate issues.
     *
     * @param int $tour_id Tour ID from external CMS
     * @param int $year Year (YYYY format)
     * @param int $month Month (1-12)
     * @return array API response data or empty array on error
     */
    public function fetch_calendar(int $tour_id, int $year, int $month): array
    {
        $url = $this->build_calendar_url($tour_id, $year, $month);

        // Try wp_remote_get first (WordPress HTTP API)
        $data = $this->fetch_with_wp_remote_get($url);

        // Fallback to file_get_contents if wp_remote_get fails
        if (empty($data)) {
            $data = $this->fetch_with_file_get_contents($url);
        }

        return $data;
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
     * Build the calendar API URL
     *
     * @param int $tour_id Tour ID
     * @param int $year Year (YYYY)
     * @param int $month Month (1-12)
     * @return string Complete API URL
     */
    private function build_calendar_url(int $tour_id, int $year, int $month): string
    {
        return sprintf(
            '%s/packages/tours/%d/calendar?year=%d&month=%d',
            $this->get_api_base_url(),
            $tour_id,
            $year,
            $month
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
            $this->log_error("HTTP {$status_code} response from API");
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
            error_log('CalendarApiService: ' . $message);
        }
    }

    /**
     * Fetch calendar data for multiple months
     *
     * Useful for fetching a range of dates in one call.
     *
     * @param int $tour_id Tour ID
     * @param int $start_year Starting year
     * @param int $start_month Starting month (1-12)
     * @param int $months_count Number of months to fetch
     * @return array Array of calendar data indexed by 'YYYY-MM'
     */
    public function fetch_calendar_range(int $tour_id, int $start_year, int $start_month, int $months_count): array
    {
        $calendar_data = [];
        $current_year = $start_year;
        $current_month = $start_month;

        for ($i = 0; $i < $months_count; $i++) {
            $key = sprintf('%04d-%02d', $current_year, $current_month);
            $calendar_data[$key] = $this->fetch_calendar($tour_id, $current_year, $current_month);

            // Increment month
            $current_month++;
            if ($current_month > 12) {
                $current_month = 1;
                $current_year++;
            }
        }

        return $calendar_data;
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
