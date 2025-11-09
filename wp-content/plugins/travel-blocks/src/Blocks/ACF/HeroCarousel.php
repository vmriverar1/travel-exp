<?php
/**
 * Block: Hero Carousel
 *
 * Full-width hero carousel with InnerBlocks and flexible card grid.
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 2.1.0 - REFACTORED: Now inherits from CarouselBlockBase (FASE 3)
 *
 * Previous Issues (NOW RESOLVED):
 * - Does NOT inherit from BlockBase ✅ NOW INHERITS CarouselBlockBase
 * - Double asset registration ✅ FIXED
 * - render_block() method name ✅ NOW render()
 * - ~70% CODE DUPLICATION with FlexibleGridCarousel ✅ NOW RESOLVED via CarouselBlockBase
 *
 * Improvements in v2.1.0 (FASE 3):
 * - Extends CarouselBlockBase (eliminates ~200+ lines of duplicated code)
 * - Uses shared carousel/style fields methods
 * - Uses shared dynamic content methods
 * - register_fields() reduced significantly
 *
 * Features:
 * - InnerBlocks for hero content (title, subtitle, buttons)
 * - 4 layout variations: bottom, top, side_left, side_right
 * - Negative margins for creative overlaps
 * - Dynamic content via ContentQueryHelper (packages/posts/deals)
 * - Manual content via ACF repeater
 * - Desktop: Responsive grid (2-4 columns)
 * - Mobile: Native scroll-snap carousel
 * - Flexible column-span pattern
 * - 6 button variants + 6 badge variants
 * - Content proportion control (text vs cards)
 */

namespace Travel\Blocks\Blocks\ACF;

use Travel\Blocks\Core\CarouselBlockBase;
use Travel\Blocks\Helpers\ContentQueryHelper;

class HeroCarousel extends CarouselBlockBase
{
    /**
     * Constructor - Initialize block properties.
     *
     * Sets up block configuration following BlockBase pattern.
     *
     * @return void
     */
    public function __construct()
    {
        $this->name        = 'hero-carousel';
        $this->title       = __('Hero Carousel (Cards with Hero Background)', 'travel-blocks');
        $this->description = __('Display cards in carousel/grid with hero background image', 'travel-blocks');
        $this->category    = 'travel';
        $this->icon        = 'slides';
        $this->keywords    = ['hero', 'carousel', 'cards', 'background', 'slider'];
        $this->mode        = 'preview';

        $this->supports = [
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
        ];

        // Default InnerBlocks example for editor
        $this->example = [
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
                        'content' => __('Discover Your Next Adventure', 'travel-blocks'),
                        'textColor' => 'white',
                    ],
                ],
                [
                    'core/paragraph',
                    [
                        'content' => __('Explore amazing destinations and create unforgettable memories with our curated travel experiences.', 'travel-blocks'),
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
                                'text' => __('Learn More', 'travel-blocks'),
                                'url' => '#',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Register the block and its fields.
     *
     * ✅ REFACTORED: Now uses parent::register() from BlockBase
     * ✅ FIXED: Removed duplicate asset registration
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();
        $this->register_fields();
    }

    /**
     * Enqueue block assets.
     *
     * ✅ Kept from original - handles custom CSS and JS for HeroCarousel
     * Note: BlockBase automatically calls this via 'enqueue_block_assets' action
     *
     * @return void
     */
    public function enqueue_assets()
    {
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

    /**
     * Render the block.
     *
     * ✅ RENAMED: render_block() → render() to match BlockBase signature
     *
     * @param array  $block      The block settings and attributes.
     * @param string $content    The block inner HTML (from InnerBlocks).
     * @param bool   $is_preview True during backend preview render.
     * @param int    $post_id    The current post ID.
     * @return void
     */
    public function render($block, $content = '', $is_preview = false, $post_id = 0)
    {
        // Get WordPress block attributes
        $block_wrapper_attributes = get_block_wrapper_attributes([
            'class' => 'hero-carousel-wrapper'
        ]);

        // Get ACF fields (ACF automatically knows the context in preview mode)
        $layout_variation = get_field('layout_variation') ?: 'bottom';

        // ✅ REFACTORED: Use shared style settings from CarouselBlockBase
        $style_data = $this->get_style_data(true); // true = include alignments

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

        // ✅ REFACTORED: Get dynamic content using shared method from CarouselBlockBase
        $dynamic_source = get_field('hc_dynamic_source');
        $cards = $this->get_dynamic_content('hc', $dynamic_source ?: 'none');

        // If no dynamic content, use manual content
        if (empty($cards)) {
            $cards = get_field('cards');

            // Si no hay cards, usar datos demo
            if (empty($cards)) {
                $cards = $this->get_demo_cards();
            } else {
                // ✅ REFACTORED: Use shared method to fill demo images
                $cards = $this->fill_demo_images($cards, 310);
            }
        }

        // ✅ REFACTORED: Use shared carousel data method from CarouselBlockBase
        $total_cards = count($cards);
        $carousel_data = $this->get_carousel_data($total_cards, $columns_desktop);

        // Get Display Fields (control what to show in each card)
        $display_fields_packages = get_field('hc_mat_dynamic_visible_fields') ?: [];
        $display_fields_posts = get_field('hc_mat_dynamic_visible_fields') ?: [];

        // ✅ REFACTORED: Pass variables to template using shared data arrays
        $template_data = array_merge([
            'block_wrapper_attributes' => $block_wrapper_attributes,
            'layout_variation' => $layout_variation,
            'content_proportion' => $content_proportion,
            'cards_proportion' => $cards_proportion,
            'hero_image' => $hero_image,
            'hero_content' => $content, // InnerBlocks rendered content
            'has_hero_text' => $has_hero_text,
            'cards' => $cards,
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
            'display_fields_packages' => $display_fields_packages,
            'display_fields_posts' => $display_fields_posts,
            'is_preview' => $is_preview,
        ], $style_data, $carousel_data); // Merge shared style and carousel data

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

    /**
     * Register ACF fields for the block.
     *
     * ✅ Made private (was public)
     * Note: 691 lines - will consolidate with FlexibleGridCarousel in FASE 3
     *
     * @return void
     */
    private function register_fields(): void
    {
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
                ],
                // ✅ REFACTORED: Use shared grid columns field from CarouselBlockBase
                $this->get_grid_columns_field('hc', 2, 4, 3), // min: 2, max: 4, default: 3
                [
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
                ],
                // ✅ REFACTORED: Use shared style settings from CarouselBlockBase
                $this->get_style_settings_fields('hc', true), // true = include text/button alignments
                [
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
                ],
                // ✅ REFACTORED: Use shared carousel settings from CarouselBlockBase
                $this->get_carousel_settings_fields('hc'),
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

    /**
     * Get block-specific ACF fields (required by CarouselBlockBase).
     *
     * HeroCarousel's specific fields are already handled in register_fields().
     * This method exists to satisfy the abstract requirement from CarouselBlockBase.
     *
     * @param string $prefix Field key prefix ('hc')
     * @return array Empty array (fields are handled elsewhere)
     */
    protected function get_block_specific_fields(string $prefix): array
    {
        // All Hero-specific fields (layout, dimensions, hero content, cards repeater)
        // are already defined in register_fields() method above.
        // This method is required by CarouselBlockBase but not used in this implementation.
        return [];
    }
}
