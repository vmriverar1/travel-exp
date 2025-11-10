<?php
/**
 * Hero Media Grid Template
 *
 * @var array $gallery Gallery images
 * @var string $map_image Map image URL
 * @var string $video_url YouTube/Vimeo video URL
 * @var array $discount_badge Discount badge data
 * @var bool $is_preview Whether this is preview mode
 */

defined('ABSPATH') || exit;

$has_gallery = !empty($gallery) && is_array($gallery);
$has_map = !empty($map_image);
$has_video = !empty($video_url);
?>

<style>
    /* Force grid layout in frontend */
    .hero-media-grid .hero-media-grid__container {
        display: grid !important;
        grid-template-columns: 65% 35% !important;
        gap: 12px !important;
    }
    .hero-media-grid .hero-media-grid__sidebar {
        display: grid !important;
        grid-template-rows: 1fr 1fr !important;
        gap: 12px !important;
    }
    @media (min-width: 1024px) {
        .hero-gallery__carousel {
            height: 545px !important;
        }
        .hero-media-grid .hero-media-grid__sidebar {
            height: 545px !important;
        }
    }
    @media (max-width: 1024px) {
        .hero-media-grid .hero-media-grid__container {
            grid-template-columns: 1fr !important;
        }
        .hero-media-grid .hero-media-grid__sidebar {
            grid-template-rows: auto !important;
            grid-template-columns: 1fr 1fr !important;
        }
    }
    @media (max-width: 768px) {
        .hero-media-grid .hero-media-grid__sidebar {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div class="hero-media-grid">
    <div class="hero-media-grid__container">
        <!-- Main Gallery (65% width) -->
        <div class="hero-media-grid__main">
            <?php if ($has_gallery): ?>
                <div class="hero-gallery">
                    <?php if ($discount_badge['show']): ?>
                        <div class="hero-gallery__discount-badge">
                            <span class="discount-percentage">-<?php echo esc_html($discount_badge['percentage']); ?>%</span>
                            <?php if (!empty($discount_badge['text'])): ?>
                                <span class="discount-text"><?php echo esc_html($discount_badge['text']); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Swiper Container -->
                    <div class="hero-gallery__carousel swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($gallery as $index => $image): ?>
                                <div class="swiper-slide">
                                    <!-- Hidden lightbox links (only accessible via button) -->
                                    <a href="<?php echo esc_url($image['url']); ?>" class="hero-gallery__lightbox-link glightbox" data-gallery="hero-gallery">
                                        <img
                                            src="<?php echo esc_url($image['url']); ?>"
                                            alt="<?php echo esc_attr($image['alt'] ?? ''); ?>"
                                            loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                                        />
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- View All Photos Button (always visible) -->
                        <button type="button" class="hero-gallery__view-button" data-gallery-trigger="hero-gallery">
                            <?php esc_html_e('View All Photos', 'travel-blocks'); ?>
                        </button>
                    </div>

                    <?php
                    // Get physical difficulty from post (if in live mode)
                    $activity_label = '';
                    $activity_dots_count = 0;

                    if (!$is_preview && get_the_ID()) {
                        $post_id = get_the_ID();
                        $physical_difficulty = get_field('physical_difficulty', $post_id);

                        if ($physical_difficulty) {
                            // Map physical_difficulty to dots count (1-5) and display labels
                            $difficulty_map = [
                                'easy' => ['label' => 'Easy', 'dots' => 1],
                                'moderate' => ['label' => 'Moderate', 'dots' => 2],
                                'moderate_demanding' => ['label' => 'Moderate - Demanding', 'dots' => 3],
                                'difficult' => ['label' => 'Difficult', 'dots' => 4],
                                'very_difficult' => ['label' => 'Very Difficult', 'dots' => 5],
                            ];

                            if (isset($difficulty_map[$physical_difficulty])) {
                                $activity_label = $difficulty_map[$physical_difficulty]['label'];
                                $activity_dots_count = $difficulty_map[$physical_difficulty]['dots'];
                            }
                        }
                    } else {
                        // Preview mode
                        $activity_label = 'Moderate - Demanding';
                        $activity_dots_count = 3;
                    }
                    ?>

                    <?php if (!empty($activity_label) && $activity_dots_count > 0): ?>
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
                </div>
            <?php else: ?>
                <div class="hero-gallery__placeholder">
                    <p><?php esc_html_e('No gallery images available', 'travel-blocks'); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar (35% width) -->
        <div class="hero-media-grid__sidebar">
            <!-- Map Image -->
            <?php if ($has_map): ?>
                <div class="hero-media-grid__map">
                    <a href="<?php echo esc_url($map_image); ?>" class="hero-media-grid__map-link glightbox">
                        <img
                            src="<?php echo esc_url($map_image); ?>"
                            alt="<?php esc_attr_e('Route map', 'travel-blocks'); ?>"
                            loading="lazy"
                        />
                        <div class="hero-media-grid__map-overlay">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                                <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span><?php esc_html_e('View Route', 'travel-blocks'); ?></span>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Video Embed -->
            <?php if ($has_video): ?>
                <div class="hero-media-grid__video">
                    <?php
                    // Extract video ID and determine platform
                    $video_embed = '';
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $matches)) {
                        $video_id = $matches[1];
                        $video_embed = sprintf(
                            '<iframe src="https://www.youtube.com/embed/%s" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
                            esc_attr($video_id)
                        );
                    } elseif (preg_match('/vimeo\.com\/([0-9]+)/', $video_url, $matches)) {
                        $video_id = $matches[1];
                        $video_embed = sprintf(
                            '<iframe src="https://player.vimeo.com/video/%s" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>',
                            esc_attr($video_id)
                        );
                    }

                    echo $video_embed;
                    ?>
                    <?php if (empty($video_embed)): ?>
                        <div class="hero-media-grid__video-placeholder">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                                <path d="M5 3L19 12L5 21V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p><?php esc_html_e('Video not available', 'travel-blocks'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!$is_preview && $has_gallery): ?>
<!-- Initialize Swiper & GLightbox -->
<script>
(function() {
    'use strict';

    function initHeroMediaGrid() {
        const block = document.querySelector('.hero-media-grid');
        if (!block || block.dataset.initialized === 'true') {
            return;
        }

        // Wait for Swiper and GLightbox to be available
        if (typeof Swiper === 'undefined' || typeof GLightbox === 'undefined') {
            setTimeout(initHeroMediaGrid, 100);
            return;
        }

        // Mark as initialized
        block.dataset.initialized = 'true';

        const swiperEl = block.querySelector('.swiper');

        // Initialize Swiper
        const swiper = new Swiper(swiperEl, {
            loop: true,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            speed: 600,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            lazy: {
                loadPrevNext: true,
            },
        });

        // Initialize GLightbox
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: false,
        });

        // View Photos button trigger
        const viewButton = block.querySelector('[data-gallery-trigger]');
        if (viewButton) {
            viewButton.addEventListener('click', function() {
                const firstLink = block.querySelector('.glightbox');
                if (firstLink) {
                    firstLink.click();
                }
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeroMediaGrid);
    } else {
        initHeroMediaGrid();
    }
})();
</script>
<?php endif; ?>
