<?php
/**
 * Block: Posts Carousel Native (CSS Scroll-Snap)
 *
 * Native CSS scroll-snap carousel with vanilla JavaScript.
 * No external dependencies (Swiper, etc.).
 *
 * ⚠️ CRITICAL DUPLICATION WARNING:
 * This block has ~70% DUPLICATION with PostsCarousel block.
 * - PostsCarousel: 755 lines, inherits BlockBase, Material Design
 * - PostsCarouselNative: 274 lines, NO BlockBase, simpler
 * Decision needed: Consolidate or deprecate one of them.
 *
 * ⚠️ ARCHITECTURAL ISSUES (Audit Score: 4/10):
 * - Does NOT inherit from BlockBase (inconsistent with best practices)
 * - Template performs queries directly (MVC violation)
 * - No DocBlocks (0/6 methods documented)
 * - Block name confusing (acf-gbr prefix)
 * - Namespace incorrect
 * - Empty constructor (unnecessary)
 *
 * Features:
 * - CSS scroll-snap native carousel
 * - Vanilla JavaScript (no libraries)
 * - Manual cards OR dynamic via ContentQueryHelper
 * - Desktop grid + Mobile slider
 * - Show/hide fields: category, location, price
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 1.1.0 - Refactored: namespace fix, added duplication and architectural warnings
 */

namespace Travel\Blocks\ACF;

use Travel\Blocks\Helpers\ContentQueryHelper;

class PostsCarouselNative
{
    /**
     * Block name identifier.
     *
     * @var string
     */
    private string $name = 'acf-gbr-posts-carousel';

    /**
     * Constructor.
     *
     * ⚠️ Note: Empty constructor, could be removed.
     */
    public function __construct()
    {
        // Methods called directly from Plugin.php
    }

    /**
     * Register block and ACF fields.
     *
     * ⚠️ DUPLICATION: Consider consolidating with PostsCarousel.
     *
     * @return void
     */
    public function register(): void
    {
        $this->register_block();
        $this->register_fields();
    }

    /**
     * Register ACF block type.
     *
     * ⚠️ ARCHITECTURAL ISSUE: Does NOT inherit from BlockBase.
     * Should extend BlockBase for consistency and code reuse.
     *
     * @return void
     */
    public function register_block(): void
    {
        acf_register_block_type([
            'name'            => $this->name,
            'title'           => __('Posts Carousel (Native CSS) - Consider using Posts Carousel instead', 'acf-gbr'),
            'description'     => __('⚠️ DUPLICATION: ~70% duplicated with Posts Carousel. Native scroll-snap carousel without external dependencies.', 'acf-gbr'),
            'category'        => 'travel',
            'icon'            => 'images-alt2',
            'keywords'        => ['posts', 'carousel', 'slider', 'native', 'scroll-snap'],
            'render_callback' => [$this, 'render'],
            'enqueue_assets'  => [$this, 'enqueue_assets'],
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
        ]);
    }

    /**
     * Registro de ACF Fields
     */
    public function register_fields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        // Get dynamic content and filter fields from Helper (with 'pc' prefix)
        $dynamic_fields = ContentQueryHelper::get_dynamic_content_fields('pc');
        $filter_fields = ContentQueryHelper::get_filter_fields('pc');

