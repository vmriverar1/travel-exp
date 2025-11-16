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

// Other settings
$show_favorite = $data['show_favorite'] ?? true;

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

                        <!-- Favorite Button (Heart - top right on image) -->
                        <?php if ($show_favorite): ?>
                            <button class="tt-card__favorite" aria-label="<?php esc_attr_e('Agregar a favoritos', 'travel-blocks'); ?>" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
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
                                <?php if (($title || $excerpt) && ($is_package ? ($location || $duration_price) : ($location || $price))): ?>
                                    <div class="tt-card__divider"></div>
                                <?php endif; ?>

                                <?php if ($is_package): ?>
                                    <!-- Package: Location -->
                                    <?php if ($location): ?>
                                        <div class="tt-card__meta">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="18" viewBox="0 0 13 18" fill="none">
                                                <path d="M6.44287 0C6.01995 0.000442936 5.60112 0.0436682 5.18638 0.129676C4.77164 0.215684 4.36895 0.342826 3.9783 0.511101C3.58765 0.679377 3.21653 0.885562 2.86494 1.12966C2.51335 1.37375 2.18803 1.65107 1.88898 1.96163C1.58992 2.27218 1.32287 2.61002 1.08782 2.97513C0.852763 3.34024 0.654215 3.72564 0.492172 4.13131C0.330128 4.53699 0.207695 4.95517 0.124873 5.38586C0.0420507 5.81654 0.000426401 6.25148 0 6.69067C0 11.7256 5.75954 17.2065 6.00684 17.4363L6.43636 17.8418C6.98303 17.3011 12.7491 12.2324 12.8857 6.69067C12.8853 6.25148 12.8437 5.81654 12.7609 5.38586C12.678 4.95517 12.5556 4.53699 12.3936 4.13131C12.2315 3.72564 12.033 3.34024 11.7979 2.97513C11.5629 2.61002 11.2958 2.27218 10.9968 1.96163C10.6977 1.65107 10.3724 1.37375 10.0208 1.12966C9.66921 0.885562 9.29809 0.679377 8.90744 0.511101C8.51679 0.342826 8.1141 0.215684 7.69936 0.129676C7.28463 0.0436683 6.8658 0.000442936 6.44287 0ZM6.44287 16.0036C5.14128 14.6519 1.30159 10.3874 1.30159 6.69067C1.30159 5.98268 1.43204 5.30162 1.69295 4.64752C1.95385 3.99341 2.32535 3.41604 2.80744 2.91541C3.28953 2.41478 3.84551 2.029 4.47539 1.75806C5.10527 1.48712 5.7611 1.35165 6.44287 1.35165C7.12465 1.35165 7.78048 1.48712 8.41035 1.75806C9.04023 2.029 9.59622 2.41478 10.0783 2.91541C10.5604 3.41605 10.9319 3.99341 11.1928 4.64752C11.4537 5.30162 11.5842 5.98268 11.5842 6.69067C11.5842 10.455 7.74446 14.6654 6.44287 16.0036ZM6.44287 3.62243C2.42096 3.75759 2.42096 9.81299 6.44287 9.94815C10.4648 9.81299 10.4648 3.75759 6.44287 3.62243ZM6.44287 8.5965C6.21158 8.5965 5.9891 8.55054 5.77542 8.45863C5.56174 8.36672 5.37313 8.23584 5.20958 8.06601C5.04604 7.89618 4.92001 7.70031 4.8315 7.47841C4.74299 7.25651 4.69874 7.02547 4.69874 6.78529C4.69874 6.54511 4.74299 6.31407 4.8315 6.09217C4.92001 5.87027 5.04604 5.6744 5.20958 5.50457C5.37313 5.33473 5.56174 5.20386 5.77542 5.11195C5.9891 5.02003 6.21158 4.97408 6.44287 4.97408C6.67416 4.97408 6.89664 5.02003 7.11032 5.11195C7.324 5.20386 7.51261 5.33473 7.67616 5.50457C7.8397 5.6744 7.96573 5.87027 8.05424 6.09217C8.14275 6.31407 8.187 6.54511 8.187 6.78529C8.187 7.02547 8.14275 7.25651 8.05424 7.47841C7.96573 7.70031 7.8397 7.89618 7.67616 8.06601C7.51261 8.23584 7.324 8.36672 7.11032 8.45863C6.89664 8.55054 6.67416 8.5965 6.44287 8.5965Z" fill="white"/>
                                            </svg>
                                            <span><?php echo esc_html($location); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Package: Combined Duration + Price -->
                                    <?php if ($duration_price): ?>
                                        <div class="tt-card__meta tt-card__meta--duration-price">
                                            <span><?php echo $has_deal_discount ? wp_kses_post($duration_price) : esc_html($duration_price); ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <!-- Regular: Location -->
                                    <?php if ($location): ?>
                                        <div class="tt-card__meta">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="18" viewBox="0 0 13 18" fill="none">
                                                <path d="M6.44287 0C6.01995 0.000442936 5.60112 0.0436682 5.18638 0.129676C4.77164 0.215684 4.36895 0.342826 3.9783 0.511101C3.58765 0.679377 3.21653 0.885562 2.86494 1.12966C2.51335 1.37375 2.18803 1.65107 1.88898 1.96163C1.58992 2.27218 1.32287 2.61002 1.08782 2.97513C0.852763 3.34024 0.654215 3.72564 0.492172 4.13131C0.330128 4.53699 0.207695 4.95517 0.124873 5.38586C0.0420507 5.81654 0.000426401 6.25148 0 6.69067C0 11.7256 5.75954 17.2065 6.00684 17.4363L6.43636 17.8418C6.98303 17.3011 12.7491 12.2324 12.8857 6.69067C12.8853 6.25148 12.8437 5.81654 12.7609 5.38586C12.678 4.95517 12.5556 4.53699 12.3936 4.13131C12.2315 3.72564 12.033 3.34024 11.7979 2.97513C11.5629 2.61002 11.2958 2.27218 10.9968 1.96163C10.6977 1.65107 10.3724 1.37375 10.0208 1.12966C9.66921 0.885562 9.29809 0.679377 8.90744 0.511101C8.51679 0.342826 8.1141 0.215684 7.69936 0.129676C7.28463 0.0436683 6.8658 0.000442936 6.44287 0ZM6.44287 16.0036C5.14128 14.6519 1.30159 10.3874 1.30159 6.69067C1.30159 5.98268 1.43204 5.30162 1.69295 4.64752C1.95385 3.99341 2.32535 3.41604 2.80744 2.91541C3.28953 2.41478 3.84551 2.029 4.47539 1.75806C5.10527 1.48712 5.7611 1.35165 6.44287 1.35165C7.12465 1.35165 7.78048 1.48712 8.41035 1.75806C9.04023 2.029 9.59622 2.41478 10.0783 2.91541C10.5604 3.41605 10.9319 3.99341 11.1928 4.64752C11.4537 5.30162 11.5842 5.98268 11.5842 6.69067C11.5842 10.455 7.74446 14.6654 6.44287 16.0036ZM6.44287 3.62243C2.42096 3.75759 2.42096 9.81299 6.44287 9.94815C10.4648 9.81299 10.4648 3.75759 6.44287 3.62243ZM6.44287 8.5965C6.21158 8.5965 5.9891 8.55054 5.77542 8.45863C5.56174 8.36672 5.37313 8.23584 5.20958 8.06601C5.04604 7.89618 4.92001 7.70031 4.8315 7.47841C4.74299 7.25651 4.69874 7.02547 4.69874 6.78529C4.69874 6.54511 4.74299 6.31407 4.8315 6.09217C4.92001 5.87027 5.04604 5.6744 5.20958 5.50457C5.37313 5.33473 5.56174 5.20386 5.77542 5.11195C5.9891 5.02003 6.21158 4.97408 6.44287 4.97408C6.67416 4.97408 6.89664 5.02003 7.11032 5.11195C7.324 5.20386 7.51261 5.33473 7.67616 5.50457C7.8397 5.6744 7.96573 5.87027 8.05424 6.09217C8.14275 6.31407 8.187 6.54511 8.187 6.78529C8.187 7.02547 8.14275 7.25651 8.05424 7.47841C7.96573 7.70031 7.8397 7.89618 7.67616 8.06601C7.51261 8.23584 7.324 8.36672 7.11032 8.45863C6.89664 8.55054 6.67416 8.5965 6.44287 8.5965Z" fill="white"/>
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
