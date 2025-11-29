<?php
/**
 * Template: Side Left
 * Layout: Cards on left side with half hidden, navigation arrows on right
 */

// Variables available from render_block:
// $hero_image, $hero_content (InnerBlocks), $has_hero_text, $cards, $columns_desktop, $negative_margin, $hero_height_mobile, $hero_height_tablet, $hero_height_desktop, $show_arrows, $show_dots, $enable_autoplay, $autoplay_delay, $is_carousel
// $button_color_variant, $badge_color_variant, $text_alignment, $button_alignment, $cards_negative_margin_top, $cards_negative_margin_bottom, $cards_negative_margin_left, $cards_negative_margin_right, $cards_height, $cards_width
// $content_proportion, $cards_proportion (dynamic width for text and cards areas)

$carousel_id = 'hc-' . uniqid();
$carousel_attrs = [
    'data-autoplay' => $enable_autoplay ? 'true' : 'false',
    'data-delay' => $autoplay_delay,
    'data-columns' => $columns_desktop,
    'data-is-carousel' => $is_carousel ? 'true' : 'false',
    'data-variation' => 'side_left',
];

$hero_url = !empty($hero_image['sizes']['large']) ? $hero_image['sizes']['large'] : (!empty($hero_image['url']) ? $hero_image['url'] : '');
?>

<style>
/* CSS Variables for dynamic positioning */
#<?php echo esc_attr($carousel_id); ?> {
    --margin-left: <?php echo esc_attr($cards_negative_margin_left); ?>vw;
    --margin-right: <?php echo esc_attr($cards_negative_margin_right); ?>vw;
    --margin-top: <?php echo esc_attr($cards_negative_margin_top); ?>vh;
    --margin-bottom: <?php echo esc_attr($cards_negative_margin_bottom); ?>vh;
}

/* Mobile */
#<?php echo esc_attr($carousel_id); ?> {
    min-height: <?php echo esc_attr($hero_height_mobile); ?>vh;
}

/* Tablet */
@media (min-width: 768px) and (max-width: 1023px) {
    #<?php echo esc_attr($carousel_id); ?> {
        min-height: <?php echo esc_attr($hero_height_tablet); ?>vh;
    }
}

/* Desktop */
@media (min-width: 1024px) {
    #<?php echo esc_attr($carousel_id); ?> {
        min-height: <?php echo esc_attr($hero_height_desktop); ?>vh;
    }
}

/* Cards Height */
#<?php echo esc_attr($carousel_id); ?> .hc-card {
    height: <?php echo esc_attr($cards_height); ?>px !important;
    min-height: <?php echo esc_attr($cards_height); ?>px !important;
<?php if (!empty($cards_width)): ?>
    width: <?php echo absint($cards_width); ?>px !important;
    min-width: <?php echo absint($cards_width); ?>px !important;
    max-width: <?php echo absint($cards_width); ?>px !important;
    flex: 0 0 <?php echo absint($cards_width); ?>px !important;
<?php endif; ?>
}

/* Negative Margin - Half Hidden Effect */
<?php if ($negative_margin > 0): ?>
#<?php echo esc_attr($carousel_id); ?> .hc-cards-section {
    margin-left: -<?php echo absint($negative_margin); ?>px !important;
}
<?php endif; ?>

/* Cards Negative Margins Top/Bottom (vh) */
<?php if ($cards_negative_margin_top != 0): ?>
#<?php echo esc_attr($carousel_id); ?> .hc-cards-section {
    margin-top: -<?php echo esc_attr($cards_negative_margin_top); ?>vh !important;
}
<?php endif; ?>

<?php if ($cards_negative_margin_bottom != 0): ?>
#<?php echo esc_attr($carousel_id); ?> .hc-cards-section {
    margin-bottom: -<?php echo esc_attr($cards_negative_margin_bottom); ?>vh !important;
}
<?php endif; ?>

/* Cards Negative Margins Left/Right (vw) - for side variations */
<?php if ($cards_negative_margin_left != 0): ?>
#<?php echo esc_attr($carousel_id); ?> .hc-cards-section {
    margin-left: -<?php echo esc_attr($cards_negative_margin_left); ?>vw !important;
}
<?php endif; ?>

<?php if ($cards_negative_margin_right != 0): ?>
#<?php echo esc_attr($carousel_id); ?> .hc-cards-section {
    margin-right: -<?php echo esc_attr($cards_negative_margin_right); ?>vw !important;
}
<?php endif; ?>
</style>

<?php
// Merge block wrapper attributes with carousel attributes and add alignment classes
$wrapper_attrs = $block_wrapper_attributes;
$class_to_add = 'hc-hero-carousel hc-hero-carousel--side-left text-align-' . esc_attr($text_alignment) . ' button-align-' . esc_attr($button_alignment);
if (strpos($wrapper_attrs, 'class="') !== false) {
    $wrapper_attrs = str_replace('class="', 'class="' . $class_to_add . ' ', $wrapper_attrs);
} else {
    $wrapper_attrs .= ' class="' . $class_to_add . '"';
}
$wrapper_attrs .= ' id="' . esc_attr($carousel_id) . '"';
$wrapper_attrs .= ' data-variation="side_left"'; // Help React identify this as a different component
foreach ($carousel_attrs as $key => $value) {
    $wrapper_attrs .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
}
?>

