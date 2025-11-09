<?php
/**
 * Block: Contact Form
 *
 * Hero contact form with background image, AJAX submission, and email notifications.
 * Critical block for lead generation and customer inquiries.
 *
 * Features:
 * - Hero-style form with background image
 * - AJAX form submission with nonce security
 * - Complete field validation and sanitization
 * - HTML email template with responsive design
 * - Package inquiry integration
 * - Form success/error messaging
 *
 * Security:
 * - Nonce verification for all submissions
 * - Complete input sanitization
 * - Field validation with error messages
 * - Safe email header handling
 *
 * ⚠️ PRODUCTION CRITICAL: This block handles live customer inquiries.
 * DO NOT modify AJAX action names or nonce keys without coordinating JS changes.
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 1.1.0 - Refactored: namespace fix, improved validation structure, added docs
 */

namespace Travel\Blocks\ACF;

use Travel\Blocks\Core\BlockBase;

class ContactForm extends BlockBase
{
    public function __construct()
    {
        $this->name        = 'contact-form';
        $this->title       = __('Contact Form', 'travel-blocks');
        $this->description = __('Hero contact form with background image', 'travel-blocks');
        $this->category    = 'template-blocks';
        $this->icon        = 'email';
        $this->keywords    = ['contact', 'form', 'email', 'inquiry', 'hero'];
        $this->mode        = 'preview';

        $this->supports = [
            'align' => ['wide', 'full'],
            'mode'  => true,
            'jsx'   => true,
            'anchor' => true,
            'customClassName' => true,
            'multiple' => true,
        ];
    }

    /**
     * Register block and its ACF fields.
     *
     * Registers ACF block type and loads field configuration from external JSON file.
     * Also registers AJAX handlers for form submission (both authenticated and public).
     *
     * ACF Fields Source: /travel-acf-fields/acf-json/group_contact_form_hero.json
     * - Form fields configuration (first name, last name, email, phone, etc.)
     * - Background image settings
     * - Form styling options
     *
     * AJAX Actions Registered:
     * - wp_ajax_travel_hero_form_submit (logged-in users)
     * - wp_ajax_nopriv_travel_hero_form_submit (public)
     *
     * ⚠️ Critical Dependency: Requires external ACF JSON file to function.
     * If JSON file is missing, block will register but have no editable fields.
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();

        // Register ACF fields for this block
        // Load fields from JSON to maintain exact structure
        if (function_exists('acf_add_local_field_group')) {
            $json_file = WP_CONTENT_DIR . '/plugins/travel-acf-fields/acf-json/group_contact_form_hero.json';

            if (file_exists($json_file)) {
                $json_data = json_decode(file_get_contents($json_file), true);

                if ($json_data && isset($json_data['fields'])) {
                    // Register field group with inline fields from JSON
                    acf_add_local_field_group([
                        'key' => 'group_contact_form_hero',
                        'title' => __('Hero Form Settings', 'travel-blocks'),
                        'fields' => $json_data['fields'],
                        'location' => [
                            [
                                [
                                    'param' => 'block',
                                    'operator' => '==',
                                    'value' => 'acf/contact-form',
                                ],
                            ],
                        ],
                        'menu_order' => 0,
                        'position' => 'normal',
                        'style' => 'default',
                        'label_placement' => 'top',
                        'instruction_placement' => 'label',
                        'active' => true,
                    ]);
                }
            } else {
                // Log error if JSON file is missing
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('ContactForm: ACF JSON file not found at ' . $json_file);
                }
            }
        }

        // Register AJAX handlers for form submission
        add_action('wp_ajax_travel_hero_form_submit', [$this, 'handle_form_submit']);
        add_action('wp_ajax_nopriv_travel_hero_form_submit', [$this, 'handle_form_submit']);
    }

    /**
     * Enqueue block-specific assets.
     *
     * @return void
     */
    public function enqueue_assets(): void
    {
        // Enqueue styles for both frontend and editor
        wp_enqueue_style(
            'contact-form-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/contact-form.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        // Enqueue scripts only on frontend
        if (!is_admin()) {
            wp_enqueue_script(
                'contact-form-script',
                TRAVEL_BLOCKS_URL . 'assets/blocks/contact-form.js',
                [],
                TRAVEL_BLOCKS_VERSION,
                true
            );

            wp_localize_script('contact-form-script', 'travelContactForm', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('travel_contact_form'),
            ]);
        }
    }

