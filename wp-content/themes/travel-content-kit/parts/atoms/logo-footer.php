<?php
/**
 * Atom: Footer Logo
 * Logo principal del footer con lema incluido
 */

// Check if ACF is available
if (!function_exists('get_field')) {
    function get_field($field, $context = null) { return null; }
}

$footer_logo_id = get_field('footer_logo', 'option');
$tagline = get_bloginfo('description') ?: 'I am guide, I am guardian, I am bridge.';
$slogan = 'Fly straight to the heart of the Inca land';
?>

<div class="logo-footer">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-footer__link" aria-label="<?php bloginfo('name'); ?> - Home">
        <?php if ($footer_logo_id): ?>
            <?php echo wp_get_attachment_image($footer_logo_id, 'medium', false, [
                'class' => 'logo-footer__image',
                'alt' => get_bloginfo('name'),
            ]); ?>
        <?php else: ?>
            <svg class="logo-footer__icon" width="120" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <!-- Simplified "M" monogram for Machu Picchu -->
                <path d="M8 32V8L20 20L32 8V32H28V16L20 24L12 16V32H8Z" fill="currentColor"/>
            </svg>
            <span class="logo-footer__text"><?php bloginfo('name'); ?></span>
        <?php endif; ?>
    </a>
    <p class="logo-footer__tagline"><?php echo esc_html($tagline); ?></p>
    <p class="logo-footer__slogan"><?php echo esc_html($slogan); ?></p>
</div>
