<?php
// Detectar si estamos en una página de package
$is_package_page = is_singular('package') || is_post_type_archive('package');

// Logo por defecto (blanco para header con gradiente oscuro)
$logo_id = function_exists('get_field') ? get_field('header_logo', 'option') : false;

// Logo de colores para páginas de package (fondo blanco)
$logo_color_id = function_exists('get_field') ? get_field('header_logo_color', 'option') : false;

// Si estamos en package y existe logo de colores, usarlo
if ($is_package_page && $logo_color_id) {
    $logo_id = $logo_color_id;
}

$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : false;
?>

<a href="<?php echo esc_url(home_url('/')); ?>" class="logo" aria-label="<?php bloginfo('name'); ?> - Home">
    <?php if ($logo_url): ?>
        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo('name'); ?>" class="logo__image" />
    <?php else: ?>
        <!-- Fallback SVG logo if no custom logo is set -->
        <svg class="logo__icon" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <!-- Simplified "M" monogram for Machu Picchu -->
            <path d="M8 32V8L20 20L32 8V32H28V16L20 24L12 16V32H8Z" fill="currentColor"/>
        </svg>
        <span class="logo__text">Machu Picchu Peru.com</span>
    <?php endif; ?>
</a>
