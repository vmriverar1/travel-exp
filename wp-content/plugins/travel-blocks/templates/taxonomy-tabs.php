<?php
/**
 * Template: Taxonomy Tabs
 *
 * Organiza cards por taxonomías/categorías en tabs navegables
 *
 * @var array $block Block settings from ACF
 * @var string $content InnerBlocks content
 * @var bool $is_preview Whether in editor preview
 * @var int $post_id Current post ID
 */

// Get the TaxonomyTabs instance
$taxonomy_tabs_instance = $GLOBALS['taxonomy_tabs_instance'] ?? null;

if (!$taxonomy_tabs_instance) {
    echo '<div style="padding: 20px; background: #f0f0f0; border: 2px dashed #ccc; text-align: center;">';
    echo '<p>Error: TaxonomyTabs instance not found.</p>';
    echo '</div>';
    return;
}

// Get processed data from the instance
$data = $taxonomy_tabs_instance->get_template_data($block);
$tabs = $data['tabs'] ?? [];
$appearance = $data['appearance'] ?? [];
$slider = $data['slider'] ?? [];

// Appearance settings
$tabs_style = $appearance['tabs_style'] ?? 'pills';
$tabs_alignment = $appearance['tabs_alignment'] ?? 'center';
$cards_per_row = $appearance['cards_per_row'] ?? 3;
$card_gap = $appearance['card_gap'] ?? 24;
$card_height_desktop = $appearance['card_height_desktop'] ?? 450;
$button_color_variant = $appearance['button_color_variant'] ?? 'primary';
$badge_color_variant = $appearance['badge_color_variant'] ?? 'secondary';

// Slider settings (mobile)
$card_height = $slider['card_height'] ?? 450;
$show_arrows = $slider['show_arrows'] ?? true;
$arrows_position = $slider['arrows_position'] ?? 'sides';
$show_dots = $slider['show_dots'] ?? true;
$autoplay = $slider['autoplay'] ?? false;
$autoplay_delay = $slider['autoplay_delay'] ?? 5000;
$slider_speed = $slider['slider_speed'] ?? 0.4;

// Block settings
$block_id = 'tt-' . ($block['id'] ?? uniqid());
$align = $block['align'] ?? 'wide';

// Wrapper classes
$wrapper_classes = ['taxonomy-tabs-wrapper'];
if ($tabs_style === 'hero-overlap') {
    $wrapper_classes[] = 'taxonomy-tabs-wrapper--hero-overlap';
}

// If no tabs, show message
if (empty($tabs)) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo '<div style="padding: 20px; background: #f0f0f0; border: 2px dashed #ccc; text-align: center;">';
        echo '<p>Taxonomy Tabs: Sin tabs para mostrar. Selecciona términos de taxonomía o activa el modo vista previa.</p>';
        echo '</div>';
    }
    return;
}

// Block classes
$classes = [
    'taxonomy-tabs',
    'taxonomy-tabs--' . $tabs_style,
    'taxonomy-tabs--align-' . $tabs_alignment,
    'arrows-' . $arrows_position,
    'align' . $align,
];
?>

