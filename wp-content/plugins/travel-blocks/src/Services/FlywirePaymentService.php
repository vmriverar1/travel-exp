<?php

namespace Travel\Blocks\Services;

/**
 * Flywire Payment Service
 *
 * Handles booking creation and invoice updates for Flywire payment integration.
 * Communicates with the external Valencia Travel CMS API for payment processing.
 *
 * @package Travel\Blocks\Services
 * @since 1.0.0
 */
class FlywirePaymentService
{
    /**
     * Default timeout for API requests in seconds
     *
     * @var int
     */
    private const DEFAULT_TIMEOUT = 30;

    /**
     * Fee rate for Flywire payments
     *
     * @var float
     */
    private const FEE_RATE = 0.04; // 4%

    /**
     * Payment type constants
     *
     * @var int
     */
    private const PAYMENT_TOTAL = 1;
    private const PAYMENT_200_DOLARES = 4;

    /**
     * Cached API base URL
     *
     * @var string|null
     */
    private ?string $cached_base_url = null;

    /**
     * Create a booking
     *
     * Sends booking data to the external API to create a new reservation.
     *
     * @param string $uuid Booking UUID
     * @param array $data Booking data
     * @return array API response
     */
    public function create_booking(string $uuid, array $data): array
    {
        // Log booking attempt
        $this->log_info('Creating booking with UUID: ' . $uuid);
        $this->log_debug('Booking data: ' . json_encode($data, JSON_PRETTY_PRINT));

        // Validate required fields
        $validation = $this->validate_booking_data($data);
        if (!$validation['valid']) {
            $this->log_error('Validation failed: ' . $validation['error']);
            return [
                'success' => false,
                'error' => $validation['error'],
                'details' => 'Validation error',
            ];
        }

        // Calculate fee if not provided
        if (!isset($data['fee_amount'])) {
            $fee_data = $this->calculate_fee($data['total'] ?? 0);
            $data['fee_rate'] = $fee_data['fee_rate'];
            $data['fee_amount'] = $fee_data['fee_amount'];
            $this->log_debug('Fee calculated: ' . $fee_data['fee_amount']);
        }

        // Set payment method
        $data['payment_method'] = $data['payment_method'] ?? 'flywire';

        // Build endpoint URL
        $url = $this->build_booking_url($uuid);
        $this->log_info('Booking API URL: ' . $url);

        // Make API request
        $response = $this->make_post_request($url, $data);

        if (isset($response['error'])) {
            $error_msg = $response['error'];
            $this->log_error('Failed to create booking: ' . $error_msg);
            $this->log_error('URL: ' . $url);
            $this->log_error('HTTP Status: ' . ($response['http_status'] ?? 'unknown'));
            $this->log_error('Response body: ' . ($response['response_body'] ?? 'empty'));

            return [
                'success' => false,
                'error' => $error_msg,
                'details' => [
                    'url' => $url,
                    'http_status' => $response['http_status'] ?? null,
                    'response_body' => $response['response_body'] ?? null,
                ],
            ];
        }

        $this->log_info('Booking created successfully');
        $this->log_debug('Response: ' . json_encode($response, JSON_PRETTY_PRINT));

        return [
            'success' => true,
            'data' => $response,
        ];
    }

    /**
     * Update invoice after payment
     *
     * Updates invoice with payment details and billing information.
     *
     * @param array $data Invoice update data
     * @return array API response
     */
    public function update_invoice(array $data): array
    {
        // Build endpoint URL
        $url = $this->build_update_invoice_url();

        // Wrap data in expected format
        $payload = ['data' => $data];

        // Make API request
        $response = $this->make_post_request($url, $payload);

        if (isset($response['error'])) {
            $this->log_error('Failed to update invoice: ' . $response['error']);
            return [
                'success' => false,
                'error' => $response['error'],
            ];
        }

        return [
            'success' => true,
            'data' => $response,
        ];
    }

    /**
     * Calculate payment fee
     *
     * Calculates 4% processing fee for Flywire payments.
     *
     * @param float $total Base amount
     * @return array Fee details
     */
    public function calculate_fee(float $total): array
    {
        $fee_amount = round($total * self::FEE_RATE, 2);

        return [
            'base_amount' => $total,
            'fee_rate' => self::FEE_RATE * 100, // Convert to percentage
            'fee_amount' => $fee_amount,
            'total_amount' => $total + $fee_amount,
        ];
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
        $default_url = 'https://cms.valenciatravelcusco.com';
        $this->cached_base_url = $default_url;
        return $default_url;
    }

    /**
     * Build booking creation URL
     *
     * @param string $uuid Booking UUID
     * @return string Complete API URL
     */
    private function build_booking_url(string $uuid): string
    {
        return sprintf(
            '%s/booking/%s/',
            $this->get_api_base_url(),
            $uuid
        );
    }

