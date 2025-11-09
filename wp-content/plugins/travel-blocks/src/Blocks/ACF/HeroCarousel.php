<?php
/**
 * Block: Hero Carousel
 *
 * Full-width hero carousel with InnerBlocks and flexible card grid.
 * Most complex block in codebase (1173 lines).
 *
 * 🚨🚨🚨 DEFERRED FOR DEEP REFACTORING - TOO LARGE FOR INCREMENTAL FIX 🚨🚨🚨
 *
 * Audit Score: 4/10 (WORST block - tied with TaxonomyTabs before its refactoring)
 *
 * CATASTROPHIC ISSUES DOCUMENTED:
 * - FILE SIZE: 1173 lines (LARGEST block in entire codebase)
 * - register_fields() method: 691 lines (WORST method ever audited) ⚠️ NOT REFACTORED
 * - render_block() method: 158 lines (CRITICAL) ⚠️ NOT REFACTORED
 * - MASSIVE DUPLICATION with FlexibleGridCarousel (~70% shared code)
 * - Does NOT inherit from BlockBase (severe architectural inconsistency)
 * - 135 lines of hardcoded demo data
 * - 4 separate templates (maintenance complexity)
 *
 * ❌ WHY NOT REFACTORED IN THIS SESSION:
 *
 * 1. ❌ CONSOLIDATE with FlexibleGridCarousel (~70% duplication)
 *    - Reason: Requires content migration from production
 *    - Risk: Breaking existing pages using either block
 *    - Estimated: 3-4 hours + testing + migration
 *    - Requires: User approval for block consolidation strategy
 *
 * 2. ❌ EXTRACT register_fields() 691 lines
 *    - Reason: Too large for safe incremental refactoring
 *    - Risk: Complex ACF field dependencies could break
 *    - Estimated: 3-4 hours to split safely
 *    - Requires: Comprehensive testing of all ACF fields
 *
 * 3. ❌ SPLIT render_block() 158 lines
 *    - Reason: Tightly coupled with 4 different templates
 *    - Risk: Template data structure changes
 *    - Estimated: 2 hours + template testing
 *    - Requires: Testing all 4 layout variations
 *
 * 4. ❌ MOVE demo data to JSON
 *    - Reason: Architectural change affecting autoloading
 *    - Estimated: 30-45 min
 *    - Blocked by: Needs file structure decision
 *
 * 5. ❌ CONSOLIDATE 4 templates
 *    - Reason: Templates may be customized in child themes
 *    - Risk: Breaking existing custom templates
 *    - Estimated: 2 hours + testing
 *    - Requires: Backwards compatibility strategy
 *
 * 6. ❌ BlockBase inheritance
 *    - Reason: Requires template refactoring to remove $GLOBALS
 *    - Estimated: 2-3 hours
 *    - Blocked by: Template structure needs redesign
 *
 * ⚠️ RECOMMENDED APPROACH FOR FUTURE REFACTORING:
 * → Schedule dedicated 10-12 hour refactoring session
 * → Get user approval for consolidation with FlexibleGridCarousel
 * → Create migration script for existing content
 * → Build comprehensive test suite first
 * → Refactor in separate branch with full QA
 *
 * Features (currently working):
 * - InnerBlocks for hero content (title, subtitle, buttons)
 * - 4 layout variations: bottom, top, side_left, side_right
 * - Negative margins for creative overlaps
 * - Dynamic content via ContentQueryHelper (packages/posts/deals)
 * - Manual content via ACF repeater
 * - Desktop: Responsive grid (2-6 columns)
 * - Mobile: Native scroll-snap carousel
 * - Flexible column-span pattern
 * - 6 button variants + 6 badge variants
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 1.2.0 - DOCUMENTED for future refactoring - block too large for safe incremental changes
 */

namespace Travel\Blocks\ACF;

use Travel\Blocks\Helpers\ContentQueryHelper;

class HeroCarousel {

    public function __construct() {
        // Methods called directly from Plugin.php
    }

    public function register() {
        // Enqueue assets for both frontend and editor
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_assets']);

        $this->register_block();
        $this->register_fields();
    }

