<?php
/**
 * Template: Hero Contact Form Block
 *
 * Hero form with full-bleed background image and translucent overlay card
 *
 * Available variables:
 * @var string $block_id           Unique block ID
 * @var string $class_name          Block CSS classes
 * @var array  $form                Form configuration data
 * @var bool   $is_preview          Whether in preview mode
 * @var int    $current_package_id  Current package ID (if on package page)
 * @var string $package_title       Current package title
 */

// Get ACF fields
$bg_desktop = get_field('hero_background_desktop');
$bg_mobile = get_field('hero_background_mobile');
$focal_position = get_field('hero_focal_position') ?: 'center center';
$height_desktop = get_field('hero_height_desktop') ?: 700;
$height_tablet = get_field('hero_height_tablet') ?: 600;
$height_mobile = get_field('hero_height_mobile') ?: 500;

$overlay_color = get_field('overlay_color') ?: '#0a797e';
$overlay_opacity = get_field('overlay_opacity') ?: 28;
$overlay_blur = get_field('overlay_blur') ?: 8;
$card_radius = get_field('card_border_radius') ?: 26;
$card_max_width = get_field('card_max_width') ?: 1100;
$card_padding_desktop = get_field('card_padding_desktop') ?: 60;
$card_padding_mobile = get_field('card_padding_mobile') ?: 32;

$title_part_1 = get_field('title_part_1') ?: 'Let Our Team Of ';
$title_highlight_1 = get_field('title_highlight_1') ?: 'Local Experts';
$title_highlight_1_color = get_field('title_highlight_1_color') ?: '#0a797e';
$title_part_2 = get_field('title_part_2') ?: ' Help You Choose Your Perfect Peruvian Adventure. ';
$title_highlight_2 = get_field('title_highlight_2') ?: 'Get in touch today!';
$title_highlight_2_color = get_field('title_highlight_2_color') ?: '#e78c85';

// Enable flags with proper defaults (always true if not explicitly set to false)
$enable_first_name = get_field('enable_first_name');
$enable_first_name = ($enable_first_name === false || $enable_first_name === '') ? true : $enable_first_name;

$enable_last_name = get_field('enable_last_name');
$enable_last_name = ($enable_last_name === false || $enable_last_name === '') ? true : $enable_last_name;

$enable_email = get_field('enable_email');
$enable_email = ($enable_email === false || $enable_email === '') ? true : $enable_email;

$enable_phone = get_field('enable_phone');
$enable_phone = ($enable_phone === false || $enable_phone === '') ? true : $enable_phone;

$enable_country = get_field('enable_country');
$enable_country = ($enable_country === false || $enable_country === '') ? true : $enable_country;

$country_list = get_field('country_list');

$enable_package = get_field('enable_package');
$enable_package = ($enable_package === false || $enable_package === '') ? true : $enable_package;

$package_source = get_field('package_source') ?: 'cpt';
$package_manual_list = get_field('package_manual_list');

$enable_message = get_field('enable_message');
$enable_message = ($enable_message === false || $enable_message === '') ? true : $enable_message;

$message_placeholder = get_field('message_placeholder') ?: 'Questions / Comments / Useful Information';

$legal_notice = get_field('legal_notice') ?: '* By submitting this form, you agree to our <a href="/privacy-policy">privacy policy</a>';
$legal_chip_bg = get_field('legal_chip_bg') ?: 'rgba(255,255,255,0.12)';
$cta_label = get_field('cta_label') ?: 'Connect With Us';
$cta_bg_color = get_field('cta_bg_color') ?: '#e78c85';
$cta_text_color = get_field('cta_text_color') ?: '#ffffff';

$recipient_email = get_field('recipient_email') ?: get_option('admin_email');
$success_message = get_field('success_message') ?: 'Thank you! We\'ll be in touch within 24 hours.';
$error_message = get_field('error_message') ?: 'Something went wrong. Please try again or contact us directly.';
$enable_ajax = get_field('enable_ajax') !== false ? get_field('enable_ajax') : true;

// Background images with fallbacks
$bg_desktop_url = $is_preview && !$bg_desktop
    ? 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=1920&h=680&fit=crop'
    : ($bg_desktop['url'] ?? '');

