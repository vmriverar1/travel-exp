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
    ['platform' => 'facebook', 'url' => 'https://facebook.com/machupicchuperu/reviews'],
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
    <?php
    // Review Logos (from ACF Options)
    $review_logos = get_field('footer_review_logos', 'option');
    if ($review_logos && is_array($review_logos)):
    ?>
        <div class="social-media-bar__section social-media-bar__section--reviews">
            <span class="social-media-bar__label">Reviews:</span>
            <ul class="social-media-bar__logos" role="list">
                <?php foreach ($review_logos as $logo): ?>
                    <?php
                    // Handle both ID and array formats
                    $logo_image = $logo['logo_image'] ?? null;

                    // If it's just an ID, get the full image array
                    if ($logo_image && is_numeric($logo_image)) {
                        $logo_image = wp_get_attachment_image_src($logo_image, 'full');
                        if ($logo_image) {
                            $logo_data = [
                                'url' => $logo_image[0],
                                'width' => $logo_image[1],
                                'height' => $logo_image[2],
                                'alt' => get_post_meta($logo['logo_image'], '_wp_attachment_image_alt', true),
                            ];
                        } else {
                            $logo_data = null;
                        }
                    } elseif (is_array($logo_image)) {
                        $logo_data = $logo_image;
                    } else {
                        $logo_data = null;
                    }

                    if ($logo_data && !empty($logo_data['url'])):
                    ?>
                        <li class="social-media-bar__logo-item">
                            <?php if (!empty($logo['logo_url'])): ?>
                                <a href="<?php echo esc_url($logo['logo_url']); ?>"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="social-media-bar__logo-link">
                                    <img src="<?php echo esc_url($logo_data['url']); ?>"
                                         alt="<?php echo esc_attr($logo_data['alt'] ?: 'Review Platform Logo'); ?>"
                                         loading="lazy"
                                         class="social-media-bar__logo-image">
                                </a>
                            <?php else: ?>
                                <img src="<?php echo esc_url($logo_data['url']); ?>"
                                     alt="<?php echo esc_attr($logo_data['alt'] ?: 'Review Platform Logo'); ?>"
                                     loading="lazy"
                                     class="social-media-bar__logo-image">
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
