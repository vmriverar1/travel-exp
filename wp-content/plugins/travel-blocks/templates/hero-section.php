<?php
/**
 * Template: Hero Section Block
 *
 * @var array  $block            Block settings
 * @var bool   $is_preview       Whether in preview mode
 * @var array  $background_image Background image data
 * @var int    $overlay_opacity  Overlay opacity (0-100)
 * @var string $title            Hero title
 * @var string $subtitle         Hero subtitle
 * @var string $cta_text         CTA button text
 * @var string $cta_url          CTA button URL
 * @var string $height           Hero height (small|medium|large|full)
 *
 * @package Travel\Blocks
 */

// Generate unique block ID
$block_id = 'hero-' . $block['id'];

// Block classes
$classes = ['acf-block', 'acf-block-hero-section', 'hero-section'];
$classes[] = 'hero-section--' . esc_attr($height);

if (!empty($block['className'])) {
    $classes[] = $block['className'];
}
if (!empty($block['align'])) {
    $classes[] = 'align' . $block['align'];
}

// Get background image URL
$bg_url = '';
if ($background_image && isset($background_image['url'])) {
    $bg_url = esc_url($background_image['url']);
}

// Inline styles for background and overlay
$styles = [];
if ($bg_url) {
    $styles[] = 'background-image: url(' . $bg_url . ');';
}

?>
<section id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>" <?php if (!empty($styles)) echo 'style="' . esc_attr(implode(' ', $styles)) . '"'; ?>>

    <?php if ($overlay_opacity > 0): ?>
        <div class="hero-section__overlay" style="opacity: <?php echo esc_attr($overlay_opacity / 100); ?>"></div>
    <?php endif; ?>

    <div class="hero-section__content">
        <div class="hero-section__inner">

            <?php if ($title): ?>
                <h1 class="hero-section__title"><?php echo esc_html($title); ?></h1>
            <?php endif; ?>

            <?php if ($subtitle): ?>
                <p class="hero-section__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>

            <?php if ($cta_text && $cta_url): ?>
                <div class="hero-section__actions">
                    <a href="<?php echo esc_url($cta_url); ?>" class="hero-section__cta btn btn-primary">
                        <?php echo esc_html($cta_text); ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>

</section>
