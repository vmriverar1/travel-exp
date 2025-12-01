<?php
/**
 * Template: Posts Carousel (Material Design)
 *
 * Desktop: Grid 3 columnas con hover effect
 * Mobile: Slider Material Design
 * ACF Repeater para control manual
 *
 * @var array $data Block data and settings
 */

// Log template start
if (function_exists('travel_info')) {
    travel_info('Template posts-carousel iniciado', [
        'data_keys' => array_keys($data),
    ]);
}

// Get cards from data
$cards = $data['cards'] ?? [];

if (function_exists('travel_info')) {
    travel_info('Cards en template', [
        'count' => count($cards),
        'is_array' => is_array($cards),
    ]);
}

// Get global settings
$card_style = $data['card_style'] ?? 'overlay';
$button_color_variant = $data['button_color_variant'] ?? 'primary';
$badge_color_variant = $data['badge_color_variant'] ?? 'secondary';
$text_alignment = $data['text_alignment'] ?? 'left';
$button_alignment = $data['button_alignment'] ?? 'left';
$show_favorite = $data['show_favorite'] ?? true;
$description_lines = $data['description_lines'] ?? 3; // Number of lines for description (default 3)

// Get Display Fields
$display_fields_packages = $data['display_fields_packages'] ?? [];
$display_fields_posts = $data['display_fields_posts'] ?? [];

// If no cards, don't render
if (empty($cards)) {
    if (function_exists('travel_warning')) {
        travel_warning('Template sin cards para renderizar');
    }

    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo '<div style="padding: 20px; background: #f0f0f0; border: 2px dashed #ccc; text-align: center;">';
        echo '<p>Posts Carousel: Sin cards para mostrar. Agrega cards desde el panel de ACF.</p>';
        echo '</div>';
    }
    return;
}

// Block classes
$classes = [
    'posts-carousel',
    'posts-carousel--material',
    'posts-carousel--' . $card_style,
    'align' . $data['align'],
    'hover-' . $data['hover_effect'],
    'arrows-' . $data['arrows_position'],
    'text-align-' . $text_alignment,
    'button-align-' . $button_alignment,
];

// Slider settings (solo mobile)
$slider_settings = [
    'autoplay' => $data['autoplay'] ? '1' : '0',
    'delay' => $data['autoplay_delay'],
    'speed' => $data['slider_speed'],
];
?>

