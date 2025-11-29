<?php

/**

 * Template: Package Header

 *

 * Displays package subtitle, overview, and key metadata

 *

 * @var string $subtitle    Package subtitle (optional)

 * @var string $overview    Package description/overview

 * @var array  $metadata    Metadata array ['duration', 'departure', 'difficulty', 'service_type']

 * @var bool   $is_preview  Whether in preview mode

 *

 * @package Travel\Blocks

 */



defined('ABSPATH') || exit;



// Filter out empty metadata

$metadata = array_filter($metadata);



$has_subtitle = !empty($subtitle);

$has_overview = !empty($overview);

$has_metadata = !empty($metadata);

?>



<div class="package-header">

    <div class="package-header__container">

        <?php if ($has_subtitle): ?>

            <p class="package-header__subtitle"><?php echo esc_html($subtitle); ?></p>

        <?php endif; ?>



        <?php if ($has_overview): ?>

            <div class="package-header__overview">

                <h2><?php esc_html_e('Overview', 'travel-blocks'); ?></h2>

                <?php echo wp_kses_post(wpautop($overview)); ?>

            </div>

        <?php endif; ?>



        <?php if ($has_metadata): ?>

            <div class="package-header__metadata">

                <ul class="package-header__metadata-list">

                    <?php if (!empty($metadata['duration'])): ?>

                        <li class="package-header__metadata-item">

                            <span class="metadata-icon">

                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                                    <circle cx="12" cy="12" r="10"></circle>

                                    <polyline points="12 6 12 12 16 14"></polyline>

                                </svg>

                            </span>

                            <span class="metadata-label"><?php esc_html_e('Duration:', 'travel-blocks'); ?></span>

                            <span class="metadata-value"><?php echo esc_html($metadata['duration']); ?></span>

                        </li>

                    <?php endif; ?>



                    <?php if (!empty($metadata['departure'])): ?>

                        <li class="package-header__metadata-item">

                            <span class="metadata-icon">

                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>

                                    <circle cx="12" cy="10" r="3"></circle>

                                </svg>

                            </span>

                            <span class="metadata-label"><?php esc_html_e('Departure:', 'travel-blocks'); ?></span>

                            <span class="metadata-value"><?php echo esc_html($metadata['departure']); ?></span>

                        </li>

                    <?php endif; ?>



                    <?php if (!empty($metadata['difficulty'])): ?>

                        <li class="package-header__metadata-item">

                            <span class="metadata-icon">

                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>

                                </svg>

                            </span>

                            <span class="metadata-label"><?php esc_html_e('Difficulty:', 'travel-blocks'); ?></span>

                            <span class="metadata-value"><?php echo esc_html($metadata['difficulty']); ?></span>

                        </li>

                    <?php endif; ?>



                    <?php if (!empty($metadata['service_type'])): ?>

                        <li class="package-header__metadata-item">

                            <span class="metadata-icon">

                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>

                                    <circle cx="9" cy="7" r="4"></circle>

                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>

                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>

                                </svg>

                            </span>

                            <span class="metadata-label"><?php esc_html_e('Service Type:', 'travel-blocks'); ?></span>

                            <span class="metadata-value"><?php echo esc_html($metadata['service_type']); ?></span>

                        </li>

                    <?php endif; ?>

                </ul>

            </div>

        <?php endif; ?>

    </div>

</div>

