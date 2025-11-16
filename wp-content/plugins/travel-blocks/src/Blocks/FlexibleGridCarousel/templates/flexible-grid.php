<?php
/**
 * Template: Flexible Grid
 * Layout: Desktop grid with cards + text blocks, Mobile carousel (cards only) with text separate
 */

// Variables available from render_block:
// $items, $cards, $text_blocks, $columns_desktop, $text_position_mobile, $show_arrows, $show_dots, $enable_autoplay, $autoplay_delay
// $button_color_variant, $badge_color_variant, $text_alignment, $button_alignment

$carousel_id = 'fgc-' . uniqid();
$carousel_attrs = [
    'data-autoplay' => $enable_autoplay ? 'true' : 'false',
    'data-delay' => $autoplay_delay,
    'data-columns' => $columns_desktop,
    'data-text-position' => $text_position_mobile,
];
?>

<?php
// Merge block wrapper attributes with carousel attributes and add alignment classes
$wrapper_attrs = $block_wrapper_attributes;
$class_to_add = 'text-align-' . esc_attr($text_alignment) . ' button-align-' . esc_attr($button_alignment);
if (strpos($wrapper_attrs, 'class="') !== false) {
    $wrapper_attrs = str_replace('class="', 'class="' . $class_to_add . ' ', $wrapper_attrs);
} else {
    $wrapper_attrs .= ' class="' . $class_to_add . '"';
}
?>

