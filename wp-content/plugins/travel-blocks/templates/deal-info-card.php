<?php
/**
 * Template: Deal Info Card
 *
 * @var array $discount_percentage
 * @var array $start_date_formatted
 * @var array $end_date_formatted
 * @var array $is_active
 * @var array $status
 */

defined('ABSPATH') || exit;

$wrapper_class = 'deal-info-card';
if (!$is_active) {
    $wrapper_class .= ' deal-info-card--' . esc_attr($status);
}
?>

<div class="<?php echo esc_attr($wrapper_class); ?>">

    <!-- Discount Badge -->
    <?php if ($discount_percentage > 0): ?>
    <div class="deal-info-card__discount">
        <span class="deal-info-card__discount-value"><?php echo esc_html($discount_percentage); ?>%</span>
        <span class="deal-info-card__discount-label"><?php esc_html_e('OFF', 'travel-blocks'); ?></span>
    </div>
    <?php endif; ?>

    <div class="deal-info-card__divider"></div>

    <!-- Deal Validity -->
    <div class="deal-info-card__validity">
        <h4 class="deal-info-card__validity-title"><?php esc_html_e('Deal Validity', 'travel-blocks'); ?></h4>

        <?php if ($start_date_formatted && $end_date_formatted): ?>
        <p class="deal-info-card__date">
            <svg class="deal-info-card__date-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <strong><?php esc_html_e('Starts:', 'travel-blocks'); ?></strong> <?php echo esc_html($start_date_formatted); ?>
        </p>
        <p class="deal-info-card__date">
            <svg class="deal-info-card__date-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <strong><?php esc_html_e('Ends:', 'travel-blocks'); ?></strong> <?php echo esc_html($end_date_formatted); ?>
        </p>
        <?php endif; ?>

        <?php if (!$is_active): ?>
        <div class="deal-info-card__status deal-info-card__status--<?php echo esc_attr($status); ?>">
            <?php if ($status === 'scheduled'): ?>
                <span>⏰ <?php esc_html_e('Coming Soon', 'travel-blocks'); ?></span>
            <?php elseif ($status === 'expired'): ?>
                <span>⏱️ <?php esc_html_e('Deal Expired', 'travel-blocks'); ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="deal-info-card__divider"></div>

    <!-- CTA Button -->
    <?php if ($is_active): ?>
    <div class="deal-info-card__cta">
        <a href="#packages" class="deal-info-card__button">
            <?php esc_html_e('View Packages', 'travel-blocks'); ?>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
    </div>
    <?php endif; ?>

    <!-- Contact Info -->
    <div class="deal-info-card__contact">
        <p class="deal-info-card__contact-text">
            <?php esc_html_e('Questions? Contact us at:', 'travel-blocks'); ?><br>
            <strong>info@travel.com</strong>
        </p>
    </div>

    <!-- Benefits List -->
    <div class="deal-info-card__benefits">
        <h5 class="deal-info-card__benefits-title"><?php esc_html_e('Why Book With Us', 'travel-blocks'); ?></h5>
        <ul class="deal-info-card__benefits-list">
            <li>✓ <?php esc_html_e('Best Price Guarantee', 'travel-blocks'); ?></li>
            <li>✓ <?php esc_html_e('Free Cancellation', 'travel-blocks'); ?></li>
            <li>✓ <?php esc_html_e('24/7 Customer Support', 'travel-blocks'); ?></li>
            <li>✓ <?php esc_html_e('Secure Payment', 'travel-blocks'); ?></li>
            <li>✓ <?php esc_html_e('Instant Confirmation', 'travel-blocks'); ?></li>
        </ul>
    </div>

</div>
