<?php
/**
 * Template: Contact Planner Form
 *
 * Formulario de contacto con imagen de fondo y panel flotante
 *
 * Available variables:
 * @var string $block_id
 * @var string $class_name
 * @var string $background_image
 * @var int    $overlay_opacity
 * @var string $panel_title
 * @var string $panel_subtitle
 * @var string $highlight_word
 * @var string $button_text
 * @var string $success_message
 * @var bool   $is_preview
 * @var int    $current_package_id
 * @var string $package_title
 */

use Travel\Blocks\Helpers\IconHelper;

// Calculate overlay opacity as decimal
$overlay_alpha = $overlay_opacity / 100;
?>

<div
    id="<?php echo esc_attr($block_id); ?>"
    class="<?php echo esc_attr($class_name); ?>"
    style="background-image: url('<?php echo esc_url($background_image); ?>');"
>
    <!-- Overlay -->
    <div class="contact-planner-form__overlay" style="background-color: rgba(0, 0, 0, <?php echo esc_attr($overlay_alpha); ?>);"></div>

    <!-- Floating Panel -->
    <div class="contact-planner-form__panel">

        <!-- Panel Header -->
        <div class="contact-planner-form__header">
            <h2 class="contact-planner-form__title">
                <?php
                // Highlight specific word in title
                if (!empty($highlight_word) && stripos($panel_title, $highlight_word) !== false) {
                    $highlighted_title = preg_replace(
                        '/\b(' . preg_quote($highlight_word, '/') . ')\b/i',
                        '<span class="highlight">$1</span>',
                        $panel_title
                    );
                    echo wp_kses_post($highlighted_title);
                } else {
                    echo esc_html($panel_title);
                }
                ?>
            </h2>
            <?php if (!empty($panel_subtitle)): ?>
                <p class="contact-planner-form__subtitle"><?php echo esc_html($panel_subtitle); ?></p>
            <?php endif; ?>
        </div>

        <!-- Form -->
        <form class="contact-planner-form__form" data-package-id="<?php echo esc_attr($current_package_id); ?>" data-package-title="<?php echo esc_attr($package_title); ?>">

            <div class="contact-planner-form__grid">

                <!-- First Name -->
                <div class="contact-planner-form__field">
                    <label for="<?php echo esc_attr($block_id); ?>-first-name">
                        <?php _e('First Name', 'travel-blocks'); ?>
                    </label>
                    <input
                        type="text"
                        id="<?php echo esc_attr($block_id); ?>-first-name"
                        name="first_name"
                        required
                        placeholder="<?php esc_attr_e('John', 'travel-blocks'); ?>"
                    />
                </div>

                <!-- Email -->
                <div class="contact-planner-form__field">
                    <label for="<?php echo esc_attr($block_id); ?>-email">
                        <?php _e('Email', 'travel-blocks'); ?>
                    </label>
                    <input
                        type="email"
                        id="<?php echo esc_attr($block_id); ?>-email"
                        name="email"
                        required
                        placeholder="<?php esc_attr_e('john@example.com', 'travel-blocks'); ?>"
                    />
                </div>

                <!-- Country -->
                <div class="contact-planner-form__field">
                    <label for="<?php echo esc_attr($block_id); ?>-country">
                        <?php _e('Country', 'travel-blocks'); ?>
                    </label>
                    <input
                        type="text"
                        id="<?php echo esc_attr($block_id); ?>-country"
                        name="country"
                        placeholder="<?php esc_attr_e('United States', 'travel-blocks'); ?>"
                    />
                </div>

                <!-- Travel Dates -->
                <div class="contact-planner-form__field">
                    <label for="<?php echo esc_attr($block_id); ?>-travel-dates">
                        <?php _e('Travel Dates', 'travel-blocks'); ?>
                    </label>
                    <input
                        type="text"
                        id="<?php echo esc_attr($block_id); ?>-travel-dates"
                        name="travel_dates"
                        placeholder="<?php esc_attr_e('June 2025', 'travel-blocks'); ?>"
                    />
                </div>

                <!-- Group Size -->
                <div class="contact-planner-form__field">
                    <label for="<?php echo esc_attr($block_id); ?>-group-size">
                        <?php _e('Group Size', 'travel-blocks'); ?>
                    </label>
                    <select
                        id="<?php echo esc_attr($block_id); ?>-group-size"
                        name="group_size"
                    >
                        <option value=""><?php _e('Select...', 'travel-blocks'); ?></option>
                        <option value="1">1 person</option>
                        <option value="2">2 people</option>
                        <option value="3-4">3-4 people</option>
                        <option value="5-8">5-8 people</option>
                        <option value="9+">9+ people</option>
                    </select>
                </div>

                <!-- Call Preference -->
                <div class="contact-planner-form__field contact-planner-form__field--checkbox">
                    <label>
                        <input
                            type="checkbox"
                            id="<?php echo esc_attr($block_id); ?>-call-preference"
                            name="call_preference"
                            value="yes"
                        />
                        <span><?php _e('I prefer to be contacted by phone', 'travel-blocks'); ?></span>
                    </label>
                </div>

            </div>

            <!-- Submit Button -->
            <div class="contact-planner-form__submit">
                <button type="submit" class="contact-planner-form__button">
                    <?php echo esc_html($button_text); ?>
                </button>
            </div>

            <!-- Messages -->
            <div class="contact-planner-form__messages">
                <div class="contact-planner-form__success" style="display: none;">
                    <?php echo IconHelper::get_icon_svg('check-circle', 24, 'var(--color-success)'); ?>
                    <span><?php echo esc_html($success_message); ?></span>
                </div>
                <div class="contact-planner-form__error" style="display: none;">
                    <?php echo IconHelper::get_icon_svg('alert-circle', 24, 'var(--color-error)'); ?>
                    <span><?php _e('There was an error. Please try again.', 'travel-blocks'); ?></span>
                </div>
            </div>

        </form>

    </div>
</div>
