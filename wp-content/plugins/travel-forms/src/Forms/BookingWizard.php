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
        // DEBUG
        error_log('BookingWizard::register() called');

        // Render wizard template in footer
        add_action('wp_footer', [$this, 'render_wizard_template']);

        // Enqueue wizard assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // AJAX endpoint for wizard submission
        add_action('wp_ajax_submit_booking_wizard', [$this, 'handle_submission']);
        add_action('wp_ajax_nopriv_submit_booking_wizard', [$this, 'handle_submission']);
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
}
