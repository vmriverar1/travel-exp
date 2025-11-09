<?php
/**
 * Block: Posts List Advanced
 *
 * Simple blog posts grid with optional Swiper mobile slider.
 * SSR-optimized for SEO.
 *
 * 🚨🚨🚨 DEPRECATED - DO NOT USE 🚨🚨🚨
 *
 * This block is DEPRECATED and scheduled for removal.
 * Audit Score: 2/10 (WORST block audited)
 *
 * CRITICAL ISSUES:
 * - Previously did NOT register ACF fields (block was non-functional) - NOW FIXED
 * - Does NOT inherit from BlockBase (architectural inconsistency)
 * - Template performs queries directly (severe MVC violation)
 * - Uses $GLOBALS anti-pattern for data passing
 * - External CDN dependency (Swiper.js from jsdelivr)
 * - Functionality DUPLICATED by PostsCarousel block (which is superior)
 * - Block name confusing (acf-gbr prefix unclear)
 * - No DocBlocks (0/3 methods documented)
 *
 * ⚠️ MIGRATION PATH:
 * Use "Posts Carousel" block instead - it provides:
 * - All features of this block PLUS more
 * - Proper BlockBase inheritance
 * - ContentQueryHelper integration
 * - More configuration options
 * - Better architecture
 * - Active maintenance
 *
 * ⚠️ IF YOU MUST USE THIS BLOCK:
 * It will work temporarily (ACF fields now registered), but:
 * - DO NOT build new content with it
 * - Plan migration to PostsCarousel
 * - This block will be removed in future version
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 1.1.0 - DEPRECATED: Fixed ACF fields, added deprecation warning
 * @deprecated 1.1.0 Use PostsCarousel block instead
 */

namespace Travel\Blocks\Blocks\ACF;

class PostsListAdvanced
{
    /**
     * Block name identifier.
     *
     * @var string
     */
    private string $name = 'acf-gbr-posts-list-advanced';

    /**
     * Constructor.
     *
     * ⚠️ Note: Constructor only registers ACF init hook.
     * Could be refactored to register directly from Plugin.php.
     */
    public function __construct()
    {
        add_action('acf/init', [$this, 'register']);
    }

    /**
     * Register block and ACF fields.
     *
     * Registers ACF block type with minimal configuration and
     * registers ACF fields that were previously missing (critical fix).
     *
     * ⚠️ DEPRECATED: Use PostsCarousel block instead.
     *
     * @return void
     */
    public function register(): void
    {
        // Register ACF block type
        acf_register_block_type([
            'name'            => $this->name,
            'title'           => __('Posts List Advanced (SSR + Swiper Mobile) - DEPRECATED', 'acf-gbr'),
            'description'     => __('⚠️ DEPRECATED: Use Posts Carousel instead. SSR optimized grid with optional Swiper mobile.', 'acf-gbr'),
            'category'        => 'travel',
            'icon'            => 'slides',
            'keywords'        => ['posts', 'slider', 'grid', 'responsive', 'deprecated'],
            'render_callback' => [$this, 'render'],
            'supports'        => [
                'align' => ['wide', 'full'],
                'mode' => true,
                'jsx' => true,
                'spacing' => [
                    'margin' => true,
                    'padding' => true,
                    'blockGap' => true,
                ],
                'color' => [
                    'background' => true,
                    'text' => true,
                    'gradients' => true,
                ],
                'typography' => [
                    'fontSize' => true,
                    'lineHeight' => true,
                ],
                'anchor' => true,
                'customClassName' => true,
            ],
            'enqueue_assets'  => function () {
                // CSS base
                wp_enqueue_style(
                    "{$this->name}-base",
                    TRAVEL_BLOCKS_URL . 'assets/blocks/PostsListAdvanced/style.css',
                    [],
                    TRAVEL_BLOCKS_VERSION
                );
            },
        ]);

        // 🚨 CRITICAL FIX: Register ACF fields (were missing before)
        // These fields were referenced but never registered, making block non-functional
        $this->register_fields();
    }

