<?php
/**
 * Template: Itinerary Day-by-Day Block
 *
 * Available variables:
 * @var string $block_id
 * @var string $class_name
 * @var array  $days
 * @var string $accordion_style
 * @var string $default_state
 * @var bool   $show_day_numbers
 * @var bool   $show_meals
 * @var bool   $show_accommodation
 * @var bool   $is_preview
 */

use Travel\Blocks\Helpers\IconHelper;

if (empty($days)) {
    echo '<div class="itinerary-placeholder">';
    echo '<p>' . __('No itinerary days available.', 'travel-blocks') . '</p>';
    echo '</div>';
    return;
}
?>

<div
    id="<?php echo esc_attr($block_id); ?>"
    class="<?php echo esc_attr($class_name); ?>"
    data-default-state="<?php echo esc_attr($default_state); ?>"
>
    <div class="itinerary-day-by-day__inner">

        <?php foreach ($days as $index => $day):
            $day_number = $day['day_number'] ?? ($index + 1);
            $day_title = $day['day_title'] ?? '';
            $day_description = $day['day_description'] ?? '';
            $day_gallery = $day['day_gallery'] ?? [];
            $day_items = $day['day_items'] ?? [];
            $day_accommodation = $day['day_accommodation'] ?? '';
            $day_altitude = $day['day_altitude'] ?? '';
            $day_limit = $day['day_limit'] ?? '';

            // Determine if this day should be open by default
            $is_open = false;
            if ($default_state === 'all_open') {
                $is_open = true;
            } elseif ($default_state === 'first_open' && $index === 0) {
                $is_open = true;
            }

            $item_class = 'itinerary-day__item';
            if ($is_open) {
                $item_class .= ' itinerary-day__item--open';
            }
        ?>

        <div class="<?php echo esc_attr($item_class); ?>" data-day-index="<?php echo esc_attr($index); ?>">

            <!-- Day Header (Accordion Trigger) -->
            <button
                type="button"
                class="itinerary-day__header"
                aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
                aria-controls="itinerary-content-<?php echo esc_attr($block_id . '-' . $index); ?>"
            >
                <div class="itinerary-day__header-left">
                    <?php if ($show_day_numbers): ?>
                        <div class="itinerary-day__number">
                            <span><?php echo esc_html($day_number); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="itinerary-day__header-text">
                        <?php if ($show_day_numbers): ?>
                            <div class="itinerary-day__label">
                                <?php printf(__('Day %d', 'travel-blocks'), $day_number); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($day_title): ?>
                            <h3 class="itinerary-day__title"><?php echo esc_html($day_title); ?></h3>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="itinerary-day__header-right">
                    <?php if ($day_altitude): ?>
                        <div class="itinerary-day__altitude-preview">
                            <span class="itinerary-day__altitude-icon" title="<?php esc_attr_e('Max altitude', 'travel-blocks'); ?>">
                                <?php echo IconHelper::get_icon_svg('trending-up', 16, 'var(--color-gray-600)'); ?>
                            </span>
                            <span class="itinerary-day__altitude-text"><?php echo esc_html($day_altitude); ?>m</span>
                        </div>
                    <?php endif; ?>

                    <div class="itinerary-day__toggle-icon">
                        <?php echo IconHelper::get_icon_svg('chevron-down', 24, 'currentColor'); ?>
                    </div>
                </div>
            </button>

            <!-- Day Content (Accordion Panel) -->
            <div
                id="itinerary-content-<?php echo esc_attr($block_id . '-' . $index); ?>"
                class="itinerary-day__content"
                <?php if (!$is_open): ?>hidden<?php endif; ?>
            >
                <div class="itinerary-day__content-inner">

                    <?php if ($day_description || !empty($day_gallery)): ?>
                        <div class="itinerary-day__description-wrapper">
                            <?php if (!empty($day_gallery)): ?>
                                <div class="itinerary-day__gallery">
                                    <div class="itinerary-gallery-slider swiper">
                                        <div class="swiper-wrapper">
                                            <?php foreach ($day_gallery as $img_index => $image): ?>
                                                <div class="swiper-slide">
                                                    <img
                                                        src="<?php echo esc_url($image['url']); ?>"
                                                        alt="<?php echo esc_attr($image['alt']); ?>"
                                                        loading="<?php echo $img_index === 0 ? 'eager' : 'lazy'; ?>"
                                                    />
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="swiper-pagination"></div>
                                </div>
                            <?php endif; ?>

                            <?php if ($day_description): ?>
                                <div class="itinerary-day__description">
                                    <?php echo wp_kses_post($day_description); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($day_items)): ?>
                        <div class="itinerary-day__items">
                            <h4 class="itinerary-day__section-title">
                                <?php echo IconHelper::get_icon_svg('list', 18, 'var(--color-coral)'); ?>
                                <?php _e('Services & Activities', 'travel-blocks'); ?>
                            </h4>
                            <ul class="itinerary-day__items-list">
                                <?php foreach ($day_items as $item): ?>
                                    <li>
                                        <?php if (!empty($item['type_service'])): ?>
                                            <strong><?php echo esc_html($item['type_service']); ?>:</strong>
                                        <?php endif; ?>
                                        <?php echo esc_html($item['text']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="itinerary-day__meta-info">
                        <?php if ($show_accommodation && $day_accommodation): ?>
                            <div class="itinerary-day__accommodation">
                                <h4 class="itinerary-day__section-title">
                                    <?php echo IconHelper::get_icon_svg('home', 18, 'var(--color-coral)'); ?>
                                    <?php _e('Accommodation', 'travel-blocks'); ?>
                                </h4>
                                <p><?php echo esc_html($day_accommodation); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($day_altitude): ?>
                            <div class="itinerary-day__altitude-detail">
                                <h4 class="itinerary-day__section-title">
                                    <?php echo IconHelper::get_icon_svg('trending-up', 18, 'var(--color-coral)'); ?>
                                    <?php _e('Maximum Altitude', 'travel-blocks'); ?>
                                </h4>
                                <p><?php echo esc_html($day_altitude); ?> <?php _e('meters above sea level', 'travel-blocks'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>

        <?php endforeach; ?>

    </div>
</div>