    /**
     * Render the block output.
     *
     * @param array  $block      Block settings
     * @param string $content    Block content
     * @param bool   $is_preview Whether in preview mode
     * @param int    $post_id    Current post ID
     *
     * @return void
     */
    public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        // Handle new ACF block format
        if ($is_preview instanceof \WP_Block) {
            $post_id = $is_preview->context['postId'] ?? get_the_ID();
            $is_preview = false;
        }

        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $data = [
            'block_id' => 'contact-form-' . uniqid(),
            'class_name' => !empty($block['className']) ? $block['className'] : '',
            'is_preview' => $is_preview,
            'current_package_id' => $post_id,
            'package_title' => get_the_title($post_id),
            'block' => $block,
        ];

        $this->load_template('contact-form', $data);
    }

    /**
     * Handle AJAX form submission
     */
    public function handle_form_submit(): void
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'travel_contact_form')) {
            wp_send_json_error([
                'message' => __('Security verification failed. Please refresh the page and try again.', 'travel-blocks')
            ], 403);
        }

        // Sanitize and validate form data
        $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';
        $package_interest = isset($_POST['package_interest']) ? sanitize_text_field($_POST['package_interest']) : '';
        $package_id = isset($_POST['package_id']) ? absint($_POST['package_id']) : 0;
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

        // Validate required fields
        $errors = [];

        if (empty($first_name)) {
            $errors[] = __('First name is required.', 'travel-blocks');
        }

        if (empty($last_name)) {
            $errors[] = __('Last name is required.', 'travel-blocks');
        }

        if (empty($email)) {
            $errors[] = __('Email is required.', 'travel-blocks');
        } elseif (!is_email($email)) {
            $errors[] = __('Please enter a valid email address.', 'travel-blocks');
        }

        if (empty($country)) {
            $errors[] = __('Country is required.', 'travel-blocks');
        }

        if (empty($message)) {
            $errors[] = __('Message is required.', 'travel-blocks');
        }

        // Return errors if validation failed
        if (!empty($errors)) {
            wp_send_json_error([
                'message' => implode(' ', $errors)
            ], 400);
        }

        // Get recipient email (from ACF or default to admin email)
        $recipient = get_option('admin_email');

        // Get package title if package_id is provided
        $package_title = '';
        if ($package_id > 0) {
            $package = get_post($package_id);
            if ($package && $package->post_type === 'package') {
                $package_title = $package->post_title;
            }
        } elseif (!empty($package_interest)) {
            // If package_interest is provided but not package_id, use package_interest directly
            $package_title = $package_interest;
        }

        // Build email content
        $email_subject = sprintf(
            __('New Contact Form Submission from %s %s', 'travel-blocks'),
            $first_name,
            $last_name
        );

        $email_body = $this->build_email_template([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'country' => $country,
            'package_title' => $package_title,
            'message' => $message,
        ]);

        // Set email headers
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: ' . $email,
        ];

        // Send email
        $mail_sent = wp_mail($recipient, $email_subject, $email_body, $headers);

        if ($mail_sent) {
            wp_send_json_success([
                'message' => __('Thank you! Your message has been sent successfully. We\'ll get back to you within 24 hours.', 'travel-blocks')
            ]);
        } else {
            wp_send_json_error([
                'message' => __('Sorry, there was an error sending your message. Please try again or contact us directly.', 'travel-blocks')
            ], 500);
        }
    }

    /**
     * Build HTML email template
     */
    private function build_email_template(array $data): string
    {
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $email = $data['email'];
        $phone = $data['phone'];
        $country = $data['country'];
        $package_title = $data['package_title'];
        $message = $data['message'];
        $date = date_i18n(get_option('date_format') . ' ' . get_option('time_format'));

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                    line-height: 1.6;
                    color: #1F2937;
                    background-color: #F9FAFB;
                    margin: 0;
                    padding: 0;
                }
                .email-container {
                    max-width: 600px;
                    margin: 40px auto;
                    background: #FFFFFF;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .email-header {
                    background: linear-gradient(135deg, #0A797E 0%, #0A646A 100%);
                    color: #FFFFFF;
                    padding: 32px 24px;
                    text-align: center;
                }
                .email-header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                }
                .email-header p {
                    margin: 8px 0 0 0;
                    font-size: 14px;
                    opacity: 0.9;
                }
                .email-body {
                    padding: 32px 24px;
                }
                .field-group {
                    margin-bottom: 24px;
                }
                .field-label {
                    font-size: 12px;
                    font-weight: 600;
                    text-transform: uppercase;
                    color: #6B7280;
                    letter-spacing: 0.5px;
                    margin-bottom: 6px;
                }
                .field-value {
                    font-size: 16px;
                    color: #1F2937;
                    padding: 12px 16px;
                    background: #F9FAFB;
                    border-left: 3px solid #0A797E;
                    border-radius: 6px;
                }
                .field-value.message {
                    white-space: pre-wrap;
                    min-height: 60px;
                }
                .contact-info {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 16px;
                    margin-bottom: 24px;
                }
                .email-footer {
                    background: #F9FAFB;
                    padding: 24px;
                    text-align: center;
                    border-top: 1px solid #E5E7EB;
                }
                .email-footer p {
                    margin: 0;
                    font-size: 14px;
                    color: #6B7280;
                }
                .highlight {
                    display: inline-block;
                    background: #E78C85;
                    color: #FFFFFF;
                    padding: 4px 12px;
                    border-radius: 6px;
                    font-size: 12px;
                    font-weight: 600;
                    margin-top: 8px;
                }
                @media only screen and (max-width: 600px) {
                    .email-container {
                        margin: 20px;
                    }
                    .contact-info {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <!-- Header -->
                <div class="email-header">
                    <h1>🌎 New Contact Form Submission</h1>
                    <p><?php echo esc_html($date); ?></p>
                </div>

                <!-- Body -->
                <div class="email-body">
                    <!-- Name & Contact Info -->
                    <div class="contact-info">
                        <div class="field-group">
                            <div class="field-label">First Name</div>
                            <div class="field-value"><?php echo esc_html($first_name); ?></div>
                        </div>
                        <div class="field-group">
                            <div class="field-label">Last Name</div>
                            <div class="field-value"><?php echo esc_html($last_name); ?></div>
                        </div>
                    </div>

                    <div class="contact-info">
                        <div class="field-group">
                            <div class="field-label">Email Address</div>
                            <div class="field-value">
                                <a href="mailto:<?php echo esc_attr($email); ?>" style="color: #0A797E; text-decoration: none;">
                                    <?php echo esc_html($email); ?>
                                </a>
                            </div>
                        </div>
                        <?php if (!empty($phone)): ?>
                            <div class="field-group">
                                <div class="field-label">Phone</div>
                                <div class="field-value">
                                    <a href="tel:<?php echo esc_attr($phone); ?>" style="color: #0A797E; text-decoration: none;">
                                        <?php echo esc_html($phone); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="contact-info">
                        <div class="field-group">
                            <div class="field-label">Country</div>
                            <div class="field-value"><?php echo esc_html($country); ?></div>
                        </div>
                        <?php if (!empty($package_title)): ?>
                            <div class="field-group">
                                <div class="field-label">Package Interest</div>
                                <div class="field-value">
                                    <strong><?php echo esc_html($package_title); ?></strong>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Message -->
                    <div class="field-group">
                        <div class="field-label">Message</div>
                        <div class="field-value message"><?php echo esc_html($message); ?></div>
                    </div>

                    <?php if (!empty($package_title)): ?>
                        <span class="highlight">Package Inquiry</span>
                    <?php endif; ?>
                </div>

                <!-- Footer -->
                <div class="email-footer">
                    <p>
                        This message was sent from the contact form on <?php echo esc_html(get_bloginfo('name')); ?><br>
                        <a href="<?php echo esc_url(home_url('/')); ?>" style="color: #0A797E; text-decoration: none;">
                            <?php echo esc_html(home_url('/')); ?>
                        </a>
                    </p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
