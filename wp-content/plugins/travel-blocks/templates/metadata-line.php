<?php
/**
 * Template: Metadata Line Block
 *
 * Available variables:
 * @var string $block_id
 * @var string $class_name
 * @var string $metadata_color
 * @var array  $package_data
 * @var bool   $is_preview
 */

use Travel\Blocks\Helpers\IconHelper;

// Extract package data
$origin = $package_data['origin'] ?? '';
$difficulty = $package_data['difficulty'] ?? '';
$type = $package_data['type'] ?? '';
$group_size = $package_data['group_size'] ?? '';
$languages = $package_data['languages'] ?? '';

// Map difficulty to human-readable labels
$difficulty_labels = [
    'easy' => __('Easy', 'travel-blocks'),
    'moderate' => __('Moderate', 'travel-blocks'),
    'moderate_demanding' => __('Moderate Demanding', 'travel-blocks'),
    'difficult' => __('Difficult', 'travel-blocks'),
    'very_difficult' => __('Very Difficult', 'travel-blocks'),
];
$difficulty_text = $difficulty_labels[$difficulty] ?? ucfirst($difficulty);

// Map type to human-readable labels
$type_labels = [
    'shared' => __('Shared', 'travel-blocks'),
    'private' => __('Private', 'travel-blocks'),
];
$type_text = $type_labels[$type] ?? ucfirst($type);
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($class_name); ?>" data-color="<?php echo esc_attr($metadata_color); ?>">

    <!-- Metadata Line -->
    <div class="metadata-line__content metadata-line__content--<?php echo esc_attr($metadata_color); ?>">
        <?php $items = []; ?>

        <?php if ($origin): ?>
            <?php $items[] = sprintf(
                '<span class="metadata-line__item">%s <span class="metadata-line__text">%s %s</span></span>',
                IconHelper::get_icon_svg('map-pin', 18),
                __('from', 'travel-blocks'),
                esc_html($origin)
            ); ?>
        <?php endif; ?>

        <?php if ($difficulty_text): ?>
            <?php $items[] = sprintf(
                '<span class="metadata-line__item">%s <span class="metadata-line__text">%s</span></span>',
                IconHelper::get_icon_svg('backpack', 18),
                esc_html($difficulty_text)
            ); ?>
        <?php endif; ?>

        <?php if ($type_text): ?>
            <?php $items[] = sprintf(
                '<span class="metadata-line__item">%s <span class="metadata-line__text">%s</span></span>',
                IconHelper::get_icon_svg('users', 18),
                esc_html($type_text)
            ); ?>
        <?php endif; ?>

        <?php if ($group_size): ?>
            <?php $items[] = sprintf(
                '<span class="metadata-line__item">%s <span class="metadata-line__text">%s</span></span>',
                IconHelper::get_icon_svg('users', 18),
                esc_html($group_size)
            ); ?>
        <?php endif; ?>

        <?php if ($languages): ?>
            <?php $items[] = sprintf(
                '<span class="metadata-line__item">%s <span class="metadata-line__text">%s</span></span>',
                IconHelper::get_icon_svg('globe', 18),
                esc_html($languages)
            ); ?>
        <?php endif; ?>

        <?php echo implode('<span class="metadata-line__separator">•</span>', $items); ?>
    </div>

</div>
