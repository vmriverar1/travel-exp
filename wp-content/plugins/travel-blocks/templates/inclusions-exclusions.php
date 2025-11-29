<?php
/**
 * Template: Inclusions & Exclusions Block
 *
 * Available variables:
 * @var string $block_id
 * @var string $class_name
 * @var array  $inclusions
 * @var array  $exclusions
 * @var string $layout
 * @var string $style
 * @var string $inclusions_title
 * @var string $exclusions_title
 * @var bool   $show_icons
 * @var bool   $is_preview
 */

use Travel\Blocks\Helpers\IconHelper;

if (empty($inclusions) && empty($exclusions)) {
    echo '<div class="inclusions-exclusions-placeholder">';
    echo '<p>' . __('No inclusions or exclusions available.', 'travel-blocks') . '</p>';
    echo '</div>';
    return;
}
?>

<div
    id="<?php echo esc_attr($block_id); ?>"
    class="<?php echo esc_attr($class_name); ?>"
>
    <div class="inclusions-exclusions__inner">

        <?php if ($layout === 'accordion'): ?>
            <!-- Accordion Layout -->

            <?php if (!empty($inclusions)): ?>
                <div class="inclusions-exclusions__accordion-item inclusions-exclusions__accordion-item--inclusions">
                    <button
                        type="button"
                        class="inclusions-exclusions__accordion-header"
                        aria-expanded="true"
                        aria-controls="<?php echo esc_attr($block_id); ?>-inclusions"
                    >
                        <span class="inclusions-exclusions__accordion-icon">
                            <?php echo IconHelper::get_icon_svg('check-circle', 24, 'var(--color-success)'); ?>
                        </span>
                        <h3 class="inclusions-exclusions__accordion-title"><?php echo esc_html($inclusions_title); ?></h3>
                        <span class="inclusions-exclusions__accordion-toggle">
                            <?php echo IconHelper::get_icon_svg('chevron-down', 20, 'currentColor'); ?>
                        </span>
                    </button>
                    <div
                        id="<?php echo esc_attr($block_id); ?>-inclusions"
                        class="inclusions-exclusions__accordion-content"
                    >
                        <ul class="inclusions-exclusions__list inclusions-exclusions__list--inclusions">
                            <?php foreach ($inclusions as $item): ?>
                                <li class="inclusions-exclusions__item">
                                    <?php if ($show_icons): ?>
                                        <span class="inclusions-exclusions__item-icon">
                                            <?php echo IconHelper::get_icon_svg($item['icon'] ?? 'check', 20, 'var(--color-success)'); ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="inclusions-exclusions__item-text"><?php echo esc_html($item['text']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($exclusions)): ?>
                <div class="inclusions-exclusions__accordion-item inclusions-exclusions__accordion-item--exclusions">
                    <button
                        type="button"
                        class="inclusions-exclusions__accordion-header"
                        aria-expanded="false"
                        aria-controls="<?php echo esc_attr($block_id); ?>-exclusions"
                    >
                        <span class="inclusions-exclusions__accordion-icon">
                            <?php echo IconHelper::get_icon_svg('x-circle', 24, 'var(--color-error)'); ?>
                        </span>
                        <h3 class="inclusions-exclusions__accordion-title"><?php echo esc_html($exclusions_title); ?></h3>
                        <span class="inclusions-exclusions__accordion-toggle">
                            <?php echo IconHelper::get_icon_svg('chevron-down', 20, 'currentColor'); ?>
                        </span>
                    </button>
                    <div
                        id="<?php echo esc_attr($block_id); ?>-exclusions"
                        class="inclusions-exclusions__accordion-content"
                        hidden
                    >
                        <ul class="inclusions-exclusions__list inclusions-exclusions__list--exclusions">
                            <?php foreach ($exclusions as $item): ?>
                                <li class="inclusions-exclusions__item">
                                    <?php if ($show_icons): ?>
                                        <span class="inclusions-exclusions__item-icon">
                                            <?php echo IconHelper::get_icon_svg($item['icon'] ?? 'x', 20, 'var(--color-error)'); ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="inclusions-exclusions__item-text"><?php echo esc_html($item['text']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Two-Column or Stacked Layout -->

            <div class="inclusions-exclusions__columns">

                <?php if (!empty($inclusions)): ?>
                    <div class="inclusions-exclusions__column inclusions-exclusions__column--inclusions">
                        <div class="inclusions-exclusions__section">
                            <div class="inclusions-exclusions__header">
                                <span class="inclusions-exclusions__header-icon">
                                    <?php echo IconHelper::get_icon_svg('check-circle', 24, 'var(--color-success)'); ?>
                                </span>
                                <h3 class="inclusions-exclusions__title"><?php echo esc_html($inclusions_title); ?></h3>
                            </div>
                            <ul class="inclusions-exclusions__list inclusions-exclusions__list--inclusions">
                                <?php foreach ($inclusions as $item): ?>
                                    <li class="inclusions-exclusions__item">
                                        <?php if ($show_icons): ?>
                                            <span class="inclusions-exclusions__item-icon">
                                                <?php echo IconHelper::get_icon_svg($item['icon'] ?? 'check', 20, 'var(--color-success)'); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="inclusions-exclusions__item-text"><?php echo esc_html($item['text']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($exclusions)): ?>
                    <div class="inclusions-exclusions__column inclusions-exclusions__column--exclusions">
                        <div class="inclusions-exclusions__section">
                            <div class="inclusions-exclusions__header">
                                <span class="inclusions-exclusions__header-icon">
                                    <?php echo IconHelper::get_icon_svg('x-circle', 24, 'var(--color-error)'); ?>
                                </span>
                                <h3 class="inclusions-exclusions__title"><?php echo esc_html($exclusions_title); ?></h3>
                            </div>
                            <ul class="inclusions-exclusions__list inclusions-exclusions__list--exclusions">
                                <?php foreach ($exclusions as $item): ?>
                                    <li class="inclusions-exclusions__item">
                                        <?php if ($show_icons): ?>
                                            <span class="inclusions-exclusions__item-icon">
                                                <?php echo IconHelper::get_icon_svg($item['icon'] ?? 'x', 20, 'var(--color-error)'); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="inclusions-exclusions__item-text"><?php echo esc_html($item['text']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>
</div>
