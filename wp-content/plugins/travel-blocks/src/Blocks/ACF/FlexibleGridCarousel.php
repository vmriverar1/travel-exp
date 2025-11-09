<?php
/**
 * Block: Flexible Grid Carousel
 *
 * Advanced grid/carousel with mixed content: cards AND text blocks.
 * Desktop: Responsive grid. Mobile: Native carousel.
 *
 * 🚨 DEFERRED - BLOCKED BY HeroCarousel DEPENDENCY 🚨
 *
 * Audit Score: 5.5/10 (CRITICAL - but blocked by HeroCarousel)
 *
 * CRITICAL ARCHITECTURAL ISSUES DOCUMENTED:
 * - FILE SIZE: 756 lines
 * - Does NOT inherit from BlockBase (inconsistent architecture)
 * - register_fields() method: 363 lines (CRITICAL) ⚠️ NOT REFACTORED
 * - render() method: 127 lines ⚠️ NOT REFACTORED
 * - 150 lines of hardcoded demo data
 * - ~70% CODE DUPLICATION with HeroCarousel (1173 lines)
 * - Double asset registration (enqueue_block_assets + enqueue_block_editor_assets)
 *
 * ❌ WHY NOT REFACTORED IN THIS SESSION:
 *
 * 1. ❌ BLOCKED BY HeroCarousel consolidation
 *    - Reason: ~70% shared code with HeroCarousel
 *    - Risk: Refactoring one without the other worsens duplication
 *    - Estimated: 4-6 hours to consolidate both blocks
 *    - Requires: User approval + consolidation strategy + migration
 *
 * 2. ❌ EXTRACT register_fields() 363 lines
 *    - Reason: Blocked by consolidation decision
 *    - Risk: Wasted effort if blocks are consolidated
 *    - Estimated: 2-3 hours (but may be obsolete after consolidation)
 *
 * 3. ❌ SPLIT render() 127 lines
 *    - Reason: Coupled with HeroCarousel render logic
 *    - Risk: Diverging implementations complicate future consolidation
 *    - Estimated: 1.5 hours
 *
 * 4. ❌ MOVE demo data to JSON
 *    - Reason: Same as HeroCarousel
 *    - Estimated: 30-45 min
 *    - Blocked by: File structure decision
 *
 * 5. ❌ BlockBase inheritance
 *    - Reason: Architectural decision needed first
 *    - Risk: May conflict with consolidation approach
 *    - Estimated: 2 hours
 *
 * ⚠️ RECOMMENDED APPROACH FOR FUTURE REFACTORING:
 * → FIRST: Get user approval to consolidate HeroCarousel + FlexibleGridCarousel
 * → Create unified "Advanced Grid/Hero Block" with layout variations
 * → Migrate existing content from both blocks
 * → THEN refactor the consolidated block properly
 * → Estimated: 10-15 hours total for consolidation + refactoring
 *
 * Features (currently working):
 * - Mixed content: Cards (image+title+excerpt+CTA) + Text Blocks (title+text)
 * - Dynamic content via ContentQueryHelper (packages/posts/deals)
 * - Manual content via ACF repeater
 * - Desktop: Responsive grid (2-6 columns)
 * - Mobile: Native scroll-snap carousel
 * - Flexible column-span pattern
 * - 6 button variants + 6 badge variants
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 1.2.0 - DOCUMENTED - deferred pending HeroCarousel consolidation decision
 */

namespace Travel\Blocks\Blocks\ACF;

use Travel\Blocks\Helpers\ContentQueryHelper;

