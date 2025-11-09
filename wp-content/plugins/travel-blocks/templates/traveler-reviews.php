<?php
/**
 * Template: Traveler Reviews
 *
 * Grid grande de reviews con filtros
 *
 * Available variables:
 * @var string $block_id
 * @var string $class_name
 * @var string $section_title
 * @var string $section_subtitle
 * @var array  $reviews
 * @var array  $platforms
 * @var bool   $show_platform_filter
 * @var int    $reviews_per_page
 * @var int    $grid_columns
 * @var string $pagination_type
 * @var bool   $is_preview
 * @var string $schema
 */

use Travel\Blocks\Helpers\IconHelper;

if (empty($reviews)) {
    echo '<div class="traveler-reviews-placeholder">';
    echo '<p>' . __('No reviews available.', 'travel-blocks') . '</p>';
    echo '</div>';
    return;
}

// Platform labels
$platform_labels = [
    'tripadvisor' => 'TripAdvisor',
    'google' => 'Google',
    'facebook' => 'Facebook',
];
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($class_name); ?>" data-reviews-per-page="<?php echo esc_attr($reviews_per_page); ?>">

    <!-- Header -->
    <div class="traveler-reviews__header">
        <?php if (!empty($section_title)): ?>
            <h2 class="traveler-reviews__title"><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>
        <?php if (!empty($section_subtitle)): ?>
            <p class="traveler-reviews__subtitle"><?php echo esc_html($section_subtitle); ?></p>
        <?php endif; ?>
    </div>

    <?php if ($show_platform_filter && count($platforms) > 1): ?>
        <!-- Platform Filters -->
        <div class="traveler-reviews__filters">
            <button class="traveler-reviews__filter-button active" data-platform="all">
                <?php _e('All Reviews', 'travel-blocks'); ?>
            </button>
            <?php foreach ($platforms as $platform): ?>
                <button class="traveler-reviews__filter-button" data-platform="<?php echo esc_attr($platform); ?>">
                    <?php echo esc_html($platform_labels[$platform] ?? ucfirst($platform)); ?>
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Reviews Grid -->
    <div class="traveler-reviews__grid" style="--grid-columns: <?php echo esc_attr($grid_columns); ?>;">
        <?php foreach ($reviews as $index => $review):
            $rating = intval($review['rating'] ?? 5);
            $author = $review['author'] ?? 'Anonymous';
            $origin = $review['origin'] ?? '';
            $traveler_type = $review['traveler_type'] ?? '';
            $content = $review['content'] ?? '';
            $platform = $review['platform'] ?? 'tripadvisor';
            $date = $review['date'] ?? '';
            $avatar = $review['avatar'] ?? '';

            // Card visibility class (for pagination)
            $card_class = $index < $reviews_per_page ? 'traveler-reviews__card' : 'traveler-reviews__card hidden';
        ?>
            <div class="<?php echo esc_attr($card_class); ?>" data-platform="<?php echo esc_attr($platform); ?>" data-index="<?php echo esc_attr($index); ?>">

                <!-- Card Header -->
                <div class="traveler-reviews__card-header">
                    <div class="traveler-reviews__avatar">
                        <?php if (!empty($avatar)): ?>
                            <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($author); ?>" />
                        <?php else: ?>
                            <?php echo IconHelper::get_icon_svg('user', 32, 'var(--color-gray-400)'); ?>
                        <?php endif; ?>
                    </div>
                    <div class="traveler-reviews__author-info">
                        <strong class="traveler-reviews__author-name"><?php echo esc_html($author); ?></strong>
                        <?php if (!empty($origin)): ?>
                            <span class="traveler-reviews__origin">
                                <?php echo IconHelper::get_icon_svg('map-pin', 14, 'currentColor'); ?>
                                <?php echo esc_html($origin); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($traveler_type)): ?>
                            <span class="traveler-reviews__traveler-type"><?php echo esc_html($traveler_type); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Rating Stars -->
                <div class="traveler-reviews__rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php
                        $color = $i <= $rating ? '#FFB400' : '#E0E0E0';
                        echo IconHelper::get_icon_svg('star', 16, $color);
                        ?>
                    <?php endfor; ?>
                    <?php if (!empty($date)): ?>
                        <span class="traveler-reviews__date"><?php echo date_i18n('M Y', strtotime($date)); ?></span>
                    <?php endif; ?>
                </div>

                <!-- Review Content -->
                <p class="traveler-reviews__content"><?php echo esc_html($content); ?></p>

                <!-- Platform Badge -->
                <div class="traveler-reviews__platform">
                    <span class="traveler-reviews__platform-badge traveler-reviews__platform-badge--<?php echo esc_attr($platform); ?>">
                        <?php echo esc_html($platform_labels[$platform] ?? ucfirst($platform)); ?>
                    </span>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <!-- No Results Message -->
    <div class="traveler-reviews__no-results" style="display: none;">
        <p><?php _e('No reviews found for this filter.', 'travel-blocks'); ?></p>
    </div>

    <?php if ($pagination_type === 'show_more' && count($reviews) > $reviews_per_page): ?>
        <!-- Show More Button -->
        <div class="traveler-reviews__pagination">
            <button class="traveler-reviews__show-more">
                <?php _e('Show more reviews', 'travel-blocks'); ?>
            </button>
            <span class="traveler-reviews__showing">
                <?php printf(__('Showing %d of %d reviews', 'travel-blocks'), $reviews_per_page, count($reviews)); ?>
            </span>
        </div>
    <?php endif; ?>

</div>

<?php if (!empty($schema) && !$is_preview): ?>
    <!-- Schema.org Review markup -->
    <script type="application/ld+json">
    <?php echo $schema; ?>
    </script>
<?php endif; ?>