<div <?php echo $wrapper_attrs; ?>>
<div class="fgc-flexible-grid"
     id="<?php echo esc_attr($carousel_id); ?>"
     style="--card-min-height: <?php echo esc_attr($card_min_height ?? 450); ?>px;"
     <?php foreach ($carousel_attrs as $key => $value): ?>
        <?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>"
     <?php endforeach; ?>>

    <!-- Mobile Text Blocks (Above) -->
    <?php if (!empty($text_blocks) && $text_position_mobile === 'above'): ?>
    <div class="fgc-text-blocks fgc-text-blocks--mobile fgc-text-blocks--above">
        <?php foreach ($text_blocks as $text_block): ?>
        <div class="fgc-text-block">
            <div class="fgc-text-content">
                <?php echo wp_kses_post($text_block['content']); ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Main Grid/Carousel Container -->
    <div class="fgc-container">
        <!-- Skeleton Loader -->
        <div class="fgc-skeleton" aria-hidden="true">
            <?php for ($i = 0; $i < $columns_desktop; $i++): ?>
            <div class="fgc-skeleton-item">
                <div class="fgc-skeleton-image"></div>
                <div class="fgc-skeleton-line fgc-skeleton-line--title"></div>
                <div class="fgc-skeleton-line fgc-skeleton-line--text"></div>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Items Grid (Desktop) / Carousel (Mobile) -->
        <div class="fgc-items" role="region" aria-label="Items grid">
            <?php foreach ($items as $index => $item): ?>

                <?php if ($item['acf_fc_layout'] === 'card'): ?>
                <!-- Card Item (HeroCarousel style) -->
                <?php
                // Badge color: use individual if exists, otherwise use global
                $card_badge_color = !empty($item['badge_color_variant']) ? $item['badge_color_variant'] : $badge_color_variant;

                $card_style = '';
                if (!empty($item['image'])) {
                    $card_img_url = $item['image']['sizes']['large'] ?? $item['image']['url'];
                    $card_style = 'style="background-image: url(\'' . esc_url($card_img_url) . '\');"';
                }
                ?>
                <article class="fgc-item fgc-item--card"
                     data-index="<?php echo esc_attr($index); ?>"
                     data-type="card"
                     role="group"
                     aria-label="Card <?php echo esc_attr($index + 1); ?> of <?php echo count($items); ?>"
                     <?php echo $card_style; ?>>

                    <!-- Gradient Overlay -->
                    <div class="fgc-card__overlay"></div>

                    <!-- Category Badge -->
                    <?php if (!empty($item['category'])): ?>
                        <span class="fgc-card__category fgc-card__category--<?php echo esc_attr($card_badge_color); ?>"><?php echo esc_html($item['category']); ?></span>
                    <?php endif; ?>

                    <!-- Content -->
                    <?php if (!empty($item['link'])): ?>
                    <a href="<?php echo esc_url($item['link']['url']); ?>" class="fgc-card__link">
                    <?php endif; ?>
                        <div class="fgc-card__content">
                            <?php if (!empty($item['title'])): ?>
                            <h3 class="fgc-card__title"><?php echo esc_html($item['title']); ?></h3>
                            <?php endif; ?>

                            <?php if (!empty($item['description'])): ?>
                            <p class="fgc-card__excerpt"><?php echo esc_html($item['description']); ?></p>
                            <?php endif; ?>

                            <!-- Divider Line -->
                            <?php
                            $is_package = !empty($item['is_package']);
                            $duration_price = $item['duration_price'] ?? '';
                            $has_deal_discount = $item['has_deal_discount'] ?? false;
                            ?>
                            <?php if ((!empty($item['title']) || !empty($item['description'])) && ($is_package ? $duration_price : (!empty($item['location']) || !empty($item['price'])))): ?>
                                <div class="fgc-card__divider"></div>
                            <?php endif; ?>

                            <?php if ($is_package && $duration_price): ?>
                                <!-- Package: Combined Duration + Price -->
                                <div class="fgc-card__location">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                        <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <span><?php echo $has_deal_discount ? wp_kses_post($duration_price) : esc_html($duration_price); ?></span>
                                </div>
                            <?php else: ?>
                                <!-- Regular: Location -->
                                <?php if (!empty($item['location'])): ?>
                                    <div class="fgc-card__location">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="currentColor"/>
                                        </svg>
                                        <span><?php echo esc_html($item['location']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <!-- Regular: Price -->
                                <?php if (!empty($item['price'])): ?>
                                    <div class="fgc-card__price"><?php echo $has_deal_discount ? wp_kses_post($item['price']) : esc_html($item['price']); ?></div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Meta Information (Duration, Rating, Group Size) - Only for non-package items -->
                            <?php if (!$is_package && (!empty($item['duration']) || !empty($item['rating']) || !empty($item['group_size']))): ?>
                                <div class="fgc-card__meta">
                                    <!-- Duration -->
                                    <?php if (!empty($item['duration'])): ?>
                                        <div class="fgc-card__meta-item fgc-card__duration">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                            <span><?php echo esc_html($item['duration']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Rating -->
                                    <?php if (!empty($item['rating'])): ?>
                                        <div class="fgc-card__meta-item fgc-card__rating">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <span><?php echo esc_html($item['rating']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Group Size -->
                                    <?php if (!empty($item['group_size'])): ?>
                                        <div class="fgc-card__meta-item fgc-card__group-size">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span><?php echo esc_html($item['group_size']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- CTA Button -->
                            <?php if (!empty($item['cta_text'])): ?>
                                <button class="fgc-card__button fgc-card__button--<?php echo esc_attr($button_color_variant); ?>">
                                    <?php echo esc_html($item['cta_text']); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php if (!empty($item['link'])): ?>
                    </a>
                    <?php endif; ?>
                </article>

                <?php elseif ($item['acf_fc_layout'] === 'text_block'): ?>
                <!-- Text Block Item (Desktop Only) -->
                <div class="fgc-item fgc-item--text fgc-item--desktop-only"
                     data-index="<?php echo esc_attr($index); ?>"
                     data-type="text_block">

                    <div class="fgc-text-block">
                        <div class="fgc-text-content">
                            <?php echo wp_kses_post($item['content']); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            <?php endforeach; ?>
        </div>

        <!-- Navigation Arrows (Mobile Only) -->
        <?php if ($show_arrows): ?>
        <button type="button"
                class="fgc-nav fgc-nav--prev"
                aria-label="Previous item"
                disabled>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <button type="button"
                class="fgc-nav fgc-nav--next"
                aria-label="Next item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
        <?php endif; ?>

        <!-- Pagination Dots (Mobile Only) -->
        <?php if ($show_dots): ?>
        <div class="fgc-dots" role="tablist" aria-label="Carousel navigation"></div>
        <?php endif; ?>
    </div>

    <!-- Mobile Text Blocks (Below) -->
    <?php if (!empty($text_blocks) && $text_position_mobile === 'below'): ?>
    <div class="fgc-text-blocks fgc-text-blocks--mobile fgc-text-blocks--below">
        <?php foreach ($text_blocks as $text_block): ?>
        <div class="fgc-text-block">
            <div class="fgc-text-content">
                <?php echo wp_kses_post($text_block['content']); ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
</div>
