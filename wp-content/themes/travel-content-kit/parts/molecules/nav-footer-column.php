<?php
/**
 * Molecule: Footer Navigation Column
 * Columna de navegación para footer
 *
 * @param string $menu_location - ubicación del menú registrado
 * @param string $title - Título opcional para la columna
 * @param bool $collapsible - Si debe ser collapsible en mobile (default: true)
 */

$menu_location = $args['menu_location'] ?? '';
$title = $args['title'] ?? '';
$collapsible = $args['collapsible'] ?? true;
$unique_id = wp_unique_id('footer-nav-');

if (!$menu_location || !has_nav_menu($menu_location)) {
    return;
}

$menu_object = wp_get_nav_menu_object(get_nav_menu_locations()[$menu_location]);
$menu_title = $title ?: ($menu_object ? $menu_object->name : '');
?>

<div class="nav-footer-column <?php echo $collapsible ? 'nav-footer-column--collapsible' : ''; ?>">
    <?php if ($menu_title): ?>
        <h3
            class="nav-footer-column__title"
            <?php if ($collapsible): ?>
                role="button"
                aria-expanded="false"
                aria-controls="<?php echo esc_attr($unique_id); ?>"
                tabindex="0"
            <?php endif; ?>
        >
            <?php echo esc_html($menu_title); ?>
        </h3>
    <?php endif; ?>

    <div
        class="nav-footer-column__content"
        id="<?php echo esc_attr($unique_id); ?>"
        <?php if ($collapsible): ?>
            aria-hidden="true"
        <?php endif; ?>
    >
        <?php
        wp_nav_menu([
            'theme_location' => $menu_location,
            'container' => false,
            'menu_class' => 'nav-footer-column__list',
            'fallback_cb' => false,
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'depth' => 1,
        ]);
        ?>
    </div>
</div>
