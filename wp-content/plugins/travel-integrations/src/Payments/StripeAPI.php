<?php
/**
 * Stripe API Integration
 *
 * Handles payment processing with Stripe.
 *
 * @package Travel\Integrations\Payments
 * @since 1.0.0
 */

namespace Travel\Integrations\Payments;

class StripeAPI
{
    private const API_BASE_URL = 'https://api.stripe.com/v1';

    private string $secret_key;
    private bool $test_mode;

    public function __construct()
    {
        $this->test_mode = get_option('travel_stripe_test_mode', true);
        $this->secret_key = get_option('travel_stripe_secret_key', '');
    }

    /**
     * Check if Stripe is configured.
     *
     * @return bool
     */
    public function is_configured(): bool
    {
        return !empty($this->secret_key);
    }

    /**
     * Create a Payment Intent.
     *
     * @param array $data Payment data
     *
     * @return array|false Payment Intent data or false on failure
     */
    public function create_payment_intent(array $data)
    {
        if (!$this->is_configured()) {
            return false;
        }

        $endpoint = '/payment_intents';

        $body = [
            'amount' => absint($data['amount']), // Amount in cents
            'currency' => strtolower($data['currency'] ?? 'usd'),
            'description' => $data['description'] ?? '',
            'metadata' => $data['metadata'] ?? [],
        ];

        // Add customer if provided
        if (!empty($data['customer_email'])) {
            $customer = $this->create_or_get_customer($data['customer_email'], $data['customer_name'] ?? '');
            if ($customer && isset($customer['id'])) {
                $body['customer'] = $customer['id'];
            }
        }

        $response = $this->make_request('POST', $endpoint, $body);

        return $response;
    }

    /**
     * Create or get existing Stripe customer.
     *
     * @param string $email Email address
     * @param string $name  Customer name
     *
     * @return array|false Customer data or false on failure
     */
    public function create_or_get_customer(string $email, string $name = '')
    {
        // Search for existing customer
        $search_response = $this->make_request('GET', '/customers', [
            'email' => $email,
            'limit' => 1,
        ]);

        if ($search_response && !empty($search_response['data'])) {
            return $search_response['data'][0];
        }

        // Create new customer
        $body = [
            'email' => $email,
        ];

        if ($name) {
            $body['name'] = $name;
        }

        return $this->make_request('POST', '/customers', $body);
    }

    /**
     * Retrieve a Payment Intent.
     *
     * @param string $payment_intent_id Payment Intent ID
     *
     * @return array|false Payment Intent data or false on failure
     */
    public function retrieve_payment_intent(string $payment_intent_id)
    {
        if (!$this->is_configured()) {
            return false;
        }

        $endpoint = '/payment_intents/' . $payment_intent_id;

        return $this->make_request('GET', $endpoint);
    }

    /**
     * Make an API request to Stripe.
     *
     * @param string $method   HTTP method
     * @param string $endpoint API endpoint
     * @param array  $body     Request body
     *
     * @return array|false Response data or false on failure
     */
    private function make_request(string $method, string $endpoint, array $body = [])
    {
        $url = self::API_BASE_URL . $endpoint;

        $args = [
            'method' => $method,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secret_key,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'timeout' => 30,
        ];

        if ($method === 'GET' && !empty($body)) {
            $url .= '?' . http_build_query($body);
        } elseif (!empty($body)) {
            $args['body'] = $body;
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            error_log('Stripe API Error: ' . $response->get_error_message());
            return false;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        if ($status_code >= 200 && $status_code < 300) {
            return $data;
        }

        // Log error
        error_log('Stripe API Error: Status ' . $status_code . ' - ' . $response_body);

        return false;
    }

    /**
     * Verify webhook signature.
     *
     * @param string $payload   Webhook payload
     * @param string $signature Stripe signature header
     *
     * @return bool True if valid, false otherwise
     */
    public function verify_webhook_signature(string $payload, string $signature): bool
    {
        $webhook_secret = get_option('travel_stripe_webhook_secret', '');

        if (empty($webhook_secret)) {
            return false;
        }

        $elements = explode(',', $signature);
        $timestamp = '';
        $sig_hash = '';

        foreach ($elements as $element) {
            list($key, $value) = explode('=', $element, 2);
            if ($key === 't') {
                $timestamp = $value;
            } elseif ($key === 'v1') {
                $sig_hash = $value;
            }
        }

        $signed_payload = $timestamp . '.' . $payload;
        $expected_sig = hash_hmac('sha256', $signed_payload, $webhook_secret);

        return hash_equals($expected_sig, $sig_hash);
    }
}
