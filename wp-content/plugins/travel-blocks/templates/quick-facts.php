<?php
/**
 * Template: Quick Facts Block
 */

use Travel\Blocks\Helpers\IconHelper;

if (empty($facts)) return;
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($class_name); ?>">
    <div class="quick-facts__inner">

        <?php if ($section_title): ?>
            <h3 class="quick-facts__title"><?php echo esc_html($section_title); ?></h3>
        <?php endif; ?>

        <div class="quick-facts__list">
            <?php foreach ($facts as $fact): ?>
                <div class="quick-facts__item">
                    <div class="quick-facts__icon">
                        <?php
                        $icon_sizes = [
                            'small' => 24,
                            'medium' => 32,
                            'large' => 48,
                        ];
                        $size = $icon_sizes[$icon_size] ?? 32;
                        echo IconHelper::get_icon_svg($fact['icon'] ?? 'check', $size, $icon_color);
                        ?>
                    </div>
                    <div class="quick-facts__content">
                        <div class="quick-facts__label"><?php echo esc_html($fact['label']); ?></div>
                        <div class="quick-facts__value"><?php echo esc_html($fact['value']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>
