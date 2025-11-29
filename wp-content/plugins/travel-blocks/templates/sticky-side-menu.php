<?php

/**
 * Template: Sticky Side Menu
 *
 * @package Travel\Blocks
 * @var array $data
 */

// Extract data
$block_id = $data['block_id'] ?? 'ssm-' . uniqid();
$show_phone = $data['show_phone'] ?? true;
$phone_number = $data['phone_number'] ?? '+51 999 999 999';
$phone_icon = $data['phone_icon'] ?? true;
$show_cta = $data['show_cta'] ?? true;
$cta_text = $data['cta_text'] ?? 'Contactar';
$cta_url = $data['cta_url'] ?? '#';
$cta_style = $data['cta_style'] ?? 'primary';
$show_hamburger = $data['show_hamburger'] ?? true;
$menu_location = $data['menu_location'] ?? null;
$offset_value = $data['offset_value'] ?? 20;
$offset_unit = $data['offset_unit'] ?? 'vh';
$shadow_intensity = $data['shadow_intensity'] ?? 5;
$hide_mobile = $data['hide_mobile'] ?? false;
$is_preview = $data['is_preview'] ?? false;

// Calculate shadow based on intensity (1-10)
$shadow_alpha = ($shadow_intensity / 10) * 0.5; // Max alpha 0.5
$shadow_blur = 8 + ($shadow_intensity * 2); // 8px to 28px

// Build classes
$wrapper_classes = ['wp-block-travel-sticky-side-menu'];
if ($hide_mobile) {
    $wrapper_classes[] = 'hide-mobile';
}

$menu_classes = ['sticky-side-menu'];
?>

<div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>" id="<?php echo esc_attr($block_id); ?>">
    <div class="<?php echo esc_attr(implode(' ', $menu_classes)); ?>"
        style="--offset-top: <?php echo esc_attr($offset_value . $offset_unit); ?>; --shadow-blur: <?php echo esc_attr($shadow_blur); ?>px; --shadow-alpha: <?php echo esc_attr($shadow_alpha); ?>;"
        data-sticky-menu>

        <?php if ($show_phone && $phone_number): ?>
            <!-- Teléfono -->
            <a href="tel:<?php echo esc_attr(str_replace(' ', '', $phone_number)); ?>"
                class="sticky-side-menu__phone"
                aria-label="<?php esc_attr_e('Llamar', 'travel-blocks'); ?>">
                <?php if ($phone_icon): ?>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                    </svg>
                <?php endif; ?>
                <span><?php echo esc_html($phone_number); ?></span>
            </a>
        <?php endif; ?>

        <?php if ($show_cta && $cta_text): ?>
            <!-- CTA Button -->
            <a href="<?php echo esc_url($cta_url); ?>"
                class="sticky-side-menu__cta btn-cta btn-cta--<?php echo esc_attr($cta_style); ?>"
                aria-label="<?php echo esc_attr($cta_text); ?>">
                <?php echo esc_html($cta_text); ?>
            </a>
        <?php endif; ?>

        <?php if ($show_hamburger): ?>
            <!-- Hamburger Button -->
            <button class="sticky-side-menu__hamburger"
                aria-label="<?php esc_attr_e('Abrir menú', 'travel-blocks'); ?>"
                aria-expanded="false"
                data-ssm-hamburger>
                <svg width="30" height="20" viewBox="0 0 30 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 20H30V16.6664H0V20ZM0 11.6664H30V8.33359H0V11.6664ZM0 0V3.33359H30V0H0Z" fill="black" />
                </svg>
            </button>
        <?php endif; ?>
    </div>
</div>