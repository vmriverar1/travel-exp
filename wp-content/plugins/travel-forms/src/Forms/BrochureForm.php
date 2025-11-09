<?php
/**
 * Brochure Request Form
 *
 * Form to request travel brochures and catalogs.
 *
 * @package Travel\Forms\Forms
 * @since 1.0.0
 */

namespace Travel\Forms\Forms;

use Travel\Forms\Core\FormBase;

class BrochureForm extends FormBase
{
    public function __construct()
    {
        parent::__construct();

        $this->form_id = 'brochure-form';
        $this->form_name = __('Brochure Request Form', 'travel-forms');

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
            'country' => [
                'type' => 'text',
                'label' => __('Country', 'travel-forms'),
                'placeholder' => __('Your country', 'travel-forms'),
                'required' => false,
            ],
            'brochure_type' => [
                'type' => 'select',
                'label' => __('Brochure Type', 'travel-forms'),
                'required' => true,
                'options' => [
                    'general' => __('General Travel Guide', 'travel-forms'),
                    'machu_picchu' => __('Machu Picchu Tours', 'travel-forms'),
                    'cusco' => __('Cusco & Sacred Valley', 'travel-forms'),
                    'amazon' => __('Amazon Jungle Tours', 'travel-forms'),
                    'adventure' => __('Adventure Tours', 'travel-forms'),
                    'luxury' => __('Luxury Travel', 'travel-forms'),
                    'all' => __('All Brochures', 'travel-forms'),
                ],
            ],
            'format' => [
                'type' => 'select',
                'label' => __('Preferred Format', 'travel-forms'),
                'required' => true,
                'options' => [
                    'digital' => __('Digital (PDF via email)', 'travel-forms'),
                    'physical' => __('Physical (Mailed to address)', 'travel-forms'),
                ],
            ],
            'address' => [
                'type' => 'textarea',
                'label' => __('Mailing Address', 'travel-forms'),
                'placeholder' => __('Only required for physical brochures', 'travel-forms'),
                'required' => false,
            ],
            'travel_timeline' => [
                'type' => 'select',
                'label' => __('When are you planning to travel?', 'travel-forms'),
                'required' => false,
                'options' => [
                    '' => __('Select...', 'travel-forms'),
                    '1-3_months' => __('1-3 months', 'travel-forms'),
                    '3-6_months' => __('3-6 months', 'travel-forms'),
                    '6-12_months' => __('6-12 months', 'travel-forms'),
                    'over_1_year' => __('Over 1 year', 'travel-forms'),
                    'undecided' => __('Just researching', 'travel-forms'),
                ],
            ],
            'newsletter' => [
                'type' => 'checkbox',
                'label' => __('Newsletter Subscription', 'travel-forms'),
                'placeholder' => __('Yes, I want to receive travel tips and special offers', 'travel-forms'),
                'required' => false,
            ],
            'consent' => [
                'type' => 'checkbox',
                'label' => __('Privacy Consent', 'travel-forms'),
                'placeholder' => __('I agree to receive the requested brochure and marketing communications', 'travel-forms'),
                'required' => true,
            ],
        ];

        $this->rules = [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email',
            'country' => 'max:100',
            'brochure_type' => 'required',
            'format' => 'required',
            'address' => 'max:300',
            'consent' => 'required',
        ];

        $this->field_types = [
            'name' => 'text',
            'email' => 'email',
            'country' => 'text',
            'brochure_type' => 'text',
            'format' => 'text',
            'address' => 'textarea',
            'travel_timeline' => 'text',
            'newsletter' => 'boolean',
            'consent' => 'boolean',
        ];
    }

    /**
     * Render the brochure request form.
     *
     * @param array $atts Form attributes
     *
     * @return void
     */
    public function render(array $atts = []): void
    {
        ?>
        <div class="travel-form brochure-form-wrapper">
            <?php if ($atts['show_title']): ?>
                <h2 class="form-title"><?php echo esc_html($atts['title']); ?></h2>
                <p class="form-description"><?php _e('Request our free travel brochures to plan your perfect Peru adventure.', 'travel-forms'); ?></p>
            <?php endif; ?>

            <form id="<?php echo esc_attr($this->form_id); ?>" class="travel-form-inner" data-form-id="<?php echo esc_attr($this->form_id); ?>">
                <?php foreach ($this->fields as $field_name => $field_config): ?>
                    <?php
                    // Show/hide address field based on format selection (handled by JS)
                    $conditional_class = ($field_name === 'address') ? 'conditional-field hidden' : '';
                    echo '<div class="' . $conditional_class . '">';
                    $this->render_field($field_name, $field_config);
                    echo '</div>';
                    ?>
                <?php endforeach; ?>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php _e('Request Brochure', 'travel-forms'); ?>
                    </button>
                </div>

                <div class="form-messages"></div>
            </form>
        </div>
        <?php
    }

    /**
     * Get success message for brochure form.
     *
     * @return string
     */
    protected function get_success_message(): string
    {
        return __('Thank you! Your brochure request has been received. Check your email for the digital version, or expect physical delivery within 10-15 business days.', 'travel-forms');
    }
}
