<?php
/**
 * Template: Product Gallery Hero Block
 *
 * Available variables:
 * @var string $block_id
 * @var string $class_name
 * @var array  $gallery
 * @var bool   $show_discount
 * @var string $discount_text
 * @var string $discount_color
 * @var string $badge_position
 * @var bool   $show_thumbnails
 * @var string $thumbnail_shape
 * @var bool   $show_view_photos
 * @var string $button_text
 * @var bool   $enable_lightbox
 * @var int    $autoplay_interval
 * @var bool   $is_preview
 */

use Travel\Blocks\Helpers\IconHelper;

// If no gallery, show placeholder
if (empty($gallery)) {
    echo '<div class="product-gallery-hero-placeholder">';
    echo '<p>' . __('No images in gallery. Please add images to the Package gallery or upload manual images.', 'travel-blocks') . '</p>';
    echo '</div>';
    return;
}
?>

<div
    id="<?php echo esc_attr($block_id); ?>"
    class="<?php echo esc_attr($class_name); ?>"
    data-autoplay="<?php echo esc_attr($autoplay_interval); ?>"
    data-lightbox="<?php echo esc_attr($enable_lightbox ? '1' : '0'); ?>"
>
    <?php if ($show_discount && !empty($discount_text)): ?>
        <!-- Diagonal Discount Badge -->
        <div
            class="gallery-hero__discount-badge gallery-hero__discount-badge--<?php echo esc_attr($badge_position); ?>"
            style="background-color: <?php echo esc_attr($discount_color); ?>;"
        >
            <?php echo esc_html($discount_text); ?>
        </div>
    <?php endif; ?>

    <!-- Main Swiper Container -->
    <div class="gallery-hero__swiper swiper">
        <div class="swiper-wrapper">
            <?php foreach ($gallery as $image): ?>
                <div class="swiper-slide">
                    <?php if ($enable_lightbox): ?>
                        <a href="<?php echo esc_url($image['url']); ?>" class="gallery-hero__lightbox-link glightbox" data-gallery="product-gallery">
                            <img
                                src="<?php echo esc_url($image['sizes']['large'] ?? $image['url']); ?>"
                                alt="<?php echo esc_attr($image['alt'] ?? $image['title']); ?>"
                                loading="lazy"
                                class="gallery-hero__image"
                            />
                        </a>
                    <?php else: ?>
                        <img
                            src="<?php echo esc_url($image['sizes']['large'] ?? $image['url']); ?>"
                            alt="<?php echo esc_attr($image['alt'] ?? $image['title']); ?>"
                            loading="lazy"
                            class="gallery-hero__image"
                        />
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigation Arrows -->
        <div class="gallery-hero__nav-button gallery-hero__nav-button--prev">
            <?php echo IconHelper::get_icon_svg('arrow-right', 32, '#FFFFFF'); ?>
        </div>
        <div class="gallery-hero__nav-button gallery-hero__nav-button--next">
            <?php echo IconHelper::get_icon_svg('arrow-right', 32, '#FFFFFF'); ?>
        </div>

        <?php if ($show_thumbnails): ?>
            <!-- Thumbnail Pagination -->
            <div class="gallery-hero__pagination swiper-pagination swiper-pagination--<?php echo esc_attr($thumbnail_shape); ?>"></div>
        <?php endif; ?>
    </div>

    <?php if (!empty($activity_level) && $activity_dots_count > 0): ?>
        <!-- Activity Level Indicator -->
        <div class="gallery-hero__activity-indicator">
            <!-- Mountain Icon SVG -->
            <div class="activity-indicator__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="51" height="32" viewBox="0 0 51 32" fill="none">
                    <g clip-path="url(#clip0_312_46)">
                        <path d="M17.2484 16.1995C18.4034 14.1092 19.439 12.2185 20.489 10.3325C22.1642 7.33015 23.8298 4.31827 25.5479 1.3349C25.8782 0.861265 26.2555 0.421997 26.6743 0.0237427C27.1005 0.441988 27.4871 0.898384 27.8293 1.38716C29.8767 4.96911 31.8812 8.5748 34.005 12.3515C34.74 11.0261 35.3556 9.83846 36.0524 8.70307C36.4071 8.21712 36.8014 7.7611 37.2313 7.33965C37.6414 7.73555 38.0122 8.16985 38.3385 8.63656C42.4207 15.8195 46.4869 23.0087 50.5373 30.2043C50.6408 30.3723 50.7332 30.5469 50.8141 30.7268C51.215 31.677 50.9907 32.0142 49.9884 32.0237C49.2964 32.0237 48.6044 31.981 47.9124 31.981H2.77311C2.18131 31.981 1.58475 32.0427 0.997716 31.981C0.0431961 31.905 -0.209762 31.5059 0.181591 30.6128C0.23409 30.4941 0.296131 30.3753 0.358175 30.2613C4.17625 23.4901 7.99434 16.7221 11.8124 9.95722C11.9231 9.75636 12.0442 9.56133 12.1751 9.37291C12.5569 8.83134 12.9912 8.83609 13.3778 9.33015C13.6623 9.73031 13.9191 10.1493 14.1462 10.5843C15.1342 12.3705 16.1364 14.171 17.2484 16.1995ZM42.6195 30.6081C42.4573 30.2708 42.3523 30.0095 42.2139 29.7672C40.8489 27.3397 39.5126 24.8931 38.0856 22.5035C37.6364 21.7503 37.0506 21.0866 36.3579 20.5463C34.1482 18.8408 31.8717 17.2209 29.619 15.5724C29.3374 15.3682 29.0415 15.1876 28.7122 14.9691C28.1586 15.6864 27.6718 16.342 27.1563 16.9786C26.512 17.7625 26.1589 17.772 25.5432 16.9406C24.8607 16.019 24.2403 15.0404 23.5816 14.0902C22.7226 12.8646 20.5129 13.0071 19.797 14.323C16.997 19.4727 14.1939 24.6239 11.3876 29.7767C11.254 30.019 11.1633 30.285 11.0345 30.589L42.6195 30.6081ZM1.72313 30.6603C4.26693 30.6603 6.59119 30.6603 8.92022 30.6318C9.08127 30.5956 9.2327 30.5256 9.36439 30.4265C9.49608 30.3274 9.60498 30.2014 9.68383 30.057C10.7958 28.095 11.8697 26.1045 12.9531 24.1235L14.9767 20.3848L13.3587 19.0546C13.2538 19.2457 13.1341 19.4283 13.0008 19.6009C12.2133 20.4513 12.0463 20.4703 11.2063 19.6009C10.9076 19.2438 10.6394 18.8624 10.4045 18.4608C8.21864 19.6627 6.79163 21.0451 5.98984 23.0166C5.79435 23.4317 5.57114 23.8332 5.32169 24.2185L1.72313 30.6603ZM21.1142 11.924C21.3272 11.9574 21.5432 11.9686 21.7585 11.9572C23.1521 11.6437 24.0446 12.2755 24.7366 13.4252C25.2139 14.2138 25.8104 14.9311 26.3927 15.7339C26.7316 15.3064 26.9607 15.0309 27.1802 14.7458C28.4927 13.0736 28.4927 13.0736 30.2251 14.3515L35.8043 18.4513L35.8997 18.342L26.6743 2.09499C24.7939 5.42516 22.9946 8.59856 21.1142 11.924ZM49.1246 30.6271C47.5019 27.7244 45.9795 24.9881 44.4379 22.2613C44.3056 22.0323 44.1268 21.8333 43.9129 21.6769C42.1805 20.399 40.4385 19.1306 38.6631 17.8385L37.551 18.7886C39.7369 22.6508 41.8464 26.4418 44.0275 30.2138C44.1069 30.3232 44.2088 30.4146 44.3265 30.4818C44.4442 30.5489 44.5749 30.5904 44.7099 30.6033C46.1226 30.6461 47.5353 30.6271 49.1246 30.6271ZM37.0117 17.4727C37.2084 17.327 37.395 17.1683 37.5701 16.9976C38.2287 16.228 38.8778 16.2328 39.6605 16.8836C40.4432 17.5344 41.3834 18.1473 42.252 18.7838L42.3905 18.6556L37.217 9.40617C36.4152 10.8313 35.7184 11.9952 35.0645 13.2066C34.9987 13.3288 34.9592 13.4633 34.9485 13.6016C34.9378 13.7398 34.9562 13.8787 35.0025 14.0095C35.6229 15.1496 36.3006 16.2613 37.0117 17.4727ZM15.535 19.0831C16.4896 18.1568 16.6471 17.2921 15.8214 16.285C15.4699 15.773 15.1617 15.2327 14.9003 14.6698C14.2226 13.4917 13.5401 12.3183 12.7669 10.9881L9.1493 17.2779C10.7863 16.2185 11.1633 18.1045 12.156 18.4798C12.2904 18.3809 12.4136 18.2678 12.5235 18.1425C13.0867 17.3349 13.6594 17.4537 14.2942 18.0665C14.6521 18.4275 15.0817 18.7268 15.5207 19.0831H15.535Z" fill="white"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_312_46">
                            <rect width="51" height="32" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </div>

            <!-- Activity Level Text -->
            <span class="activity-indicator__label"><?php echo esc_html($activity_label); ?></span>

            <!-- Activity Dots -->
            <div class="activity-indicator__dots">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="activity-dot <?php echo $i <= $activity_dots_count ? 'active' : ''; ?>"></span>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($show_view_photos && $enable_lightbox): ?>
        <!-- View Photos Button -->
        <button
            type="button"
            class="gallery-hero__view-button"
            data-gallery-trigger="product-gallery"
        >
            <?php echo esc_html($button_text); ?>
        </button>
    <?php endif; ?>
