<?php



namespace Travel\Blocks\Services;



/**

 * Stripe Payment Service

 *

 * Handles Stripe payment integration for creating checkout sessions

 * and verifying webhook signatures. Prepares data for frontend Stripe Checkout.

 *

 * @package Travel\Blocks\Services

 * @since 1.0.0

 */

class StripePaymentService

{

    /**

     * Stripe API base URL

     *

     * @var string

     */

    private const STRIPE_API_BASE_URL = 'https://api.stripe.com';



    /**

     * Stripe API version

     *

     * @var string

     */

    private const STRIPE_API_VERSION = '2023-10-16';



    /**

     * Default timeout for API requests in seconds

     *

     * @var int

     */

    private const DEFAULT_TIMEOUT = 30;



    /**

     * Fee rate for US customers

     *

     * @var float

     */

    private const FEE_RATE_US = 0.04; // 4%



    /**

     * Fee rate for non-US customers

     *

     * @var float

     */

    private const FEE_RATE_NON_US = 0.05; // 5%



    /**

     * Cached configuration values

     *

     * @var array|null

     */

    private ?array $cached_config = null;



    /**

     * Create a Stripe Checkout Session

     *

     * Prepares and creates a checkout session for embedded or hosted checkout.

     * Returns session details including clientSecret for frontend integration.

     *

     * @param array $data Checkout session data

     * @return array Response with success status and session details

     */

    public function create_checkout_session(array $data): array

    {

        // Validate required fields

        $validation = $this->validate_session_data($data);

        if (!$validation['valid']) {

            return [

                'success' => false,

                'error' => $validation['error'],

            ];

        }



        // Get configuration

        $config = $this->get_config();

        if (empty($config['secret_key'])) {

            return [

                'success' => false,

                'error' => 'Stripe secret key not configured',

            ];

        }



        // Build session configuration

        $session_config = $this->build_session_config($data, $config);



        // Make API request to create session

        $response = $this->create_stripe_session($session_config, $config['secret_key']);



        if (isset($response['error'])) {

            $this->log_error('Failed to create Stripe session: ' . $response['error']);

            return [

                'success' => false,

                'error' => $response['error'],

            ];

        }



        return [

            'success' => true,

            'url' => $response['url'] ?? '',

            'sessionId' => $response['id'] ?? '',

            'clientSecret' => $response['client_secret'] ?? '',

        ];

    }



    /**

     * Verify Stripe webhook signature

     *

     * @param string $payload Raw POST body from webhook

     * @param string $signature Stripe-Signature header value

     * @return array Verification result

     */

    public function verify_webhook_signature(string $payload, string $signature): array

    {

        $config = $this->get_config();

        $webhook_secret = $config['webhook_secret'] ?? '';



        if (empty($webhook_secret)) {

            return [

                'valid' => false,

                'error' => 'Webhook secret not configured',

            ];

        }



        try {

            // Extract timestamp and signatures from header

            $elements = explode(',', $signature);

            $timestamp = null;

            $signatures = [];



            foreach ($elements as $element) {

                list($key, $value) = explode('=', $element, 2);

                if ($key === 't') {

                    $timestamp = $value;

                } elseif ($key === 'v1') {

                    $signatures[] = $value;

                }

            }



            if (empty($timestamp) || empty($signatures)) {

                return [

                    'valid' => false,

                    'error' => 'Invalid signature format',

                ];

            }



            // Construct signed payload

            $signed_payload = "{$timestamp}.{$payload}";

            $expected_signature = hash_hmac('sha256', $signed_payload, $webhook_secret);



            // Compare signatures

            $signature_valid = false;

            foreach ($signatures as $sig) {

                if (hash_equals($expected_signature, $sig)) {

                    $signature_valid = true;

                    break;

                }

            }



            if (!$signature_valid) {

                return [

                    'valid' => false,

                    'error' => 'Signature verification failed',

                ];

            }



            // Check timestamp (reject if older than 5 minutes)

            $current_time = time();

            if (abs($current_time - $timestamp) > 300) {

                return [

                    'valid' => false,

                    'error' => 'Timestamp too old',

                ];

            }



            return [

                'valid' => true,

                'event' => json_decode($payload, true),

            ];



        } catch (\Exception $e) {

            $this->log_error('Webhook verification error: ' . $e->getMessage());

            return [

                'valid' => false,

                'error' => $e->getMessage(),

            ];

        }

    }



    /**

     * Calculate payment fee based on country

     *

     * @param float $amount Base amount

     * @param string $country Country code (e.g., 'US', 'PE')

     * @return array Fee details

     */

    public function calculate_fee(float $amount, string $country = ''): array

    {

        $is_us = (strtoupper($country) === 'US');

        $fee_rate = $is_us ? self::FEE_RATE_US : self::FEE_RATE_NON_US;



        // For non-US, apply as /0.95 (dividing to get final amount that includes fee)

        if ($is_us) {

            $fee_amount = round($amount * $fee_rate, 2);

            $total = $amount + $fee_amount;

        } else {

            $total = round($amount / 0.95, 2);

            $fee_amount = round($total - $amount, 2);

        }



        return [

            'base_amount' => $amount,

            'fee_rate' => $fee_rate * 100, // Convert to percentage

            'fee_amount' => $fee_amount,

            'total_amount' => $total,

            'is_us' => $is_us,

        ];

    }



    /**

     * Get configuration from ACF Global Options

     *

     * @return array Configuration array

     */

    private function get_config(): array

