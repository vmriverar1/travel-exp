<?php
/**
 * Template: Full Body Portrait
 * Layout: Vertical photo (full body) + name + position
 */

// Variables available from render_block:
// $team_members, $columns_desktop, $image_height, $show_arrows, $show_dots, $enable_autoplay, $autoplay_delay

$carousel_id = 'tc-' . uniqid();
$carousel_attrs = [
    'data-autoplay' => $enable_autoplay ? 'true' : 'false',
    'data-delay' => $autoplay_delay,
    'data-columns' => $columns_desktop,
];
?>

<style>
#<?php echo esc_attr($carousel_id); ?> .tc-full-body .tc-image-wrapper {
    height: <?php echo esc_attr($image_height); ?>px;
}
</style>

<div <?php echo $block_wrapper_attributes; ?>>
<div class="tc-carousel tc-carousel--full-body"
     id="<?php echo esc_attr($carousel_id); ?>"
     <?php foreach ($carousel_attrs as $key => $value): ?>
        <?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>"
     <?php endforeach; ?>>

    <!-- Skeleton Loader -->
    <div class="tc-skeleton" aria-hidden="true">
        <?php for ($i = 0; $i < $columns_desktop; $i++): ?>
        <div class="tc-skeleton-item tc-skeleton-item--tall">
            <div class="tc-skeleton-image tc-skeleton-image--tall"></div>
            <div class="tc-skeleton-line tc-skeleton-line--title"></div>
            <div class="tc-skeleton-line tc-skeleton-line--small"></div>
        </div>
        <?php endfor; ?>
    </div>

    <!-- Slides Container -->
    <div class="tc-slides" role="region" aria-label="Team members carousel">
        <?php foreach ($team_members as $index => $member): ?>
        <div class="tc-slide"
             data-index="<?php echo esc_attr($index); ?>"
             role="group"
             aria-label="Team member <?php echo esc_attr($index + 1); ?> of <?php echo count($team_members); ?>">

            <div class="tc-full-body">
                <?php if (!empty($member['image'])): ?>
                <div class="tc-image-wrapper tc-image-wrapper--vertical">
                    <img
                        src="<?php echo esc_url($member['image']['sizes']['large'] ?? $member['image']['url']); ?>"
                        alt="<?php echo esc_attr($member['image']['alt'] ?? $member['name'] ?? ''); ?>"
                        class="tc-image"
                        loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                    >
                </div>
                <?php endif; ?>

                <div class="tc-info">
                    <?php if (!empty($member['name'])): ?>
                    <h3 class="tc-name"><?php echo esc_html($member['name']); ?></h3>
                    <?php endif; ?>

                    <?php if (!empty($member['position'])): ?>
                    <p class="tc-position"><?php echo esc_html($member['position']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Navigation Arrows -->
    <?php if ($show_arrows): ?>
    <button type="button"
            class="tc-nav tc-nav--prev"
            aria-label="Previous team member"
            disabled>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>
    <button type="button"
            class="tc-nav tc-nav--next"
            aria-label="Next team member">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </button>
    <?php endif; ?>

    <!-- Pagination Dots -->
    <?php if ($show_dots): ?>
    <div class="tc-dots" role="tablist" aria-label="Carousel navigation"></div>
    <?php endif; ?>
</div>
</div>
