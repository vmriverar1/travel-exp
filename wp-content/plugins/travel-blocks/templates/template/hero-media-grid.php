<?php

/**

 * Template: Hero Media Grid

 *

 * Gallery carousel (65%) + Map and Video sidebar (35%)

 *

 * @var array  $gallery         Gallery images [{url, alt, title}, ...]

 * @var string $map_image       Map image URL

 * @var string $video_embed     Video iframe HTML (already processed)

 * @var array  $discount_badge  Discount data ['show' => bool, 'percentage' => int, 'text' => string]

 * @var array  $activity_level  Activity level ['label' => string, 'dots' => int (0-5)]

 * @var bool   $is_preview      Whether in preview mode

 *

 * @package Travel\Blocks

 */



defined('ABSPATH') || exit;



$has_gallery = !empty($gallery) && is_array($gallery);

$has_map = !empty($map_image);

$has_video = !empty($video_embed);

$has_activity = !empty($activity_level['label']) && $activity_level['dots'] > 0;

?>



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



                    <!-- Activity Level Indicator (if available) -->

                    <?php if ($has_activity): ?>

                        <div class="hero-gallery__activity-level">

                            <span class="activity-level-label"><?php esc_html_e('Activity Level:', 'travel-blocks'); ?></span>

                            <div class="activity-level-dots">

                                <?php for ($i = 1; $i <= 5; $i++): ?>

                                    <span class="activity-dot <?php echo $i <= $activity_level['dots'] ? 'active' : ''; ?>"></span>

                                <?php endfor; ?>

                            </div>

                            <span class="activity-level-text"><?php echo esc_html($activity_level['label']); ?></span>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>



        <!-- Sidebar: Map + Video (35% width) -->

        <div class="hero-media-grid__sidebar">

            <!-- Map Image -->

            <?php if ($has_map): ?>

                <div class="hero-media-grid__map">

                    <a href="<?php echo esc_url($map_image); ?>" class="hero-map__link glightbox" data-gallery="hero-map">

                        <img

                            src="<?php echo esc_url($map_image); ?>"

                            alt="<?php esc_attr_e('Route Map', 'travel-blocks'); ?>"

                            loading="lazy"

                        />

                        <span class="hero-map__overlay">

                            <span class="hero-map__icon">🗺️</span>

                            <span class="hero-map__text"><?php esc_html_e('View Map', 'travel-blocks'); ?></span>

                        </span>

                    </a>

                </div>

            <?php endif; ?>



            <!-- Video Embed -->

            <?php if ($has_video): ?>

                <div class="hero-media-grid__video">

                    <?php echo wp_kses_post($video_embed); ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

