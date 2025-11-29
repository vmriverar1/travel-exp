<?php
/**
 * Template: Mini Reviews List (Sidebar)
 *
 * Simple vertical list of customer reviews - NO Swiper carousel
 * Perfect for sidebar display with compact mini-reviews
 *
 * Available variables:
 * @var string $block_id
 * @var string $class_name
 * @var array  $reviews
 * @var bool   $is_preview
 */

use Travel\Blocks\Helpers\IconHelper;

if (empty($reviews)) {
    echo '<div class="mini-reviews-placeholder">';
    echo '<p>' . __('No reviews available.', 'travel-blocks') . '</p>';
    echo '</div>';
    return;
}

// Limit to 3 reviews in sidebar
$reviews = array_slice($reviews, 0, 3);
?>

<div id="<?php echo esc_attr($block_id); ?>" class="mini-reviews-list <?php echo esc_attr($class_name); ?>">
    <?php foreach ($reviews as $review):
        $rating = intval($review['rating'] ?? 5);
        $author = $review['author'] ?? 'Anonymous';
        $content = $review['content'] ?? '';
        $country = $review['country'] ?? '';
        $date = $review['date'] ?? '';

        // Truncate content to 120 characters for mini display
        if (strlen($content) > 120) {
            $content = substr($content, 0, 120) . '...';
        }
    ?>
        <div class="mini-review-card">

            <!-- Rating Stars -->
            <div class="mini-review-rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php
                    $color = $i <= $rating ? '#FFB400' : '#E0E0E0';
                    echo IconHelper::get_icon_svg('star', 14, $color);
                    ?>
                <?php endfor; ?>
            </div>

            <!-- Review Text -->
            <p class="mini-review-text"><?php echo esc_html($content); ?></p>

            <!-- Author Info -->
            <div class="mini-review-author">
                <div class="mini-review-avatar">
                    <?php echo IconHelper::get_icon_svg('user', 16, 'var(--color-gray-400)'); ?>
                </div>
                <div class="mini-review-author-info">
                    <strong class="mini-review-author-name"><?php echo esc_html($author); ?></strong>
                    <?php if ($country): ?>
                        <span class="mini-review-country">
                            <?php echo IconHelper::get_icon_svg('map-pin', 12, 'currentColor'); ?>
                            <?php echo esc_html($country); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    <?php endforeach; ?>
</div>
