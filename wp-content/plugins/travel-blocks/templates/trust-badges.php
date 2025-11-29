<?php
/**
 * Template: Trust Badges Block
 */

use Travel\Blocks\Helpers\IconHelper;

if (empty($badges)) return;
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($class_name); ?>">
    <div class="trust-badges__inner">

        <?php if ($section_title): ?>
            <h3 class="trust-badges__title"><?php echo esc_html($section_title); ?></h3>
        <?php endif; ?>

        <div class="trust-badges__list">
            <?php foreach ($badges as $badge): ?>
                <div class="trust-badges__item">

                    <div class="trust-badges__icon-wrapper">
                        <?php if ($badge['badge_type'] === 'image' && !empty($badge['image'])): ?>
                            <img
                                src="<?php echo esc_url($badge['image']['sizes']['thumbnail'] ?? $badge['image']['url']); ?>"
                                alt="<?php echo esc_attr($badge['title']); ?>"
                                class="trust-badges__image"
                            />
                        <?php else: ?>
                            <div class="trust-badges__icon">
                                <?php echo IconHelper::get_icon_svg($badge['icon'] ?? 'shield', 48, 'var(--color-teal)'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="trust-badges__content">
                        <div class="trust-badges__item-title"><?php echo esc_html($badge['title']); ?></div>
                        <?php if ($show_descriptions && !empty($badge['description'])): ?>
                            <div class="trust-badges__item-description"><?php echo esc_html($badge['description']); ?></div>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>