        // Build complete fields array
        $fields = array_merge(
            [
                // ===== TAB: GENERAL =====
                [
                    'key' => 'field_pc_tab_general',
                    'label' => '⚙️ General',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_pc_posts_per_page',
                    'label' => __('Posts to Display', 'acf-gbr'),
                    'name' => 'pc_posts_per_page',
                    'type' => 'number',
                    'instructions' => __('Number of posts to show in the carousel', 'acf-gbr'),
                    'required' => 0,
                    'default_value' => 6,
                    'min' => 1,
                    'max' => 20,
                    'step' => 1,
                ],
                [
                    'key' => 'field_pc_show_arrows',
                    'label' => __('Show Navigation Arrows', 'acf-gbr'),
                    'name' => 'pc_show_arrows',
                    'type' => 'true_false',
                    'instructions' => __('Display prev/next navigation arrows', 'acf-gbr'),
                    'default_value' => 1,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_pc_show_dots',
                    'label' => __('Show Pagination Dots', 'acf-gbr'),
                    'name' => 'pc_show_dots',
                    'type' => 'true_false',
                    'instructions' => __('Display pagination dots below carousel', 'acf-gbr'),
                    'default_value' => 1,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_pc_autoplay',
                    'label' => __('Enable Autoplay', 'acf-gbr'),
                    'name' => 'pc_autoplay',
                    'type' => 'true_false',
                    'instructions' => __('Automatically advance slides', 'acf-gbr'),
                    'default_value' => 0,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_pc_autoplay_delay',
                    'label' => __('Autoplay Delay (ms)', 'acf-gbr'),
                    'name' => 'pc_autoplay_delay',
                    'type' => 'number',
                    'instructions' => __('Time between slide transitions in milliseconds', 'acf-gbr'),
                    'required' => 0,
                    'default_value' => 5000,
                    'min' => 1000,
                    'max' => 30000,
                    'step' => 500,
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_pc_autoplay',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],
            ],
            // Dynamic content fields
            $dynamic_fields,
            // Filter fields
            $filter_fields
        );

        acf_add_local_field_group([
            'key' => 'group_posts_carousel',
            'title' => __('Posts Carousel - Settings', 'acf-gbr'),
            'fields' => $fields,
            'location' => [
                [
                    [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/' . $this->name,
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ]);
    }

    /**
     * Enqueue assets
     */
    public function enqueue_assets(): void
    {
        // CSS base
        wp_enqueue_style(
            "{$this->name}-style",
            TRAVEL_BLOCKS_URL . 'assets/blocks/PostsCarousel/style.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        // JavaScript vanilla
        wp_enqueue_script(
            "{$this->name}-script",
            TRAVEL_BLOCKS_URL . 'assets/blocks/PostsCarousel/carousel.js',
            [],
            TRAVEL_BLOCKS_VERSION,
            true
        );
    }

    /**
     * Render SSR + estructura HTML
     */
    public function render($block, $content = '', $is_preview = false, $post_id = 0): void
    {
        // Get WordPress block attributes
        $block_wrapper_attributes = get_block_wrapper_attributes([
            'class' => 'posts-carousel-wrapper'
        ]);

        // ACF fields
        $posts_per_page = (int)(get_field('pc_posts_per_page') ?: 6);
        $show_arrows = (bool)(get_field('pc_show_arrows') ?? true);
        $show_dots = (bool)(get_field('pc_show_dots') ?? true);
        $autoplay = (bool)(get_field('pc_autoplay') ?? false);
        $autoplay_delay = (int)(get_field('pc_autoplay_delay') ?: 5000);

        // Block attributes
        $block_id = 'pc-' . ($block['id'] ?? uniqid());
        $align = $block['align'] ?? 'wide';

        // Check if using dynamic content from Package CPT, Blog Posts, or Deal
        $dynamic_source = get_field('pc_dynamic_source');
        $items = [];
        $use_dynamic = false;

        if ($dynamic_source === 'package') {
            // Get dynamic packages from ContentQueryHelper with 'pc' prefix
            $items = ContentQueryHelper::get_content('pc', 'package');
            $use_dynamic = true;
            if (function_exists('travel_info')) {
                travel_info('Usando contenido dinámico de packages', [
                    'cards_count' => count($items),
                ]);
            }
        } elseif ($dynamic_source === 'post') {
            // Get dynamic blog posts from ContentQueryHelper with 'pc' prefix
            $items = ContentQueryHelper::get_content('pc', 'post');
            $use_dynamic = true;
            if (function_exists('travel_info')) {
                travel_info('Usando contenido dinámico de blog posts', [
                    'cards_count' => count($items),
                ]);
            }
        } elseif ($dynamic_source === 'deal') {
            // Dynamic content from selected deal's packages
            $deal_id = get_field('pc_deal_selector');
            if ($deal_id) {
                $items = ContentQueryHelper::get_deal_packages($deal_id, 'pc');
                $use_dynamic = true;
                if (function_exists('travel_info')) {
                    travel_info('Usando paquetes del deal seleccionado', [
                        'deal_id' => $deal_id,
                        'cards_count' => count($items),
                    ]);
                }
            }
        }

        // Pass data to template
        $data = [
            'block_wrapper_attributes' => $block_wrapper_attributes,
            'block_id' => $block_id,
            'align' => $align,
            'posts_per_page' => $posts_per_page,
            'show_arrows' => $show_arrows,
            'show_dots' => $show_dots,
            'autoplay' => $autoplay,
            'autoplay_delay' => $autoplay_delay,
            'is_preview' => $is_preview,
            'use_dynamic' => $use_dynamic,
            'items' => $items,
        ];

        $template = TRAVEL_BLOCKS_PATH . 'src/Blocks/PostsCarousel/templates/editorial-carousel.php';
        if (file_exists($template)) {
            include $template;
        }
    }
}
