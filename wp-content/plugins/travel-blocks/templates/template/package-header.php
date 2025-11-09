<?php
/**
 * Package Header Template
 *
 * @var string $subtitle Package subtitle
 * @var string $overview Package overview/description
 * @var array $metadata Package metadata (duration, departure, difficulty, service_type)
 * @var bool $is_preview Whether this is preview mode
 */

defined('ABSPATH') || exit;
?>

<header class="package-header">
    <div class="package-header__container">
        <!-- Subtitle -->
        <?php if (!empty($subtitle)): ?>
            <p class="package-header__subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>

        <!-- Overview -->
        <?php if (!empty($overview)): ?>
            <div class="package-header__overview">
                <h2><?php esc_html_e('Overview', 'travel-blocks'); ?></h2>
                <?php echo wp_kses_post(wpautop($overview)); ?>
            </div>
        <?php endif; ?>

        <!-- Metadata -->
        <?php if (!empty(array_filter($metadata))): ?>
            <div class="package-header__metadata">
                <ul class="package-header__metadata-list">
                    <?php if (!empty($metadata['duration'])): ?>
                        <li class="package-header__metadata-item">
                            <svg class="metadata-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span class="metadata-label"><?php esc_html_e('Duration:', 'travel-blocks'); ?></span>
                            <span class="metadata-value"><?php echo esc_html($metadata['duration']); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($metadata['departure'])): ?>
                        <li class="package-header__metadata-item">
                            <svg class="metadata-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="metadata-label"><?php esc_html_e('Departure:', 'travel-blocks'); ?></span>
                            <span class="metadata-value"><?php echo esc_html($metadata['departure']); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($metadata['difficulty'])): ?>
                        <li class="package-header__metadata-item">
                            <svg class="metadata-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="metadata-label"><?php esc_html_e('Difficulty:', 'travel-blocks'); ?></span>
                            <span class="metadata-value"><?php echo esc_html($metadata['difficulty']); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($metadata['service_type'])): ?>
                        <li class="package-header__metadata-item">
                            <svg class="metadata-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="metadata-label"><?php esc_html_e('Type:', 'travel-blocks'); ?></span>
                            <span class="metadata-value"><?php echo esc_html($metadata['service_type']); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</header>
