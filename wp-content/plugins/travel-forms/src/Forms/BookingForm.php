<?php
/**
 * Booking Form
 *
 * Tour booking/reservation request form.
 *
 * @package Travel\Forms\Forms
 * @since 1.0.0
 */

namespace Travel\Forms\Forms;

use Travel\Forms\Core\FormBase;

class BookingForm extends FormBase
{
    public function __construct()
    {
        parent::__construct();

        $this->form_id = 'booking-form';
        $this->form_name = __('Booking Form', 'travel-forms');

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
                'required' => true,
            ],
            'country' => [
                'type' => 'text',
                'label' => __('Country', 'travel-forms'),
                'placeholder' => __('Your country', 'travel-forms'),
                'required' => true,
            ],
            'tour_name' => [
                'type' => 'text',
                'label' => __('Tour Name', 'travel-forms'),
                'placeholder' => __('Which tour are you interested in?', 'travel-forms'),
                'required' => true,
            ],
            'travel_date' => [
                'type' => 'date',
                'label' => __('Preferred Travel Date', 'travel-forms'),
                'placeholder' => __('Select date', 'travel-forms'),
                'required' => true,
            ],
            'num_travelers' => [
                'type' => 'number',
                'label' => __('Number of Travelers', 'travel-forms'),
                'placeholder' => __('How many people?', 'travel-forms'),
                'required' => true,
            ],
            'accommodation_preference' => [
                'type' => 'select',
                'label' => __('Accommodation Preference', 'travel-forms'),
                'required' => false,
                'options' => [
                    '' => __('Select...', 'travel-forms'),
                    'budget' => __('Budget', 'travel-forms'),
                    'standard' => __('Standard', 'travel-forms'),
                    'deluxe' => __('Deluxe', 'travel-forms'),
                    'luxury' => __('Luxury', 'travel-forms'),
                ],
            ],
            'special_requests' => [
                'type' => 'textarea',
                'label' => __('Special Requests', 'travel-forms'),
                'placeholder' => __('Any dietary restrictions, special occasions, or other requests?', 'travel-forms'),
                'required' => false,
            ],
            'how_did_you_hear' => [
                'type' => 'select',
                'label' => __('How did you hear about us?', 'travel-forms'),
                'required' => false,
                'options' => [
                    '' => __('Select...', 'travel-forms'),
                    'google' => __('Google Search', 'travel-forms'),
                    'facebook' => __('Facebook', 'travel-forms'),
                    'instagram' => __('Instagram', 'travel-forms'),
                    'tripadvisor' => __('TripAdvisor', 'travel-forms'),
                    'recommendation' => __('Friend/Family Recommendation', 'travel-forms'),
                    'other' => __('Other', 'travel-forms'),
                ],
            ],
            'consent' => [
                'type' => 'checkbox',
                'label' => __('Privacy Consent', 'travel-forms'),
                'placeholder' => __('I agree to receive booking confirmations and travel updates', 'travel-forms'),
                'required' => true,
            ],
        ];

        $this->rules = [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email',
            'phone' => 'required|phone',
            'country' => 'required|max:100',
            'tour_name' => 'required|max:200',
            'travel_date' => 'required|date',
            'num_travelers' => 'required|numeric',
            'special_requests' => 'max:500',
            'consent' => 'required',
        ];

        $this->field_types = [
            'name' => 'text',
            'email' => 'email',
            'phone' => 'phone',
            'country' => 'text',
            'tour_name' => 'text',
            'travel_date' => 'date',
            'num_travelers' => 'integer',
            'accommodation_preference' => 'text',
            'special_requests' => 'textarea',
            'how_did_you_hear' => 'text',
            'consent' => 'boolean',
        ];
    }

    /**
     * Render the booking form.
     *
     * @param array $atts Form attributes
     *
     * @return void
     */
    public function render(array $atts = []): void
    {
        // Pre-fill tour name from URL parameter
        $tour_name = $_GET['tour'] ?? '';

        ?>
        <div class="travel-form booking-form-wrapper">
            <?php if ($atts['show_title']): ?>
                <h2 class="form-title"><?php echo esc_html($atts['title']); ?></h2>
                <p class="form-description"><?php _e('Fill out the form below to request a booking. We will contact you within 24 hours to confirm availability and details.', 'travel-forms'); ?></p>
            <?php endif; ?>

            <form id="<?php echo esc_attr($this->form_id); ?>" class="travel-form-inner" data-form-id="<?php echo esc_attr($this->form_id); ?>">
                <?php foreach ($this->fields as $field_name => $field_config): ?>
                    <?php
                    // Pre-fill tour name if available
                    if ($field_name === 'tour_name' && $tour_name) {
                        echo '<input type="hidden" name="tour_name" value="' . esc_attr($tour_name) . '" />';
                        echo '<div class="form-field form-field-text"><label>' . esc_html($field_config['label']) . '</label>';
                        echo '<div class="form-field-value">' . esc_html($tour_name) . '</div></div>';
                    } else {
                        $this->render_field($field_name, $field_config);
                    }
                    ?>
                <?php endforeach; ?>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php _e('Request Booking', 'travel-forms'); ?>
                    </button>
                </div>

                <div class="form-messages"></div>
            </form>
        </div>
        <?php
    }

    /**
     * Get success message for booking form.
     *
     * @return string
     */
    protected function get_success_message(): string
    {
        return __('Thank you for your booking request! We will contact you within 24 hours to confirm availability and provide payment details.', 'travel-forms');
    }
}