<div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>">
<section
    id="<?php echo esc_attr($block_id); ?>"
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    style="--cards-per-row: <?php echo esc_attr($cards_per_row); ?>; --card-gap: <?php echo esc_attr($card_gap); ?>px; --card-height: <?php echo esc_attr($card_height); ?>px; --card-height-desktop: <?php echo esc_attr($card_height_desktop); ?>px;"
    data-tabs-count="<?php echo count($tabs); ?>"
    data-slider-autoplay="<?php echo $autoplay ? '1' : '0'; ?>"
    data-slider-delay="<?php echo esc_attr($autoplay_delay); ?>"
    data-slider-speed="<?php echo esc_attr($slider_speed); ?>">

    <!-- Tabs Navigation -->
    <div class="tt-nav-wrapper">
        <div class="tt-nav" role="tablist" aria-label="<?php esc_attr_e('Content categories', 'travel-blocks'); ?>">
            <?php foreach ($tabs as $index => $tab): ?>
                <button
                    class="tt-nav__item <?php echo $index === 0 ? 'is-active' : ''; ?> <?php echo !empty($tab['icon']) ? 'tt-nav__item--has-icon' : ''; ?>"
                    role="tab"
                    aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                    aria-controls="tt-panel-<?php echo esc_attr($block_id); ?>-<?php echo esc_attr($tab['slug']); ?>"
                    id="tt-tab-<?php echo esc_attr($block_id); ?>-<?php echo esc_attr($tab['slug']); ?>"
                    data-tab-index="<?php echo $index; ?>">

                    <?php if (!empty($tab['icon'])): ?>
                        <span class="tt-nav__icon">
                            <?php if (!empty($tab['icon']['url'])):
                                // Check if icon is SVG for inline rendering (needed for hero-overlap style)
                                $is_svg = !empty($tab['icon']['mime_type']) && $tab['icon']['mime_type'] === 'image/svg+xml';

                                if ($is_svg && !empty($tab['icon']['path'])):
                                    // Inline SVG for color manipulation
                                    $svg_content = file_get_contents($tab['icon']['path']);
                                    if ($svg_content !== false):
                                        // Add class to SVG for styling
                                        $svg_content = str_replace('<svg', '<svg class="tt-nav__icon-svg"', $svg_content);
                                        echo $svg_content;
                                    endif;
                                else:
                                    // Regular image fallback
                                    ?>
                                    <img
                                        src="<?php echo esc_url($tab['icon']['url']); ?>"
                                        alt="<?php echo esc_attr($tab['icon']['alt'] ?? $tab['name']); ?>"
                                        class="tt-nav__icon-img">
                                <?php endif; ?>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>

                    <span class="tt-nav__text"><?php echo esc_html($tab['name']); ?></span>
                    <span class="tt-nav__count"><?php echo count($tab['cards']); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- InnerBlocks Content (antes de los tabs) -->
    <div class="tt-innerblocks-content">
        <InnerBlocks />
    </div>

    <!-- Tabs Content -->
    <div class="tt-panels">
        <?php foreach ($tabs as $index => $tab): ?>
            <div
                class="tt-panel <?php echo $index === 0 ? 'is-active' : ''; ?>"
                role="tabpanel"
                aria-labelledby="tt-tab-<?php echo esc_attr($block_id); ?>-<?php echo esc_attr($tab['slug']); ?>"
                id="tt-panel-<?php echo esc_attr($block_id); ?>-<?php echo esc_attr($tab['slug']); ?>"
                data-panel-index="<?php echo $index; ?>">

                <!-- Cards Grid / Slider Container -->
                <?php if (!empty($tab['cards'])): ?>
                    <!-- Wrapper for slider + controls -->
                    <div class="tt-slider-wrapper">

                        <!-- Mobile Navigation Arrows (solo si no es 'bottom') -->
                        <?php if ($show_arrows && $arrows_position !== 'bottom'): ?>
                            <button class="tt-arrow tt-arrow--prev" aria-label="<?php esc_attr_e('Anterior', 'travel-blocks'); ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button class="tt-arrow tt-arrow--next" aria-label="<?php esc_attr_e('Siguiente', 'travel-blocks'); ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        <?php endif; ?>

                        <!-- Cards Grid (desktop) / Slider (mobile) -->
                        <div class="tt-cards-grid">
                        <?php foreach ($tab['cards'] as $card_index => $card):
                        // Get card data
                        $image_url = !empty($card['image']['url'])
                            ? $card['image']['url']
                            : 'https://picsum.photos/800/600?random=' . ($card_index + 1);

                        $title = $card['title'] ?? '';
                        $excerpt = $card['excerpt'] ?? $card['description'] ?? '';

                        // Handle link
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

                        // Badge color
                        $card_badge_color = !empty($card['badge_color_variant']) ? $card['badge_color_variant'] : $badge_color_variant;
                    ?>

                    <article class="tt-card" data-index="<?php echo $card_index; ?>">
                        <!-- Card Image -->
                        <div class="tt-card__image-bg" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                            <img
                                src="<?php echo esc_url($image_url); ?>"
                                alt="<?php echo esc_attr($image_alt); ?>"
                                loading="lazy"
                                style="display: none;">
                        </div>

                        <!-- Category Badge -->
                        <?php if ($category): ?>
                            <span class="tt-card__badge tt-card__badge--<?php echo esc_attr($card_badge_color); ?>">
                                <?php echo esc_html($category); ?>
                            </span>
                        <?php endif; ?>

                        <!-- Card Content -->
                        <a href="<?php echo esc_url($link); ?>" class="tt-card__link">
                            <div class="tt-card__content">
                                <?php if ($title): ?>
                                    <h3 class="tt-card__title"><?php echo esc_html($title); ?></h3>
                                <?php endif; ?>

                                <?php if ($excerpt): ?>
                                    <p class="tt-card__excerpt"><?php echo esc_html($excerpt); ?></p>
                                <?php endif; ?>

                                <!-- Divider Line -->
                                <?php if (($title || $excerpt) && ($is_package ? $duration_price : ($location || $price))): ?>
                                    <div class="tt-card__divider"></div>
                                <?php endif; ?>

                                <?php if ($is_package && $duration_price): ?>
                                    <!-- Package: Combined Duration + Price -->
                                    <div class="tt-card__meta">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span><?php echo $has_deal_discount ? wp_kses_post($duration_price) : esc_html($duration_price); ?></span>
                                    </div>
                                <?php else: ?>
                                    <!-- Regular: Location -->
                                    <?php if ($location): ?>
                                        <div class="tt-card__meta">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="currentColor"/>
                                            </svg>
                                            <span><?php echo esc_html($location); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Regular: Price -->
                                    <?php if ($price): ?>
                                        <div class="tt-card__price"><?php echo $has_deal_discount ? wp_kses_post($price) : esc_html($price); ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- CTA Button -->
                                <?php if ($cta_text): ?>
                                    <button class="tt-card__button tt-card__button--<?php echo esc_attr($button_color_variant); ?>">
                                        <?php echo esc_html($cta_text); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>

                        <?php endforeach; // end cards ?>
                        </div><!-- .tt-cards-grid -->

                        <!-- Mobile Pagination Dots -->
                        <?php if ($show_dots || ($show_arrows && $arrows_position === 'bottom')): ?>
                            <div class="tt-dots">
                                <!-- Botón izquierdo (solo para variante bottom) -->
                                <?php if ($show_arrows && $arrows_position === 'bottom'): ?>
                                    <button class="tt-arrow tt-arrow--prev tt-arrow--bottom" aria-label="<?php esc_attr_e('Anterior', 'travel-blocks'); ?>">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                <?php endif; ?>

                                <!-- Dots -->
                                <?php if ($show_dots): ?>
                                    <?php foreach ($tab['cards'] as $i => $card): ?>
                                        <button
                                            class="tt-dot <?php echo $i === 0 ? 'is-active' : ''; ?>"
                                            data-slide="<?php echo $i; ?>"
                                            aria-label="<?php echo esc_attr(sprintf(__('Ir a card %d', 'travel-blocks'), $i + 1)); ?>">
                                        </button>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- Botón derecho (solo para variante bottom) -->
                                <?php if ($show_arrows && $arrows_position === 'bottom'): ?>
                                    <button class="tt-arrow tt-arrow--next tt-arrow--bottom" aria-label="<?php esc_attr_e('Siguiente', 'travel-blocks'); ?>">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div><!-- .tt-dots -->
                        <?php endif; ?>

                    </div><!-- .tt-slider-wrapper -->
                <?php else: ?>
                    <div class="tt-empty-state" style="padding: 60px 20px; text-align: center; color: #64748b;">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" style="margin: 0 auto 16px; opacity: 0.3;">
                            <path d="M3 3h18v18H3z" stroke="currentColor" stroke-width="2"/>
                            <path d="M9 9l6 6m0-6l-6 6" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <p style="margin: 0; font-size: 16px; font-weight: 500;"><?php _e('No hay contenido disponible para esta categoría', 'travel-blocks'); ?></p>
                    </div>
                <?php endif; ?>

            </div><!-- .tt-panel -->
        <?php endforeach; // end tabs ?>
    </div><!-- .tt-panels -->

</section>
</div>