$bg_mobile_url = $bg_mobile['url'] ?? $bg_desktop_url;

// Default country list if not provided
$countries = [];
if ($enable_country) {
    if (!empty($country_list)) {
        $countries = array_filter(array_map('trim', explode("\n", $country_list)));
    } else {
        // Default countries
        $countries = ['United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Spain', 'Italy', 'Brazil', 'Argentina', 'Chile', 'Peru', 'Mexico', 'Other'];
    }
}

// Package list
$packages = [];
if ($enable_package) {
    if ($package_source === 'manual' && !empty($package_manual_list)) {
        $packages = array_filter(array_map('trim', explode("\n", $package_manual_list)));
    } else {
        // Load from CPT
        $package_posts = get_posts([
            'post_type' => 'package',
            'posts_per_page' => 50,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status' => 'publish'
        ]);
        foreach ($package_posts as $package) {
            $packages[$package->ID] = $package->post_title;
        }
    }
}
?>

<section
    id="<?php echo esc_attr($block_id); ?>"
    class="hero-form <?php echo esc_attr($class_name); ?>"
    style="
        --hero-height-desktop: <?php echo esc_attr($height_desktop); ?>px;
        --hero-height-tablet: <?php echo esc_attr($height_tablet); ?>px;
        --hero-height-mobile: <?php echo esc_attr($height_mobile); ?>px;
        --overlay-color: <?php echo esc_attr($overlay_color); ?>;
        --overlay-opacity: <?php echo esc_attr($overlay_opacity / 100); ?>;
        --overlay-blur: <?php echo esc_attr($overlay_blur); ?>px;
        --card-radius: <?php echo esc_attr($card_radius); ?>px;
        --card-max-width: <?php echo esc_attr($card_max_width); ?>px;
        --card-padding-desktop: <?php echo esc_attr($card_padding_desktop); ?>px;
        --card-padding-mobile: <?php echo esc_attr($card_padding_mobile); ?>px;
        --cta-bg-color: <?php echo esc_attr($cta_bg_color); ?>;
        --cta-text-color: <?php echo esc_attr($cta_text_color); ?>;
        --legal-chip-bg: <?php echo esc_attr($legal_chip_bg); ?>;
    "
    data-enable-ajax="<?php echo $enable_ajax ? '1' : '0'; ?>"
