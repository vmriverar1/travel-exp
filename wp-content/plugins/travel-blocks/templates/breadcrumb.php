<?php
/**
 * Template: Breadcrumb (Migas de Pan)
 *
 * Muestra la ruta de navegación automáticamente
 *
 * @var array $data Block data and settings
 */

// Get data
$items = $data['items'] ?? [];
$separator = $data['separator'] ?? '>';
$text_color = $data['text_color'] ?? 'default';
$block_id = $data['block_id'] ?? 'breadcrumb-' . uniqid();

// If no items, don't render
if (empty($items)) {
    return;
}

// Block classes
$classes = [
    'breadcrumb',
    'breadcrumb--color-' . $text_color,
];
?>

<nav id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>" aria-label="<?php esc_attr_e('Breadcrumb', 'travel-blocks'); ?>">
    <ol class="breadcrumb__list">
        <?php foreach ($items as $index => $item): ?>
            <li class="breadcrumb__item <?php echo $item['current'] ? 'breadcrumb__item--current' : ''; ?>">
                <?php if (!$item['current'] && !empty($item['url'])): ?>
                    <a href="<?php echo esc_url($item['url']); ?>" class="breadcrumb__link">
                        <?php echo esc_html($item['title']); ?>
                    </a>
                <?php else: ?>
                    <span class="breadcrumb__text">
                        <?php echo esc_html($item['title']); ?>
                    </span>
                <?php endif; ?>

                <?php if ($index < count($items) - 1): ?>
                    <span class="breadcrumb__separator" aria-hidden="true"><?php echo esc_html($separator); ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
