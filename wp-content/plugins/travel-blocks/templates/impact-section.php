<?php
/**
 * Template: Impact Section Block
 */

$background_style = '';
if (!empty($background_image)) {
    $bg_url = $background_image['sizes']['large'] ?? $background_image['url'];
    $background_style = sprintf(
        'background-image: url(%s);',
        esc_url($bg_url)
    );
}

$overlay_style = sprintf(
    'background-color: rgba(0, 0, 0, %s);',
    $overlay_opacity / 100
);
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($class_name); ?>">
    <div class="impact-section__background" style="<?php echo $background_style; ?>">
        <div class="impact-section__overlay" style="<?php echo $overlay_style; ?>"></div>
    </div>

    <div class="impact-section__inner">

        <div class="impact-section__header">
            <h2 class="impact-section__title"><?php echo esc_html($title); ?></h2>
            <?php if ($message): ?>
                <p class="impact-section__message"><?php echo nl2br(esc_html($message)); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($tiles)): ?>
            <div class="impact-section__tiles">
                <?php foreach ($tiles as $tile): ?>
                    <div class="impact-section__tile">
                        <?php if (!empty($tile['icon'])): ?>
                            <div class="impact-section__tile-icon">
                                <img
                                    src="<?php echo esc_url($tile['icon']['sizes']['thumbnail'] ?? $tile['icon']['url']); ?>"
                                    alt="<?php echo esc_attr($tile['title']); ?>"
                                />
                            </div>
                        <?php endif; ?>

                        <?php if ($tile['title']): ?>
                            <h3 class="impact-section__tile-title"><?php echo esc_html($tile['title']); ?></h3>
                        <?php endif; ?>

                        <?php if ($tile['text']): ?>
                            <p class="impact-section__tile-text"><?php echo nl2br(esc_html($tile['text'])); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($button_text && $button_url): ?>
            <div class="impact-section__cta">
                <a
                    href="<?php echo esc_url($button_url); ?>"
                    target="<?php echo esc_attr($button_target); ?>"
                    class="impact-section__button"
                    <?php if ($button_target === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>
                >
                    <?php echo esc_html($button_text); ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>
