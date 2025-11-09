<?php
/**
 * Template: Product Metadata Block
 *
 * Available variables:
 * @var string $block_id
 * @var string $class_name
 * @var bool   $show_tripadvisor
 * @var bool   $show_metadata
 * @var string $metadata_color
 * @var array  $package_data
 * @var bool   $is_preview
 */

use Travel\Blocks\Helpers\IconHelper;

// Extract package data
$tripadvisor_rating = $package_data['tripadvisor_rating'] ?? 0;
$tripadvisor_url = $package_data['tripadvisor_url'] ?? '';
$total_reviews = $package_data['total_reviews'] ?? 0;
$show_rating_badge = $package_data['show_rating_badge'] ?? true;
$origin = $package_data['origin'] ?? '';
$difficulty = $package_data['difficulty'] ?? '';
$duration = $package_data['duration'] ?? '';
$type = $package_data['type'] ?? '';

// Map difficulty to human-readable labels
$difficulty_labels = [
    'easy' => __('Easy', 'travel-blocks'),
    'moderate' => __('Moderate', 'travel-blocks'),
    'moderate_demanding' => __('Moderate Demanding', 'travel-blocks'),
    'difficult' => __('Difficult', 'travel-blocks'),
    'very_difficult' => __('Very Difficult', 'travel-blocks'),
];
$difficulty_text = $difficulty_labels[$difficulty] ?? ucfirst($difficulty);

// Map type to human-readable labels
$type_labels = [
    'shared' => __('Shared', 'travel-blocks'),
    'private' => __('Private', 'travel-blocks'),
];
$type_text = $type_labels[$type] ?? ucfirst($type);
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($class_name); ?>" data-color="<?php echo esc_attr($metadata_color); ?>">

    <?php if (!empty($package_title)): ?>
        <!-- Package Title -->
        <h1 class="product-metadata__title"><?php echo esc_html($package_title); ?></h1>
    <?php endif; ?>

    <?php if ($show_tripadvisor && $show_rating_badge && $tripadvisor_rating > 0): ?>
        <!-- TripAdvisor Rating Badge -->
        <div class="product-metadata__rating-badge">
            <a href="<?php echo esc_url($tripadvisor_url); ?>" target="_blank" rel="noopener noreferrer" class="product-metadata__tripadvisor-link">
                <!-- TripAdvisor Logo -->
                <svg class="product-metadata__tripadvisor-logo" width="120" height="24" viewBox="0 0 120 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.6 0 12 0zm0 3.6c4.6 0 8.4 3.8 8.4 8.4s-3.8 8.4-8.4 8.4-8.4-3.8-8.4-8.4S7.4 3.6 12 3.6z" fill="#00AF87"/>
                    <circle cx="8.5" cy="12" r="2.5" fill="#00AF87"/>
                    <circle cx="15.5" cy="12" r="2.5" fill="#00AF87"/>
                </svg>

                <!-- Star Rating -->
                <div class="product-metadata__stars" role="img" aria-label="<?php echo esc_attr($tripadvisor_rating); ?> <?php _e('out of 5 stars', 'travel-blocks'); ?>">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="product-metadata__star <?php echo $i <= round($tripadvisor_rating) ? 'product-metadata__star--filled' : 'product-metadata__star--empty'; ?>">
                            <?php echo IconHelper::get_icon_svg('star', 16, $i <= round($tripadvisor_rating) ? '#00AF87' : '#E0E0E0'); ?>
                        </span>
                    <?php endfor; ?>
                </div>

                <?php if ($total_reviews > 0): ?>
                    <span class="product-metadata__reviews-count">
                        <?php printf(__('%s reviews', 'travel-blocks'), number_format($total_reviews)); ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($show_tripadvisor && $tripadvisor_rating > 0 && !$is_preview): ?>
        <!-- Schema.org Markup -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "AggregateRating",
            "ratingValue": "<?php echo esc_js($tripadvisor_rating); ?>",
            "bestRating": "5",
            "worstRating": "1",
            "ratingCount": "<?php echo esc_js($total_reviews); ?>"
        }
        </script>
    <?php endif; ?>

</div>
