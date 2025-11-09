<style>
    /* Adjust for WordPress admin bar when logged in */
    body.admin-bar .header {
        top: 32px;
    }
    @media screen and (max-width: 782px) {
        body.admin-bar .header {
            top: 46px;
        }
    }

    .nav-main__list a::after {
        background-color: #ffffff !important;
    }
</style>
<header class="header" id="header">
    <div class="header__container">
        <!-- Logo -->
        <div class="header__logo">
            <?php get_template_part('parts/atoms/logo'); ?>
        </div>

        <div class="header__menus">
            <div class="header__menu-top">
                <!-- Desktop Navigation -->
                <div class="header__nav">
                    <?php get_template_part('parts/header/navigation'); ?>
                </div>
        
                <!-- Right Actions -->
                <div class="header__actions">
                    <!-- Phone -->
                    <?php
                    $phone = get_header_option('header_phone', '+1-(888)-803-8004');
                    $phone_link = get_header_option('header_phone_link', '+18888038004');
                    ?>
                    <a href="tel:<?php echo esc_attr($phone_link); ?>" class="header__phone">
                        <span class="header__phone-text"><?php echo esc_html($phone); ?></span>
                    </a>
        
                    <!-- Language Selector -->
                    <?php $language = get_header_option('header_language', 'EN'); ?>
                    <div class="header__lang">
                        <button type="button" class="lang-selector" aria-label="Select language">
                            <span><?php echo esc_html($language); ?></span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                    </div>
        
                    <!-- Favorites -->
                    <?php $favorites_url = get_header_option('header_favorites_url', home_url('/favorites')); ?>
                    <a href="<?php echo esc_url($favorites_url); ?>" class="header__fav" aria-label="View favorites">
                        <span class="header__fav-text">Fav</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </a>
        
                    <!-- Hamburger Menu -->
                    <div class="header__hamburger">
                        <?php get_template_part('parts/atoms/button-hamburger'); ?>
                    </div>
                </div>
            </div>
        
            <!-- Secondary Navigation (Main Pages) -->
            <?php if (has_nav_menu('secondary')): ?>
            <div class="header__secondary-nav">
                <?php
                    wp_nav_menu([
                        'theme_location' => 'secondary',
                        'container'      => false,
                        'menu_class'     => 'header__secondary-list',
                        'fallback_cb'    => false,
                        'depth'          => 3, // Permite hasta 3 niveles
                        'walker'         => new Walker_Nav_Menu(), // Usa el walker por defecto
                    ]);
                ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Aside Menu -->
<?php get_template_part('parts/header/navigation-aside'); ?>
