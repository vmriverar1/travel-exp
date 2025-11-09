<?php
/**
 * Asset Optimizer
 *
 * Optimizes CSS and JavaScript loading for better performance.
 *
 * @package Travel\Performance\Performance
 * @since 1.0.0
 */

namespace Travel\Performance\Performance;

class AssetOptimizer
{
    /**
     * Register asset optimization hooks.
     *
     * @return void
     */
    public function register(): void
    {
        // Defer non-critical JavaScript
        add_filter('script_loader_tag', [$this, 'defer_non_critical_scripts'], 10, 3);

        // Preload critical assets
        add_action('wp_head', [$this, 'preload_critical_assets'], 1);

        // Remove query strings from static resources
        add_filter('style_loader_src', [$this, 'remove_query_strings'], 10, 2);
        add_filter('script_loader_src', [$this, 'remove_query_strings'], 10, 2);

        // Optimize Google Fonts loading
        add_action('wp_head', [$this, 'preconnect_google_fonts'], 1);
    }

    /**
     * Defer non-critical JavaScript files.
     *
     * @param string $tag    Script tag
     * @param string $handle Script handle
     * @param string $src    Script source
     *
     * @return string Modified tag
     */
    public function defer_non_critical_scripts(string $tag, string $handle, string $src): string
    {
        // Don't defer if in admin
        if (is_admin()) {
            return $tag;
        }

        // Scripts that should NOT be deferred (critical scripts)
        $critical_scripts = [
            'jquery',
            'jquery-core',
            'jquery-migrate',
        ];

        if (in_array($handle, $critical_scripts)) {
            return $tag;
        }

        // Scripts that should be deferred
        $defer_scripts = [
            'travel-forms-validation',
            'acf-blocks-common',
            'swiper', // If used in ReviewsCarousel
        ];

        if (in_array($handle, $defer_scripts) || strpos($handle, 'travel-') === 0) {
            // Add defer attribute
            return str_replace(' src', ' defer src', $tag);
        }

        return $tag;
    }

    /**
     * Preload critical assets.
     *
     * @return void
     */
    public function preload_critical_assets(): void
    {
        // Preload critical CSS
        ?>
        <!-- Preload critical CSS -->
        <link rel="preload" href="<?php echo esc_url(get_stylesheet_uri()); ?>" as="style">

        <?php if (is_singular('tour')): ?>
        <!-- Preload tour-specific assets -->
        <link rel="preload" href="<?php echo esc_url(TRAVEL_PERFORMANCE_URL . 'assets/css/tour-single.css'); ?>" as="style">
        <?php endif; ?>
        <?php
    }

    /**
     * Remove query strings from static resources.
     *
     * @param string $src    Resource URL
     * @param string $handle Resource handle
     *
     * @return string Modified URL
     */
    public function remove_query_strings(string $src, string $handle): string
    {
        // Don't remove version for our own plugins (cache busting)
        if (strpos($handle, 'travel-') === 0 || strpos($handle, 'aurora-') === 0) {
            return $src;
        }

        // Remove query string
        $parts = explode('?ver', $src);
        return $parts[0];
    }

    /**
     * Add preconnect for Google Fonts.
     *
     * @return void
     */
    public function preconnect_google_fonts(): void
    {
        ?>
        <!-- Preconnect to Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <?php
    }
}
