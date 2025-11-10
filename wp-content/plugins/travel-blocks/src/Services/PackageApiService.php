<?php

namespace Travel\Blocks\Services;

/**
 * Package API Service
 *
 * Handles connections to the external Valencia Travel CMS API for package/tour data.
 * Provides methods for fetching complete package information by ID.
 *
 * @package Travel\Blocks\Services
 * @since 1.0.0
 */
class PackageApiService
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
     * Cached API base URL
     *
     * @var string|null
     */
    private ?string $cached_base_url = null;

    /**
     * Fetch package/tour data from the external API by ID
     *
     * This method attempts to fetch data using wp_remote_get first, and falls back
     * to file_get_contents if that fails. Both methods have SSL verification disabled
     * to handle potential certificate issues.
     *
     * @param int $package_id Package/Tour ID from external CMS
     * @return array API response data or empty array on error
     */
    public function fetch_package(int $package_id): array
    {
        $url = $this->build_package_url($package_id);

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
     * Build the package API URL
     *
     * @param int $package_id Package/Tour ID
     * @return string Complete API URL
     */
    private function build_package_url(int $package_id): string
    {
        return sprintf(
            '%s/packages/tours/%d',
            $this->get_api_base_url(),
            $package_id
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
            error_log('PackageApiService: ' . $message);
        }
    }

    /**
     * Fetch multiple packages by their IDs
     *
     * Useful for batch operations or related packages.
     *
     * @param array $package_ids Array of package IDs
     * @return array Array of package data indexed by package ID
     */
    public function fetch_packages(array $package_ids): array
    {
        $packages = [];

        foreach ($package_ids as $package_id) {
            if (is_numeric($package_id) && $package_id > 0) {
                $package_data = $this->fetch_package((int) $package_id);
                if (!empty($package_data)) {
                    $packages[$package_id] = $package_data;
                }
            }
        }

        return $packages;
    }

    /**
     * Check if a package exists in the external API
     *
     * @param int $package_id Package ID
     * @return bool True if package exists, false otherwise
     */
    public function package_exists(int $package_id): bool
    {
        $data = $this->fetch_package($package_id);
        return !empty($data) && isset($data['id']);
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