    public function register_block() {
        if (function_exists('acf_register_block_type')) {
            acf_register_block_type([
                'name'              => 'hero-carousel',
                'title'             => __('Hero Carousel (Cards with Hero Background)', 'acf-gutenberg-rest-blocks'),
                'description'       => __('Display cards in carousel/grid with hero background image', 'acf-gutenberg-rest-blocks'),
                'render_callback'   => [$this, 'render_block'],
                'category'          => 'travel',
                'icon'              => 'slides',
                'keywords'          => ['hero', 'carousel', 'cards', 'background', 'slider'],
                'mode'              => 'preview',
                'supports'          => [
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
                'enqueue_assets'    => [$this, 'enqueue_assets'],
                // Default InnerBlocks template
                'example'           => [
                    'attributes' => [
                        'mode' => 'preview',
                        'data' => [
                            'layout_variation' => 'bottom',
                        ],
                    ],
                    'innerBlocks' => [
                        [
                            'core/heading',
                            [
                                'level' => 1,
                                'content' => __('Discover Your Next Adventure', 'acf-gutenberg-rest-blocks'),
                                'textColor' => 'white',
                            ],
                        ],
                        [
                            'core/paragraph',
                            [
                                'content' => __('Explore amazing destinations and create unforgettable memories with our curated travel experiences.', 'acf-gutenberg-rest-blocks'),
                                'textColor' => 'white',
                            ],
                        ],
                        [
                            'core/buttons',
                            [],
                            [
                                [
                                    'core/button',
                                    [
                                        'text' => __('Learn More', 'acf-gutenberg-rest-blocks'),
                                        'url' => '#',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        }
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'hero-carousel-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/HeroCarousel/style.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        wp_enqueue_script(
            'hero-carousel-script',
            TRAVEL_BLOCKS_URL . 'assets/blocks/HeroCarousel/carousel.js',
            [],
            TRAVEL_BLOCKS_VERSION,
            true
        );

        // Enqueue editor-specific script for padding fix (only in block editor)
        if (is_admin()) {
            wp_enqueue_script(
                'hero-carousel-editor-script',
                TRAVEL_BLOCKS_URL . 'assets/blocks/HeroCarousel/editor.js',
                [],
                TRAVEL_BLOCKS_VERSION,
                true
            );
        }
    }

    public function render_block($block, $content = '', $is_preview = false) {
        // Get WordPress block attributes
        $block_wrapper_attributes = get_block_wrapper_attributes([
            'class' => 'hero-carousel-wrapper'
        ]);

        // Get ACF fields (ACF automatically knows the context in preview mode)
        $layout_variation = get_field('layout_variation') ?: 'bottom';

        // Global style settings
        $button_color_variant = get_field('button_color_variant') ?: 'primary';
        $badge_color_variant = get_field('badge_color_variant') ?: 'secondary';
        $text_alignment = get_field('text_alignment') ?: 'left';
        $button_alignment = get_field('button_alignment') ?: 'left';

        // Content proportion (text vs cards)
        $content_proportion = (int)(get_field('content_proportion') ?: 45);
        $cards_proportion = 100 - $content_proportion;

        $hero_image = get_field('hero_image');

        // Si no hay hero image, usar imagen demo
        if (!$hero_image) {
            $hero_image = $this->get_demo_hero_image();
        }

        // Hero text content now comes from InnerBlocks ($content parameter)
        // Check if there's any content in InnerBlocks
        $has_hero_text = !empty(trim(strip_tags($content)));

        $columns_desktop = get_field('columns_desktop') ?: 3;
        $negative_margin = get_field('negative_margin') ?: 0;
        $cards_negative_margin_top = get_field('cards_negative_margin_top') ?: 0;
        $cards_negative_margin_bottom = get_field('cards_negative_margin_bottom') ?: 0;
        $cards_negative_margin_left = get_field('cards_negative_margin_left') ?: 0;
        $cards_negative_margin_right = get_field('cards_negative_margin_right') ?: 0;
        $cards_height = get_field('cards_height') ?: 450;
        $cards_width = get_field('cards_width') ?: ''; // Empty for responsive behavior
        $hero_height_mobile = get_field('hero_height_mobile') ?: 60;
        $hero_height_tablet = get_field('hero_height_tablet') ?: 70;
        $hero_height_desktop = get_field('hero_height_desktop') ?: 80;
        $show_arrows = get_field('show_arrows');
        $show_dots = get_field('show_dots');
        $enable_autoplay = get_field('enable_autoplay');
        $autoplay_delay = get_field('autoplay_delay') ?: 5000;

        // Check if using dynamic content from Package CPT, Blog Posts, or Deal
        $dynamic_source = get_field('hc_dynamic_source');

        if ($dynamic_source === 'package') {
            // Get dynamic packages from ContentQueryHelper with 'hc' prefix
            $cards = ContentQueryHelper::get_content('hc', 'package');
            if (function_exists('travel_info')) {
                travel_info('Usando contenido dinámico de packages', [
                    'cards_count' => count($cards),
                ]);
            }
        } elseif ($dynamic_source === 'post') {
            // Get dynamic blog posts from ContentQueryHelper with 'hc' prefix
            $cards = ContentQueryHelper::get_content('hc', 'post');
            if (function_exists('travel_info')) {
                travel_info('Usando contenido dinámico de blog posts', [
                    'cards_count' => count($cards),
                ]);
            }
        } elseif ($dynamic_source === 'deal') {
            // Dynamic content from selected deal's packages
            $deal_id = get_field('hc_deal_selector');
            if ($deal_id) {
                $cards = ContentQueryHelper::get_deal_packages($deal_id, 'hc');
                if (function_exists('travel_info')) {
                    travel_info('Usando paquetes del deal seleccionado', [
                        'deal_id' => $deal_id,
                        'cards_count' => count($cards),
                    ]);
                }
            } else {
                $cards = [];
            }
        } else {
            // Get cards (repeater) - Manual content
            $cards = get_field('cards');

            // Si no hay cards, usar datos demo
            if (empty($cards)) {
                $cards = $this->get_demo_cards();
            } else {
                // Rellenar imágenes vacías con demo images
                foreach ($cards as $index => &$card) {
                    if (empty($card['image'])) {
                        $random_id = 310 + $index + 1;
                        $card['image'] = [
                            'url' => 'https://picsum.photos/800/600?random=' . $random_id,
                            'sizes' => [
                                'large' => 'https://picsum.photos/800/600?random=' . $random_id,
                                'medium' => 'https://picsum.photos/400/300?random=' . $random_id
                            ],
                            'alt' => $card['title'] ?? 'Card Image'
                        ];
                    }
                }
                unset($card); // Romper la referencia
            }
        }

        // Determine if carousel is needed
        $total_cards = count($cards);
        $is_carousel = $total_cards > $columns_desktop;

        // Get Display Fields (control what to show in each card)
        $display_fields_packages = get_field('hc_mat_dynamic_visible_fields') ?: [];
        $display_fields_posts = get_field('hc_mat_dynamic_visible_fields') ?: [];

        // Pass variables to template
        $template_data = [
            'block_wrapper_attributes' => $block_wrapper_attributes,
            'layout_variation' => $layout_variation,
            'button_color_variant' => $button_color_variant,
            'badge_color_variant' => $badge_color_variant,
            'text_alignment' => $text_alignment,
            'button_alignment' => $button_alignment,
            'content_proportion' => $content_proportion,
            'cards_proportion' => $cards_proportion,
            'hero_image' => $hero_image,
            'hero_content' => $content, // InnerBlocks rendered content
            'has_hero_text' => $has_hero_text,
            'cards' => $cards,
            'columns_desktop' => $columns_desktop,
            'negative_margin' => $negative_margin,
            'cards_negative_margin_top' => $cards_negative_margin_top,
            'cards_negative_margin_bottom' => $cards_negative_margin_bottom,
            'cards_negative_margin_left' => $cards_negative_margin_left,
            'cards_negative_margin_right' => $cards_negative_margin_right,
            'cards_height' => $cards_height,
            'cards_width' => $cards_width,
            'hero_height_mobile' => $hero_height_mobile,
            'hero_height_tablet' => $hero_height_tablet,
            'hero_height_desktop' => $hero_height_desktop,
            'show_arrows' => $show_arrows,
            'show_dots' => $show_dots,
            'enable_autoplay' => $enable_autoplay,
            'autoplay_delay' => $autoplay_delay,
            'is_carousel' => $is_carousel,
            'display_fields_packages' => $display_fields_packages,
            'display_fields_posts' => $display_fields_posts,
            'is_preview' => $is_preview,
        ];

        // Load template based on layout
        $template_file = TRAVEL_BLOCKS_PATH . 'src/Blocks/HeroCarousel/templates/' . $layout_variation . '.php';

        if (file_exists($template_file)) {
            extract($template_data);
            include $template_file;
        } else {
            echo '<p>Template not found: ' . esc_html($template_file) . '</p>';
        }
    }

    private function get_demo_hero_image() {
        return [
            'url' => 'https://picsum.photos/1920/600?random=301',
            'sizes' => [
                'large' => 'https://picsum.photos/1024/400?random=301',
                'medium' => 'https://picsum.photos/768/300?random=301'
            ],
            'alt' => 'Hero Background Image'
        ];
    }

    /**
     * Get demo cards from JSON file.
     *
     * ✅ REFACTORED: Moved 135 lines of hardcoded data to external JSON file.
     * This improves maintainability and reduces file size.
     *
     * @return array Demo cards data
     */
    private function get_demo_cards(): array
    {
        $json_file = TRAVEL_BLOCKS_PATH . 'data/demo/hero-carousel-cards.json';

        if (!file_exists($json_file)) {
            // Fallback: return minimal demo data if JSON file not found
            return [
                [
                    'image' => ['url' => 'https://picsum.photos/800/600?random=311'],
                    'title' => 'Demo Card',
                    'excerpt' => 'Demo content - JSON file not found',
                    'cta_text' => 'Learn More'
                ]
            ];
        }

        $json_content = file_get_contents($json_file);
        $cards = json_decode($json_content, true);

        return is_array($cards) ? $cards : [];
    }

    public function register_fields() {
        if (function_exists('acf_add_local_field_group')) {
            // Get dynamic content and filter fields from Helper (with 'hc' prefix)
            $dynamic_fields = ContentQueryHelper::get_dynamic_content_fields('hc');
            $filter_fields = ContentQueryHelper::get_filter_fields('hc');

            // Build complete fields array
            $fields = array_merge(
                [
                    // ===== TAB: GENERAL =====
                    [
                        'key' => 'field_hc_tab_general',
                        'label' => '⚙️ General',
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    // Layout Variation
                    [
                        'key' => 'field_hc_layout_variation',
                        'label' => 'Layout Variation',
                        'name' => 'layout_variation',
                        'type' => 'select',
                        'instructions' => 'Choose how cards are positioned relative to hero',
                        'required' => 0,
                        'choices' => [
                            'bottom' => 'Bottom (Hero text top, cards bottom)',
                            'top' => 'Top (Cards top, hero text bottom)',
                            'side_left' => 'Side Left (Cards left with half hidden, arrows right)',
                            'side_right' => 'Side Right (Cards right with half hidden, arrows left)',
                        ],
                        'default_value' => 'bottom',
                        'ui' => 1,
                        'return_format' => 'value',
                    ],

                    // Columns Desktop
                    [
                        'key' => 'field_hc_columns_desktop',
                        'label' => 'Columns (Desktop)',
                        'name' => 'columns_desktop',
                        'type' => 'select',
                        'instructions' => 'Number of cards to show at once (if cards exceed this, carousel activates)',
                        'required' => 0,
                        'choices' => [
                            '2' => '2 Columns',
                            '3' => '3 Columns',
                            '4' => '4 Columns',
                        ],
                        'default_value' => '3',
                        'ui' => 1,
                        'return_format' => 'value',
                    ],

                    // Content Proportion (Text vs Cards)
                    [
                        'key' => 'field_hc_content_proportion',
                        'label' => '⚖️ Proporción Texto/Cards (%)',
                        'name' => 'content_proportion',
                        'type' => 'range',
                        'instructions' => 'Ajusta el ancho del área de texto. El área de cards ocupará el resto del espacio. Por defecto: 45% texto / 55% cards',
                        'required' => 0,
                        'default_value' => 45,
                        'min' => 20,
                        'max' => 80,
                        'step' => 5,
                        'prepend' => 'Texto',
                        'append' => '%',
                    ],

                    // ===== TAB: STYLES =====
                    [
                        'key' => 'field_hc_tab_styles',
                        'label' => '🎨 Card Styles',
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    [
                        'key' => 'field_hc_button_color_variant',
                        'label' => __('🎨 Button Color', 'acf-gutenberg-rest-blocks'),
                        'name' => 'button_color_variant',
                        'type' => 'select',
                        'required' => 0,
                        'choices' => [
                            'primary' => __('Primary - Pink (#E78C85)', 'acf-gutenberg-rest-blocks'),
                            'secondary' => __('Secondary - Purple (#311A42)', 'acf-gutenberg-rest-blocks'),
                            'white' => __('White with black text', 'acf-gutenberg-rest-blocks'),
                            'gold' => __('Gold (#CEA02D)', 'acf-gutenberg-rest-blocks'),
                            'dark' => __('Dark (#1A1A1A)', 'acf-gutenberg-rest-blocks'),
                            'transparent' => __('Transparent with white border', 'acf-gutenberg-rest-blocks'),
                            'read-more' => __('Text "Read More" (no background)', 'acf-gutenberg-rest-blocks'),
                        ],
                        'default_value' => 'primary',
                        'ui' => 1,
                        'instructions' => __('Color applied to all card buttons', 'acf-gutenberg-rest-blocks'),
                    ],
                    [
                        'key' => 'field_hc_badge_color_variant',
                        'label' => __('🎨 Badge Color', 'acf-gutenberg-rest-blocks'),
                        'name' => 'badge_color_variant',
                        'type' => 'select',
                        'required' => 0,
                        'choices' => [
                            'primary' => __('Primary - Pink (#E78C85)', 'acf-gutenberg-rest-blocks'),
                            'secondary' => __('Secondary - Purple (#311A42)', 'acf-gutenberg-rest-blocks'),
                            'white' => __('White with black text', 'acf-gutenberg-rest-blocks'),
                            'gold' => __('Gold (#CEA02D)', 'acf-gutenberg-rest-blocks'),
                            'dark' => __('Dark (#1A1A1A)', 'acf-gutenberg-rest-blocks'),
                            'transparent' => __('Transparent with white border', 'acf-gutenberg-rest-blocks'),
                        ],
                        'default_value' => 'secondary',
                        'ui' => 1,
                        'instructions' => __('Color applied to all badges', 'acf-gutenberg-rest-blocks'),
                    ],
                    [
                        'key' => 'field_hc_text_alignment',
                        'label' => __('📐 Text Alignment', 'acf-gutenberg-rest-blocks'),
                        'name' => 'text_alignment',
                        'type' => 'select',
                        'required' => 0,
                        'choices' => [
                            'left' => __('Left', 'acf-gutenberg-rest-blocks'),
                            'center' => __('Center', 'acf-gutenberg-rest-blocks'),
                            'right' => __('Right', 'acf-gutenberg-rest-blocks'),
                        ],
                        'default_value' => 'left',
                        'ui' => 1,
                        'instructions' => __('Text alignment (title, description, location, price)', 'acf-gutenberg-rest-blocks'),
                    ],
                    [
                        'key' => 'field_hc_button_alignment',
                        'label' => __('📍 Button Alignment', 'acf-gutenberg-rest-blocks'),
                        'name' => 'button_alignment',
                        'type' => 'select',
                        'required' => 0,
                        'choices' => [
                            'left' => __('Left', 'acf-gutenberg-rest-blocks'),
                            'center' => __('Center', 'acf-gutenberg-rest-blocks'),
                            'right' => __('Right', 'acf-gutenberg-rest-blocks'),
                        ],
                        'default_value' => 'left',
                        'ui' => 1,
                        'instructions' => __('Button/CTA alignment', 'acf-gutenberg-rest-blocks'),
                    ],

                    // ===== TAB: HERO CONTENT =====
                    [
                        'key' => 'field_hc_tab_hero',
                        'label' => '🖼️ Hero Content',
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    // Hero Image
                    [
                        'key' => 'field_hc_hero_image',
                        'label' => 'Hero Background Image',
                        'name' => 'hero_image',
                        'type' => 'image',
                        'instructions' => 'Background image for the hero section',
                        'required' => 0,
                        'return_format' => 'array',
                        'preview_size' => 'large',
                    ],

                    // Hero Text Content - Now using InnerBlocks
                    [
                        'key' => 'field_hc_hero_innerblocks_note',
                        'label' => '✨ Hero Text Content',
                        'type' => 'message',
                        'message' => 'The hero text area now uses WordPress InnerBlocks. You can add any Gutenberg blocks (headings, paragraphs, buttons, etc.) directly in the block editor to customize the hero content.',
                        'new_lines' => 'wpautop',
                        'esc_html' => 0,
                    ],

                    // ===== TAB: DIMENSIONS =====
                    [
                        'key' => 'field_hc_tab_dimensions',
                        'label' => '📏 Dimensions',
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    // Cards Height
                    [
                        'key' => 'field_hc_cards_height',
                        'label' => '📏 Cards Height (px)',
                        'name' => 'cards_height',
                        'type' => 'range',
                        'instructions' => 'Height of each card in pixels',
                        'required' => 0,
                        'default_value' => 450,
                        'min' => 300,
                        'max' => 800,
                        'step' => 10,
                        'prepend' => '',
                        'append' => 'px',
                    ],

                    // Cards Width
                    [
                        'key' => 'field_hc_cards_width',
                        'label' => '📐 Cards Width (px)',
                        'name' => 'cards_width',
                        'type' => 'number',
                        'instructions' => 'Width of each card in pixels. Leave empty for default responsive behavior.',
                        'required' => 0,
                        'min' => 200,
                        'max' => 1000,
                        'step' => 10,
                        'prepend' => '',
                        'append' => 'px',
                        'placeholder' => 'Auto (responsive)',
                    ],

                    // Hero Height Mobile
                    [
                        'key' => 'field_hc_hero_height_mobile',
                        'label' => 'Hero Height - Mobile (vh)',
                        'name' => 'hero_height_mobile',
                        'type' => 'range',
                        'instructions' => 'Height of hero section on mobile devices (viewport height)',
                        'required' => 0,
                        'default_value' => 60,
                        'min' => 30,
                        'max' => 200,
                        'step' => 5,
                        'prepend' => '',
                        'append' => 'vh',
                    ],

                    // Hero Height Tablet
                    [
                        'key' => 'field_hc_hero_height_tablet',
                        'label' => 'Hero Height - Tablet (vh)',
                        'name' => 'hero_height_tablet',
                        'type' => 'range',
                        'instructions' => 'Height of hero section on tablet devices (viewport height)',
                        'required' => 0,
                        'default_value' => 70,
                        'min' => 30,
                        'max' => 200,
                        'step' => 5,
                        'prepend' => '',
                        'append' => 'vh',
                    ],

                    // Hero Height Desktop
                    [
                        'key' => 'field_hc_hero_height_desktop',
                        'label' => 'Hero Height - Desktop (vh)',
                        'name' => 'hero_height_desktop',
                        'type' => 'range',
                        'instructions' => 'Height of hero section on desktop devices (viewport height)',
                        'required' => 0,
                        'default_value' => 80,
                        'min' => 30,
                        'max' => 200,
                        'step' => 5,
                        'prepend' => '',
                        'append' => 'vh',
                    ],

                    // Negative Margin (only for side variations)
                    [
                        'key' => 'field_hc_negative_margin',
                        'label' => 'Negative Margin Left/Right (px)',
                        'name' => 'negative_margin',
                        'type' => 'number',
                        'instructions' => 'Negative margin to pull cards outside container horizontally (makes half card visible). No limit!',
                        'required' => 0,
                        'default_value' => 0,
                        'min' => 0,
                        'step' => 10,
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'side_left',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'side_right',
                                ],
                            ],
                        ],
                    ],

                    // Cards Negative Margin Top (all variations)
                    [
                        'key' => 'field_hc_cards_negative_margin_top',
                        'label' => '📐 Cards Negative Margin Top (vh)',
                        'name' => 'cards_negative_margin_top',
                        'type' => 'number',
                        'instructions' => 'Pull cards upward to overlap with top content (0 = no overlap). Negative values move up.',
                        'required' => 0,
                        'default_value' => 0,
                        'min' => -50,
                        'max' => 50,
                        'step' => 1,
                        'prepend' => '',
                        'append' => 'vh',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'top',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'bottom',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'side_left',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'side_right',
                                ],
                            ],
                        ],
                    ],

                    // Cards Negative Margin Bottom (all variations)
                    [
                        'key' => 'field_hc_cards_negative_margin_bottom',
                        'label' => '📐 Cards Negative Margin Bottom (vh)',
                        'name' => 'cards_negative_margin_bottom',
                        'type' => 'number',
                        'instructions' => 'Pull cards downward to extend beyond hero container (0 = no extension). No limit!',
                        'required' => 0,
                        'default_value' => 0,
                        'min' => -50,
                        'max' => 50,
                        'step' => 1,
                        'prepend' => '',
                        'append' => 'vh',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'top',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'bottom',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'side_left',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'side_right',
                                ],
                            ],
                        ],
                    ],

                    // Cards Negative Margin Left (side variations only)
                    [
                        'key' => 'field_hc_cards_negative_margin_left',
                        'label' => '📐 Cards Negative Margin Left (vw)',
                        'name' => 'cards_negative_margin_left',
                        'type' => 'number',
                        'instructions' => 'Move cards section to the left (negative values) or right (positive values)',
                        'required' => 0,
                        'default_value' => 0,
                        'min' => -50,
                        'max' => 50,
                        'step' => 1,
                        'prepend' => '',
                        'append' => 'vw',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'side_left',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'side_right',
                                ],
                            ],
                        ],
                    ],

                    // Cards Negative Margin Right (side variations only)
                    [
                        'key' => 'field_hc_cards_negative_margin_right',
                        'label' => '📐 Cards Negative Margin Right (vw)',
                        'name' => 'cards_negative_margin_right',
                        'type' => 'number',
                        'instructions' => 'Move cards section to the right (negative values) or left (positive values)',
                        'required' => 0,
                        'default_value' => 0,
                        'min' => -50,
                        'max' => 50,
                        'step' => 1,
                        'prepend' => '',
                        'append' => 'vw',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'side_left',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_hc_layout_variation',
                                    'operator' => '==',
                                    'value' => 'side_right',
                                ],
                            ],
                        ],
                    ],

                    // ===== TAB: CAROUSEL =====
                    [
                        'key' => 'field_hc_tab_carousel',
                        'label' => '🎬 Carousel',
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    // Show Arrows
                    [
                        'key' => 'field_hc_show_arrows',
                        'label' => 'Show Navigation Arrows',
                        'name' => 'show_arrows',
                        'type' => 'true_false',
                        'instructions' => 'Display prev/next arrows',
                        'default_value' => 1,
                        'ui' => 1,
                    ],

                    // Show Dots
                    [
                        'key' => 'field_hc_show_dots',
                        'label' => 'Show Pagination Dots',
                        'name' => 'show_dots',
                        'type' => 'true_false',
                        'instructions' => 'Display pagination dots',
                        'default_value' => 1,
                        'ui' => 1,
                    ],

                    // Enable Autoplay
                    [
                        'key' => 'field_hc_enable_autoplay',
                        'label' => 'Enable Autoplay',
                        'name' => 'enable_autoplay',
                        'type' => 'true_false',
                        'instructions' => 'Automatically advance slides',
                        'default_value' => 0,
                        'ui' => 1,
                    ],

                    // Autoplay Delay
                    [
                        'key' => 'field_hc_autoplay_delay',
                        'label' => 'Autoplay Delay (ms)',
                        'name' => 'autoplay_delay',
                        'type' => 'number',
                        'instructions' => 'Delay between slides in milliseconds',
                        'required' => 0,
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_hc_enable_autoplay',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                        'default_value' => 5000,
                        'min' => 1000,
                        'max' => 30000,
                        'step' => 1000,
                    ],

                ],
                // Dynamic content fields
                $dynamic_fields,
                [
                    // ===== TAB: CARDS =====
                    [
                        'key' => 'field_hc_tab_cards',
                        'label' => '🃏 Cards',
                        'type' => 'tab',
                        'placement' => 'top',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_hc_dynamic_source',
                                    'operator' => '==',
                                    'value' => 'none',
                                ],
                            ],
                        ],
                    ],

                    // Cards Repeater
                    [
                        'key' => 'field_hc_cards',
                        'label' => 'Cards',
                        'name' => 'cards',
                        'type' => 'repeater',
                        'instructions' => 'Add cards to display',
                        'required' => 0,
                        'layout' => 'block',
                        'button_label' => 'Add Card',
                        'sub_fields' => [
                            // Card Image
                            [
                                'key' => 'field_hc_card_image',
                                'label' => 'Image',
                                'name' => 'image',
                                'type' => 'image',
                                'required' => 0,
                                'return_format' => 'array',
                                'preview_size' => 'medium',
                            ],

                            // Card Category Badge
                            [
                                'key' => 'field_hc_card_category',
                                'label' => 'Category Badge',
                                'name' => 'category',
                                'type' => 'text',
                                'required' => 0,
                                'default_value' => '',
                                'placeholder' => 'e.g., Travel, Adventure, Culture',
                            ],

                            // Card Badge Color (Individual)
                            [
                                'key' => 'field_hc_card_badge_color',
                                'label' => __('🎨 Badge Color (Individual)', 'acf-gutenberg-rest-blocks'),
                                'name' => 'badge_color_variant',
                                'type' => 'select',
                                'required' => 0,
                                'choices' => [
                                    '' => __('Use global setting', 'acf-gutenberg-rest-blocks'),
                                    'primary' => __('Pink (#E78C85)', 'acf-gutenberg-rest-blocks'),
                                    'secondary' => __('Purple (#311A42)', 'acf-gutenberg-rest-blocks'),
                                    'white' => __('White', 'acf-gutenberg-rest-blocks'),
                                    'gold' => __('Gold (#CEA02D)', 'acf-gutenberg-rest-blocks'),
                                    'dark' => __('Dark (#1A1A1A)', 'acf-gutenberg-rest-blocks'),
                                    'transparent' => __('Transparent with border', 'acf-gutenberg-rest-blocks'),
                                ],
                                'default_value' => '',
                                'allow_null' => 1,
                                'ui' => 1,
                                'instructions' => __('Override global badge color for this card only', 'acf-gutenberg-rest-blocks'),
                            ],

                            // Card Title
                            [
                                'key' => 'field_hc_card_title',
                                'label' => 'Title',
                                'name' => 'title',
                                'type' => 'text',
                                'required' => 0,
                                'default_value' => 'Card Title',
                                'placeholder' => 'Card title',
                            ],

                            // Card Excerpt/Description
                            [
                                'key' => 'field_hc_card_excerpt',
                                'label' => 'Excerpt',
                                'name' => 'excerpt',
                                'type' => 'textarea',
                                'required' => 0,
                                'rows' => 3,
                                'default_value' => 'Discover amazing destinations and unforgettable experiences with our curated travel packages.',
                                'placeholder' => 'Card excerpt text...',
                            ],

                            // Card Date
                            [
                                'key' => 'field_hc_card_date',
                                'label' => 'Date',
                                'name' => 'date',
                                'type' => 'date_picker',
                                'required' => 0,
                                'display_format' => 'F j, Y',
                                'return_format' => 'F j, Y',
                                'first_day' => 1,
                            ],

                            // Card Link
                            [
                                'key' => 'field_hc_card_link',
                                'label' => 'Link',
                                'name' => 'link',
                                'type' => 'link',
                                'required' => 0,
                                'return_format' => 'array',
                            ],

                            // Card CTA Text
                            [
                                'key' => 'field_hc_card_cta_text',
                                'label' => __('🔘 CTA Button Text', 'acf-gutenberg-rest-blocks'),
                                'name' => 'cta_text',
                                'type' => 'text',
                                'required' => 0,
                                'maxlength' => 30,
                                'default_value' => __('View More', 'acf-gutenberg-rest-blocks'),
                                'placeholder' => __('e.g., Explore, Read more', 'acf-gutenberg-rest-blocks'),
                                'instructions' => __('Text for the card button/link', 'acf-gutenberg-rest-blocks'),
                            ],

                            // Card Location
                            [
                                'key' => 'field_hc_card_location',
                                'label' => __('📍 Location', 'acf-gutenberg-rest-blocks'),
                                'name' => 'location',
                                'type' => 'text',
                                'required' => 0,
                                'maxlength' => 50,
                                'placeholder' => __('e.g., Cusco, Peru', 'acf-gutenberg-rest-blocks'),
                                'instructions' => __('Location displayed below description', 'acf-gutenberg-rest-blocks'),
                            ],

                            // Card Price
                            [
                                'key' => 'field_hc_card_price',
                                'label' => __('💰 Price', 'acf-gutenberg-rest-blocks'),
                                'name' => 'price',
                                'type' => 'text',
                                'required' => 0,
                                'maxlength' => 20,
                                'placeholder' => __('e.g., $299', 'acf-gutenberg-rest-blocks'),
                                'instructions' => __('Price of tour/product', 'acf-gutenberg-rest-blocks'),
                            ],
                        ],
                    ],
                ],
                $filter_fields // Add filter fields at the end
            );

            acf_add_local_field_group([
                'key' => 'group_hero_carousel',
                'title' => 'Hero Carousel Settings',
                'fields' => $fields,
                'location' => [
                    [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/hero-carousel',
                        ],
                    ],
                ],
            ]);
        }
    }
}
