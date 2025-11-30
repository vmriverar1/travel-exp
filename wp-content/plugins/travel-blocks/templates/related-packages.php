<?php
/**
 * Template: Related Packages Block
 *
 * Diseño basado en PostsCarousel Material Design:
 * - Cards verticales con imagen de fondo completa
 * - Overlay gradient
 * - Contenido en la parte inferior
 * - Grid de 3 columnas en desktop
 *
 * @var array $data Block data
 */

use Travel\Blocks\Helpers\IconHelper;

// Extract data from template variables
$block_id = $block_id ?? 'related-packages-' . uniqid();
$class_name = $class_name ?? 'related-packages';
$section_title = $section_title ?? __('', 'travel-blocks');
$layout = $layout ?? 'vertical';
$button_color = $button_color ?? 'primary';
$badge_color = $badge_color ?? 'primary';
$button_text = $button_text ?? 'View Details';
$description_lines = $description_lines ?? 3;
$text_alignment = $text_alignment ?? 'left';
$button_alignment = $button_alignment ?? 'left';
$card_min_height = $card_min_height ?? 350;
$grid_width = $grid_width ?? '33.333';
$card_gap = $card_gap ?? 24;
$hover_effect = $hover_effect ?? 'lift';
$mobile_card_height = $mobile_card_height ?? 280;
$mobile_card_width = $mobile_card_width ?? 85;
$slider_autoplay = $slider_autoplay ?? false;
$slider_autoplay_delay = $slider_autoplay_delay ?? 5000;
$slider_speed = $slider_speed ?? 300;
$slider_show_arrows = $slider_show_arrows ?? true;
$slider_arrows_position = $slider_arrows_position ?? 'sides';
$slider_show_dots = $slider_show_dots ?? true;
$show_image = $show_image ?? true;
$show_destination = $show_destination ?? true;
$show_title = $show_title ?? true;
$show_excerpt = $show_excerpt ?? false;
$show_location = $show_location ?? false;
$show_duration = $show_duration ?? true;
$show_price = $show_price ?? true;
$show_button = $show_button ?? true;
$post_type = $post_type ?? 'package';
$packages = $packages ?? [];

// Limit to 3 packages
$packages = array_slice($packages, 0, 3);

if (empty($packages)) return;

// Add layout class and hover effect class
$layout_class = $layout === 'horizontal' ? 'related-packages--horizontal' : 'related-packages--vertical';
$hover_class = 'rp-hover--' . $hover_effect;
$text_align_class = 'rp-text-' . $text_alignment;
$button_align_class = 'rp-button-' . $button_alignment;
$arrows_position_class = 'rp-arrows--' . $slider_arrows_position;
?>

<section
    id="<?php echo esc_attr($block_id); ?>"
    class="related-packages related-packages--material <?php echo esc_attr($layout_class); ?> <?php echo esc_attr($hover_class); ?> <?php echo esc_attr($text_align_class); ?> <?php echo esc_attr($button_align_class); ?> <?php echo esc_attr($arrows_position_class); ?> <?php echo esc_attr($class_name); ?>"
    style="--card-gap: <?php echo esc_attr($card_gap); ?>px; --description-lines: <?php echo esc_attr($description_lines); ?>; --card-height: <?php echo esc_attr($card_min_height); ?>px; --mobile-card-height: <?php echo esc_attr($mobile_card_height); ?>px; --mobile-card-width: <?php echo esc_attr($mobile_card_width); ?>%;"
    data-slider-autoplay="<?php echo $slider_autoplay ? 'true' : 'false'; ?>"
    data-slider-autoplay-delay="<?php echo esc_attr($slider_autoplay_delay); ?>"
    data-slider-speed="<?php echo esc_attr($slider_speed); ?>"
    data-slider-show-arrows="<?php echo $slider_show_arrows ? 'true' : 'false'; ?>"
    data-slider-show-dots="<?php echo $slider_show_dots ? 'true' : 'false'; ?>"
