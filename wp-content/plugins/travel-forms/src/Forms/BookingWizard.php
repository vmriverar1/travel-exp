<?php
namespace Travel\Forms\Forms;

use Travel\Blocks\Services\FlywirePaymentService;
use Travel\Blocks\Services\StripePaymentService;

/**
 * Booking Wizard
 *
 * 4-step wizard for package booking that opens as a side panel (aside).
 */
class BookingWizard
{
    /**
     * Flywire payment service instance
     *
     * @var FlywirePaymentService
     */
    private $flywire_service;

    /**
     * Stripe payment service instance
     *
     * @var StripePaymentService
     */
    private $stripe_service;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->flywire_service = new FlywirePaymentService();
        $this->stripe_service = new StripePaymentService();
    }
    /**
     * Register wizard (add to footer, enqueue assets)
     */
    public function register(): void
    {
        // DEBUG
        error_log('BookingWizard::register() called');

        // Render wizard template in footer
        add_action('wp_footer', [$this, 'render_wizard_template']);

        // Enqueue wizard assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // AJAX endpoint for wizard submission
        add_action('wp_ajax_submit_booking_wizard', [$this, 'handle_submission']);
        add_action('wp_ajax_nopriv_submit_booking_wizard', [$this, 'handle_submission']);
        add_action('wp_ajax_get_package_data', [$this, 'get_package_data']);
        add_action('wp_ajax_nopriv_get_package_data', [$this, 'get_package_data']);
        add_action('wp_ajax_create_stripe_checkout', [$this, 'create_stripe_checkout']);
        add_action('wp_ajax_nopriv_create_stripe_checkout', [$this, 'create_stripe_checkout']);
        add_action('wp_ajax_get_flywire_config', [$this, 'get_flywire_config']);
        add_action('wp_ajax_nopriv_get_flywire_config', [$this, 'get_flywire_config']);
    }

    /**
     * Enqueue wizard CSS and JavaScript
     */
    public function enqueue_assets(): void
    {
        // CSS
        wp_enqueue_style(
            'booking-wizard',
            TRAVEL_FORMS_URL . 'assets/css/booking-wizard.css',
            [],
            TRAVEL_FORMS_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'booking-wizard',
            TRAVEL_FORMS_URL . 'assets/js/booking-wizard.js',
            ['jquery'],
            TRAVEL_FORMS_VERSION,
            true
        );

        // Enqueue Flywire script
        wp_enqueue_script(
            'flywire-payment',
            'https://checkout.flywire.com/flywire-payment.js',
            [],
            null,
            true
        );

        // Enqueue Stripe script
        wp_enqueue_script(
            'stripe-js',
            'https://js.stripe.com/v3/',
            [],
            null,
            true
        );

        // Get payment configurations
        $flywire_config = $this->flywire_service->get_flywire_config();
        $stripe_publishable_key = $this->stripe_service->get_publishable_key();
        $public_domain = $this->stripe_service->get_public_domain();

        // Localize script with config and translations
        wp_localize_script('booking-wizard', 'bookingWizardConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('booking_wizard_nonce'),
            'flywire' => [
                'env' => $flywire_config['env'] ?? 'demo',
                'portalCode' => $flywire_config['portal_code'] ?? 'VTR',
                'apiUrl' => $flywire_config['api_url'] ?? '',
            ],
            'stripe' => [
                'publishableKey' => $stripe_publishable_key,
                'publicDomain' => $public_domain,
            ],
            'messages' => [
                'required' => __('This field is required', 'travel-forms'),
                'email' => __('Please enter a valid email address', 'travel-forms'),
                'phone' => __('Please enter a valid phone number', 'travel-forms'),
                'success' => __('Booking submitted successfully!', 'travel-forms'),
                'error' => __('An error occurred. Please try again.', 'travel-forms'),
            ],
        ]);
    }

    /**
     * Render wizard template in footer
     */
    public function render_wizard_template(): void
    {
        // DEBUG
        error_log('BookingWizard::render_wizard_template() called');

        // Only render on single package pages or if explicitly needed
        // For now, render on all pages (wizard is hidden by default)

        // DEBUG: Verify template is being called
        if (!defined('TRAVEL_FORMS_PATH')) {
            error_log('BookingWizard: TRAVEL_FORMS_PATH not defined');
            return;
        }

        $template_path = TRAVEL_FORMS_PATH . 'templates/booking-wizard.php';

        if (!file_exists($template_path)) {
            error_log('BookingWizard: Template not found at ' . $template_path);
            return;
        }

        error_log('BookingWizard: Including template from ' . $template_path);
        include $template_path;
    }

    /**
     * Handle AJAX submission from step 4
     */
    public function handle_submission(): void
    {
        // Verify nonce
        check_ajax_referer('booking_wizard_nonce', 'nonce');

        // Get POST data
        $wizard_data = $_POST['wizardData'] ?? [];
        $payment_method = $wizard_data['step4']['paymentMethod'] ?? '';

        // Validate data
        $validation = $this->validate_wizard_data($wizard_data);
        if (!$validation['valid']) {
            wp_send_json_error([
                'message' => $validation['error'],
            ]);
            return;
        }

        // Generate UUID for booking
        $booking_uuid = wp_generate_uuid4();

        // Prepare booking data
        $booking_data = $this->prepare_booking_data($wizard_data, $booking_uuid);

        // Log booking data being sent
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[BookingWizard] Creating booking with UUID: ' . $booking_uuid);
            error_log('[BookingWizard] Booking data: ' . json_encode($booking_data, JSON_PRETTY_PRINT));
        }

        // Create booking via API
        $result = $this->flywire_service->create_booking($booking_uuid, $booking_data);

        if (!$result['success']) {
            $error_response = [
                'message' => $result['error'] ?? __('Failed to create booking', 'travel-forms'),
            ];

            // Add detailed error information if available
            if (!empty($result['details'])) {
                $error_response['details'] = $result['details'];
            }

            // Log the full error
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[BookingWizard ERROR] ' . json_encode($error_response, JSON_PRETTY_PRINT));
            }

            wp_send_json_error($error_response);
            return;
        }

        // Log success
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[BookingWizard] Booking created successfully: ' . json_encode($result['data'], JSON_PRETTY_PRINT));
        }

        // Return success with booking info
        wp_send_json_success([
            'message' => __('Booking created successfully!', 'travel-forms'),
            'bookingId' => $result['data']['id'] ?? null,
            'bookingUuid' => $booking_uuid,
            'bookingReference' => $booking_uuid,
        ]);
    }

    /**
     * Get package data via AJAX
     */
    public function get_package_data(): void
    {
        // Verify nonce
        check_ajax_referer('booking_wizard_nonce', 'nonce');

        $package_id = intval($_POST['packageId'] ?? 0);

        if (!$package_id) {
            wp_send_json_error(['message' => 'Invalid package ID']);
            return;
        }

        // Get package data
        $package = get_post($package_id);

        if (!$package || $package->post_type !== 'package') {
            wp_send_json_error(['message' => 'Package not found']);
            return;
        }

        // Get ACF fields
        $data = [
            'id' => $package_id,
            'title' => $package->post_title,
            'permalink' => get_permalink($package_id),
            'thumbnail' => get_the_post_thumbnail_url($package_id, 'medium') ?: get_the_post_thumbnail_url($package_id, 'thumbnail'),
            'price_from' => floatval(get_field('price_from', $package_id) ?: 0),
            'price_normal' => floatval(get_field('price_normal', $package_id) ?: 0),
            'duration' => intval(get_field('days', $package_id) ?: 1),
            'max_people' => intval(get_field('max_people', $package_id) ?: 12),
        ];

        wp_send_json_success($data);
    }

    /**
     * Create Stripe checkout session
     */
    public function create_stripe_checkout(): void
    {
        // Verify nonce
        check_ajax_referer('booking_wizard_nonce', 'nonce');

        // Get POST data
        $data = $_POST['checkoutData'] ?? [];
        $booking_data = $_POST['bookingData'] ?? [];

        // Validate required fields
        if (empty($data['customerEmail']) || empty($data['totalPrice'])) {
            wp_send_json_error([
                'message' => __('Missing required checkout data', 'travel-forms'),
            ]);
            return;
        }

        // Create checkout session
        $result = $this->stripe_service->create_checkout_session($data);

        if (!$result['success']) {
            wp_send_json_error([
                'message' => $result['error'] ?? __('Failed to create checkout session', 'travel-forms'),
            ]);
            return;
        }

        wp_send_json_success($result);
    }

    /**
     * Get Flywire configuration
     */
    public function get_flywire_config(): void
    {
        // Verify nonce
        check_ajax_referer('booking_wizard_nonce', 'nonce');

        $config = $this->flywire_service->get_flywire_config();

        wp_send_json_success($config);
    }

    /**
     * Validate wizard data
     *
     * @param array $data Wizard data
     * @return array Validation result
     */
    private function validate_wizard_data(array $data): array
    {
        // Check step 1 data
        if (empty($data['packageId'])) {
            return ['valid' => false, 'error' => __('Package ID is required', 'travel-forms')];
        }

        if (empty($data['departureDate'])) {
            return ['valid' => false, 'error' => __('Departure date is required', 'travel-forms')];
        }

        // Check step 3 data (billing information)
        // Note: step3 contains billing info, not passengers array
        if (empty($data['step3'])) {
            return ['valid' => false, 'error' => __('Billing information is required', 'travel-forms')];
        }

        // Validate essential billing fields
        if (empty($data['step3']['email']) || empty($data['step3']['firstName']) || empty($data['step3']['lastName'])) {
            return ['valid' => false, 'error' => __('Name and email are required', 'travel-forms')];
        }

        // Check step 4 data (payment method selection)
        if (empty($data['step4']['paymentMethod'])) {
            return ['valid' => false, 'error' => __('Payment method is required', 'travel-forms')];
        }

        return ['valid' => true];
    }

    /**
     * Prepare booking data for API
     *
     * @param array $wizard_data Wizard form data
     * @param string $uuid Booking UUID
     * @return array Formatted booking data
     */
    private function prepare_booking_data(array $wizard_data, string $uuid): array
    {
        $step1 = $wizard_data['step1'] ?? [];
        $step2 = $wizard_data['step2'] ?? [];
        $step3 = $wizard_data['step3'] ?? []; // Billing information
        $step4 = $wizard_data['step4'] ?? [];

        // Get package data
        $package_id = intval($wizard_data['packageId'] ?? 0);
        $tour_name = '';
        $tour_id = null;

        if ($package_id) {
            $package = get_post($package_id);
            $tour_name = $package ? $package->post_title : '';

            // Get the tour_id from ACF (this is the ID in the external API)
            $tour_id = get_field('tour_id', $package_id);
        }

        // Calculate totals
        $totals = $this->calculate_totals($wizard_data);

        // Payment type: 1 = full payment, 4 = $200 deposit
        $payment_option = $step4['paymentOption'] ?? 'full';
        $payment_type = ($payment_option === 'deposit') ? 4 : 1;

        // Room type
        $room_type = $step1['roomType'] ?? 'twin';
        $travellers = intval($step1['travellers'] ?? 1);

        // Calculate solo upgrade
        $solo_upgrade = 0;
        $solo_travelers = 0;
        $solo_upgrade_unit_price = 0;

        if ($room_type === 'solo' && $travellers === 1) {
            $solo_upgrade = 1;
            $solo_travelers = 1;
            $solo_upgrade_unit_price = floatval($wizard_data['singleSupp'] ?? 0);
        } elseif ($travellers > 1 && $travellers % 2 !== 0) {
            // Odd number of travellers, last one gets solo
            $solo_upgrade = 1;
            $solo_travelers = 1;
            $solo_upgrade_unit_price = floatval($wizard_data['singleSupp'] ?? 0);
        }

        // Create passengers array from billing data (step3)
        // Note: Send empty array [] as per API docs - passengers are optional
        // The API uses billing data from 'details' for contact info
        $passengers = [];

        // Format billing data for details
        $billing_data = [
            'first_name' => $step3['firstName'] ?? '',
            'last_name' => $step3['lastName'] ?? '',
            'email' => $step3['email'] ?? '',
            'phone' => $step3['phone'] ?? '',
            'address' => $step3['address'] ?? '',
            'city' => $step3['city'] ?? '',
            'state' => $step3['state'] ?? '',
            'zip' => $step3['zip'] ?? '',
            'country' => $step3['country'] ?? '',
        ];

        // Prepare package/landing/external fields
        // Use tour_id if available (external API ID), otherwise undefined
        $package_field = $tour_id ? intval($tour_id) : null;

        // Build the data array
        $booking_data = [
            'payment_method' => $step4['paymentMethod'] ?? 'flywire',
            'tour_name' => $tour_name,
            'flight' => '',  // No flight field in current wizard
            'travel_date' => $wizard_data['departureDate'] ?? '',
            'payment_type' => $payment_type,
            'total' => $totals['subtotal'],
            'amount_debt' => $totals['amount_to_pay'],
            'fee_rate' => 4,
            'fee_amount' => $totals['fee'],
            'promo' => !empty($wizard_data['hasPromo']) ? 1 : 0,
            'solo_upgrade' => $solo_upgrade,
            'solo_upgrade_unit_price' => $solo_upgrade_unit_price,
            'solo_travelers' => $solo_travelers,
            'product_unit_price' => floatval($wizard_data['selectedPrice'] ?? 0),
            'extra_services' => $this->format_extra_items($step2['extras'] ?? []),
            'optional_activities' => $this->format_extra_items($step2['addons'] ?? []),
            'passengers' => $passengers,
            'passengers_count' => $travellers,
            'details' => [
                'billingData' => $billing_data,
            ],
        ];

        // Add package/landing/external only if tour_id exists
        if ($package_field) {
            $booking_data['package'] = $package_field;
        }

        return $booking_data;
    }

    /**
     * Calculate totals for booking
     *
     * @param array $wizard_data Wizard data
     * @return array Totals breakdown
     */
    private function calculate_totals(array $wizard_data): array
    {
        $step1 = $wizard_data['step1'] ?? [];
        $step2 = $wizard_data['step2'] ?? [];
        $step4 = $wizard_data['step4'] ?? [];

        $travellers = intval($step1['travellers'] ?? 1);
        $room_type = $step1['roomType'] ?? 'twin';
        $selected_price = floatval($wizard_data['selectedPrice'] ?? 0);
        $single_supp = floatval($wizard_data['singleSupp'] ?? 0);

        // Calculate base package cost
        $package_cost = $selected_price * $travellers;

        // Add solo supplement if applicable
        if ($room_type === 'solo' && $travellers === 1) {
            $package_cost += $single_supp;
        } elseif ($travellers > 1 && $travellers % 2 !== 0) {
            // Odd number, last person gets solo supplement
            $package_cost += $single_supp;
        }

        // Calculate extras
        $extras_total = 0;
        if (!empty($step2['extras'])) {
            foreach ($step2['extras'] as $extra) {
                $extras_total += floatval($extra['price'] ?? 0) * intval($extra['quantity'] ?? 0);
            }
        }

        // Calculate addons
        $addons_total = 0;
        if (!empty($step2['addons'])) {
            foreach ($step2['addons'] as $addon) {
                $addons_total += floatval($addon['price'] ?? 0) * intval($addon['quantity'] ?? 0);
            }
        }

        $subtotal = $package_cost + $extras_total + $addons_total;

        // Determine amount to pay
        $payment_option = $step4['paymentOption'] ?? 'full';
        $amount_to_pay = ($payment_option === 'deposit') ? 200.00 : $subtotal;

        // Calculate fee (4%)
        $fee = round($amount_to_pay * 0.04, 2);
        $total = $amount_to_pay + $fee;

        return [
            'package_cost' => $package_cost,
            'extras_total' => $extras_total,
            'addons_total' => $addons_total,
            'subtotal' => $subtotal,
            'amount_to_pay' => $amount_to_pay,
            'fee' => $fee,
            'total' => $total,
        ];
    }

    /**
     * Format extra items for API
     *
     * @param array $items Items array
     * @return array Formatted items
     */
    private function format_extra_items(array $items): array
    {
        $formatted = [];

        foreach ($items as $item) {
            if (!empty($item['id']) && !empty($item['quantity'])) {
                $formatted[] = [
                    'id' => $item['id'],
                    'title' => $item['title'] ?? '',
                    'price' => floatval($item['price'] ?? 0),
                    'quantity' => intval($item['quantity']),
                ];
            }
        }

        return $formatted;
    }
}
