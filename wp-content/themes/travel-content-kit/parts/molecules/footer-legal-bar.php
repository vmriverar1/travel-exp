<?php
/**
 * Molecule: Footer Legal Bar
 * Barra inferior con información legal y enlaces
 */

// Check if ACF is available
if (!function_exists('get_field')) {
    function get_field($field, $context = null) { return null; }
}

$company_name = get_field('company_name', 'option') ?: 'Machu Picchu Peru by Valencia Travel Cusco, Inc.';
$current_year = date('Y');
?>

<div class="footer-legal-bar">
    <div class="footer-legal-bar__container">
        <div class="footer-legal-bar__copyright">
            <p class="footer-legal-bar__text">
                &copy;<?php echo esc_html($current_year); ?> <?php echo esc_html($company_name); ?>. All Rights Reserved
            </p>
        </div>

        <nav class="footer-legal-bar__nav" aria-label="Legal Navigation">
            <?php
            if (has_nav_menu('footer-legal')) {
                wp_nav_menu([
                    'theme_location' => 'footer-legal',
                    'container' => false,
                    'menu_class' => 'footer-legal-bar__list',
                    'fallback_cb' => false,
                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'depth' => 1,
                ]);
            }
            ?>
        </nav>
    </div>
</div>