</div>

<?php if (!$is_preview): ?>
<!-- Initialize Swiper & GLightbox -->
<script>
(function() {
    const blockId = '<?php echo esc_js($block_id); ?>';
    const container = document.getElementById(blockId);

    if (!container) return;

    // Wait for Swiper to be available
    function initGallery() {
        if (typeof Swiper === 'undefined') {
            setTimeout(initGallery, 100);
            return;
        }

        const swiperEl = container.querySelector('.swiper');
        const autoplay = parseInt(container.dataset.autoplay) || 0;

        // Initialize Swiper
        const swiper = new Swiper(swiperEl, {
            loop: true,
            autoplay: autoplay > 0 ? {
                delay: autoplay * 1000,
                disableOnInteraction: false,
            } : false,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.gallery-hero__nav-button--next',
                prevEl: '.gallery-hero__nav-button--prev',
            },
            lazy: {
                loadPrevNext: true,
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            speed: 600,
        });

        // Initialize GLightbox if enabled
        <?php if ($enable_lightbox): ?>
        if (typeof GLightbox !== 'undefined') {
            const lightbox = GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true,
                autoplayVideos: false,
            });

            // View Photos button trigger
            const viewButton = container.querySelector('[data-gallery-trigger]');
            if (viewButton) {
                viewButton.addEventListener('click', function() {
                    const firstLink = container.querySelector('.glightbox');
                    if (firstLink) {
                        firstLink.click();
                    }
                });
            }
        }
        <?php endif; ?>
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGallery);
    } else {
        initGallery();
    }
})();
</script>
<?php endif; ?>
