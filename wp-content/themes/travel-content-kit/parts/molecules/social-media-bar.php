<?php
/**
 * Molecule: Social Media Bar
 * Barra de redes sociales (administrable vía ACF Options)
 */

// Check if ACF is available
if (!function_exists('get_field')) {
    function get_field($field, $context = null) { return null; }
}

$social_networks = get_field('social_networks', 'option') ?: [
    ['platform' => 'facebook', 'url' => 'https://facebook.com/machupicchuperu'],
    ['platform' => 'instagram', 'url' => 'https://instagram.com/machupicchuperu'],
    ['platform' => 'pinterest', 'url' => 'https://pinterest.com/machupicchuperu'],
    ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/machupicchuperu'],
    ['platform' => 'youtube', 'url' => 'https://youtube.com/machupicchuperu'],
];

$review_platforms = get_field('review_platforms', 'option') ?: [
    ['platform' => 'tripadvisor', 'url' => 'https://tripadvisor.com/machupicchuperu'],
    ['platform' => 'google', 'url' => 'https://g.page/r/machupicchuperu'],
    ['platform' => 'facebook-review', 'url' => 'https://facebook.com/machupicchuperu/reviews'],
];
?>

<div class="social-media-bar">
    <!-- Social Networks -->
    <div class="social-media-bar__section">
        <ul class="social-media-bar__list" role="list">
            <?php foreach ($social_networks as $network): ?>
                <li class="social-media-bar__item">
                    <?php get_template_part('parts/atoms/social-icon', null, [
                        'platform' => $network['platform'],
                        'url' => $network['url'],
                        'size' => 'md',
                    ]); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Reviews -->
    <?php if (!empty($review_platforms)): ?>
        <div class="social-media-bar__section social-media-bar__section--reviews">
            <span class="social-media-bar__label">Reviews:</span>
            <ul class="social-media-bar__logos" role="list">
                <?php foreach ($review_platforms as $review): ?>
                    <li class="social-media-bar__logo-item">
                        <?php get_template_part('parts/atoms/social-icon', null, [
                            'platform' => $review['platform'],
                            'url' => $review['url'],
                            'size' => 'md',
                        ]); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