<div <?php echo $wrapper_attrs; ?> key="hero-carousel-side-left">

    <!-- Hero Background -->
    <?php if ($hero_url): ?>
    <div class="hc-hero-background" style="background-image: url('<?php echo esc_url($hero_url); ?>');">
        <div class="hc-hero-overlay"></div>
    </div>
    <?php endif; ?>

    <!-- Side Layout Container -->
    <div class="hc-side-layout" style="position: relative;">
        <!-- Cards Section (Dynamic width based on proportion) -->
        <div class="hc-cards-section" style="width: <?php echo esc_attr($cards_proportion + 30); ?>%;">
            <div class="hc-cards-wrapper">
                <!-- Skeleton Loader (hidden in preview mode) -->
                <?php if (!$is_preview): ?>
                <div class="hc-skeleton" aria-hidden="true">
                <?php for ($i = 0; $i < min($columns_desktop, count($cards)); $i++): ?>
                <div class="hc-skeleton-item">
                    <div class="hc-skeleton-image"></div>
                    <div class="hc-skeleton-line hc-skeleton-line--title"></div>
                    <div class="hc-skeleton-line hc-skeleton-line--text"></div>
                </div>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <!-- Cards Container -->
            <div class="hc-cards" role="region" aria-label="Cards carousel">
                <?php foreach ($cards as $index => $card): ?>
                <?php
                // Badge color: use individual if exists, otherwise use global
                $card_badge_color = !empty($card['badge_color_variant']) ? $card['badge_color_variant'] : $badge_color_variant;

                $card_style = '';
                if (!empty($card['image'])) {
                    $card_img_url = $card['image']['sizes']['large'] ?? $card['image']['url'];
                    $card_style = 'style="background-image: url(\'' . esc_url($card_img_url) . '\');"';
                }
                ?>
                <div class="hc-card"
                     data-index="<?php echo esc_attr($index); ?>"
                     role="group"
                     aria-label="Card <?php echo esc_attr($index + 1); ?> of <?php echo count($cards); ?>"
                     <?php echo $card_style; ?>>

                    <!-- Gradient Overlay -->
                    <div class="hc-card-overlay"></div>

                    <!-- Category Badge -->
                    <?php if (!empty($card['category'])): ?>
                        <span class="hc-card-category hc-card-category--<?php echo esc_attr($card_badge_color); ?>"><?php echo esc_html($card['category']); ?></span>
                    <?php endif; ?>

                    <!-- Content -->
                    <?php if (!empty($card['link'])): ?>
                    <a href="<?php echo esc_url($card['link']['url']); ?>" class="hc-card-link">
                    <?php endif; ?>
                        <div class="hc-card-content">
                            <?php if (!empty($card['title'])): ?>
                            <h3 class="hc-card-title"><?php echo esc_html($card['title']); ?></h3>
                            <?php endif; ?>

                            <?php if (!empty($card['excerpt'])): ?>
                            <p class="hc-card-excerpt"><?php echo esc_html($card['excerpt']); ?></p>
                            <?php endif; ?>

                            <!-- Divider Line -->
                            <?php
                            $is_package = !empty($card['is_package']);
                            $duration_price = $card['duration_price'] ?? '';
                            $has_deal_discount = $card['has_deal_discount'] ?? false;
                            ?>
                            <?php if ((!empty($card['title']) || !empty($card['excerpt'])) && ($is_package ? $duration_price : (!empty($card['location']) || !empty($card['price'])))): ?>
                                <div class="hc-card-divider"></div>
                            <?php endif; ?>

                            <?php if ($is_package && $duration_price): ?>
                                <!-- Package: Combined Duration + Price -->
                                <div class="hc-card-location">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                        <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <span><?php echo $has_deal_discount ? wp_kses_post($duration_price) : esc_html($duration_price); ?></span>
                                </div>
                            <?php else: ?>
                                <!-- Regular: Location -->
                                <?php if (!empty($card['location'])): ?>
                                    <div class="hc-card-location">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="currentColor"/>
                                        </svg>
                                        <span><?php echo esc_html($card['location']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <!-- Regular: Price -->
                                <?php if (!empty($card['price'])): ?>
                                    <div class="hc-card-price"><?php echo $has_deal_discount ? wp_kses_post($card['price']) : esc_html($card['price']); ?></div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- CTA Button -->
                            <?php if (!empty($card['cta_text'])): ?>
                                <button class="hc-card-button hc-card-button--<?php echo esc_attr($button_color_variant); ?>">
                                    <?php echo esc_html($card['cta_text']); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php if (!empty($card['link'])): ?>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

                <!-- Pagination Dots (only if carousel) -->
                <?php if ($show_dots && $is_carousel): ?>
                <div class="hc-dots" role="tablist" aria-label="Carousel navigation"></div>
                <?php endif; ?>

                <!-- Navigation Arrows (Inside cards section) - only if carousel -->
                <?php if ($show_arrows && $is_carousel): ?>
                <div class="hc-nav-section">
                    <button type="button"
                            class="hc-nav hc-nav--prev"
                            aria-label="Previous card"
                            disabled>
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>
                    <button type="button"
                            class="hc-nav hc-nav--next"
                            aria-label="Next card">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Hero Text Section (Dynamic width based on proportion) -->
        <div class="hc-hero-section" style="width: <?php echo esc_attr($content_proportion); ?>%;">
            <!-- Hero Text Content (InnerBlocks) -->
            <div class="hc-hero-text">
                <?php if ($is_preview): ?>
                    <InnerBlocks />
                <?php else: ?>
                    <?php echo $hero_content; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
