<?php
/**
 * Contact Form
 *
 * General contact/inquiry form.
 *
 * @package Travel\Forms\Forms
 * @since 1.0.0
 */

namespace Travel\Forms\Forms;

use Travel\Forms\Core\FormBase;

class ContactForm extends FormBase
{
    public function __construct()
    {
        parent::__construct();

        $this->form_id = 'contact-form';
        $this->form_name = __('Contact Form', 'travel-forms');

        $this->fields = [
            'name' => [
                'type' => 'text',
                'label' => __('Full Name', 'travel-forms'),
                'placeholder' => __('Enter your full name', 'travel-forms'),
                'required' => true,
            ],
            'email' => [
                'type' => 'email',
                'label' => __('Email Address', 'travel-forms'),
                'placeholder' => __('your@email.com', 'travel-forms'),
                'required' => true,
            ],
            'phone' => [
                'type' => 'tel',
                'label' => __('Phone Number', 'travel-forms'),
                'placeholder' => __('+51 999 999 999', 'travel-forms'),
                'required' => false,
            ],
            'country' => [
                'type' => 'text',
                'label' => __('Country', 'travel-forms'),
                'placeholder' => __('Your country', 'travel-forms'),
                'required' => false,
            ],
            'subject' => [
                'type' => 'select',
                'label' => __('Subject', 'travel-forms'),
                'required' => true,
                'options' => [
                    'general' => __('General Inquiry', 'travel-forms'),
                    'tour_info' => __('Tour Information', 'travel-forms'),
                    'booking' => __('Booking Question', 'travel-forms'),
                    'complaint' => __('Complaint/Feedback', 'travel-forms'),
                    'other' => __('Other', 'travel-forms'),
                ],
            ],
            'message' => [
                'type' => 'textarea',
                'label' => __('Message', 'travel-forms'),
                'placeholder' => __('How can we help you?', 'travel-forms'),
                'required' => true,
            ],
            'consent' => [
                'type' => 'checkbox',
                'label' => __('Privacy Consent', 'travel-forms'),
                'placeholder' => __('I agree to the privacy policy and terms of service', 'travel-forms'),
                'required' => true,
            ],
        ];

        $this->rules = [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email',
            'phone' => 'phone',
            'country' => 'max:100',
            'subject' => 'required',
            'message' => 'required|min:10|max:1000',
            'consent' => 'required',
        ];

        $this->field_types = [
            'name' => 'text',
            'email' => 'email',
            'phone' => 'phone',
            'country' => 'text',
            'subject' => 'text',
            'message' => 'textarea',
            'consent' => 'boolean',
        ];
    }

    /**
     * Render the contact form.
     *
     * @param array $atts Form attributes
     *
     * @return void
     */
    public function render(array $atts = []): void
    {
        ?>
        <div class="travel-form contact-form-wrapper">
            <?php if ($atts['show_title']): ?>
                <h2 class="form-title"><?php echo esc_html($atts['title']); ?></h2>
            <?php endif; ?>

            <form id="<?php echo esc_attr($this->form_id); ?>" class="travel-form-inner" data-form-id="<?php echo esc_attr($this->form_id); ?>">
                <?php foreach ($this->fields as $field_name => $field_config): ?>
                    <?php $this->render_field($field_name, $field_config); ?>
                <?php endforeach; ?>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php _e('Send Message', 'travel-forms'); ?>
                    </button>
                </div>

                <div class="form-messages"></div>
            </form>
        </div>
        <?php
    }

    /**
     * Get success message for contact form.
     *
     * @return string
     */
    protected function get_success_message(): string
    {
        return __('Thank you for contacting us! We will get back to you within 24 hours.', 'travel-forms');
    }
}
