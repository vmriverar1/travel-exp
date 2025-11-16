<?php
/**
 * Template: Side by Side Cards (Horizontal Layout)
 *
 * Desktop: Grid flexible - imagen + texto lado a lado
 * Mobile: Slider nativo
 * Cards horizontales (imagen a un lado, contenido al otro)
 *
 * @var array $data Block data and settings
 */

// Get cards from data
$cards = $data['cards'] ?? [];

// If no cards, don't render
if (empty($cards)) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo '<div style="padding: 20px; background: #f0f0f0; border: 2px dashed #ccc; text-align: center;">';
        echo '<p>Side by Side Cards: Sin cards para mostrar. Agrega cards desde el panel de ACF.</p>';
        echo '</div>';
    }
    return;
}

// Get settings from data
$image_position = $data['image_position'] ?? 'left';
$image_width = $data['image_width'] ?? 40;
$image_border_radius = $data['image_border_radius'] ?? 12;
$button_color_variant = $data['button_color_variant'] ?? 'primary';
$badge_color_variant = $data['badge_color_variant'] ?? 'secondary';
$text_alignment = $data['text_alignment'] ?? 'left';
$button_alignment = $data['button_alignment'] ?? 'left';
$grid_columns = $data['grid_columns'] ?? 3;
$card_gap = $data['card_gap'] ?? 32;
$card_min_height = $data['card_min_height'] ?? 450;
$hover_effect = $data['hover_effect'] ?? 'squeeze';

// Slider settings
$show_arrows = $data['show_arrows'] ?? true;
$show_dots = $data['show_dots'] ?? true;
$autoplay = $data['autoplay'] ?? false;
$autoplay_delay = $data['autoplay_delay'] ?? 5000;
$show_favorite = $data['show_favorite'] ?? true;

// Block classes
$classes = [
    'sbs-cards',
    'align' . $data['align'],
    'image-' . $image_position,
    'hover-' . $hover_effect,
    'text-align-' . $text_alignment,
    'button-align-' . $button_alignment,
];

// Block ID
$block_id = $data['block_id'] ?? 'sbs-' . uniqid();
?>

