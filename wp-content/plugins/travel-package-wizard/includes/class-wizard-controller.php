<?php
/**
 * Wizard Controller
 * Manages the wizard interface for Package CPT
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aurora_Wizard_Controller {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Wizard steps configuration
     */
    private $steps = [
        'basic' => [
            'label' => 'Basic Information',
            'icon' => '📋',
            'metaboxes' => ['group_package_general'],
            'fields' => ['featured_package', 'rating', 'service_type', 'shared_package_details', 'summary', 'description', 'included', 'not_included', 'highlight'],
        ],
        'details' => [
            'label' => 'Package Details',
            'icon' => '🔧',
            'metaboxes' => ['group_package_base_info'],
            'fields' => '*',
        ],
        'pricing' => [
            'label' => 'Pricing & Promotions',
            'icon' => '💰',
            'metaboxes' => ['group_package_pricing'],
            'fields' => '*',
        ],
        'media' => [
            'label' => 'Media & Gallery',
            'icon' => '📸',
            'metaboxes' => ['group_package_media'],
            'fields' => '*',
        ],
        'itinerary' => [
            'label' => 'Itinerary',
            'icon' => '🗺️',
            'metaboxes' => ['group_package_itinerary'],
            'fields' => '*',
        ],
        'seo' => [
            'label' => 'SEO & Publication',
            'icon' => '🔍',
            'metaboxes' => ['group_package_seo', 'group_package_additional_content'],
            'fields' => '*',
        ],
        'taxonomies' => [
            'label' => 'Categories & Tags',
            'icon' => '🏷️',
            'metaboxes' => [],
            'taxonomies' => [
                'package_type',
                'interest',
                'optional_renting',
                'included_services',
                'day',
                'additional_info',
                'tag_locations',
                'activity',
                'type_service',
                'hotel',
                'spot_calendar',
                'specialists',
                'landing_packages',
                'faq',
            ],
            'fields' => '*',
        ],
    ];

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Enqueue admin assets
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);

        // Modify admin page layout
        add_action('admin_head', [$this, 'inject_wizard_layout']);

        // Add wizard navigation to admin footer
        add_action('admin_footer', [$this, 'render_wizard_navigation']);

        // AJAX handlers
        add_action('wp_ajax_aurora_wizard_save_step', [$this, 'ajax_save_step']);
        add_action('wp_ajax_aurora_wizard_validate_step', [$this, 'ajax_validate_step']);
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_assets($hook) {
        global $post_type;

        // Only load on Package edit screens
        if (($hook === 'post.php' || $hook === 'post-new.php') && $post_type === 'package') {
            // CSS (with timestamp to force cache refresh)
            wp_enqueue_style(
                'aurora-wizard-css',
                TRAVEL_PACKAGE_WIZARD_URL . 'assets/css/admin-wizard.css',
                [],
                TRAVEL_PACKAGE_WIZARD_VERSION . '.' . time() // Force cache refresh
            );

            // JavaScript (with timestamp to force cache refresh)
            wp_enqueue_script(
                'aurora-wizard-js',
                TRAVEL_PACKAGE_WIZARD_URL . 'assets/js/wizard-navigation.js',
                ['jquery', 'wp-api'],
                TRAVEL_PACKAGE_WIZARD_VERSION . '.' . time(), // Force cache refresh
                true
            );

            // Localize script
            wp_localize_script('aurora-wizard-js', 'auroraWizard', [
                'steps' => $this->steps,
                'currentStep' => $this->get_current_step(),
                'postId' => get_the_ID(),
                'nonce' => wp_create_nonce('aurora_wizard_nonce'),
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'labels' => [
                    'next' => __('Next →', 'travel-package-wizard'),
                    'back' => __('← Back', 'travel-package-wizard'),
                    'save' => __('Save Draft', 'travel-package-wizard'),
                    'publish' => __('Publish Package', 'travel-package-wizard'),
                    'saved' => __('✓ Progress saved', 'travel-package-wizard'),
                    'autoSaved' => __('Auto-saved', 'travel-package-wizard'),
                    'validationError' => __('Please fill in all required fields', 'travel-package-wizard'),
                    'saving' => __('Saving...', 'travel-package-wizard'),
                ],
            ]);
        }
    }

    /**
     * Inject wizard layout into admin page
     */
    public function inject_wizard_layout() {
        global $post_type;
        $screen = get_current_screen();

        if (($screen->id === 'package' || $screen->id === 'edit-package') && ($screen->base === 'post')) {
            include TRAVEL_PACKAGE_WIZARD_PATH . 'templates/wizard-layout.php';
        }
    }

    /**
     * Render wizard navigation
     */
    public function render_wizard_navigation() {
        global $post_type;
        $screen = get_current_screen();

        if (($screen->id === 'package') && ($screen->base === 'post')) {
            include TRAVEL_PACKAGE_WIZARD_PATH . 'templates/wizard-navigation.php';
        }
    }

    /**
     * Get current wizard step
     */
    private function get_current_step() {
        $step = isset($_GET['wizard_step']) ? sanitize_text_field($_GET['wizard_step']) : 'basic';

        // Validate step exists
        if (!array_key_exists($step, $this->steps)) {
            $step = 'basic';
        }

        return $step;
    }

    /**
     * Get step index
     */
    public function get_step_index($step_name) {
        $steps = array_keys($this->steps);
        return array_search($step_name, $steps);
    }

    /**
     * AJAX: Save step
     */
    public function ajax_save_step() {
        // TEST: Ultra simple version to identify the issue
        $debug = [];

        $debug[] = 'STEP 1: Function called';

        try {
            $debug[] = 'STEP 2: Checking nonce';
            check_ajax_referer('aurora_wizard_nonce', 'nonce');

            $debug[] = 'STEP 3: Getting parameters';
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $step = isset($_POST['step']) ? sanitize_text_field($_POST['step']) : '';
            $acf_data = isset($_POST['acf']) ? $_POST['acf'] : [];

            $debug[] = sprintf('STEP 4: Post ID=%d, Step=%s, Fields=%d', $post_id, $step, count($acf_data));

            if (!$post_id || !$step) {
                wp_send_json_error(['message' => 'Invalid params', 'debug' => $debug]);
            }

            $debug[] = 'STEP 5: Parameters valid';

            // SIMPLIFIED: Just try to save WITHOUT sanitization
            if (!empty($acf_data) && function_exists('update_field')) {
                $debug[] = 'STEP 6: Starting field updates';

                foreach ($acf_data as $field_key => $value) {
                    if (empty($field_key) || strpos($field_key, 'message') !== false) {
                        continue;
                    }

                    $debug[] = sprintf('  - Field: %s', $field_key);
                    $debug[] = sprintf('    Original value: %s (type: %s)',
                        is_array($value) ? json_encode($value) : $value,
                        gettype($value)
                    );

                    // Handle gallery field
                    if ($field_key === 'field_package_gallery') {
                        if ($value === '' || $value === null) {
                            $value = []; // Empty gallery = empty array
                            $debug[] = '    Converted empty to array';
                        } elseif (is_string($value)) {
                            // Convert comma-separated IDs to array of integers
                            $ids = explode(',', $value);
                            $value = array_map('intval', array_filter($ids));
                            $debug[] = sprintf('    Converted string to array: %s', json_encode($value));
                        }
                    }

                    // Handle map image field
                    if ($field_key === 'field_package_map_image') {
                        if (!empty($value)) {
                            $value = intval($value);
                            $debug[] = sprintf('    Converted to int: %d', $value);
                        } else {
                            $value = '';
                            $debug[] = '    Empty map image';
                        }
                    }

                    // Handle banners repeater
                    if ($field_key === 'field_package_banners' && is_array($value) && empty($value)) {
                        $debug[] = '    Empty banners array';
                    }

                    // Handle itinerary repeater - process gallery fields within each day
                    if ($field_key === 'field_package_itinerary' && is_array($value)) {
                        foreach ($value as $day_index => &$day_data) {
                            if (isset($day_data['field_itinerary_day_gallery'])) {
                                $gallery_value = $day_data['field_itinerary_day_gallery'];

                                if ($gallery_value === '' || $gallery_value === null) {
                                    $day_data['field_itinerary_day_gallery'] = [];
                                    $debug[] = sprintf('    Day %d: Converted empty gallery to array', $day_index);
                                } elseif (is_string($gallery_value)) {
                                    // Convert comma-separated IDs to array of integers
                                    $ids = explode(',', $gallery_value);
                                    $day_data['field_itinerary_day_gallery'] = array_map('intval', array_filter($ids));
                                    $debug[] = sprintf('    Day %d: Converted gallery string to array: %s',
                                        $day_index,
                                        json_encode($day_data['field_itinerary_day_gallery'])
                                    );
                                }
                            }
                        }
                        unset($day_data); // Break reference
                        $debug[] = '    Processed itinerary gallery fields';
                    }

                    $debug[] = sprintf('    Calling update_field with: %s (type: %s)',
                        is_array($value) ? json_encode($value) : $value,
                        gettype($value)
                    );
                    $result = update_field($field_key, $value, $post_id);
                    $debug[] = sprintf('    Result: %s', $result ? 'OK' : 'FAIL');
                }

                $debug[] = 'STEP 7: All fields updated';
            }

            // Handle taxonomy data (FAQ, destinations, package_type, etc.)
            if (isset($acf_data['tax_input']) && is_array($acf_data['tax_input'])) {
                $debug[] = 'STEP 7.5: Processing taxonomy data';

                foreach ($acf_data['tax_input'] as $taxonomy => $terms) {
                    $debug[] = sprintf('  - Taxonomy: %s', $taxonomy);

                    // For hierarchical taxonomies (array of term IDs)
                    if (is_array($terms)) {
                        $term_ids = array_map('intval', $terms);
                        $result = wp_set_post_terms($post_id, $term_ids, $taxonomy);
                        $debug[] = sprintf('    Set terms (hierarchical): %s', json_encode($term_ids));
                    }
                    // For non-hierarchical taxonomies (comma-separated string)
                    else if (is_string($terms)) {
                        $result = wp_set_post_terms($post_id, $terms, $taxonomy);
                        $debug[] = sprintf('    Set terms (tags): %s', $terms);
                    }

                    if (is_wp_error($result)) {
                        $debug[] = sprintf('    ERROR: %s', $result->get_error_message());
                    } else {
                        $debug[] = '    Result: OK';
                    }
                }
            }

            $debug[] = 'STEP 8: SUCCESS - Sending response';

            wp_send_json_success([
                'message' => 'Saved',
                'step' => $step,
                'debug' => $debug
            ]);

        } catch (\Exception $e) {
            $debug[] = 'EXCEPTION: ' . $e->getMessage();
            wp_send_json_error(['message' => $e->getMessage(), 'debug' => $debug]);
        }
    }

    /**
     * Sanitize ACF field value based on field type
     */
    private function sanitize_acf_value($field_name_or_key, $value) {
        // Try to get field object by key first, then by name
        try {
            $field_object = get_field_object($field_name_or_key);
        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log(sprintf(
                    'Error getting field object for %s: %s',
                    $field_name_or_key,
                    $e->getMessage()
                ));
            }
            $field_object = false;
        }

        if (!$field_object) {
            // If it's an array (repeater data), process it
            if (is_array($value)) {
                return $value; // Will be processed by update_field
            }
            // If empty string, return it as is (don't convert to sanitized empty string)
            if ($value === '') {
                return '';
            }
            return sanitize_text_field($value);
        }

        $field_type = $field_object['type'];

        switch ($field_type) {
            case 'wysiwyg':
            case 'textarea':
                return wp_kses_post($value);

            case 'number':
            case 'range':
                return floatval($value);

            case 'true_false':
                return (bool) $value;

            case 'select':
            case 'radio':
            case 'button_group':
                // Validate against choices if available
                if (isset($field_object['choices']) && is_array($field_object['choices'])) {
                    return in_array($value, array_keys($field_object['choices'])) ? $value : '';
                }
                return sanitize_text_field($value);

            case 'url':
                return esc_url_raw($value);

            case 'email':
                return sanitize_email($value);

            case 'image':
            case 'file':
                // Image/file fields store attachment IDs
                // Empty string or 0 means no image selected
                if ($value === '' || $value === null) {
                    return '';
                }
                return intval($value);

            case 'gallery':
                // Gallery fields store array of attachment IDs
                // Handle empty values explicitly
                if ($value === '' || $value === null || $value === false) {
                    return [];
                }

                if (is_string($value)) {
                    // Empty string or whitespace-only string
                    if (trim($value) === '') {
                        return [];
                    }
                    // Comma-separated IDs
                    $ids = explode(',', $value);
                    $ids = array_filter($ids, function($id) {
                        return trim($id) !== '';
                    });
                    return array_map('intval', $ids);
                } elseif (is_array($value)) {
                    // Filter out empty values
                    $filtered = array_filter($value, function($id) {
                        return !empty($id) && $id !== '' && $id !== null;
                    });
                    return array_map('intval', $filtered);
                }
                return [];

            case 'repeater':
                // Repeater fields store array of rows
                if (is_array($value)) {
                    // If empty array, just return it
                    if (empty($value)) {
                        return [];
                    }
                    // Otherwise, ACF will handle the sanitization
                    return $value;
                }
                // If not an array, return empty array
                return [];

            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * AJAX: Validate step
     */
    public function ajax_validate_step() {
        check_ajax_referer('aurora_wizard_nonce', 'nonce');

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $step = isset($_POST['step']) ? sanitize_text_field($_POST['step']) : '';

        if (!$post_id || !$step) {
            wp_send_json_error(['message' => __('Invalid parameters', 'travel-package-wizard')]);
        }

        // Validate step fields
        $errors = $this->validate_step_fields($post_id, $step);

        if (empty($errors)) {
            wp_send_json_success(['valid' => true]);
        } else {
            wp_send_json_error([
                'valid' => false,
                'errors' => $errors,
            ]);
        }
    }

    /**
     * Validate step fields
     */
    private function validate_step_fields($post_id, $step) {
        $errors = [];

        switch ($step) {
            case 'basic':
                // Validate summary
                $summary = get_field('summary', $post_id);
                if (empty($summary)) {
                    $errors[] = __('Summary is required', 'travel-package-wizard');
                } elseif (strlen($summary) > 200) {
                    $errors[] = __('Summary must be 200 characters or less', 'travel-package-wizard');
                }

                // Validate description
                $description = get_field('description', $post_id);
                if (empty($description)) {
                    $errors[] = __('Description is required', 'travel-package-wizard');
                }

                // Validate service type
                $service_type = get_field('service_type', $post_id);

                // Debug logging
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log(sprintf(
                        'Wizard Validation [Post ID: %d] - service_type value: %s (type: %s)',
                        $post_id,
                        var_export($service_type, true),
                        gettype($service_type)
                    ));
                }

                if (empty($service_type)) {
                    $errors[] = __('Service type is required', 'travel-package-wizard');
                }
                break;

            case 'details':
                // Validate days
                $days = get_field('days', $post_id);
                if (empty($days) || $days < 1) {
                    $errors[] = __('Days is required (minimum 1)', 'travel-package-wizard');
                }

                // Validate duration
                $duration = get_field('duration', $post_id);
                if (empty($duration)) {
                    $errors[] = __('Duration is required', 'travel-package-wizard');
                }
                break;

            case 'pricing':
                // Validate price_from
                $price_from = get_field('price_from', $post_id);
                if (empty($price_from) || $price_from <= 0) {
                    $errors[] = __('Price From is required', 'travel-package-wizard');
                }

                // Validate price_normal
                $price_normal = get_field('price_normal', $post_id);
                if (empty($price_normal) || $price_normal <= 0) {
                    $errors[] = __('Normal Price is required', 'travel-package-wizard');
                }

                // Validate price logic
                if ($price_from > $price_normal) {
                    $errors[] = __('"Price From" cannot be higher than "Normal Price"', 'travel-package-wizard');
                }
                break;

            case 'media':
                // Validate featured image
                $thumbnail_id = get_post_thumbnail_id($post_id);
                if (empty($thumbnail_id)) {
                    $errors[] = __('Featured Image is required', 'travel-package-wizard');
                }
                break;
        }

        return $errors;
    }

    /**
     * Get wizard steps
     */
    public function get_steps() {
        return $this->steps;
    }
}
