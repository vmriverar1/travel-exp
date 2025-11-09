<?php
/**
 * Template: Deals Slider
 *
 * Displays deals with countdown timer and packages carousel
 *
 * @package Travel\Blocks
 * @since 1.4.0
 *
 * Variables available:
 * @var string $block_id
 * @var string $align
 * @var array  $deal_data
 * @var array  $packages
 * @var array  $settings
 * @var bool   $is_preview
 */

defined('ABSPATH') || exit;

// Ensure variables exist
$block_id = $block_id ?? 'deals-slider-' . uniqid();
$align = $align ?? 'full';
$deal_data = $deal_data ?? [];
$packages = $packages ?? [];
$settings = $settings ?? [];
$is_preview = $is_preview ?? false;

// Get background images
$bg_desktop = $settings['background_image_desktop']['url'] ?? '';
$bg_mobile = $settings['background_image_mobile']['url'] ?? $bg_desktop;
$bg_position = $settings['background_position'] ?? 'center center';

// Slider config
$slider_config = [
    'autoplay' => $settings['slider_autoplay'],
    'delay' => $settings['slider_delay'],
    'loop' => $settings['slider_loop'],
    'showArrows' => $settings['show_arrows'],
    'showDots' => $settings['show_dots'],
];

?>

<div
    id="<?php echo esc_attr($block_id); ?>"
    class="deals-slider align<?php echo esc_attr($align); ?>"
    data-slider-config='<?php echo wp_json_encode($slider_config); ?>'
    <?php if ($settings['show_countdown']): ?>
    data-end-date="<?php echo esc_attr($deal_data['end_date']); ?>"
    <?php endif; ?>
    style="--bg-desktop: url('<?php echo esc_url($bg_desktop); ?>'); --bg-mobile: url('<?php echo esc_url($bg_mobile); ?>'); --bg-position: <?php echo esc_attr($bg_position); ?>;"