<section
    id="<?php echo esc_attr($block_id); ?>"
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    data-slider-autoplay="<?php echo $autoplay ? '1' : '0'; ?>"
    data-slider-delay="<?php echo esc_attr($autoplay_delay); ?>"
    style="--image-width: <?php echo esc_attr($image_width); ?>%; --image-border-radius: <?php echo esc_attr($image_border_radius); ?>px; --card-gap: <?php echo esc_attr($card_gap); ?>px; --card-min-height: <?php echo esc_attr($card_min_height); ?>px; --grid-columns: <?php echo esc_attr($grid_columns); ?>;">

    <!-- Wrapper -->
    <div class="sbs-wrapper">

        <!-- Navigation Arrows (Mobile only) -->
        <?php if ($show_arrows): ?>
            <button class="sbs-arrow sbs-arrow--prev" aria-label="<?php esc_attr_e('Anterior', 'travel-blocks'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <button class="sbs-arrow sbs-arrow--next" aria-label="<?php esc_attr_e('Siguiente', 'travel-blocks'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        <?php endif; ?>

        <!-- Cards Container (Grid Desktop / Slider Mobile) -->
        <div class="sbs-container">
            <?php foreach ($cards as $index => $card):
                // Get card data
                $image_url = !empty($card['image']['url'])
                    ? $card['image']['url']
                    : 'https://picsum.photos/600/400?random=' . ($index + 1);

                $title = $card['title'] ?? __('Sin título', 'travel-blocks');
                $excerpt = $card['excerpt'] ?? '';

                // Handle link - can be string (manual) or array (dynamic)
                $link = '#';
                if (isset($card['link'])) {
                    if (is_array($card['link']) && !empty($card['link']['url'])) {
                        $link = $card['link']['url'];
                    } elseif (is_string($card['link'])) {
                        $link = $card['link'];
                    }
                }

                $image_alt = !empty($card['image']['alt']) ? $card['image']['alt'] : $title;
                $category = $card['category'] ?? '';
                $cta_text = $card['cta_text'] ?? '';
                $location = $card['location'] ?? '';
                $price = $card['price'] ?? '';
                $has_deal_discount = $card['has_deal_discount'] ?? false;
                $is_package = $card['is_package'] ?? false;
                $duration_price = $card['duration_price'] ?? '';
                $column_span = $card['column_span'] ?? 1;

                // Badge color: usar individual si existe, sino usar global
                $card_badge_color = !empty($card['badge_color_variant']) ? $card['badge_color_variant'] : $badge_color_variant;
            ?>

            <article
                class="sbs-card"
                data-index="<?php echo $index; ?>"
                style="grid-column: span <?php echo esc_attr($column_span); ?>;">

                <!-- Image (con link wrapper) -->
                <a href="<?php echo esc_url($link); ?>" class="sbs-card__image-link">
                    <div class="sbs-card__image">
                        <img
                            src="<?php echo esc_url($image_url); ?>"
                            alt="<?php echo esc_attr($image_alt); ?>"
                            loading="lazy">
                    </div>
                </a>

                <!-- Content -->
                <div class="sbs-card__content">

                    <!-- Category Badge -->
                    <?php if ($category): ?>
                        <span class="sbs-card__badge sbs-card__badge--<?php echo esc_attr($card_badge_color); ?>">
                            <?php echo esc_html($category); ?>
                        </span>
                    <?php endif; ?>

                    <!-- Title -->
                    <?php if ($title): ?>
                        <h3 class="sbs-card__title">
                            <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                        </h3>
                    <?php endif; ?>

                    <!-- Excerpt -->
                    <?php if ($excerpt): ?>
                        <p class="sbs-card__excerpt"><?php echo esc_html($excerpt); ?></p>
                    <?php endif; ?>

                    <!-- Divider Line -->
                    <?php if (($title || $excerpt) && ($is_package ? $duration_price : ($location || $price))): ?>
                        <div class="sbs-card__divider"></div>
                    <?php endif; ?>

                    <?php if ($is_package && $duration_price): ?>
                        <!-- Package: Combined Duration + Price -->
                        <div class="sbs-card__location">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span><?php echo $has_deal_discount ? wp_kses_post($duration_price) : esc_html($duration_price); ?></span>
                        </div>
                    <?php else: ?>
                        <!-- Regular: Location -->
                        <?php if ($location): ?>
                            <div class="sbs-card__location">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="currentColor"/>
                                </svg>
                                <span><?php echo esc_html($location); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Regular: Price -->
                        <?php if ($price): ?>
                            <div class="sbs-card__price"><?php echo $has_deal_discount ? wp_kses_post($price) : esc_html($price); ?></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- CTA Button -->
                    <?php if ($cta_text): ?>
                        <a href="<?php echo esc_url($link); ?>" class="sbs-card__button sbs-card__button--<?php echo esc_attr($button_color_variant); ?>">
                            <?php echo esc_html($cta_text); ?>
                        </a>
                    <?php endif; ?>

                </div><!-- .sbs-card__content -->

            </article><!-- .sbs-card -->

            <?php endforeach; ?>
        </div><!-- .sbs-container -->

        <!-- Pagination Dots (Mobile only) -->
        <?php if ($show_dots): ?>
            <div class="sbs-dots">
                <?php foreach ($cards as $i => $card): ?>
                    <button
                        class="sbs-dot <?php echo $i === 0 ? 'is-active' : ''; ?>"
                        data-slide="<?php echo $i; ?>"
                        aria-label="<?php echo esc_attr(sprintf(__('Ir a card %d', 'travel-blocks'), $i + 1)); ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div><!-- .sbs-wrapper -->

</section>
