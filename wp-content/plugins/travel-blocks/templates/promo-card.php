<?php
/**
 * Template: Promo Card Block
 */

$custom_styles = sprintf(
    'background-color: %s; color: %s;',
    esc_attr($background_color),
    esc_attr($text_color)
);
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($class_name); ?>" style="<?php echo $custom_styles; ?>">
    <div class="promo-card__inner">

        <?php if (!empty($image)): ?>
            <div class="promo-card__image-wrapper">
                <img
                    src="<?php echo esc_url($image['sizes']['medium'] ?? $image['url']); ?>"
                    alt="<?php echo esc_attr($image['alt'] ?: $title); ?>"
                    class="promo-card__image"
                />
            </div>
        <?php endif; ?>

        <div class="promo-card__content">
            <h3 class="promo-card__title"><?php echo esc_html($title); ?></h3>

            <?php if ($description): ?>
                <p class="promo-card__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <?php if ($button_text && $button_url): ?>
                <a
                    href="<?php echo esc_url($button_url); ?>"
                    target="<?php echo esc_attr($button_target); ?>"
                    class="promo-card__button promo-card__button--<?php echo esc_attr($button_style); ?>"
                    <?php if ($button_target === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>
                >
                    <?php echo esc_html($button_text); ?>
                </a>
            <?php endif; ?>
        </div>

    </div>
</div>
