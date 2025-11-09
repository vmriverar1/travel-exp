<?php
/**
 * Stripe Webhook Handler
 *
 * Processes Stripe webhook events.
 *
 * @package Travel\Integrations\Payments
 * @since 1.0.0
 */

namespace Travel\Integrations\Payments;

class WebhookHandler
{
    /**
     * Handle incoming webhook request.
     *
     * @param \WP_REST_Request $request Webhook request
     *
     * @return \WP_REST_Response
     */
    public function handle(\WP_REST_Request $request): \WP_REST_Response
    {
        $payload = $request->get_body();
        $signature = $request->get_header('stripe-signature');

        if (!$signature) {
            return new \WP_REST_Response([
                'error' => 'Missing signature header',
            ], 400);
        }

        // Verify signature
        $stripe = new StripeAPI();
        if (!$stripe->verify_webhook_signature($payload, $signature)) {
            error_log('Stripe webhook: Invalid signature');
            return new \WP_REST_Response([
                'error' => 'Invalid signature',
            ], 401);
        }

        // Parse event
        $event = json_decode($payload, true);

        if (!$event || !isset($event['type'])) {
            return new \WP_REST_Response([
                'error' => 'Invalid event data',
            ], 400);
        }

        // Log event
        error_log('Stripe webhook received: ' . $event['type']);

        // Process event based on type
        $result = $this->process_event($event);

        if ($result) {
            return new \WP_REST_Response(['received' => true], 200);
        }

        return new \WP_REST_Response([
            'error' => 'Event processing failed',
        ], 500);
    }

    /**
     * Process webhook event based on type.
     *
     * @param array $event Event data
     *
     * @return bool True on success, false on failure
     */
    private function process_event(array $event): bool
    {
        $type = $event['type'];
        $data = $event['data']['object'] ?? [];

        switch ($type) {
            case 'payment_intent.succeeded':
                return $this->handle_payment_succeeded($data);

            case 'payment_intent.payment_failed':
                return $this->handle_payment_failed($data);

            case 'charge.refunded':
                return $this->handle_refund($data);

            case 'customer.created':
                return $this->handle_customer_created($data);

            default:
                // Log unhandled event types
                error_log('Unhandled Stripe event type: ' . $type);
                return true; // Return true to acknowledge receipt
        }
    }

    /**
     * Handle successful payment.
     *
     * @param array $payment_intent Payment Intent data
     *
     * @return bool
     */
    private function handle_payment_succeeded(array $payment_intent): bool
    {
        $payment_id = $payment_intent['id'] ?? '';
        $amount = ($payment_intent['amount'] ?? 0) / 100; // Convert from cents
        $currency = strtoupper($payment_intent['currency'] ?? 'USD');
        $metadata = $payment_intent['metadata'] ?? [];

        // Log successful payment
        error_log("Payment succeeded: $payment_id - $amount $currency");

        // Store payment record
        $this->store_payment_record([
            'payment_id' => $payment_id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'succeeded',
            'metadata' => $metadata,
            'customer_id' => $payment_intent['customer'] ?? '',
        ]);

        // Send confirmation email
        $this->send_payment_confirmation($payment_intent);

        // Trigger custom action for other plugins to hook into
        do_action('travel_payment_succeeded', $payment_intent);

        return true;
    }

    /**
     * Handle failed payment.
     *
     * @param array $payment_intent Payment Intent data
     *
     * @return bool
     */
    private function handle_payment_failed(array $payment_intent): bool
    {
        $payment_id = $payment_intent['id'] ?? '';
        $error_message = $payment_intent['last_payment_error']['message'] ?? 'Unknown error';

        error_log("Payment failed: $payment_id - $error_message");

        // Store failed payment record
        $this->store_payment_record([
            'payment_id' => $payment_id,
            'amount' => ($payment_intent['amount'] ?? 0) / 100,
            'currency' => strtoupper($payment_intent['currency'] ?? 'USD'),
            'status' => 'failed',
            'error_message' => $error_message,
        ]);

        // Trigger custom action
        do_action('travel_payment_failed', $payment_intent);

        return true;
    }

    /**
     * Handle refund.
     *
     * @param array $charge Charge data
     *
     * @return bool
     */
    private function handle_refund(array $charge): bool
    {
        $charge_id = $charge['id'] ?? '';
        $refunded_amount = ($charge['amount_refunded'] ?? 0) / 100;

        error_log("Refund processed: $charge_id - Refunded: $refunded_amount");

        // Trigger custom action
        do_action('travel_payment_refunded', $charge);

        return true;
    }

    /**
     * Handle customer creation.
     *
     * @param array $customer Customer data
     *
     * @return bool
     */
    private function handle_customer_created(array $customer): bool
    {
        error_log('New Stripe customer created: ' . ($customer['id'] ?? ''));

        // Trigger custom action
        do_action('travel_customer_created', $customer);

        return true;
    }

    /**
     * Store payment record in WordPress.
     *
     * @param array $payment_data Payment data
     *
     * @return void
     */
    private function store_payment_record(array $payment_data): void
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'travel_payments';

        // Create table if it doesn't exist
        $this->create_payments_table();

        $wpdb->insert(
            $table_name,
            [
                'payment_id' => $payment_data['payment_id'],
                'amount' => $payment_data['amount'],
                'currency' => $payment_data['currency'],
                'status' => $payment_data['status'],
                'metadata' => wp_json_encode($payment_data['metadata'] ?? []),
                'customer_id' => $payment_data['customer_id'] ?? '',
                'error_message' => $payment_data['error_message'] ?? '',
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s']
        );
    }

    /**
     * Create payments table.
     *
     * @return void
     */
    private function create_payments_table(): void
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'travel_payments';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            payment_id varchar(100) NOT NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(3) DEFAULT 'USD',
            status varchar(20) NOT NULL,
            metadata longtext,
            customer_id varchar(100),
            error_message text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY payment_id (payment_id),
            KEY status (status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Send payment confirmation email.
     *
     * @param array $payment_intent Payment Intent data
     *
     * @return void
     */
    private function send_payment_confirmation(array $payment_intent): void
    {
        $metadata = $payment_intent['metadata'] ?? [];
        $customer_email = $metadata['customer_email'] ?? get_option('admin_email');

        if (!$customer_email) {
            return;
        }

        $amount = ($payment_intent['amount'] ?? 0) / 100;
        $currency = strtoupper($payment_intent['currency'] ?? 'USD');

        $subject = __('Payment Confirmation', 'travel-integrations');
        $message = sprintf(
            __('Your payment of %s %s has been successfully processed.', 'travel-integrations'),
            $amount,
            $currency
        );

        $message .= "\n\n";
        $message .= __('Payment ID:', 'travel-integrations') . ' ' . $payment_intent['id'];

        wp_mail($customer_email, $subject, $message);
    }
}
