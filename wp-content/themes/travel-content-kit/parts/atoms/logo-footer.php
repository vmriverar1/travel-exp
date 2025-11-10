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
        <?php endif; ?>
        <img src="https://cliente.sistemaveme.com/wp-content/uploads/2025/11/machu-picchu-peru-logo.png" alt="">
    </a>
    <p class="logo-footer__tagline"><?php echo esc_html($tagline); ?></p>
    <p class="logo-footer__slogan"><?php echo esc_html($slogan); ?></p>
</div>
