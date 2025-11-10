<?php
/**
 * Template: Static CTA Block
 *
 * @var array  $block            Block settings
 * @var bool   $is_preview       Whether in preview mode
 * @var string $title            CTA title
 * @var string $subtitle         CTA subtitle
 * @var string $background_type  Background type
 * @var array  $background_image Background image data
 * @var string $background_color Background color
 * @var int    $overlay_opacity  Overlay opacity
 * @var array  $buttons          Buttons array
 *
 * @package Travel\Blocks
 */

// Validate minimum required content
if (empty($title) && empty($buttons)) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo '<div style="padding: 20px; background: #fff3cd; border: 2px solid #ffc107; margin: 20px 0;">';
        echo '<p><strong>Static CTA:</strong> Configure título y botones para mostrar el bloque.</p>';
        echo '</div>';
    }
    return;
}

// Generate unique block ID
$block_id = 'static-cta-' . $block['id'];

// Block classes
$classes = ['acf-block', 'acf-block-static-cta', 'static-cta'];
$classes[] = 'static-cta--' . esc_attr($background_type);

if (!empty($block['className'])) {
    $classes[] = $block['className'];
}
if (!empty($block['align'])) {
    $classes[] = 'align' . $block['align'];
}

// Inline styles
$styles = [];

if ($background_type === 'image' && $background_image && isset($background_image['url'])) {
    $styles[] = 'background-image: url(' . esc_url($background_image['url']) . ');';
} elseif ($background_type === 'color' && $background_color) {
    $styles[] = 'background-color: ' . esc_attr($background_color) . ';';
} elseif ($background_type === 'gradient') {
    $styles[] = 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);';
}

?>
<section id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>" <?php if (!empty($styles)) echo 'style="' . esc_attr(implode(' ', $styles)) . '"'; ?>>

    <?php if ($background_type === 'image' && $overlay_opacity > 0): ?>
        <div class="static-cta__overlay" style="opacity: <?php echo esc_attr($overlay_opacity / 100); ?>"></div>
    <?php endif; ?>

    <div class="static-cta__content">
        <div class="static-cta__inner">

            <?php if ($title): ?>
                <h2 class="static-cta__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($subtitle): ?>
                <p class="static-cta__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>

            <?php if (!empty($buttons)): ?>
                <div class="static-cta__actions">
                    <?php foreach ($buttons as $button): ?>
                        <?php if (!empty($button['text']) && !empty($button['url'])): ?>
                            <a href="<?php echo esc_url($button['url']); ?>" class="static-cta__button btn btn-<?php echo esc_attr($button['style'] ?? 'primary'); ?>">
                                <?php echo esc_html($button['text']); ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

</section>
