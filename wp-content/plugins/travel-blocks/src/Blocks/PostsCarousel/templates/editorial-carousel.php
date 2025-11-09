<?php
/**
 * Template: Posts Carousel (Native CSS Scroll-Snap)
 *
 * @var array $data Block data and settings
 */

// Check if using dynamic packages or regular posts
$use_dynamic = $data['use_dynamic'] ?? false;
$items = [];

if ($use_dynamic) {
    // Use dynamic content from ContentQueryHelper (packages or posts)
    $items = $data['items'] ?? [];
    $query = null;
} else {
    // Query posts
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => $data['posts_per_page'],
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ];

    $query = new \WP_Query($args);
}

// Block classes
$classes = ['posts-carousel', 'align' . $data['align']];
?>

<div <?php echo $data['block_wrapper_attributes']; ?>>
<section
    id="<?php echo esc_attr($data['block_id']); ?>"
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    data-autoplay="<?php echo esc_attr($data['autoplay'] ? '1' : '0'); ?>"
    data-delay="<?php echo esc_attr($data['autoplay_delay']); ?>">

    <?php if ($use_dynamic ? !empty($items) : $query->have_posts()): ?>

        <!-- Skeleton Loader (visible mientras carga) -->
        <div class="pc-loader">
            <div class="pc-skeleton"></div>
        </div>

        <!-- Carousel Container -->
        <div class="pc-carousel">

            <!-- Navigation Arrows -->
            <?php if ($data['show_arrows']): ?>
                <button class="pc-nav pc-nav--prev" aria-label="<?php esc_attr_e('Previous slide', 'acf-gbr'); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button class="pc-nav pc-nav--next" aria-label="<?php esc_attr_e('Next slide', 'acf-gbr'); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            <?php endif; ?>

            <!-- Slides Wrapper -->
            <div class="pc-slides">
                <?php
                $index = 0;

                if ($use_dynamic) {
                    // Dynamic content (packages or blog posts)
                    foreach ($items as $item) {
                        // Extract data (already formatted by ContentQueryHelper)
                        $thumbnail = !empty($item['image']['sizes']['large']) ? $item['image']['sizes']['large'] : (!empty($item['image']['url']) ? $item['image']['url'] : '');
                        $cat_name = $item['category'] ?? '';
                        $title = $item['title'] ?? '';
                        $excerpt = $item['description'] ?? '';
                        $date = $item['date'] ?? '';
                        $link = $item['link']['url'] ?? '#';
                        $cta_text = $item['cta_text'] ?? 'Ver Paquete';
                        $location = $item['location'] ?? '';
                        $price = $item['price'] ?? '';
                        $has_deal_discount = $item['has_deal_discount'] ?? false;
                        $is_package = $item['is_package'] ?? false;
                        $duration_price = $item['duration_price'] ?? '';
                        ?>

                        <article
                            class="pc-slide <?php echo $index === 0 ? 'is-active' : ''; ?>"
                            data-slide-index="<?php echo $index; ?>"
                            style="<?php echo $thumbnail ? 'background-image: url(' . esc_url($thumbnail) . ');' : ''; ?>">

                            <!-- Gradient Overlay -->
                            <div class="pc-slide__overlay"></div>

                            <!-- Category Badge -->
                            <?php if ($cat_name): ?>
                                <span class="pc-slide__category"><?php echo esc_html($cat_name); ?></span>
                            <?php endif; ?>

                            <!-- Content -->
                            <div class="pc-slide__content">
                                <div class="pc-slide__text">
                                    <h3 class="pc-slide__title">
                                        <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                                    </h3>
                                    <p class="pc-slide__excerpt"><?php echo esc_html($excerpt); ?></p>

                                    <?php if ($is_package && $duration_price): ?>
                                        <!-- Package: Combined Duration + Price -->
                                        <div class="pc-slide__meta">
                                            <span class="pc-slide__location">⏱️ <?php echo $has_deal_discount ? wp_kses_post($duration_price) : esc_html($duration_price); ?></span>
                                        </div>
                                    <?php elseif ($location || $price): ?>
                                        <!-- Regular: Location and Price -->
                                        <div class="pc-slide__meta">
                                            <?php if ($location): ?>
                                                <span class="pc-slide__location">📍 <?php echo esc_html($location); ?></span>
                                            <?php endif; ?>
                                            <?php if ($price): ?>
                                                <span class="pc-slide__price"><?php echo $has_deal_discount ? wp_kses_post($price) : esc_html($price); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($date): ?>
                                        <time class="pc-slide__date"><?php echo esc_html($date); ?></time>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo esc_url($link); ?>" class="pc-slide__readmore">
                                    <?php echo esc_html($cta_text); ?>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </a>
                            </div>
                        </article>

                        <?php
                        $index++;
                    }
                } else {
                    // Regular posts
                    while ($query->have_posts()):
                        $query->the_post();

                        // Get post data
                        $thumbnail = has_post_thumbnail()
                            ? get_the_post_thumbnail_url(get_the_ID(), 'large')
                            : '';

                        $category = get_the_category();
                        $cat_name = !empty($category) ? esc_html($category[0]->name) : '';

                        $excerpt = wp_trim_words(get_the_excerpt(), 20, '...');
                    ?>

                    <article
                        class="pc-slide <?php echo $index === 0 ? 'is-active' : ''; ?>"
                        data-slide-index="<?php echo $index; ?>"
                        style="<?php echo $thumbnail ? 'background-image: url(' . esc_url($thumbnail) . ');' : ''; ?>">

                        <!-- Gradient Overlay -->
                        <div class="pc-slide__overlay"></div>

                        <!-- Category Badge -->
                        <?php if ($cat_name): ?>
                            <span class="pc-slide__category"><?php echo $cat_name; ?></span>
                        <?php endif; ?>

                        <!-- Content -->
                        <div class="pc-slide__content">
                            <div class="pc-slide__text">
                                <h3 class="pc-slide__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="pc-slide__excerpt"><?php echo esc_html($excerpt); ?></p>
                                <time class="pc-slide__date" datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="pc-slide__readmore">
                                <?php _e('Read More', 'acf-gbr'); ?>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </a>
                        </div>
                    </article>

                    <?php
                        $index++;
                    endwhile;
                    wp_reset_postdata();
                }
                ?>
            </div>

            <!-- Pagination Dots -->
            <?php if ($data['show_dots']): ?>
                <div class="pc-dots">
                    <?php for ($i = 0; $i < $index; $i++): ?>
                        <button
                            class="pc-dot <?php echo $i === 0 ? 'is-active' : ''; ?>"
                            data-slide="<?php echo $i; ?>"
                            aria-label="<?php echo esc_attr(sprintf(__('Go to slide %d', 'acf-gbr'), $i + 1)); ?>">
                        </button>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        </div>

    <?php else: ?>
        <p class="pc-empty"><?php _e('No posts found.', 'acf-gbr'); ?></p>
    <?php endif; ?>

</section>
</div>
