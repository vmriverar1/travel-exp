<?php
/**
 * Promo Cards Template
 *
 * @var array $cards Array with image, alt, height
 */

defined('ABSPATH') || exit;

if (empty($cards)) {
    return;
}
?>

<div class="promo-cards">
    <div class="promo-cards__container">
        <?php foreach ($cards as $card): ?>
            <?php
            $card_classes = ['promo-card'];
            if (!empty($card['enable_pdf']) && !empty($card['package_id'])) {
                $card_classes[] = 'promo-card--pdf-enabled';
            }
            if (!empty($card['link'])) {
                $card_classes[] = 'promo-card--clickable';
            }

            $has_link = !empty($card['link']);
            $tag = $has_link ? 'a' : 'div';
            ?>
            <<?php echo $tag; ?>
                class="<?php echo esc_attr(implode(' ', $card_classes)); ?>"
                style="height: <?php echo esc_attr($card['height']); ?>px;"
                <?php if ($has_link): ?>
                    href="<?php echo esc_url($card['link']); ?>"
                <?php endif; ?>
                <?php if (!empty($card['enable_pdf']) && !empty($card['package_id'])): ?>
                    data-package-id="<?php echo esc_attr($card['package_id']); ?>"
                <?php endif; ?>
            >
                <?php if (!empty($card['image'])): ?>
                    <img
                        src="<?php echo esc_url($card['image']); ?>"
                        alt="<?php echo esc_attr($card['alt']); ?>"
                        class="promo-card__image"
                        loading="lazy"
                    />
                <?php endif; ?>
            </<?php echo $tag; ?>>
        <?php endforeach; ?>
    </div>
</div>