>

    <?php if ($settings['show_countdown'] && !empty($deal_data['end_date'])): ?>
    <!-- Countdown Bar -->
    <div class="deals-slider__countdown-bar">
        <div class="deals-slider__countdown-content">
            <!-- Clock Icon + Text -->
            <div class="deals-slider__countdown-text">
                <svg class="deals-slider__clock-icon" xmlns="http://www.w3.org/2000/svg" width="46" height="40" viewBox="0 0 46 40" fill="none">
                    <path d="M9.1595 20.0028C10.7951 20.0028 12.5125 20.0028 14.2299 20.0028C11.8583 23.039 9.5684 26.0752 7.19675 29.1113C4.82509 26.0752 2.45344 23.039 0 20.0028C1.71741 20.0028 3.35303 20.0028 5.07044 20.0028C5.15222 15.2887 6.5425 11.054 9.65018 7.37861C12.6761 3.78312 16.5198 1.30622 21.2631 0.427325C29.4412 -1.09077 36.2291 1.46602 41.6266 7.61831C41.0542 8.0977 40.4817 8.49721 39.991 8.8967C39.5003 9.2962 38.9279 9.6957 38.4372 10.0952C33.2032 3.70322 24.6161 2.58462 18.4826 5.54091C11.4494 8.8967 9.1595 15.1289 9.1595 20.0028Z" fill="#FFF600"/>
                    <path d="M23.6287 8.41755C24.4465 8.41755 25.3461 8.41755 26.1639 8.41755C26.1639 8.57734 26.1639 8.73714 26.1639 8.81704C26.1639 12.1728 26.1639 15.5286 26.1639 18.8844C26.1639 19.1241 26.2457 19.2839 26.491 19.4437C29.2716 21.4412 32.1339 23.5186 34.9145 25.5161C34.9962 25.596 35.078 25.6759 35.1598 25.7558C34.6691 26.395 34.1784 27.0342 33.6877 27.5935C33.6877 27.6734 33.4424 27.5935 33.3606 27.5136C32.2975 26.7146 31.2343 25.9955 30.1712 25.1965C28.0448 23.6784 25.9185 22.1603 23.7922 20.5623C23.7104 20.4824 23.5469 20.3226 23.5469 20.2427C23.5469 16.2477 23.5469 12.3326 23.5469 8.33765C23.5469 8.49745 23.5469 8.49744 23.6287 8.41755Z" fill="#FFF600"/>
                    <path d="M31.6513 39.0185C31.2424 37.7402 30.8335 36.5417 30.4246 35.2633C32.0602 34.704 33.614 33.9849 35.0861 32.9462C35.9039 34.0648 36.6399 35.1035 37.4578 36.1422C35.6586 37.4206 33.7776 38.3794 31.6513 39.0185Z" fill="#FFF600"/>
                    <path d="M45.0625 25.9159C43.754 25.5164 42.5272 25.1169 41.2188 24.7174C41.3823 23.8385 41.6277 23.0395 41.7094 22.2405C41.7912 21.4415 41.873 20.5626 41.9548 19.6837C43.2633 19.6837 44.5718 19.6837 45.9621 19.6837C46.0438 21.7611 45.7167 23.8385 45.0625 25.9159Z" fill="#FFF600"/>
                    <path d="M40.2393 27.1138C41.466 27.753 42.6109 28.3123 43.8377 28.8716C42.8563 30.7892 41.6296 32.4671 40.0757 33.9852C39.0943 33.0264 38.113 32.1475 37.1316 31.1887C38.3583 30.0701 39.4215 28.7118 40.2393 27.1138Z" fill="#FFF600"/>
                    <path d="M22.5732 35.7424C24.2906 36.062 26.008 36.062 27.8072 35.9022C27.9708 37.1806 28.2161 38.459 28.3797 39.7374C27.4801 40.1369 23.1457 40.057 21.8372 39.6575C22.0825 38.2992 22.3278 37.0208 22.5732 35.7424Z" fill="#FFF600"/>
                    <path d="M43.4282 10.3353C44.3278 11.6137 45.5545 15.1293 45.5545 16.3278C44.246 16.5675 42.9375 16.8072 41.629 17.0469C41.4655 16.168 41.2201 15.369 40.9748 14.57C40.6477 13.771 40.3205 13.0519 39.9934 12.2529C41.1383 11.6137 42.2833 10.9745 43.4282 10.3353Z" fill="#FFF600"/>
                    <path d="M15.4544 32.5471C16.8447 33.5858 18.3168 34.4647 20.0342 35.024C19.5435 36.3024 19.1346 37.5009 18.6439 38.6994C16.5993 37.9803 14.7184 36.9416 12.9192 35.5833C13.737 34.6245 14.5548 33.5858 15.4544 32.5471Z" fill="#FFF600"/>
                </svg>
                <div class="deals-slider__countdown-labels">
                    <div class="deals-slider__countdown-label-1"><?php echo esc_html($settings['countdown_text_1']); ?></div>
                    <div class="deals-slider__countdown-label-2"><?php echo esc_html($settings['countdown_text_2']); ?></div>
                </div>
            </div>

            <!-- Countdown Pills -->
            <div class="deals-slider__countdown-timer">
                <div class="deals-slider__countdown-pill">
                    <span class="deals-slider__countdown-value" data-unit="days">00</span>
                    <span class="deals-slider__countdown-unit">DAYS</span>
                </div>
                <div class="deals-slider__countdown-pill">
                    <span class="deals-slider__countdown-value" data-unit="hours">00</span>
                    <span class="deals-slider__countdown-unit">HRS</span>
                </div>
                <div class="deals-slider__countdown-pill">
                    <span class="deals-slider__countdown-value" data-unit="minutes">00</span>
                    <span class="deals-slider__countdown-unit">MIN</span>
                </div>
                <div class="deals-slider__countdown-pill">
                    <span class="deals-slider__countdown-value" data-unit="seconds">00</span>
                    <span class="deals-slider__countdown-unit">SEC</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Slider Container -->
    <div class="deals-slider__container">
        <div class="swiper deals-slider__swiper">
            <div class="swiper-wrapper">

                <?php foreach ($packages as $index => $package): ?>
                <div class="swiper-slide">
                    <div class="deals-slider__card">

                        <!-- Left Column: Image -->
                        <div class="deals-slider__image-col">
                            <?php if (!empty($package['thumbnail_url'])): ?>
                            <div class="deals-slider__image-wrapper">
                                <img
                                    src="<?php echo esc_url($package['thumbnail_url']); ?>"
                                    alt="<?php echo esc_attr($package['title']); ?>"
                                    class="deals-slider__image"
                                    loading="lazy"
                                >

                                <?php if ($settings['show_ribbon'] && !empty($package['promo_tag'])): ?>
                                <!-- Ribbon -->
                                <div
                                    class="deals-slider__ribbon"
                                    style="background-color: <?php echo esc_attr($package['promo_tag_color']); ?>;"
                                >
                                    <?php echo esc_html($package['promo_tag']); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Right Column: Info -->
                        <div class="deals-slider__info-col">

                            <!-- Content Area (70%) -->
                            <div class="deals-slider__content">

                                <!-- Title + Days Badge -->
                                <div class="deals-slider__title-row">
                                    <h3 class="deals-slider__title">
                                        <a href="<?php echo esc_url($package['url']); ?>" class="deals-slider__title-link">
                                            <?php echo esc_html($package['title']); ?>
                                        </a>
                                    </h3>
                                    <?php if (!empty($package['days'])): ?>
                                    <span class="deals-slider__days-badge">
                                        <?php echo esc_html($package['days']); ?> Day<?php echo $package['days'] > 1 ? 's' : ''; ?>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Summary -->
                                <?php if (!empty($package['summary'])): ?>
                                <p class="deals-slider__summary">
                                    <?php echo esc_html($package['summary']); ?>
                                </p>
                                <?php endif; ?>

                                <!-- Features Grid 2x2 -->
                                <div class="deals-slider__features">

                                    <!-- Row 1, Col 1: Package Type -->
                                    <?php if (!empty($package['package_type'])): ?>
                                    <div class="deals-slider__feature">
                                        <svg class="deals-slider__feature-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                        </svg>
                                        <div class="deals-slider__feature-text">
                                            <span class="deals-slider__feature-label">Package Type</span>
                                            <span class="deals-slider__feature-value"><?php echo esc_html($package['package_type']); ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Row 1, Col 2: Physical Rating -->
                                    <?php if (!empty($package['physical_difficulty'])): ?>
                                    <div class="deals-slider__feature">
                                        <svg class="deals-slider__feature-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                        </svg>
                                        <div class="deals-slider__feature-text">
                                            <span class="deals-slider__feature-label">Physical Rating</span>
                                            <span class="deals-slider__feature-value"><?php echo esc_html(ucfirst(str_replace('_', ' ', $package['physical_difficulty']))); ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Row 2, Col 1: Review Rating (Stars) -->
                                    <?php if (!empty($package['rating'])): ?>
                                    <div class="deals-slider__feature deals-slider__feature--stars">
                                        <div class="deals-slider__stars">
                                            <?php
                                            $rating = floatval($package['rating']);
                                            $full_stars = floor($rating);
                                            $half_star = ($rating - $full_stars) >= 0.5;
                                            $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

                                            // Full stars
                                            for ($i = 0; $i < $full_stars; $i++):
                                            ?>
                                            <svg class="deals-slider__star deals-slider__star--filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <?php endfor; ?>

                                            <?php if ($half_star): ?>
                                            <svg class="deals-slider__star deals-slider__star--half" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <defs>
                                                    <linearGradient id="half-<?php echo esc_attr($block_id); ?>-<?php echo $index; ?>">
                                                        <stop offset="50%" stop-color="currentColor"/>
                                                        <stop offset="50%" stop-color="transparent"/>
                                                    </linearGradient>
                                                </defs>
                                                <path fill="url(#half-<?php echo esc_attr($block_id); ?>-<?php echo $index; ?>)" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <?php endif; ?>

                                            <?php for ($i = 0; $i < $empty_stars; $i++): ?>
                                            <svg class="deals-slider__star deals-slider__star--empty" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Row 2, Col 2: Includes (Pictograms) -->
                                    <?php if (!empty($package['included_services']) && count($package['included_services']) > 0): ?>
                                    <div class="deals-slider__feature deals-slider__feature--includes">
                                        <span class="deals-slider__feature-label">Include:</span>
                                        <div class="deals-slider__includes">
                                            <?php foreach (array_slice($package['included_services'], 0, 4) as $service): ?>
                                            <span
                                                class="deals-slider__include-icon"
                                                title="<?php echo esc_attr($service['name']); ?>"
                                                aria-label="<?php echo esc_attr($service['name']); ?>"
                                            >
                                                <?php
                                                // Map service slug to icon
                                                $icon_map = [
                                                    'bus' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17 20H7V21C7 21.5523 6.55228 22 6 22H5C4.44772 22 4 21.5523 4 21V12H3C2.44772 12 2 11.5523 2 11V8C2 7.44772 2.44772 7 3 7H4V5C4 3.89543 4.89543 3 6 3H18C19.1046 3 20 3.89543 20 5V7H21C21.5523 7 22 7.44772 22 8V11C22 11.5523 21.5523 12 21 12H20V21C20 21.5523 19.5523 22 19 22H18C17.4477 22 17 21.5523 17 21V20ZM6 5V15H18V5H6ZM7 16C6.44772 16 6 16.4477 6 17C6 17.5523 6.44772 18 7 18C7.55228 18 8 17.5523 8 17C8 16.4477 7.55228 16 7 16ZM17 16C16.4477 16 16 16.4477 16 17C16 17.5523 16.4477 18 17 18C17.5523 18 18 17.5523 18 17C18 16.4477 17.5523 16 17 16Z"/></svg>',
                                                    'train' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8 2 4 2.5 4 6V15.5C4 17.43 5.57 19 7.5 19L6 20.5V21H7.5L9.5 19H14.5L16.5 21H18V20.5L16.5 19C18.43 19 20 17.43 20 15.5V6C20 2.5 16 2 12 2M7.5 17C6.67 17 6 16.33 6 15.5C6 14.67 6.67 14 7.5 14C8.33 14 9 14.67 9 15.5C9 16.33 8.33 17 7.5 17M11 11H6V6H11V11M12 4.29C14.89 4.47 17.5 5.3 17.5 6.5V9H13V6.5C13 5.3 12.39 4.47 12 4.29M16.5 17C15.67 17 15 16.33 15 15.5C15 14.67 15.67 14 16.5 14C17.33 14 18 14.67 18 15.5C18 16.33 17.33 17 16.5 17M18 11H13V6H18V11Z"/></svg>',
                                                    'tent' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 22H22L12 2M12 7L17.5 19H14L12 14L10 19H6.5L12 7Z"/></svg>',
                                                    'hotel' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19 7H11V14H3V5H1V20H3V17H21V20H23V11C23 8.79086 21.2091 7 19 7ZM7 13C8.10457 13 9 12.1046 9 11C9 9.89543 8.10457 9 7 9C5.89543 9 5 9.89543 5 11C5 12.1046 5.89543 13 7 13Z"/></svg>',
                                                    'meals' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M16 6V14H18.5V22H20.5V14H23V6C23 4.34315 21.6569 3 20 3H19C17.3431 3 16 4.34315 16 6ZM1 22H3V12H4V22H6V12H7V22H9V10C9 7.79086 7.20914 6 5 6C2.79086 6 1 7.79086 1 10V22Z"/></svg>',
                                                    'guide' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z"/></svg>',
                                                ];

                                                echo $icon_map[$service['slug']] ?? '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>';
                                                ?>
                                            </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                </div><!-- .deals-slider__features -->

                            </div><!-- .deals-slider__content -->

                            <!-- Price/Actions Area (30%) -->
                            <div class="deals-slider__actions">

                                <!-- Price -->
                                <div class="deals-slider__price-section">
                                    <span class="deals-slider__price-label">Price from USD:</span>
                                    <div class="deals-slider__price">
                                        $<?php echo number_format($package['price_offer'] > 0 ? $package['price_offer'] : $package['price_normal'], 0); ?>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="deals-slider__buttons">
                                    <a
                                        href="<?php echo esc_url($package['url']); ?>"
                                        class="deals-slider__button deals-slider__button--secondary"
                                    >
                                        <?php echo esc_html($settings['view_button_text']); ?>
                                    </a>
                                    <a
                                        href="<?php echo esc_url($package['url']); ?>#book"
                                        class="deals-slider__button deals-slider__button--primary"
                                    >
                                        <?php echo esc_html($settings['book_button_text']); ?>
                                    </a>
                                </div>

                            </div><!-- .deals-slider__actions -->

                        </div><!-- .deals-slider__info-col -->

                    </div><!-- .deals-slider__card -->
                </div><!-- .swiper-slide -->
                <?php endforeach; ?>

            </div><!-- .swiper-wrapper -->

        </div><!-- .deals-slider__swiper -->

        <!-- Navigation Container: Arrows + Dots -->
        <?php if (count($packages) > 1 && ($settings['show_arrows'] || $settings['show_dots'])): ?>
        <div class="deals-slider__navigation">

            <!-- Left Arrow -->
            <?php if ($settings['show_arrows']): ?>
            <div class="deals-slider__arrow deals-slider__arrow--prev">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </div>
            <?php endif; ?>

            <!-- Pagination Dots -->
            <?php if ($settings['show_dots']): ?>
            <div class="deals-slider__pagination"></div>
            <?php endif; ?>

            <!-- Right Arrow -->
            <?php if ($settings['show_arrows']): ?>
            <div class="deals-slider__arrow deals-slider__arrow--next">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </div>
            <?php endif; ?>

        </div><!-- .deals-slider__navigation -->
        <?php endif; ?>
    </div><!-- .deals-slider__container -->

</div><!-- .deals-slider -->