class FlexibleGridCarousel {

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
                'name'              => 'flexible-grid-carousel',
                'title'             => __('Flexible Grid Carousel (Cards + Text Blocks)', 'acf-gutenberg-rest-blocks'),
                'description'       => __('Grid of cards with optional WYSIWYG text blocks', 'acf-gutenberg-rest-blocks'),
                'render_callback'   => [$this, 'render_block'],
                'category'          => 'travel',
                'icon'              => 'grid-view',
                'keywords'          => ['flexible', 'grid', 'carousel', 'cards', 'text', 'wysiwyg'],
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
            ]);
        }
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'flexible-grid-carousel-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/FlexibleGridCarousel/style.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        wp_enqueue_script(
            'flexible-grid-carousel-script',
            TRAVEL_BLOCKS_URL . 'assets/blocks/FlexibleGridCarousel/carousel.js',
            [],
            TRAVEL_BLOCKS_VERSION,
            true
        );
    }

    public function render_block($block, $content = '', $is_preview = false) {
        // Get WordPress block attributes
        $block_wrapper_attributes = get_block_wrapper_attributes([
            'class' => 'flexible-grid-carousel-wrapper'
        ]);

        // Get ACF fields (ACF automatically knows the context in preview mode)
        $columns_desktop = get_field('columns_desktop') ?: 3;
        $text_position_mobile = get_field('text_position_mobile') ?: 'above';
        $show_arrows = get_field('show_arrows');
        $show_dots = get_field('show_dots');
        $enable_autoplay = get_field('enable_autoplay');
        $autoplay_delay = get_field('autoplay_delay') ?: 5000;

        // Global style settings
        $button_color_variant = get_field('button_color_variant') ?: 'primary';
        $badge_color_variant = get_field('badge_color_variant') ?: 'secondary';
        $text_alignment = get_field('text_alignment') ?: 'left';
        $button_alignment = get_field('button_alignment') ?: 'left';

        // Check dynamic content source
        $dynamic_source = get_field('fgc_dynamic_source') ?: 'none';

        // Get items based on source
        if ($dynamic_source === 'package') {
            // Dynamic content from packages CPT using ContentQueryHelper
            $items = ContentQueryHelper::get_content('fgc', 'package');
            if (function_exists('travel_info')) {
                travel_info('Usando contenido dinámico de packages', [
                    'cards_count' => count($items),
                ]);
            }
        } elseif ($dynamic_source === 'post') {
            // Dynamic content from blog posts using ContentQueryHelper
            $items = ContentQueryHelper::get_content('fgc', 'post');
            if (function_exists('travel_info')) {
                travel_info('Usando contenido dinámico de blog posts', [
                    'cards_count' => count($items),
                ]);
            }
        } elseif ($dynamic_source === 'deal') {
            // Dynamic content from selected deal's packages
            $deal_id = get_field('fgc_deal_selector');
            if ($deal_id) {
                $items = ContentQueryHelper::get_deal_packages($deal_id, 'fgc');
                if (function_exists('travel_info')) {
                    travel_info('Usando paquetes del deal seleccionado', [
                        'deal_id' => $deal_id,
                        'cards_count' => count($items),
                    ]);
                }
            } else {
                $items = [];
            }
        } else {
            // Manual content (existing logic)
            $items = get_field('items');

            // Si no hay items, usar datos demo
            if (empty($items)) {
                $items = $this->get_demo_items();
            } else {
                // Rellenar imágenes vacías con demo images en cards
                foreach ($items as $index => &$item) {
                    if ($item['acf_fc_layout'] === 'card' && empty($item['image'])) {
                        $random_id = 400 + $index + 1;
                        $item['image'] = [
                            'url' => 'https://picsum.photos/400/300?random=' . $random_id,
                            'sizes' => [
                                'medium' => 'https://picsum.photos/300/225?random=' . $random_id
                            ],
                            'alt' => $item['title'] ?? 'Card Image'
                        ];
                    }
                }
                unset($item); // Romper la referencia
            }
        }

        // Separate cards and text blocks
        $cards = [];
        $text_blocks = [];
        foreach ($items as $index => $item) {
            if ($item['acf_fc_layout'] === 'card') {
                $item['original_index'] = $index;
                $cards[] = $item;
            } elseif ($item['acf_fc_layout'] === 'text_block') {
                $item['original_index'] = $index;
                $text_blocks[] = $item;
            }
        }

        // Get Display Fields (control what to show in each card)
        $display_fields_packages = get_field('fgc_mat_dynamic_visible_fields') ?: [];
        $display_fields_posts = get_field('fgc_mat_dynamic_visible_fields') ?: [];

        // Pass variables to template
        $template_data = [
            'block_wrapper_attributes' => $block_wrapper_attributes,
            'items' => $items,
            'cards' => $cards,
            'text_blocks' => $text_blocks,
            'columns_desktop' => $columns_desktop,
            'text_position_mobile' => $text_position_mobile,
            'button_color_variant' => $button_color_variant,
            'badge_color_variant' => $badge_color_variant,
            'text_alignment' => $text_alignment,
            'button_alignment' => $button_alignment,
            'show_arrows' => $show_arrows,
            'show_dots' => $show_dots,
            'enable_autoplay' => $enable_autoplay,
            'autoplay_delay' => $autoplay_delay,
            'display_fields_packages' => $display_fields_packages,
            'display_fields_posts' => $display_fields_posts,
            'is_preview' => $is_preview,
        ];

        // Load template
        $template_file = TRAVEL_BLOCKS_PATH . 'src/Blocks/FlexibleGridCarousel/templates/flexible-grid.php';

        if (file_exists($template_file)) {
            extract($template_data);
            include $template_file;
        } else {
            echo '<p>Template not found: ' . esc_html($template_file) . '</p>';
        }
    }

    private function get_demo_items() {
        return [
            // Card 1
            [
                'acf_fc_layout' => 'card',
                'image' => [
                    'url' => 'https://picsum.photos/400/300?random=401',
                    'sizes' => [
                        'medium' => 'https://picsum.photos/300/225?random=401'
                    ],
                    'alt' => 'Destination Guide'
                ],
                'title' => 'Destination Guides',
                'description' => 'Comprehensive guides to the world\'s most exciting destinations. Find insider tips, must-see attractions, and hidden gems.',
                'link' => [
                    'url' => '#',
                    'title' => 'Browse Guides',
                    'target' => ''
                ]
            ],
            // Text Block 1
            [
                'acf_fc_layout' => 'text_block',
                'content' => '<h3>Welcome to Our Travel Platform</h3>
<p>Discover extraordinary journeys tailored to your dreams. Whether you\'re seeking adventure, relaxation, or cultural immersion, we have the perfect trip for you.</p>
<ul>
    <li>Expert local guides</li>
    <li>Small group experiences</li>
    <li>Sustainable travel practices</li>
    <li>24/7 support</li>
</ul>
<p><strong>Join thousands of satisfied travelers</strong> who have explored the world with us.</p>'
            ],
            // Card 2
            [
                'acf_fc_layout' => 'card',
                'image' => [
                    'url' => 'https://picsum.photos/400/300?random=401',
                    'sizes' => [
                        'medium' => 'https://picsum.photos/300/225?random=401'
                    ],
                    'alt' => 'Travel Planning'
                ],
                'title' => 'Trip Planning Services',
                'description' => 'Let our experts craft your perfect itinerary. From flights to accommodations, we handle every detail of your journey.',
                'link' => [
                    'url' => '#',
                    'title' => 'Start Planning',
                    'target' => ''
                ]
            ],
            // Card 3
            [
                'acf_fc_layout' => 'card',
                'image' => [
                    'url' => 'https://picsum.photos/400/300?random=401',
                    'sizes' => [
                        'medium' => 'https://picsum.photos/300/225?random=401'
                    ],
                    'alt' => 'Group Tours'
                ],
                'title' => 'Group Adventures',
                'description' => 'Join like-minded travelers on curated group tours. Make new friends while exploring amazing destinations together.',
                'link' => [
                    'url' => '#',
                    'title' => 'View Tours',
                    'target' => ''
                ]
            ],
            // Card 4
            [
                'acf_fc_layout' => 'card',
                'image' => [
                    'url' => 'https://picsum.photos/400/300?random=401',
                    'sizes' => [
                        'medium' => 'https://picsum.photos/300/225?random=401'
                    ],
                    'alt' => 'Travel Insurance'
                ],
                'title' => 'Travel Protection',
                'description' => 'Travel with peace of mind. Our comprehensive insurance options cover medical emergencies, cancellations, and more.',
                'link' => [
                    'url' => '#',
                    'title' => 'Get Coverage',
                    'target' => ''
                ]
            ],
            // Text Block 2
            [
                'acf_fc_layout' => 'text_block',
                'content' => '<h3>Why Choose Us?</h3>
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px;">
    <h4 style="margin-top: 0;">Special Offer</h4>
    <p>Book your next adventure before the end of the month and receive:</p>
    <ul style="list-style: none; padding-left: 0;">
        <li>✈️ 15% off international flights</li>
        <li>🏨 Free hotel upgrade (subject to availability)</li>
        <li>🎒 Complimentary travel gear package</li>
        <li>📸 Professional photo session at destination</li>
    </ul>
    <p style="margin-bottom: 0;"><em>Terms and conditions apply. Contact us for details.</em></p>
</div>'
            ],
            // Card 5
            [
                'acf_fc_layout' => 'card',
                'image' => [
                    'url' => 'https://picsum.photos/400/300?random=401',
                    'sizes' => [
                        'medium' => 'https://picsum.photos/300/225?random=401'
                    ],
                    'alt' => 'Local Experiences'
                ],
                'title' => 'Authentic Local Tours',
                'description' => 'Experience destinations through the eyes of locals. Enjoy home-cooked meals, traditional crafts, and genuine connections.',
                'link' => [
                    'url' => '#',
                    'title' => 'Explore Local',
                    'target' => ''
                ]
            ],
            // Card 6
            [
                'acf_fc_layout' => 'card',
                'image' => [
                    'url' => 'https://picsum.photos/400/300?random=401',
                    'sizes' => [
                        'medium' => 'https://picsum.photos/300/225?random=401'
                    ],
                    'alt' => 'Travel Blog'
                ],
                'title' => 'Travel Stories & Tips',
                'description' => 'Read inspiring travel stories, practical tips, and destination insights from our community of adventurers.',
                'link' => [
                    'url' => '#',
                    'title' => 'Read Blog',
                    'target' => ''
                ]
            ],
            // Text Block 3
            [
                'acf_fc_layout' => 'text_block',
                'content' => '<h3>Ready to Start Your Journey?</h3>
<p style="font-size: 1.2em;">Don\'t just dream about your next adventure – make it happen! Our team of travel experts is ready to help you create memories that will last a lifetime.</p>
<blockquote style="border-left: 4px solid #4A5568; padding-left: 20px; margin: 20px 0; font-style: italic;">
    <p>"The world is a book, and those who do not travel read only one page."</p>
    <footer>— Saint Augustine</footer>
</blockquote>
<p><a href="#" style="display: inline-block; background: #4A5568; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px;">Contact Us Today</a></p>'
            ]
        ];
    }

    public function register_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        // Get dynamic content and filter fields from ContentQueryHelper with 'fgc' prefix
        $dynamic_fields = ContentQueryHelper::get_dynamic_content_fields('fgc');
        $filter_fields = ContentQueryHelper::get_filter_fields('fgc');

        // Build complete fields array
        $fields = array_merge(
            // ===== TAB: GENERAL =====
            [
                [
                    'key' => 'field_fgc_tab_general',
                    'label' => '⚙️ General',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_fgc_columns_desktop',
                    'label' => 'Columns (Desktop)',
                    'name' => 'columns_desktop',
                    'type' => 'select',
                    'instructions' => 'Number of columns on desktop grid',
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
                [
                    'key' => 'field_fgc_text_position_mobile',
                    'label' => 'Text Position (Mobile)',
                    'name' => 'text_position_mobile',
                    'type' => 'select',
                    'instructions' => 'Where to display text blocks on mobile',
                    'required' => 0,
                    'choices' => [
                        'above' => 'Above carousel',
                        'below' => 'Below carousel',
                    ],
                    'default_value' => 'above',
                    'ui' => 1,
                    'return_format' => 'value',
                ],
            ],
            // ===== TAB: CARD STYLES =====
            [
                [
                    'key' => 'field_fgc_tab_styles',
                    'label' => '🎨 Card Styles',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_fgc_button_color_variant',
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
                    'key' => 'field_fgc_badge_color_variant',
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
                    'key' => 'field_fgc_text_alignment',
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
                    'key' => 'field_fgc_button_alignment',
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
            ],
            // ===== TAB: CAROUSEL =====
            [
                [
                    'key' => 'field_fgc_tab_carousel',
                    'label' => '🎬 Carousel',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_fgc_show_arrows',
                    'label' => 'Show Navigation Arrows',
                    'name' => 'show_arrows',
                    'type' => 'true_false',
                    'instructions' => 'Display prev/next arrows',
                    'default_value' => 1,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_fgc_show_dots',
                    'label' => 'Show Pagination Dots',
                    'name' => 'show_dots',
                    'type' => 'true_false',
                    'instructions' => 'Display pagination dots',
                    'default_value' => 1,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_fgc_enable_autoplay',
                    'label' => 'Enable Autoplay',
                    'name' => 'enable_autoplay',
                    'type' => 'true_false',
                    'instructions' => 'Automatically advance slides',
                    'default_value' => 0,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_fgc_autoplay_delay',
                    'label' => 'Autoplay Delay (ms)',
                    'name' => 'autoplay_delay',
                    'type' => 'number',
                    'instructions' => 'Delay between slides in milliseconds',
                    'required' => 0,
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_fgc_enable_autoplay',
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
            // Dynamic content fields from helper
            $dynamic_fields,
            $filter_fields,
            // ===== TAB: ITEMS (Manual Content) =====
            [
                [
                    'key' => 'field_fgc_tab_items',
                    'label' => '🃏 Items',
                    'type' => 'tab',
                    'placement' => 'top',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_fgc_dynamic_source',
                                'operator' => '==',
                                'value' => 'none',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_fgc_items',
                    'label' => 'Items',
                    'name' => 'items',
                    'type' => 'flexible_content',
                    'instructions' => 'Add cards or text blocks. Desktop shows grid, mobile shows cards as carousel and text blocks separately.',
                    'required' => 0,
                    'button_label' => 'Add Item',
                    'layouts' => [
                        // Card Layout
                        'card' => [
                            'key' => 'layout_fgc_card',
                            'name' => 'card',
                            'label' => 'Card',
                            'display' => 'block',
                            'sub_fields' => [
                                [
                                    'key' => 'field_fgc_card_image',
                                    'label' => 'Image',
                                    'name' => 'image',
                                    'type' => 'image',
                                    'required' => 0,
                                    'return_format' => 'array',
                                    'preview_size' => 'medium',
                                ],
                                [
                                    'key' => 'field_fgc_card_category',
                                    'label' => 'Category Badge',
                                    'name' => 'category',
                                    'type' => 'text',
                                    'required' => 0,
                                    'default_value' => '',
                                    'placeholder' => 'e.g., Travel, Adventure, Culture',
                                ],
                                [
                                    'key' => 'field_fgc_card_badge_color',
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
                                [
                                    'key' => 'field_fgc_card_title',
                                    'label' => 'Title',
                                    'name' => 'title',
                                    'type' => 'text',
                                    'required' => 0,
                                    'default_value' => 'Card Title',
                                    'placeholder' => 'Card title',
                                ],
                                [
                                    'key' => 'field_fgc_card_description',
                                    'label' => 'Excerpt',
                                    'name' => 'description',
                                    'type' => 'textarea',
                                    'required' => 0,
                                    'rows' => 3,
                                    'default_value' => 'Discover amazing destinations and experiences with our travel services.',
                                    'placeholder' => 'Card excerpt text...',
                                ],
                                [
                                    'key' => 'field_fgc_card_location',
                                    'label' => __('📍 Location', 'acf-gutenberg-rest-blocks'),
                                    'name' => 'location',
                                    'type' => 'text',
                                    'required' => 0,
                                    'maxlength' => 50,
                                    'placeholder' => __('e.g., Cusco, Peru', 'acf-gutenberg-rest-blocks'),
                                    'instructions' => __('Location displayed below description', 'acf-gutenberg-rest-blocks'),
                                ],
                                [
                                    'key' => 'field_fgc_card_price',
                                    'label' => __('💰 Price', 'acf-gutenberg-rest-blocks'),
                                    'name' => 'price',
                                    'type' => 'text',
                                    'required' => 0,
                                    'maxlength' => 20,
                                    'placeholder' => __('e.g., $299', 'acf-gutenberg-rest-blocks'),
                                    'instructions' => __('Price of tour/product', 'acf-gutenberg-rest-blocks'),
                                ],
                                [
                                    'key' => 'field_fgc_card_link',
                                    'label' => 'Link',
                                    'name' => 'link',
                                    'type' => 'link',
                                    'required' => 0,
                                    'return_format' => 'array',
                                ],
                                [
                                    'key' => 'field_fgc_card_cta_text',
                                    'label' => __('🔘 CTA Button Text', 'acf-gutenberg-rest-blocks'),
                                    'name' => 'cta_text',
                                    'type' => 'text',
                                    'required' => 0,
                                    'maxlength' => 30,
                                    'default_value' => __('View More', 'acf-gutenberg-rest-blocks'),
                                    'placeholder' => __('e.g., Explore, Read more', 'acf-gutenberg-rest-blocks'),
                                    'instructions' => __('Text for the card button/link', 'acf-gutenberg-rest-blocks'),
                                ],
                            ],
                        ],
                        // Text Block Layout
                        'text_block' => [
                            'key' => 'layout_fgc_text_block',
                            'name' => 'text_block',
                            'label' => 'Text Block',
                            'display' => 'block',
                            'sub_fields' => [
                                [
                                    'key' => 'field_fgc_text_content',
                                    'label' => 'Content',
                                    'name' => 'content',
                                    'type' => 'wysiwyg',
                                    'required' => 0,
                                    'tabs' => 'all',
                                    'toolbar' => 'full',
                                    'media_upload' => 1,
                                    'delay' => 0,
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        acf_add_local_field_group([
            'key' => 'group_flexible_grid_carousel',
            'title' => 'Flexible Grid Carousel Settings',
            'fields' => $fields,
            'location' => [
                [
                    [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/flexible-grid-carousel',
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
}