<section
    id="<?php echo esc_attr($data['block_id']); ?>"
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    data-slider-autoplay="<?php echo esc_attr($slider_settings['autoplay']); ?>"
    data-slider-delay="<?php echo esc_attr($slider_settings['delay']); ?>"
    data-slider-speed="<?php echo esc_attr($slider_settings['speed']); ?>"
    style="--card-gap: <?php echo esc_attr($data['card_gap']); ?>px; --desktop-columns: <?php echo esc_attr($data['desktop_columns']); ?>; --tablet-columns: <?php echo esc_attr($data['tablet_columns']); ?>; --card-height: <?php echo esc_attr($data['card_height']); ?>px; --card-height-desktop: <?php echo esc_attr($data['card_height_desktop'] ?? 450); ?>px; --description-lines: <?php echo esc_attr($description_lines); ?>;">

    <!-- Wrapper para slider + dots -->
    <div class="pc-wrapper">

        <!-- Mobile Navigation Arrows (fuera del container para overlay-2) -->
        <?php if ($data['show_arrows'] && $data['arrows_position'] !== 'bottom'): ?>
            <div class="pc-nav-wrapper">
                <button class="pc-arrow pc-arrow--prev" aria-label="<?php esc_attr_e('Anterior', 'travel-blocks'); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button class="pc-arrow pc-arrow--next" aria-label="<?php esc_attr_e('Siguiente', 'travel-blocks'); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <!-- Grid Container (Desktop) / Slider Container (Mobile) -->
        <div class="pc-container">

            <!-- Cards Grid/Slider -->
            <div class="pc-grid">
            <?php
            // Agrupar cards por filas según columnas desktop (solo para desktop)
            $desktop_columns = $data['desktop_columns'] ?? 3;
            $rows = array_chunk($cards, $desktop_columns);

            foreach ($rows as $row_index => $row_cards):
            ?>
                <div class="pc-row">
                <?php foreach ($row_cards as $index => $card):
                    $actual_index = ($row_index * $desktop_columns) + $index;
                // Get common card data
                $image_url = !empty($card['image']['url'])
                    ? $card['image']['url']
                    : 'https://picsum.photos/800/600?random=' . ($actual_index + 1);

                $title = $card['title'] ?? '';
                $excerpt = $card['excerpt'] ?? '';

                // Handle link - can be string (manual) or array (dynamic from ContentQueryHelper)
                $link = '#';
                if (isset($card['link'])) {
                    if (is_array($card['link']) && !empty($card['link']['url'])) {
                        $link = $card['link']['url'];
                    } elseif (is_string($card['link'])) {
                        $link = $card['link'];
                    }
                }

                // Card image alt text
                $image_alt = !empty($card['image']['alt'])
                    ? $card['image']['alt']
                    : $title;

                // Get card fields
                $category = $card['category'] ?? '';
                $cta_text = $card['cta_text'] ?? '';
                $location = $card['location'] ?? '';
                $price = $card['price'] ?? '';
                $has_deal_discount = $card['has_deal_discount'] ?? false;
                $deal_discount = $card['deal_discount'] ?? '';
                $promo_enabled = $card['promo_enabled'] ?? false;
                $promo_tag = $card['promo_tag'] ?? '';
                $is_package = $card['is_package'] ?? false;
                $duration_price = $card['duration_price'] ?? '';

                // Determine which display fields to use
                $display_fields = $is_package ? $display_fields_packages : $display_fields_posts;

                // Check visibility flags
                $show_location = is_array($display_fields) && in_array('location', $display_fields);
                $show_price = is_array($display_fields) && in_array('price', $display_fields);

                // Badge color: usar individual si existe, sino usar global
                $card_badge_color = !empty($card['badge_color_variant']) ? $card['badge_color_variant'] : $badge_color_variant;
            ?>

            <article class="pc-card pc-card--<?php echo esc_attr($card_style); ?>" data-index="<?php echo $actual_index; ?>">

                <?php if ($card_style === 'vertical'): ?>
                    <!-- VERTICAL STYLE: Image on top, content below -->
                    <div class="pc-card__image-wrapper">
                        <img
                            src="<?php echo esc_url($image_url); ?>"
                            alt="<?php echo esc_attr($image_alt); ?>"
                            loading="lazy"
                            class="pc-card__image">

                        <!-- Category Badge (top left on image) -->
                        <?php if ($category): ?>
                            <span class="pc-card__badge pc-card__badge--<?php echo esc_attr($card_badge_color); ?>"><?php echo esc_html($category); ?></span>
                        <?php endif; ?>

                        <!-- Favorite Button (Heart - top right on image) -->
                        <?php if ($show_favorite): ?>
                            <button class="pc-card__favorite" aria-label="<?php esc_attr_e('Agregar a favoritos', 'travel-blocks'); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 15" fill="none">
                                    <path d="M7.83 13.34L7.82 13.34L1.95 7.52C0.15 5.66 0.15 2.72 1.93 0.93C3.24 -0.38 5.37 -0.38 6.68 0.93L7.83 2.07L8.97 0.93C10.28 -0.38 12.41 -0.38 13.72 0.93C15.5 2.72 15.5 5.66 13.7 7.52L7.83 13.34Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <!-- OVERLAY / OVERLAY-SPLIT STYLE: Image background with overlay content -->
                    <div class="pc-card__image-bg" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                        <img
                            src="<?php echo esc_url($image_url); ?>"
                            alt="<?php echo esc_attr($image_alt); ?>"
                            loading="lazy"
                            style="display: none;">
                    </div>

                    <!-- Category Badge -->
                    <?php if ($category): ?>
                        <span class="pc-card__badge pc-card__badge--<?php echo esc_attr($card_badge_color); ?>"><?php echo esc_html($category); ?></span>
                    <?php endif; ?>

                    <!-- Promo Ribbon (only for overlay-2 with promo enabled) -->
                    <?php if ($card_style === 'overlay-2' && $promo_enabled && $promo_tag): ?>
                        <span class="pc-card__discount-ribbon"><?php echo esc_html($promo_tag); ?></span>
                    <?php endif; ?>

                    <!-- Favorite Button (Heart) -->
                    <?php if ($show_favorite): ?>
                        <button class="pc-card__favorite" aria-label="<?php esc_attr_e('Agregar a favoritos', 'travel-blocks'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 15" fill="none">
                                <path d="M7.83 13.34L7.82 13.34L1.95 7.52C0.15 5.66 0.15 2.72 1.93 0.93C3.24 -0.38 5.37 -0.38 6.68 0.93L7.83 2.07L8.97 0.93C10.28 -0.38 12.41 -0.38 13.72 0.93C15.5 2.72 15.5 5.66 13.7 7.52L7.83 13.34Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Card Content (Bottom) -->
                <a href="<?php echo esc_url($link); ?>" class="pc-card__link">
                    <div class="pc-card__content">

                        <?php if ($card_style === 'overlay-2'): ?>
                            <!-- ========== OVERLAY-2: Estructura idéntica a Taxonomy Tabs ========== -->
                            <?php if ($title): ?>
                                <h3 class="pc-card__title"><?php echo esc_html($title); ?></h3>
                            <?php endif; ?>

                            <?php if ($excerpt): ?>
                                <p class="pc-card__excerpt"><?php echo esc_html($excerpt); ?></p>
                            <?php endif; ?>

                            <!-- Divider Line (después del excerpt, antes de meta) -->
                            <?php if (($title || $excerpt) && ($is_package ? ($location || $duration_price) : ($location || $price))): ?>
                                <div class="pc-card__divider"></div>
                            <?php endif; ?>

                            <?php if ($is_package): ?>
                                <!-- Package: Location -->
                                <?php if ($location): ?>
                                    <div class="pc-card__meta">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="18" viewBox="0 0 13 18" fill="none">
                                            <path d="M6.44287 0C6.01995 0.000442936 5.60112 0.0436682 5.18638 0.129676C4.77164 0.215684 4.36895 0.342826 3.9783 0.511101C3.58765 0.679377 3.21653 0.885562 2.86494 1.12966C2.51335 1.37375 2.18803 1.65107 1.88898 1.96163C1.58992 2.27218 1.32287 2.61002 1.08782 2.97513C0.852763 3.34024 0.654215 3.72564 0.492172 4.13131C0.330128 4.53699 0.207695 4.95517 0.124873 5.38586C0.0420507 5.81654 0.000426401 6.25148 0 6.69067C0 11.7256 5.75954 17.2065 6.00684 17.4363L6.43636 17.8418C6.98303 17.3011 12.7491 12.2324 12.8857 6.69067C12.8853 6.25148 12.8437 5.81654 12.7609 5.38586C12.678 4.95517 12.5556 4.53699 12.3936 4.13131C12.2315 3.72564 12.033 3.34024 11.7979 2.97513C11.5629 2.61002 11.2958 2.27218 10.9968 1.96163C10.6977 1.65107 10.3724 1.37375 10.0208 1.12966C9.66921 0.885562 9.29809 0.679377 8.90744 0.511101C8.51679 0.342826 8.1141 0.215684 7.69936 0.129676C7.28463 0.0436683 6.8658 0.000442936 6.44287 0ZM6.44287 16.0036C5.14128 14.6519 1.30159 10.3874 1.30159 6.69067C1.30159 5.98268 1.43204 5.30162 1.69295 4.64752C1.95385 3.99341 2.32535 3.41604 2.80744 2.91541C3.28953 2.41478 3.84551 2.029 4.47539 1.75806C5.10527 1.48712 5.7611 1.35165 6.44287 1.35165C7.12465 1.35165 7.78048 1.48712 8.41035 1.75806C9.04023 2.029 9.59622 2.41478 10.0783 2.91541C10.5604 3.41605 10.9319 3.99341 11.1928 4.64752C11.4537 5.30162 11.5842 5.98268 11.5842 6.69067C11.5842 10.455 7.74446 14.6654 6.44287 16.0036ZM6.44287 3.62243C2.42096 3.75759 2.42096 9.81299 6.44287 9.94815C10.4648 9.81299 10.4648 3.75759 6.44287 3.62243ZM6.44287 8.5965C6.21158 8.5965 5.9891 8.55054 5.77542 8.45863C5.56174 8.36672 5.37313 8.23584 5.20958 8.06601C5.04604 7.89618 4.92001 7.70031 4.8315 7.47841C4.74299 7.25651 4.69874 7.02547 4.69874 6.78529C4.69874 6.54511 4.74299 6.31407 4.8315 6.09217C4.92001 5.87027 5.04604 5.6744 5.20958 5.50457C5.37313 5.33473 5.56174 5.20386 5.77542 5.11195C5.9891 5.02003 6.21158 4.97408 6.44287 4.97408C6.67416 4.97408 6.89664 5.02003 7.11032 5.11195C7.324 5.20386 7.51261 5.33473 7.67616 5.50457C7.8397 5.6744 7.96573 5.87027 8.05424 6.09217C8.14275 6.31407 8.187 6.54511 8.187 6.78529C8.187 7.02547 8.14275 7.25651 8.05424 7.47841C7.96573 7.70031 7.8397 7.89618 7.67616 8.06601C7.51261 8.23584 7.324 8.36672 7.11032 8.45863C6.89664 8.55054 6.67416 8.5965 6.44287 8.5965Z" fill="white"/>
                                        </svg>
                                        <span><?php echo esc_html($location); ?></span>
                                    </div>
                                <?php endif; ?>

                                <!-- Package: Duration/Price + Button en misma línea -->
                                <div class="pc-card__bottom-row">
                                    <?php if ($duration_price): ?>
                                        <div class="pc-card__meta pc-card__meta--duration-price">
                                            <span><?php echo $has_deal_discount ? wp_kses_post($duration_price) : esc_html($duration_price); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($cta_text): ?>
                                        <button class="pc-card__button pc-card__button--<?php echo esc_attr($button_color_variant); ?>">
                                            <?php echo esc_html($cta_text); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <!-- Regular: Location -->
                                <?php if ($show_location && $location): ?>
                                    <div class="pc-card__meta">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="18" viewBox="0 0 13 18" fill="none">
                                            <path d="M6.44287 0C6.01995 0.000442936 5.60112 0.0436682 5.18638 0.129676C4.77164 0.215684 4.36895 0.342826 3.9783 0.511101C3.58765 0.679377 3.21653 0.885562 2.86494 1.12966C2.51335 1.37375 2.18803 1.65107 1.88898 1.96163C1.58992 2.27218 1.32287 2.61002 1.08782 2.97513C0.852763 3.34024 0.654215 3.72564 0.492172 4.13131C0.330128 4.53699 0.207695 4.95517 0.124873 5.38586C0.0420507 5.81654 0.000426401 6.25148 0 6.69067C0 11.7256 5.75954 17.2065 6.00684 17.4363L6.43636 17.8418C6.98303 17.3011 12.7491 12.2324 12.8857 6.69067C12.8853 6.25148 12.8437 5.81654 12.7609 5.38586C12.678 4.95517 12.5556 4.53699 12.3936 4.13131C12.2315 3.72564 12.033 3.34024 11.7979 2.97513C11.5629 2.61002 11.2958 2.27218 10.9968 1.96163C10.6977 1.65107 10.3724 1.37375 10.0208 1.12966C9.66921 0.885562 9.29809 0.679377 8.90744 0.511101C8.51679 0.342826 8.1141 0.215684 7.69936 0.129676C7.28463 0.0436683 6.8658 0.000442936 6.44287 0ZM6.44287 16.0036C5.14128 14.6519 1.30159 10.3874 1.30159 6.69067C1.30159 5.98268 1.43204 5.30162 1.69295 4.64752C1.95385 3.99341 2.32535 3.41604 2.80744 2.91541C3.28953 2.41478 3.84551 2.029 4.47539 1.75806C5.10527 1.48712 5.7611 1.35165 6.44287 1.35165C7.12465 1.35165 7.78048 1.48712 8.41035 1.75806C9.04023 2.029 9.59622 2.41478 10.0783 2.91541C10.5604 3.41605 10.9319 3.99341 11.1928 4.64752C11.4537 5.30162 11.5842 5.98268 11.5842 6.69067C11.5842 10.455 7.74446 14.6654 6.44287 16.0036ZM6.44287 3.62243C2.42096 3.75759 2.42096 9.81299 6.44287 9.94815C10.4648 9.81299 10.4648 3.75759 6.44287 3.62243ZM6.44287 8.5965C6.21158 8.5965 5.9891 8.55054 5.77542 8.45863C5.56174 8.36672 5.37313 8.23584 5.20958 8.06601C5.04604 7.89618 4.92001 7.70031 4.8315 7.47841C4.74299 7.25651 4.69874 7.02547 4.69874 6.78529C4.69874 6.54511 4.74299 6.31407 4.8315 6.09217C4.92001 5.87027 5.04604 5.6744 5.20958 5.50457C5.37313 5.33473 5.56174 5.20386 5.77542 5.11195C5.9891 5.02003 6.21158 4.97408 6.44287 4.97408C6.67416 4.97408 6.89664 5.02003 7.11032 5.11195C7.324 5.20386 7.51261 5.33473 7.67616 5.50457C7.8397 5.6744 7.96573 5.87027 8.05424 6.09217C8.14275 6.31407 8.187 6.54511 8.187 6.78529C8.187 7.02547 8.14275 7.25651 8.05424 7.47841C7.96573 7.70031 7.8397 7.89618 7.67616 8.06601C7.51261 8.23584 7.324 8.36672 7.11032 8.45863C6.89664 8.55054 6.67416 8.5965 6.44287 8.5965Z" fill="white"/>
                                        </svg>
                                        <span><?php echo esc_html($location); ?></span>
                                    </div>
                                <?php endif; ?>

                                <!-- Regular: Price + Button en misma línea -->
                                <div class="pc-card__bottom-row">
                                    <?php if ($show_price && $price): ?>
                                        <div class="pc-card__price"><?php echo $has_deal_discount ? wp_kses_post($price) : esc_html($price); ?></div>
                                    <?php endif; ?>
                                    <?php if ($cta_text): ?>
                                        <button class="pc-card__button pc-card__button--<?php echo esc_attr($button_color_variant); ?>">
                                            <?php echo esc_html($cta_text); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        <?php elseif ($card_style === 'overlay'): ?>
                            <!-- ========== OVERLAY: Botón inline con descripción ========== -->
                            <?php if ($title): ?>
                                <h3 class="pc-card__title"><?php echo esc_html($title); ?></h3>
                            <?php endif; ?>

                            <!-- Divider Line -->
                            <?php if ($title && $excerpt): ?>
                                <div class="pc-card__divider"></div>
                            <?php endif; ?>

                            <?php if ($excerpt): ?>
                                <!-- Excerpt con botón inline a la derecha (última línea) -->
                                <div class="pc-card__excerpt-wrapper">
                                    <p class="pc-card__excerpt"><?php if ($cta_text): ?><span class="pc-card__inline-cta pc-card__inline-cta--<?php echo esc_attr($button_color_variant); ?>"><?php echo esc_html($cta_text); ?></span><?php endif; ?><?php echo esc_html($excerpt); ?></p>
                                </div>
                            <?php endif; ?>

                        <?php elseif ($card_style === 'overlay-split'): ?>
                            <!-- OVERLAY-SPLIT: 50/50 Bottom Layout -->
                            <?php if ($title): ?>
                                <h3 class="pc-card__title"><?php echo esc_html($title); ?></h3>
                            <?php endif; ?>

                            <?php if ($excerpt): ?>
                                <p class="pc-card__excerpt"><?php echo esc_html($excerpt); ?></p>
                            <?php endif; ?>

                            <!-- Divider Line -->
                            <?php if (($title || $excerpt) && ($duration_price || $location || $price)): ?>
                                <div class="pc-card__divider"></div>
                            <?php endif; ?>

                            <div class="pc-card__bottom-split">
                                <!-- Left 50%: Meta Info (Location, Price, Duration) -->
                                <div class="pc-card__meta-info">
                                    <?php if ($is_package): ?>
                                        <!-- Package: Location + Duration + Price -->
                                        <?php if ($show_location && !empty($location)): ?>
                                            <span class="pc-card__location">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="18" viewBox="0 0 13 18" fill="none">
                                                    <path d="M6.44287 0C6.01995 0.000442936 5.60112 0.0436682 5.18638 0.129676C4.77164 0.215684 4.36895 0.342826 3.9783 0.511101C3.58765 0.679377 3.21653 0.885562 2.86494 1.12966C2.51335 1.37375 2.18803 1.65107 1.88898 1.96163C1.58992 2.27218 1.32287 2.61002 1.08782 2.97513C0.852763 3.34024 0.654215 3.72564 0.492172 4.13131C0.330128 4.53699 0.207695 4.95517 0.124873 5.38586C0.0420507 5.81654 0.000426401 6.25148 0 6.69067C0 11.7256 5.75954 17.2065 6.00684 17.4363L6.43636 17.8418C6.98303 17.3011 12.7491 12.2324 12.8857 6.69067C12.8853 6.25148 12.8437 5.81654 12.7609 5.38586C12.678 4.95517 12.5556 4.53699 12.3936 4.13131C12.2315 3.72564 12.033 3.34024 11.7979 2.97513C11.5629 2.61002 11.2958 2.27218 10.9968 1.96163C10.6977 1.65107 10.3724 1.37375 10.0208 1.12966C9.66921 0.885562 9.29809 0.679377 8.90744 0.511101C8.51679 0.342826 8.1141 0.215684 7.69936 0.129676C7.28463 0.0436683 6.8658 0.000442936 6.44287 0ZM6.44287 16.0036C5.14128 14.6519 1.30159 10.3874 1.30159 6.69067C1.30159 5.98268 1.43204 5.30162 1.69295 4.64752C1.95385 3.99341 2.32535 3.41604 2.80744 2.91541C3.28953 2.41478 3.84551 2.029 4.47539 1.75806C5.10527 1.48712 5.7611 1.35165 6.44287 1.35165C7.12465 1.35165 7.78048 1.48712 8.41035 1.75806C9.04023 2.029 9.59622 2.41478 10.0783 2.91541C10.5604 3.41605 10.9319 3.99341 11.1928 4.64752C11.4537 5.30162 11.5842 5.98268 11.5842 6.69067C11.5842 10.455 7.74446 14.6654 6.44287 16.0036ZM6.44287 3.62243C2.42096 3.75759 2.42096 9.81299 6.44287 9.94815C10.4648 9.81299 10.4648 3.75759 6.44287 3.62243ZM6.44287 8.5965C6.21158 8.5965 5.9891 8.55054 5.77542 8.45863C5.56174 8.36672 5.37313 8.23584 5.20958 8.06601C5.04604 7.89618 4.92001 7.70031 4.8315 7.47841C4.74299 7.25651 4.69874 7.02547 4.69874 6.78529C4.69874 6.54511 4.74299 6.31407 4.8315 6.09217C4.92001 5.87027 5.04604 5.6744 5.20958 5.50457C5.37313 5.33473 5.56174 5.20386 5.77542 5.11195C5.9891 5.02003 6.21158 4.97408 6.44287 4.97408C6.67416 4.97408 6.89664 5.02003 7.11032 5.11195C7.324 5.20386 7.51261 5.33473 7.67616 5.50457C7.8397 5.6744 7.96573 5.87027 8.05424 6.09217C8.14275 6.31407 8.187 6.54511 8.187 6.78529C8.187 7.02547 8.14275 7.25651 8.05424 7.47841C7.96573 7.70031 7.8397 7.89618 7.67616 8.06601C7.51261 8.23584 7.324 8.36672 7.11032 8.45863C6.89664 8.55054 6.67416 8.5965 6.44287 8.5965Z" fill="white"/>
                                                </svg>
                                                <?php echo esc_html($location); ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if (!empty($duration_price)): ?>
                                            <span class="pc-card__meta-item"><?php echo $has_deal_discount ? wp_kses_post($duration_price) : esc_html($duration_price); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <!-- Blog Post: Location + Price -->
                                        <?php if ($show_location && !empty($location)): ?>
                                            <span class="pc-card__location">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="18" viewBox="0 0 13 18" fill="none">
                                                    <path d="M6.44287 0C6.01995 0.000442936 5.60112 0.0436682 5.18638 0.129676C4.77164 0.215684 4.36895 0.342826 3.9783 0.511101C3.58765 0.679377 3.21653 0.885562 2.86494 1.12966C2.51335 1.37375 2.18803 1.65107 1.88898 1.96163C1.58992 2.27218 1.32287 2.61002 1.08782 2.97513C0.852763 3.34024 0.654215 3.72564 0.492172 4.13131C0.330128 4.53699 0.207695 4.95517 0.124873 5.38586C0.0420507 5.81654 0.000426401 6.25148 0 6.69067C0 11.7256 5.75954 17.2065 6.00684 17.4363L6.43636 17.8418C6.98303 17.3011 12.7491 12.2324 12.8857 6.69067C12.8853 6.25148 12.8437 5.81654 12.7609 5.38586C12.678 4.95517 12.5556 4.53699 12.3936 4.13131C12.2315 3.72564 12.033 3.34024 11.7979 2.97513C11.5629 2.61002 11.2958 2.27218 10.9968 1.96163C10.6977 1.65107 10.3724 1.37375 10.0208 1.12966C9.66921 0.885562 9.29809 0.679377 8.90744 0.511101C8.51679 0.342826 8.1141 0.215684 7.69936 0.129676C7.28463 0.0436683 6.8658 0.000442936 6.44287 0ZM6.44287 16.0036C5.14128 14.6519 1.30159 10.3874 1.30159 6.69067C1.30159 5.98268 1.43204 5.30162 1.69295 4.64752C1.95385 3.99341 2.32535 3.41604 2.80744 2.91541C3.28953 2.41478 3.84551 2.029 4.47539 1.75806C5.10527 1.48712 5.7611 1.35165 6.44287 1.35165C7.12465 1.35165 7.78048 1.48712 8.41035 1.75806C9.04023 2.029 9.59622 2.41478 10.0783 2.91541C10.5604 3.41605 10.9319 3.99341 11.1928 4.64752C11.4537 5.30162 11.5842 5.98268 11.5842 6.69067C11.5842 10.455 7.74446 14.6654 6.44287 16.0036ZM6.44287 3.62243C2.42096 3.75759 2.42096 9.81299 6.44287 9.94815C10.4648 9.81299 10.4648 3.75759 6.44287 3.62243ZM6.44287 8.5965C6.21158 8.5965 5.9891 8.55054 5.77542 8.45863C5.56174 8.36672 5.37313 8.23584 5.20958 8.06601C5.04604 7.89618 4.92001 7.70031 4.8315 7.47841C4.74299 7.25651 4.69874 7.02547 4.69874 6.78529C4.69874 6.54511 4.74299 6.31407 4.8315 6.09217C4.92001 5.87027 5.04604 5.6744 5.20958 5.50457C5.37313 5.33473 5.56174 5.20386 5.77542 5.11195C5.9891 5.02003 6.21158 4.97408 6.44287 4.97408C6.67416 4.97408 6.89664 5.02003 7.11032 5.11195C7.324 5.20386 7.51261 5.33473 7.67616 5.50457C7.8397 5.6744 7.96573 5.87027 8.05424 6.09217C8.14275 6.31407 8.187 6.54511 8.187 6.78529C8.187 7.02547 8.14275 7.25651 8.05424 7.47841C7.96573 7.70031 7.8397 7.89618 7.67616 8.06601C7.51261 8.23584 7.324 8.36672 7.11032 8.45863C6.89664 8.55054 6.67416 8.5965 6.44287 8.5965Z" fill="white"/>
                                                </svg>
                                                <?php echo esc_html($location); ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($show_price && !empty($price)): ?>
                                            <span class="pc-card__price"><?php echo $has_deal_discount ? wp_kses_post($price) : esc_html($price); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Right 50%: CTA Button -->
                                <div class="pc-card__button-wrapper">
                                    <?php if ($cta_text): ?>
                                        <button class="pc-card__button pc-card__button--<?php echo esc_attr($button_color_variant); ?>">
                                            <?php echo esc_html($cta_text); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                        <?php else: ?>
                            <!-- OVERLAY / VERTICAL: Default Layout -->

                            <!-- Centered text wrapper -->
                            <div class="pc-card__text-wrapper">
                                <?php if ($is_package && !empty($duration_price)): ?>
                                    <!-- Package: Combined Duration + Price (inline format like "7 Days | From $1,145") -->
                                    <div class="pc-card__meta-line">
                                        <span class="pc-card__meta-item"><?php echo $has_deal_discount ? wp_kses_post($duration_price) : esc_html($duration_price); ?></span>
                                    </div>
                                <?php else: ?>
                                    <!-- Regular: Location and/or Price (controlled by Display Fields) -->
                                    <?php if (($show_location && !empty($location)) || ($show_price && !empty($price))): ?>
                                        <div class="pc-card__meta-line">
                                            <?php if ($show_location && !empty($location)): ?>
                                                <span class="pc-card__meta-item pc-card__meta-item--location">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="18" viewBox="0 0 13 18" fill="none">
                                                        <path d="M6.44287 0C6.01995 0.000442936 5.60112 0.0436682 5.18638 0.129676C4.77164 0.215684 4.36895 0.342826 3.9783 0.511101C3.58765 0.679377 3.21653 0.885562 2.86494 1.12966C2.51335 1.37375 2.18803 1.65107 1.88898 1.96163C1.58992 2.27218 1.32287 2.61002 1.08782 2.97513C0.852763 3.34024 0.654215 3.72564 0.492172 4.13131C0.330128 4.53699 0.207695 4.95517 0.124873 5.38586C0.0420507 5.81654 0.000426401 6.25148 0 6.69067C0 11.7256 5.75954 17.2065 6.00684 17.4363L6.43636 17.8418C6.98303 17.3011 12.7491 12.2324 12.8857 6.69067C12.8853 6.25148 12.8437 5.81654 12.7609 5.38586C12.678 4.95517 12.5556 4.53699 12.3936 4.13131C12.2315 3.72564 12.033 3.34024 11.7979 2.97513C11.5629 2.61002 11.2958 2.27218 10.9968 1.96163C10.6977 1.65107 10.3724 1.37375 10.0208 1.12966C9.66921 0.885562 9.29809 0.679377 8.90744 0.511101C8.51679 0.342826 8.1141 0.215684 7.69936 0.129676C7.28463 0.0436683 6.8658 0.000442936 6.44287 0ZM6.44287 16.0036C5.14128 14.6519 1.30159 10.3874 1.30159 6.69067C1.30159 5.98268 1.43204 5.30162 1.69295 4.64752C1.95385 3.99341 2.32535 3.41604 2.80744 2.91541C3.28953 2.41478 3.84551 2.029 4.47539 1.75806C5.10527 1.48712 5.7611 1.35165 6.44287 1.35165C7.12465 1.35165 7.78048 1.48712 8.41035 1.75806C9.04023 2.029 9.59622 2.41478 10.0783 2.91541C10.5604 3.41605 10.9319 3.99341 11.1928 4.64752C11.4537 5.30162 11.5842 5.98268 11.5842 6.69067C11.5842 10.455 7.74446 14.6654 6.44287 16.0036ZM6.44287 3.62243C2.42096 3.75759 2.42096 9.81299 6.44287 9.94815C10.4648 9.81299 10.4648 3.75759 6.44287 3.62243ZM6.44287 8.5965C6.21158 8.5965 5.9891 8.55054 5.77542 8.45863C5.56174 8.36672 5.37313 8.23584 5.20958 8.06601C5.04604 7.89618 4.92001 7.70031 4.8315 7.47841C4.74299 7.25651 4.69874 7.02547 4.69874 6.78529C4.69874 6.54511 4.74299 6.31407 4.8315 6.09217C4.92001 5.87027 5.04604 5.6744 5.20958 5.50457C5.37313 5.33473 5.56174 5.20386 5.77542 5.11195C5.9891 5.02003 6.21158 4.97408 6.44287 4.97408C6.67416 4.97408 6.89664 5.02003 7.11032 5.11195C7.324 5.20386 7.51261 5.33473 7.67616 5.50457C7.8397 5.6744 7.96573 5.87027 8.05424 6.09217C8.14275 6.31407 8.187 6.54511 8.187 6.78529C8.187 7.02547 8.14275 7.25651 8.05424 7.47841C7.96573 7.70031 7.8397 7.89618 7.67616 8.06601C7.51261 8.23584 7.324 8.36672 7.11032 8.45863C6.89664 8.55054 6.67416 8.5965 6.44287 8.5965Z" fill="white"/>
                                                    </svg>
                                                    <?php echo esc_html($location); ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if ($show_location && !empty($location) && $show_price && !empty($price)): ?>
                                                <span class="pc-card__meta-separator">|</span>
                                            <?php endif; ?>

                                            <?php if ($show_price && !empty($price)): ?>
                                                <span class="pc-card__meta-item pc-card__meta-item--price"><?php echo $has_deal_discount ? wp_kses_post($price) : esc_html($price); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <!-- CTA Button (para vertical y overlay-2, overlay usa inline-cta) -->
                            <?php if ($cta_text && $card_style !== 'overlay'): ?>
                                <button class="pc-card__button pc-card__button--<?php echo esc_attr($button_color_variant); ?>">
                                    <?php echo esc_html($cta_text); ?>
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </a>
            </article>

                <?php endforeach; // end cards in row ?>
                </div><!-- .pc-row -->
            <?php endforeach; // end rows ?>
            </div><!-- .pc-grid -->

        </div><!-- .pc-container -->

        <!-- Mobile Pagination Dots -->
        <?php if ($data['show_dots'] || ($data['show_arrows'] && $data['arrows_position'] === 'bottom')): ?>
            <div class="pc-dots">
                <!-- Botón izquierdo (solo para variante bottom) -->
                <?php if ($data['show_arrows'] && $data['arrows_position'] === 'bottom'): ?>
                    <button class="pc-arrow pc-arrow--prev pc-arrow--bottom" aria-label="<?php esc_attr_e('Anterior', 'travel-blocks'); ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                <?php endif; ?>

                <!-- Dots -->
                <?php if ($data['show_dots']): ?>
                    <?php foreach ($cards as $i => $card): ?>
                        <button
                            class="pc-dot <?php echo $i === 0 ? 'is-active' : ''; ?>"
                            data-slide="<?php echo $i; ?>"
                            aria-label="<?php echo esc_attr(sprintf(__('Ir a card %d', 'travel-blocks'), $i + 1)); ?>">
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Botón derecho (solo para variante bottom) -->
                <?php if ($data['show_arrows'] && $data['arrows_position'] === 'bottom'): ?>
                    <button class="pc-arrow pc-arrow--next pc-arrow--bottom" aria-label="<?php esc_attr_e('Siguiente', 'travel-blocks'); ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>

</section>
