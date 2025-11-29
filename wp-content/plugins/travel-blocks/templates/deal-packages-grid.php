<?php
/**
 * Template: Deal Packages Grid
 *
 * @var array $packages
 * @var int $columns
 */

defined('ABSPATH') || exit;

if (empty($packages)) {
    echo '<p class="deal-packages-grid__empty">' . esc_html__('No packages available.', 'travel-blocks') . '</p>';
    return;
}

$grid_class = 'deal-packages-grid deal-packages-grid--cols-' . esc_attr($columns);
?>

<div class="<?php echo esc_attr($grid_class); ?>" id="packages">
    <?php foreach ($packages as $package): ?>
    <div class="deal-package-card">

        <!-- Package Image -->
        <div class="deal-package-card__image">
            <a href="<?php echo esc_url($package['url']); ?>">
                <?php if ($package['thumbnail_url']): ?>
                <img
                    src="<?php echo esc_url($package['thumbnail_url']); ?>"
                    alt="<?php echo esc_attr($package['title']); ?>"
                    loading="lazy"
                >
                <?php else: ?>
                <div class="deal-package-card__image-placeholder">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </div>
                <?php endif; ?>
            </a>

            <!-- Promo Badge -->
            <?php if (!empty($package['promo_tag'])): ?>
            <div class="deal-package-card__badge" style="background-color: <?php echo esc_attr($package['promo_color'] ?: '#2563eb'); ?>">
                <?php echo esc_html($package['promo_tag']); ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Package Content -->
        <div class="deal-package-card__content">

            <!-- Package Title -->
            <h3 class="deal-package-card__title">
                <a href="<?php echo esc_url($package['url']); ?>">
                    <?php echo esc_html($package['title']); ?>
                </a>
            </h3>

            <!-- Package Meta -->
            <div class="deal-package-card__meta">
                <?php if ($package['duration']): ?>
                <span class="deal-package-card__meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <?php echo esc_html($package['duration']); ?>
                </span>
                <?php endif; ?>

                <?php if ($package['difficulty']): ?>
                <span class="deal-package-card__meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                    <?php echo esc_html($package['difficulty']); ?>
                </span>
                <?php endif; ?>

                <?php if ($package['origin']): ?>
                <span class="deal-package-card__meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <?php echo esc_html($package['origin']); ?>
                </span>
                <?php endif; ?>
            </div>

            <!-- Package Excerpt -->
            <?php if ($package['excerpt']): ?>
            <p class="deal-package-card__excerpt">
                <?php echo esc_html(wp_trim_words($package['excerpt'], 20)); ?>
            </p>
            <?php endif; ?>

            <!-- Package Footer -->
            <div class="deal-package-card__footer">
                <?php if ($package['price_from']): ?>
                <div class="deal-package-card__price">
                    <span class="deal-package-card__price-label"><?php esc_html_e('From', 'travel-blocks'); ?></span>
                    <span class="deal-package-card__price-value">$<?php echo number_format($package['price_from'], 0); ?></span>
                </div>
                <?php endif; ?>

                <a href="<?php echo esc_url($package['url']); ?>" class="deal-package-card__button">
                    <?php esc_html_e('View Details', 'travel-blocks'); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>

        </div>

    </div>
    <?php endforeach; ?>
</div>