    /**
     * Register ACF fields for this block.
     *
     * 🚨 CRITICAL FIX: This method was MISSING in original code.
     * Block was calling get_field() for fields that were never registered.
     *
     * Fields registered:
     * - pla_posts_per_page: Number of posts to display (1-20, default 6)
     * - pla_enable_swiper_mobile: Toggle Swiper mobile slider
     *
     * @return void
     */
    private function register_fields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_posts_list_advanced',
            'title' => __('Posts List Advanced - Settings (DEPRECATED)', 'travel-blocks'),
            'fields' => [
                [
                    'key' => 'field_pla_posts_per_page',
                    'label' => __('Posts to Display', 'travel-blocks'),
                    'name' => 'pla_posts_per_page',
                    'type' => 'number',
                    'instructions' => __('Number of blog posts to show', 'travel-blocks'),
                    'default_value' => 6,
                    'min' => 1,
                    'max' => 20,
                    'step' => 1,
                ],
                [
                    'key' => 'field_pla_enable_swiper_mobile',
                    'label' => __('Enable Swiper Mobile Slider', 'travel-blocks'),
                    'name' => 'pla_enable_swiper_mobile',
                    'type' => 'true_false',
                    'instructions' => __('Enable Swiper.js slider on mobile devices (loads from CDN)', 'travel-blocks'),
                    'default_value' => 0,
                    'ui' => 1,
                    'ui_on_text' => __('Yes', 'travel-blocks'),
                    'ui_off_text' => __('No', 'travel-blocks'),
                ],
                [
                    'key' => 'field_pla_deprecation_notice',
                    'label' => __('⚠️ DEPRECATION NOTICE', 'travel-blocks'),
                    'name' => 'pla_deprecation_notice',
                    'type' => 'message',
                    'message' => __('This block is DEPRECATED. Please use "Posts Carousel" block instead for better features and active maintenance. This block will be removed in a future version.', 'travel-blocks'),
                    'new_lines' => 'wpautop',
                    'esc_html' => 0,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/acf-gbr-posts-list-advanced',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'active' => true,
        ]);
    }

    /**
     * Render block output.
     *
     * Renders SSR-optimized blog posts grid with optional Swiper mobile slider.
     *
     * ⚠️ ARCHITECTURAL ISSUES:
     * - Uses $GLOBALS to pass data (anti-pattern)
     * - Loads Swiper from CDN (external dependency)
     * - Template performs WP_Query directly (MVC violation)
     * - Does NOT use load_template() method (inconsistent)
     *
     * @param array  $block      Block settings and attributes
     * @param string $content    Block content (unused)
     * @param bool   $is_preview Whether block is being previewed in editor
     * @param int    $post_id    Current post ID
     *
     * @return void
     */
    public function render($block, $content = '', $is_preview = false, $post_id = 0): void
    {
        // Get WordPress block attributes
        $block_wrapper_attributes = get_block_wrapper_attributes([
            'class' => 'posts-list-advanced-wrapper'
        ]);

        // Detect if Swiper Mobile is enabled
        $enable_swiper_mobile = (bool) get_field('pla_enable_swiper_mobile');

        if ($enable_swiper_mobile) {
            // Enqueue Swiper from CDN (⚠️ external dependency)
            wp_enqueue_style(
                'swiper',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
                [],
                '11.0.0'
            );

            wp_enqueue_script(
                'swiper',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
                [],
                '11.0.0',
                true
            );
            wp_script_add_data('swiper', 'async', true);
            wp_script_add_data('swiper', 'defer', true);

            // Conditional initialization script
            wp_enqueue_script(
                "{$this->name}-view",
                TRAVEL_BLOCKS_URL . 'assets/blocks/PostsListAdvanced/view-swiper.js',
                ['swiper'],
                TRAVEL_BLOCKS_VERSION,
                true
            );

            wp_localize_script("{$this->name}-view", 'PLA_FLAGS', [
                'enableMobile' => true,
            ]);
        }

        // ⚠️ Anti-pattern: Pass data via $GLOBALS instead of $data array
        $GLOBALS['pla_block_wrapper_attributes'] = $block_wrapper_attributes;

        // ⚠️ Direct include instead of load_template() method
        $template = TRAVEL_BLOCKS_PATH . 'src/Blocks/PostsListAdvanced/templates/editorial-grid.php';
        if (file_exists($template)) {
            include $template;
        }
    }
}
