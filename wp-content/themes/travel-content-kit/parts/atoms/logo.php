<?php
// Detectar si estamos en una página de package
$is_package_page = is_singular('package') || is_post_type_archive('package');

// Logo por defecto (blanco para header con gradiente oscuro)
$logo_id = function_exists('get_field') ? get_field('header_logo', 'option') : false;

// Logo de colores para páginas de package (fondo blanco)
$logo_color_id = function_exists('get_field') ? get_field('header_logo_color', 'option') : false;

$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : false;
$logo_color_url = $logo_color_id ? wp_get_attachment_image_url($logo_color_id, 'full') : false;
?>

<a href="<?php echo esc_url(home_url('/')); ?>" class="logo" aria-label="<?php bloginfo('name'); ?> - Home">
    <?php if ($is_package_page) : ?>
        <img src="<?php echo esc_url($logo_color_url); ?>" alt="<?php bloginfo('name'); ?>" class="logo__image" />
    <?php else: ?>
        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo('name'); ?>" class="logo__image" />
        <img src="<?php echo esc_url($logo_color_url); ?>" alt="<?php bloginfo('name'); ?>" class="logo__image color" />
    <?php endif; ?>
</a>
