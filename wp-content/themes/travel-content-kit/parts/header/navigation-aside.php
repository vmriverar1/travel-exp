<?php
// Get all data from ACF options
$tour_title = get_header_option('aside_tour_title', 'Tour Packages');
$reviews_title = get_header_option('aside_reviews_title', 'Hear from travelers');
$reviews_text = get_header_option('aside_reviews_text', '+2315 Real stories traveling with us');
$faqs_title = get_header_option('aside_faqs_title', 'FAQs');
$faqs_text = get_header_option('aside_faqs_text', 'Clear, simple answers to help you plan with confidence.');
$faqs_url = get_header_option('aside_faqs_url', home_url('/faqs'));
$faqs_button = get_header_option('aside_faqs_button_text', 'Get my answers');
$tailor_title = get_header_option('aside_tailor_title', 'Tailor Made Tours');
$tailor_text = get_header_option('aside_tailor_text', 'Your trip, your way fully customized to your style, time and interests.');
$tailor_url = get_header_option('aside_tailor_url', home_url('/custom-tours'));
$tailor_button = get_header_option('aside_tailor_button_text', 'Design my journey');
$favorites_title = get_header_option('aside_favorites_title', 'Favorites');
$favorites_text = get_header_option('aside_favorites_text', 'Here you will find your chosen experiences of Machu Picchu Peru.');
$favorites_url = get_header_option('aside_favorites_url', home_url('/favorites'));
$favorites_button = get_header_option('aside_favorites_button_text', 'My Favs');
$contact_url = get_header_option('aside_contact_url', home_url('/contact'));
$contact_button = get_header_option('aside_contact_button_text', 'Contact Us');
$phone = get_header_option('header_phone', '+1-(888)-803-8004');
$phone_link = get_header_option('header_phone_link', '+18888038004');
$facebook = get_header_option('social_facebook', '');
$instagram = get_header_option('social_instagram', '');
$pinterest = get_header_option('social_pinterest', '');
$youtube = get_header_option('social_youtube', '');
$tiktok = get_header_option('social_tiktok', '');
?>

