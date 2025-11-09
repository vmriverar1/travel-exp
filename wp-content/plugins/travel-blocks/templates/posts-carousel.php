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
    style="--card-gap: <?php echo esc_attr($data['card_gap']); ?>px; --desktop-columns: <?php echo esc_attr($data['desktop_columns']); ?>; --tablet-columns: <?php echo esc_attr($data['tablet_columns']); ?>; --card-height: <?php echo esc_attr($data['card_height']); ?>px;">

    <!-- Wrapper para slider + dots -->
    <div class="pc-wrapper">

        <!-- Grid Container (Desktop) / Slider Container (Mobile) -->
        <div class="pc-container">

            <!-- Mobile Navigation Arrows (solo si no es 'bottom') -->
            <?php if ($data['show_arrows'] && $data['arrows_position'] !== 'bottom'): ?>
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
            <?php endif; ?>

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
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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

                    <!-- Favorite Button (Heart) -->
                    <?php if ($show_favorite): ?>
                        <button class="pc-card__favorite" aria-label="<?php esc_attr_e('Agregar a favoritos', 'travel-blocks'); ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Card Content (Bottom) -->
                <a href="<?php echo esc_url($link); ?>" class="pc-card__link">
                    <div class="pc-card__content">
                        <?php if ($title): ?>
                            <h3 class="pc-card__title"><?php echo esc_html($title); ?></h3>
                        <?php endif; ?>

                        <?php if ($excerpt): ?>
                            <p class="pc-card__excerpt"><?php echo esc_html($excerpt); ?></p>
                        <?php endif; ?>

                        <?php if ($card_style === 'overlay-split'): ?>
                            <!-- OVERLAY-SPLIT: 50/50 Bottom Layout -->

                            <div class="pc-card__bottom-split">
                                <!-- Left 50%: Meta Info (Location, Price, Duration) -->
                                <div class="pc-card__meta-info">
                                    <?php if ($is_package): ?>
                                        <!-- Package: Location + Duration + Price -->
                                        <?php if ($show_location && !empty($location)): ?>
                                            <span class="pc-card__location">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="currentColor"/>
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
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="currentColor"/>
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

                            <!-- Divider Line -->
                            <?php if (($title || $excerpt) && ($duration_price || $location || $price)): ?>
                                <div class="pc-card__divider"></div>
                            <?php endif; ?>

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
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="currentColor"/>
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

                            <!-- CTA Button -->
                            <?php if ($cta_text): ?>
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
