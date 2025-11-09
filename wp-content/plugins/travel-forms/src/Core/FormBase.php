<?php
/**
 * Abstract Base Class for Forms
 *
 * Provides reusable structure for all forms.
 *
 * @package Travel\Forms\Core
 * @since 1.0.0
 */

namespace Travel\Forms\Core;

use Travel\Forms\Integrations\HubSpotAPI;

abstract class FormBase
{
    /**
     * Form identifier.
     */
    protected string $form_id;

    /**
     * Form name.
     */
    protected string $form_name;

    /**
     * Form fields configuration.
     */
    protected array $fields = [];

    /**
     * Validation rules.
     */
    protected array $rules = [];

    /**
     * Field types for sanitization.
     */
    protected array $field_types = [];

    /**
     * Validator instance.
     */
    protected Validator $validator;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Register form hooks.
     *
     * @return void
     */
    public function register(): void
    {
        // Register AJAX handler for logged-in users
        add_action('wp_ajax_submit_' . $this->form_id, [$this, 'handle_submission']);

        // Register AJAX handler for non-logged-in users
        add_action('wp_ajax_nopriv_submit_' . $this->form_id, [$this, 'handle_submission']);

        // Register shortcode
        add_shortcode($this->form_id, [$this, 'render_shortcode']);
    }

    /**
     * Handle form submission.
     *
     * @return void
     */
    public function handle_submission(): void
    {
        // Verify nonce
        if (!check_ajax_referer('travel_forms_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Security check failed', 'travel-forms'),
            ], 403);
        }

        // Get form data
        $raw_data = $_POST['form_data'] ?? [];

        // Sanitize data
        $sanitized_data = Sanitizer::sanitize($raw_data, $this->field_types);

        // Validate data
        if (!$this->validator->validate($sanitized_data, $this->rules)) {
            wp_send_json_error([
                'message' => __('Validation failed', 'travel-forms'),
                'errors' => $this->validator->get_errors(),
            ], 422);
        }

        // Save to database
        $submission_id = Database::insert_submission($this->form_id, $sanitized_data);

        if (!$submission_id) {
            wp_send_json_error([
                'message' => __('Failed to save form submission', 'travel-forms'),
            ], 500);
        }

        // Send to HubSpot if enabled
        if (get_option('travel_forms_hubspot_enabled', false)) {
            $hubspot = new HubSpotAPI();
            $hubspot_result = $hubspot->create_contact($sanitized_data, $this->form_id);

            if ($hubspot_result && !empty($hubspot_result['id'])) {
                Database::mark_hubspot_sent($submission_id, $hubspot_result['id']);
            }
        }

        // Send email notification
        $this->send_email_notification($sanitized_data, $submission_id);

        // Update status
        Database::update_status($submission_id, 'completed');

        // Success response
        wp_send_json_success([
            'message' => $this->get_success_message(),
            'submission_id' => $submission_id,
        ]);
    }

    /**
     * Render form via shortcode.
     *
     * @param array $atts Shortcode attributes
     *
     * @return string Form HTML
     */
    public function render_shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'title' => $this->form_name,
            'show_title' => true,
        ], $atts);

        ob_start();
        $this->render($atts);
        return ob_get_clean();
    }

    /**
     * Render the form HTML.
     * Must be implemented by child classes.
     *
     * @param array $atts Form attributes
     *
     * @return void
     */
    abstract public function render(array $atts = []): void;

    /**
     * Send email notification.
     *
     * @param array $data          Form data
     * @param int   $submission_id Submission ID
     *
     * @return bool
     */
    protected function send_email_notification(array $data, int $submission_id): bool
    {
        $admin_email = get_option('admin_email');
        $subject = sprintf(__('New %s Submission #%d', 'travel-forms'), $this->form_name, $submission_id);

        $message = sprintf(__('New form submission received:', 'travel-forms')) . "\n\n";
        $message .= sprintf(__('Form: %s', 'travel-forms'), $this->form_name) . "\n";
        $message .= sprintf(__('Submission ID: %d', 'travel-forms'), $submission_id) . "\n";
        $message .= sprintf(__('Date: %s', 'travel-forms'), current_time('mysql')) . "\n\n";
        $message .= __('Details:', 'travel-forms') . "\n";
        $message .= "---\n";

        foreach ($data as $field => $value) {
            $label = ucwords(str_replace('_', ' ', $field));
            $message .= "$label: $value\n";
        }

        return wp_mail($admin_email, $subject, $message);
    }

    /**
     * Get success message.
     *
     * @return string
     */
    protected function get_success_message(): string
    {
        return __('Thank you! Your form has been submitted successfully.', 'travel-forms');
    }

    /**
     * Render a form field.
     *
     * @param string $field_name Field name
     * @param array  $field_config Field configuration
     *
     * @return void
     */
    protected function render_field(string $field_name, array $field_config): void
    {
        $type = $field_config['type'] ?? 'text';
        $label = $field_config['label'] ?? ucwords(str_replace('_', ' ', $field_name));
        $placeholder = $field_config['placeholder'] ?? '';
        $required = $field_config['required'] ?? false;
        $options = $field_config['options'] ?? [];

        $required_attr = $required ? 'required' : '';
        $required_mark = $required ? '<span class="required">*</span>' : '';

        echo '<div class="form-field form-field-' . esc_attr($type) . '">';
        echo '<label for="' . esc_attr($field_name) . '">' . esc_html($label) . ' ' . $required_mark . '</label>';

        switch ($type) {
            case 'textarea':
                echo '<textarea id="' . esc_attr($field_name) . '" name="' . esc_attr($field_name) . '" placeholder="' . esc_attr($placeholder) . '" ' . $required_attr . '></textarea>';
                break;

            case 'select':
                echo '<select id="' . esc_attr($field_name) . '" name="' . esc_attr($field_name) . '" ' . $required_attr . '>';
                echo '<option value="">' . esc_html__('Select...', 'travel-forms') . '</option>';
                foreach ($options as $value => $text) {
                    echo '<option value="' . esc_attr($value) . '">' . esc_html($text) . '</option>';
                }
                echo '</select>';
                break;

            case 'checkbox':
                echo '<input type="checkbox" id="' . esc_attr($field_name) . '" name="' . esc_attr($field_name) . '" value="1" ' . $required_attr . ' />';
                echo '<span class="checkbox-label">' . esc_html($placeholder) . '</span>';
                break;

            default:
                echo '<input type="' . esc_attr($type) . '" id="' . esc_attr($field_name) . '" name="' . esc_attr($field_name) . '" placeholder="' . esc_attr($placeholder) . '" ' . $required_attr . ' />';
        }

        echo '<span class="error-message"></span>';
        echo '</div>';
    }
}