    {

        // Return cached config if available

        if ($this->cached_config !== null) {

            return $this->cached_config;

        }



        $config = [];



        if (function_exists('get_field')) {

            $config['secret_key'] = get_field('stripe_secret_key', 'option') ?: '';

            $config['publishable_key'] = get_field('stripe_publishable_key', 'option') ?: '';

            $config['webhook_secret'] = get_field('stripe_webhook_secret', 'option') ?: '';

            $config['public_domain'] = get_field('public_domain', 'option') ?: home_url();

        }



        $this->cached_config = $config;

        return $config;

    }



    /**

     * Validate checkout session data

     *

     * @param array $data Session data

     * @return array Validation result

     */

    private function validate_session_data(array $data): array

    {

        $required_fields = [

            'customerEmail',

            'totalPrice',

            'packageName',

            'bookingReference',

        ];



        foreach ($required_fields as $field) {

            if (empty($data[$field])) {

                return [

                    'valid' => false,

                    'error' => "Missing required field: {$field}",

                ];

            }

        }



        return ['valid' => true];

    }



    /**

     * Build Stripe session configuration

     *

     * @param array $data Checkout data

     * @param array $config Service configuration

     * @return array Stripe session parameters

     */

    private function build_session_config(array $data, array $config): array

    {

        $public_domain = rtrim($config['public_domain'], '/');

        $booking_ref = $data['bookingReference'];



        return [

            'payment_method_types' => ['card'],

            'mode' => 'payment',

            'ui_mode' => 'embedded', // Use embedded checkout

            'line_items' => [

                [

                    'price_data' => [

                        'currency' => $data['currency'] ?? 'usd',

                        'product_data' => [

                            'name' => $data['packageName'],

                            'description' => $data['packageDetails'] ?? '',

                        ],

                        'unit_amount' => (int) round($data['totalPrice'] * 100), // Convert to cents

                    ],

                    'quantity' => 1,

                ],

            ],

            'customer_email' => $data['customerEmail'],

            'metadata' => [

                'booking_id' => $data['bookingId'] ?? '',

                'booking_reference' => $booking_ref,

                'package_id' => $data['packageId'] ?? '',

                'passenger_count' => $data['passengerCount'] ?? 1,

            ],

            'return_url' => "{$public_domain}/booking/confirmation?session_id={CHECKOUT_SESSION_ID}",

        ];

    }



    /**

     * Create Stripe session via API

     *

     * @param array $session_config Session configuration

     * @param string $secret_key Stripe secret key

     * @return array API response

     */

    private function create_stripe_session(array $session_config, string $secret_key): array

    {

        $url = self::STRIPE_API_BASE_URL . '/v1/checkout/sessions';



        $response = wp_remote_post($url, [

            'timeout' => self::DEFAULT_TIMEOUT,

            'headers' => [

                'Authorization' => 'Bearer ' . $secret_key,

                'Content-Type' => 'application/x-www-form-urlencoded',

                'Stripe-Version' => self::STRIPE_API_VERSION,

            ],

            'body' => $this->build_stripe_request_body($session_config),

        ]);



        if (is_wp_error($response)) {

            return ['error' => $response->get_error_message()];

        }



        $status_code = wp_remote_retrieve_response_code($response);

        $body = wp_remote_retrieve_body($response);

        $data = json_decode($body, true);



        if ($status_code !== 200) {

            $error_message = $data['error']['message'] ?? 'Unknown Stripe API error';

            return ['error' => $error_message];

        }



        return $data;

    }



    /**

     * Build Stripe API request body (URL-encoded format)

     *

     * @param array $data Request data

     * @return string URL-encoded body

     */

    private function build_stripe_request_body(array $data): string

    {

        $params = [];



        // Payment method types

        if (isset($data['payment_method_types'])) {

            foreach ($data['payment_method_types'] as $index => $type) {

                $params["payment_method_types[{$index}]"] = $type;

            }

        }



        // Simple fields

        $simple_fields = ['mode', 'ui_mode', 'customer_email', 'return_url'];

        foreach ($simple_fields as $field) {

            if (isset($data[$field])) {

                $params[$field] = $data[$field];

            }

        }



        // Line items

        if (isset($data['line_items'])) {

            foreach ($data['line_items'] as $item_index => $item) {

                $params["line_items[{$item_index}][quantity]"] = $item['quantity'];



                if (isset($item['price_data'])) {

                    $price_data = $item['price_data'];

                    $params["line_items[{$item_index}][price_data][currency]"] = $price_data['currency'];

                    $params["line_items[{$item_index}][price_data][unit_amount]"] = $price_data['unit_amount'];



                    if (isset($price_data['product_data'])) {

                        $params["line_items[{$item_index}][price_data][product_data][name]"] = $price_data['product_data']['name'];

                        if (!empty($price_data['product_data']['description'])) {

                            $params["line_items[{$item_index}][price_data][product_data][description]"] = $price_data['product_data']['description'];

                        }

                    }

                }

            }

        }



        // Metadata

        if (isset($data['metadata'])) {

            foreach ($data['metadata'] as $key => $value) {

                $params["metadata[{$key}]"] = $value;

            }

        }



        return http_build_query($params);

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

            error_log('StripePaymentService: ' . $message);

        }

    }



    /**

     * Get Stripe publishable key (for frontend use)

     *

     * @return string Publishable key

     */

    public function get_publishable_key(): string

    {

        $config = $this->get_config();

        return $config['publishable_key'] ?? '';

    }



    /**

     * Get public domain URL

     *

     * @return string Public domain URL

     */

    public function get_public_domain(): string

    {

        $config = $this->get_config();

        return $config['public_domain'] ?? home_url();

    }

}