>
    <!-- Background Image -->
    <div class="hero-form__background">
        <picture>
            <?php if ($bg_mobile_url): ?>
                <source media="(max-width: 767px)" srcset="<?php echo esc_url($bg_mobile_url); ?>">
            <?php endif; ?>
            <img
                src="<?php echo esc_url($bg_desktop_url); ?>"
                alt="Contact us background"
                style="object-position: <?php echo esc_attr($focal_position); ?>;"
            >
        </picture>
    </div>

    <!-- Translucent Overlay -->
    <div class="hero-form__overlay"></div>

    <!-- Form Card -->
    <div class="hero-form__card">

        <!-- Title with colored spans -->
        <h2 class="hero-form__title">
            <?php echo esc_html($title_part_1); ?><span style="color: <?php echo esc_attr($title_highlight_1_color); ?>;"><?php echo esc_html($title_highlight_1); ?></span><?php echo esc_html($title_part_2); ?><span style="color: <?php echo esc_attr($title_highlight_2_color); ?>;"><?php echo esc_html($title_highlight_2); ?></span>
        </h2>

        <!-- Form -->
        <form
            class="hero-form__form"
            id="<?php echo esc_attr($block_id); ?>-form"
            method="post"
            data-recipient="<?php echo esc_attr($recipient_email); ?>"
            data-package-id="<?php echo esc_attr($current_package_id); ?>"
        >

            <!-- Hidden fields -->
            <input type="hidden" name="action" value="travel_hero_form_submit">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('travel_hero_form'); ?>">
            <input type="hidden" name="package_id" value="<?php echo esc_attr($current_package_id); ?>">

            <!-- Form Grid (2 columns on desktop) -->
            <div class="form-grid">

                <!-- Row 1: First Name + Last Name -->
                <?php if ($enable_first_name): ?>
                    <div class="form-field">
                        <label for="<?php echo esc_attr($block_id); ?>-first-name" class="sr-only">First Name *</label>
                        <input
                            type="text"
                            id="<?php echo esc_attr($block_id); ?>-first-name"
                            name="first_name"
                            placeholder="First Name*"
                            required
                            aria-required="true"
                        >
                    </div>
                <?php endif; ?>

                <?php if ($enable_last_name): ?>
                    <div class="form-field">
                        <label for="<?php echo esc_attr($block_id); ?>-last-name" class="sr-only">Last Name *</label>
                        <input
                            type="text"
                            id="<?php echo esc_attr($block_id); ?>-last-name"
                            name="last_name"
                            placeholder="Last Name*"
                            required
                            aria-required="true"
                        >
                    </div>
                <?php endif; ?>

                <!-- Row 2: Email + Phone -->
                <?php if ($enable_email): ?>
                    <div class="form-field">
                        <label for="<?php echo esc_attr($block_id); ?>-email" class="sr-only">Email *</label>
                        <input
                            type="email"
                            id="<?php echo esc_attr($block_id); ?>-email"
                            name="email"
                            placeholder="E-mail Address*"
                            required
                            aria-required="true"
                        >
                    </div>
                <?php endif; ?>

                <?php if ($enable_phone): ?>
                    <div class="form-field">
                        <label for="<?php echo esc_attr($block_id); ?>-phone" class="sr-only">Phone</label>
                        <input
                            type="tel"
                            id="<?php echo esc_attr($block_id); ?>-phone"
                            name="phone"
                            placeholder="Cellphone / Telephone"
                        >
                    </div>
                <?php endif; ?>

                <!-- Row 3: Country + Package (with addon) -->
                <?php if ($enable_country): ?>
                    <div class="form-field">
                        <label for="<?php echo esc_attr($block_id); ?>-country" class="sr-only">Country *</label>
                        <div class="select-wrapper">
                            <select
                                id="<?php echo esc_attr($block_id); ?>-country"
                                name="country"
                                required
                                aria-required="true"
                            >
                                <option value="">Select Country*</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?php echo esc_attr($country); ?>">
                                        <?php echo esc_html($country); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="select-addon">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($enable_package): ?>
                    <div class="form-field">
                        <label for="<?php echo esc_attr($block_id); ?>-package" class="sr-only">Package</label>
                        <div class="select-wrapper">
                            <select
                                id="<?php echo esc_attr($block_id); ?>-package"
                                name="package_interest"
                            >
                                <option value="">Select Package</option>
                                <?php foreach ($packages as $package_id => $package_title): ?>
                                    <option
                                        value="<?php echo esc_attr($package_id); ?>"
                                        <?php selected($package_id, $current_package_id); ?>
                                    >
                                        <?php
                                        // Truncate long titles
                                        $truncated = strlen($package_title) > 50
                                            ? substr($package_title, 0, 47) . '...'
                                            : $package_title;
                                        echo esc_html($truncated);
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="select-addon">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Row 4: Message (full width) -->
                <?php if ($enable_message): ?>
                    <div class="form-field form-field--full">
                        <label for="<?php echo esc_attr($block_id); ?>-message" class="sr-only">Message *</label>
                        <textarea
                            id="<?php echo esc_attr($block_id); ?>-message"
                            name="message"
                            rows="4"
                            placeholder="<?php echo esc_attr($message_placeholder); ?>*"
                            required
                            aria-required="true"
                        ></textarea>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Legal Notice (chip) -->
            <div class="legal-chip">
                <?php echo wp_kses_post($legal_notice); ?>
            </div>

            <!-- CTA Button -->
            <button type="submit" class="btn-cta">
                <span class="btn-cta__text"><?php echo esc_html($cta_label); ?></span>
                <span class="btn-cta__loading" hidden>
                    <svg class="spinner" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <circle class="spinner__circle" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round"/>
                    </svg>
                    <?php _e('Sending...', 'travel-blocks'); ?>
                </span>
            </button>

            <!-- Messages -->
            <div class="hero-form__messages">
                <div class="hero-form__success" hidden role="alert">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="22 4 12 14.01 9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?php echo esc_html($success_message); ?></span>
                </div>
                <div class="hero-form__error" hidden role="alert">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span><?php echo esc_html($error_message); ?></span>
                </div>
            </div>

        </form>

    </div>

</section>