<aside id="aside-menu" class="nav-aside" aria-label="Mobile Navigation" hidden>
    <div class="nav-aside__overlay" aria-hidden="true"></div>

    <div class="nav-aside__panel">
        <!-- White Background Section (Logo + Two Columns) -->
        <div class="nav-aside__white-section">
            <!-- Header with Logo -->
            <div class="nav-aside__header">
                <?php get_template_part('parts/atoms/logo-aside'); ?>
                <?php get_template_part('parts/atoms/button-close'); ?>
            </div>

            <!-- Two Columns Layout -->
            <div class="nav-aside__columns">
                <!-- Column 1: Tour Packages, FAQs, Tailor Made -->
                <div class="nav-aside__column">
                    <!-- Tour Packages -->
                    <section class="nav-aside__section nav-aside__section--compact">
                        <h2 class="nav-aside__title"><?php echo esc_html($tour_title); ?></h2>
                        <?php
                        if (has_nav_menu('aside')) {
                            wp_nav_menu([
                                'theme_location' => 'aside',
                                'container' => false,
                                'menu_class' => 'nav-aside__list',
                                'fallback_cb' => false,
                                'depth' => 1,
                            ]);
                        }
                        ?>
                    </section>

                    <!-- FAQs -->
                    <section class="nav-aside__section nav-aside__section--compact">
                        <h2 class="nav-aside__title"><?php echo esc_html($faqs_title); ?></h2>
                        <p class="nav-aside__text"><?php echo esc_html($faqs_text); ?></p>
                        <a href="<?php echo esc_url($faqs_url); ?>" class="nav-aside__link">
                            <?php echo esc_html($faqs_button); ?> <span aria-hidden="true">›</span>
                        </a>
                    </section>

                    <!-- Tailor Made Tours -->
                    <section class="nav-aside__section nav-aside__section--compact">
                        <h2 class="nav-aside__title"><?php echo esc_html($tailor_title); ?></h2>
                        <p class="nav-aside__text"><?php echo esc_html($tailor_text); ?></p>
                        <a href="<?php echo esc_url($tailor_url); ?>" class="nav-aside__link">
                            <?php echo esc_html($tailor_button); ?> <span aria-hidden="true">›</span>
                        </a>
                    </section>
                </div>

                <!-- Column 2: Hear from travelers, Favorites -->
                <div class="nav-aside__column">
                    <!-- Hear from travelers -->
                    <section class="nav-aside__section nav-aside__section--compact nav-aside__section--reviews">
                        <h2 class="nav-aside__title"><?php echo esc_html($reviews_title); ?></h2>
                        <div class="nav-aside__section-container">
                            <p class="nav-aside__text"><?php echo esc_html($reviews_text); ?></p>
    
                            <!-- 5 Stars Rating -->
                            <div class="nav-aside__rating">
                                <svg class="nav-aside__star" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <svg class="nav-aside__star" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <svg class="nav-aside__star" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <svg class="nav-aside__star" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <svg class="nav-aside__star" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
    
                            <?php
                            $review_badges = function_exists('get_field') ? get_field('aside_review_badges', 'option') : [];
                            if ($review_badges):
                            ?>
                            <div class="nav-aside__review-badges">
                                <?php foreach ($review_badges as $badge):
                                    $badge_image = $badge['badge_image'] ?? '';
                                    $badge_url = $badge['badge_url'] ?? '';
                                    if ($badge_image):
                                        if ($badge_url):
                                ?>
                                    <a href="<?php echo esc_url($badge_url); ?>" class="nav-aside__review-badge" target="_blank" rel="noopener noreferrer">
                                        <img src="<?php echo esc_url($badge_image); ?>" alt="Review Badge" loading="lazy" />
                                    </a>
                                <?php else: ?>
                                    <div class="nav-aside__review-badge">
                                        <img src="<?php echo esc_url($badge_image); ?>" alt="Review Badge" loading="lazy" />
                                    </div>
                                <?php
                                        endif;
                                    endif;
                                endforeach;
                                ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <!-- Favorites -->
                    <section class="nav-aside__section nav-aside__section--compact nav-aside__section--favorites">
                        <h2 class="nav-aside__title nav-aside__title--with-icon">
                            <?php echo esc_html($favorites_title); ?>
                            <svg class="nav-aside__title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </h2>
                        <p class="nav-aside__text"><?php echo esc_html($favorites_text); ?></p>
                        <a href="<?php echo esc_url($favorites_url); ?>" class="nav-aside__link">
                            <?php echo esc_html($favorites_button); ?> <span aria-hidden="true">›</span>
                        </a>
                    </section>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Two Columns (Links + Contact) -->
        <div class="nav-aside__bottom-section">
            <div class="nav-aside__bottom-columns">
                <!-- Left Column: Secondary Links -->
                <div class="nav-aside__bottom-column">
                    <?php
                    if (has_nav_menu('aside-secondary')) {
                        wp_nav_menu([
                            'theme_location' => 'aside-secondary',
                            'container' => false,
                            'menu_class' => 'nav-aside__footer-list',
                            'fallback_cb' => false,
                            'depth' => 1,
                        ]);
                    }
                    ?>
                </div>

                <!-- Right Column: Contact -->
                <div class="nav-aside__bottom-column nav-aside__bottom-column--contact">
                    <a href="<?php echo esc_url($contact_url); ?>" class="nav-aside__contact-btn">
                        <?php echo esc_html($contact_button); ?>
                    </a>
                    <a href="tel:<?php echo esc_attr($phone_link); ?>" class="nav-aside__phone">
                        <?php echo esc_html($phone); ?>
                    </a>
                    <?php
                    $social_icons = function_exists('get_field') ? get_field('aside_social_icons', 'option') : [];
                    if ($social_icons):
                    ?>
                    <div class="nav-aside__social">
                        <?php
                        foreach ($social_icons as $icon):
                            $icon_type = $icon['icon_type'] ?? '';
                            $icon_url = $icon['icon_url'] ?? '';
                            if ($icon_type && $icon_url):
                                $icon_label = ucfirst($icon_type);
                        ?>
                        <a href="<?php echo esc_url($icon_url); ?>" aria-label="<?php echo esc_attr($icon_label); ?>" class="nav-aside__social-link" target="_blank" rel="noopener noreferrer">
                            <?php echo get_social_icon_svg($icon_type); ?>
                        </a>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</aside>
