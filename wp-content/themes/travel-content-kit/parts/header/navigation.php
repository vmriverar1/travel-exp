<nav class="nav-main" aria-label="Primary Navigation">
    <?php
    if (has_nav_menu('primary')) {
        wp_nav_menu([
            'theme_location' => 'primary',
            'container' => false,
            'menu_class' => 'nav-main__list',
            'fallback_cb' => false,
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'depth' => 1, // Single level menu
        ]);
    }
    ?>
</nav>
