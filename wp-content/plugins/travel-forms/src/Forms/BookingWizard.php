<?php
namespace Travel\Forms\Forms;

/**
 * Booking Wizard
 *
 * 4-step wizard for package booking that opens as a side panel (aside).
 */
class BookingWizard
{
    /**
     * Register wizard (add to footer, enqueue assets)
     */
    public function register(): void
    {
        // Render wizard template in footer
        add_action('wp_footer', [$this, 'render_wizard_template']);

        // Enqueue wizard assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // AJAX endpoints
        add_action('wp_ajax_submit_booking_wizard', [$this, 'handle_submission']);
        add_action('wp_ajax_nopriv_submit_booking_wizard', [$this, 'handle_submission']);
        add_action('wp_ajax_get_package_data', [$this, 'get_package_data']);
        add_action('wp_ajax_nopriv_get_package_data', [$this, 'get_package_data']);
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

        // Localize script with config and translations
        wp_localize_script('booking-wizard', 'bookingWizardConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('booking_wizard_nonce'),
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
        if (!defined('TRAVEL_FORMS_PATH')) {
            return;
        }

        $template_path = TRAVEL_FORMS_PATH . 'templates/booking-wizard.php';

        if (!file_exists($template_path)) {
            return;
        }

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
        $data = $_POST['wizardData'] ?? [];

        // Validate data
        // TODO: Implement validation logic

        // Process booking
        // TODO: Implement booking logic (save to database, send emails, etc.)

        // Send response
        wp_send_json_success([
            'message' => __('Booking submitted successfully!', 'travel-forms'),
            'bookingId' => uniqid('BK-'),
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
}
