<?php
/**
 * Template: CTA Banner Block
 *
 * Available variables:
 * @var string $block_id
 * @var string $class_name
 * @var string $cta_title
 * @var string $cta_subtitle
 * @var string $cta_description
 * @var string $primary_button_text
 * @var string $primary_button_url
 * @var string $primary_button_icon
 * @var bool   $show_secondary_button
 * @var string $secondary_button_text
 * @var string $secondary_button_url
 * @var string $banner_style
 * @var string $background_styles
 * @var string $text_color
 * @var string $content_alignment
 * @var bool   $is_preview
 */

use Travel\Blocks\Helpers\IconHelper;
?>

<div
    id="<?php echo esc_attr($block_id); ?>"
    class="<?php echo esc_attr($class_name); ?>"
    style="<?php echo esc_attr($background_styles); ?>"
>
    <div class="cta-banner__inner">
        <div class="cta-banner__content">

            <?php if ($cta_subtitle): ?>
                <div class="cta-banner__subtitle"><?php echo esc_html($cta_subtitle); ?></div>
            <?php endif; ?>

            <h2 class="cta-banner__title"><?php echo esc_html($cta_title); ?></h2>

            <?php if ($cta_description): ?>
                <p class="cta-banner__description"><?php echo esc_html($cta_description); ?></p>
            <?php endif; ?>

            <div class="cta-banner__buttons">
                <?php if ($primary_button_url): ?>
                    <a href="<?php echo esc_url($primary_button_url); ?>" class="cta-banner__button cta-banner__button--primary">
                        <?php if ($primary_button_icon): ?>
                            <?php echo IconHelper::get_icon_svg($primary_button_icon, 20, 'currentColor'); ?>
                        <?php endif; ?>
                        <span><?php echo esc_html($primary_button_text); ?></span>
                    </a>
                <?php endif; ?>

                <?php if ($show_secondary_button && $secondary_button_url): ?>
                    <a href="<?php echo esc_url($secondary_button_url); ?>" class="cta-banner__button cta-banner__button--secondary">
                        <span><?php echo esc_html($secondary_button_text); ?></span>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