>

    <?php if ($section_title): ?>
        <h2 class="related-packages__title"><?php echo esc_html($section_title); ?></h2>
    <?php endif; ?>

    <div class="related-packages__grid">
        <?php foreach ($packages as $index => $package):
            $image_url = $package['featured_image'] ?: 'https://picsum.photos/800/600?random=' . ($index + 1);
            $title = $package['title'] ?? __('Sin título', 'travel-blocks');
            $permalink = $package['permalink'] ?? '#';
            $destination = $package['destination'] ?? '';
            $duration = $package['duration'] ?? '';
            $price = (float)($package['price'] ?? 0);
            $excerpt = $package['excerpt'] ?? '';
            $location = $package['location'] ?? '';
        ?>
            <article class="rp-card" style="min-height: <?php echo esc_attr($card_min_height); ?>px; flex: 0 0 calc(<?php echo esc_attr($grid_width); ?>% - 12px); max-width: calc(<?php echo esc_attr($grid_width); ?>% - 12px);">
                <!-- Card Image Background (Full) -->
                <?php if ($show_image): ?>
                    <div class="rp-card__image-bg" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                        <img
                            src="<?php echo esc_url($image_url); ?>"
                            alt="<?php echo esc_attr($title); ?>"
                            loading="lazy"
                            style="display: none;">
                    </div>
                <?php endif; ?>

                <!-- Card Link and Content -->
                <a href="<?php echo esc_url($permalink); ?>" class="rp-card__link">
                    <div class="rp-card__content">

                        <!-- Destination Badge (above title) -->
                        <?php if ($show_destination && $destination): ?>
                            <span class="rp-card__badge rp-card__badge--<?php echo esc_attr($badge_color); ?>"><?php echo esc_html($destination); ?></span>
                        <?php endif; ?>

                        <!-- Title -->
                        <?php if ($show_title): ?>
                            <h3 class="rp-card__title"><?php echo esc_html($title); ?></h3>
                        <?php endif; ?>

                        <?php if ($post_type === 'post'): ?>
                            <!-- Excerpt for Blog Posts -->
                            <?php if ($show_excerpt && !empty($excerpt)): ?>
                                <p class="rp-card__excerpt"><?php echo esc_html(wp_trim_words($excerpt, 15)); ?></p>
                            <?php endif; ?>

                            <!-- Date for Blog Posts -->
                            <?php if ($show_duration && $duration): ?>
                                <div class="rp-card__meta-line">
                                    <span class="rp-card__meta-item">
                                        <?php echo IconHelper::get_icon_svg('calendar', 14, 'currentColor'); ?>
                                        <?php echo esc_html($duration); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Excerpt for Packages -->
                            <?php if ($show_excerpt && !empty($excerpt)): ?>
                                <p class="rp-card__excerpt"><?php echo esc_html(wp_trim_words($excerpt, 15)); ?></p>
                            <?php endif; ?>

                            <!-- Location for Packages -->
                            <?php if ($show_location && !empty($location)): ?>
                                <div class="rp-card__location">
                                    <?php echo IconHelper::get_icon_svg('map-pin', 14, 'currentColor'); ?>
                                    <span><?php echo esc_html($location); ?></span>
                                </div>
                            <?php endif; ?>

                            <!-- Divider Line for Packages -->
                            <?php if (($show_duration && $duration) || ($show_price && $price > 0)): ?>
                                <div class="rp-card__divider"></div>
                            <?php endif; ?>

                            <!-- Meta Line for Packages (Duration | Price) -->
                            <?php if (($show_duration && $duration) || ($show_price && $price > 0)): ?>
                                <div class="rp-card__meta-line">
                                    <?php if ($show_duration && $duration): ?>
                                        <span class="rp-card__meta-item"><?php echo esc_html($duration); ?></span>
                                    <?php endif; ?>

                                    <?php if ($show_duration && $duration && $show_price && $price > 0): ?>
                                        <span class="rp-card__meta-separator">|</span>
                                    <?php endif; ?>

                                    <?php if ($show_price && $price > 0): ?>
                                        <span class="rp-card__meta-item">
                                            <?php _e('From', 'travel-blocks'); ?> <strong>$<?php echo number_format($price, 0); ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- CTA Button -->
                        <?php if ($show_button): ?>
                            <button class="rp-card__button rp-card__button--<?php echo esc_attr($button_color); ?>">
                                <?php echo esc_html($button_text); ?>
                                <?php echo IconHelper::get_icon_svg('arrow-right', 16, 'currentColor'); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Slider Navigation Arrows (Mobile Only) -->
    <?php if ($slider_show_arrows): ?>
        <button class="rp-slider__arrow rp-slider__arrow--prev" aria-label="Previous slide">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <button class="rp-slider__arrow rp-slider__arrow--next" aria-label="Next slide">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    <?php endif; ?>

    <!-- Slider Pagination Dots (Mobile Only) -->
    <?php if ($slider_show_dots): ?>
        <div class="rp-slider__dots" role="tablist">
            <?php foreach ($packages as $index => $package): ?>
                <button
                    class="rp-slider__dot <?php echo $index === 0 ? 'rp-slider__dot--active' : ''; ?>"
                    data-slide-index="<?php echo esc_attr($index); ?>"
                    role="tab"
                    aria-label="Go to slide <?php echo esc_attr($index + 1); ?>"
                    aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                ></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>