    /**
     * Build invoice update URL
     *
     * @return string Complete API URL
     */
    private function build_update_invoice_url(): string
    {
        return sprintf(
            '%s/flywire-notifications',
            $this->get_api_base_url()
        );
    }

    /**
     * Make POST request to API
     *
     * @param string $url API endpoint URL
     * @param array $data Request data
     * @return array Response data or error
     */
    private function make_post_request(string $url, array $data): array
    {
        $json_body = json_encode($data);
        $this->log_debug('POST to: ' . $url);
        $this->log_debug('Request body: ' . $json_body);

        $response = wp_remote_post($url, [
            'timeout' => self::DEFAULT_TIMEOUT,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
            ],
            'body' => $json_body,
        ]);

        if (is_wp_error($response)) {
            $error_msg = $response->get_error_message();
            $this->log_error('WP_Error: ' . $error_msg);
            return [
                'error' => $error_msg,
                'http_status' => null,
                'response_body' => null,
            ];
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        $this->log_debug('Response status: ' . $status_code);
        $this->log_debug('Response body: ' . substr($body, 0, 500)); // First 500 chars

        // Try to decode JSON response
        $decoded = json_decode($body, true);

        // Check for successful status codes
        if ($status_code >= 200 && $status_code < 300) {
            return $decoded ?: ['success' => true];
        }

        // Handle error responses
        $error_message = 'API request failed';
        if (is_array($decoded) && isset($decoded['error'])) {
            $error_message = $decoded['error'];
        } elseif (is_array($decoded) && isset($decoded['message'])) {
            $error_message = $decoded['message'];
        } elseif (!empty($body)) {
            // If no structured error, use raw body
            $error_message = 'API Error (HTTP ' . $status_code . '): ' . substr($body, 0, 200);
        }

        $this->log_error("HTTP {$status_code} from {$url}: {$error_message}");

        return [
            'error' => $error_message,
            'http_status' => $status_code,
            'response_body' => $body,
        ];
    }

    /**
     * Validate booking data
     *
     * @param array $data Booking data
     * @return array Validation result
     */
    private function validate_booking_data(array $data): array
    {
        $required_fields = [
            'tour_name',
            'travel_date',
            'payment_type',
            'total',
            'passengers_count',
        ];

        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return [
                    'valid' => false,
                    'error' => "Missing required field: {$field}",
                ];
            }
        }

        // Validate payment_type is valid
        if (!in_array($data['payment_type'], [self::PAYMENT_TOTAL, self::PAYMENT_200_DOLARES])) {
            return [
                'valid' => false,
                'error' => 'Invalid payment_type value',
            ];
        }

        return ['valid' => true];
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
            error_log('[FlywirePaymentService ERROR] ' . $message);
        }
    }

    /**
     * Log info message if WP_DEBUG is enabled
     *
     * @param string $message Info message
     * @return void
     */
    private function log_info(string $message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[FlywirePaymentService INFO] ' . $message);
        }
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
            error_log('[FlywirePaymentService DEBUG] ' . $message);
        }
    }

    /**
     * Get invoice details
     *
     * Fetches invoice information by payment ID or UUID.
     *
     * @param string $identifier Payment ID or UUID
     * @return array Invoice data or error
     */
    public function get_invoice(string $identifier): array
    {
        $url = sprintf(
            '%s/invoice/%s/',
            $this->get_api_base_url(),
            $identifier
        );

        $response = wp_remote_get($url, [
            'timeout' => self::DEFAULT_TIMEOUT,
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
            ],
        ]);

        if (is_wp_error($response)) {
            return ['error' => $response->get_error_message()];
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($status_code !== 200) {
            $error = $data['error'] ?? $data['message'] ?? 'Failed to fetch invoice';
            return ['error' => $error];
        }

        return $data;
    }

    /**
     * Get invoice PDF URL
     *
     * Returns the URL to download invoice PDF.
     *
     * @param string $identifier Payment ID or UUID
     * @return string PDF URL
     */
    public function get_invoice_pdf_url(string $identifier): string
    {
        return sprintf(
            '%s/invoice/pdf/%s/',
            $this->get_api_base_url(),
            $identifier
        );
    }

    /**
     * Get Flywire configuration
     *
     * Returns configuration for frontend Flywire integration.
     *
     * @return array Configuration array
     */
    public function get_flywire_config(): array
    {
        $config = [];

        if (function_exists('get_field')) {
            $config['env'] = get_field('flywire_env', 'option') ?: 'demo';
            $config['portal_code'] = get_field('flywire_portal_code', 'option') ?: 'VTR';
            $config['api_url'] = $this->get_api_base_url();
        }

        return $config;
    }

    /**
     * Get API base URL (public method)
     *
     * @return string API base URL
     */
    public function get_base_url(): string
    {
        return $this->get_api_base_url();
    }
}
