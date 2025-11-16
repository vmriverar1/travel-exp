<?php
/**
 * Block: Flexible Grid Carousel
 *
 * Advanced grid/carousel with mixed content: cards AND text blocks.
 * Desktop: Responsive grid. Mobile: Native carousel.
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 2.1.0 - REFACTORED: Now inherits from CarouselBlockBase (FASE 3)
 *
 * Previous Issues (NOW RESOLVED):
 * - Does NOT inherit from BlockBase ✅ NOW INHERITS CarouselBlockBase
 * - Double asset registration ✅ FIXED
 * - render_block() method name ✅ NOW render()
 * - ~70% CODE DUPLICATION with HeroCarousel ✅ NOW RESOLVED via CarouselBlockBase
 *
 * Improvements in v2.1.0 (FASE 3):
 * - Extends CarouselBlockBase (eliminates ~150+ lines of duplicated code)
 * - Uses shared carousel/style fields methods
 * - Uses shared dynamic content methods
 * - register_fields() reduced significantly
 *
 * Features:
 * - Mixed content: Cards (image+title+excerpt+CTA) + Text Blocks (WYSIWYG)
 * - Dynamic content via ContentQueryHelper (packages/posts/deals)
 * - Manual content via ACF flexible content
 * - Desktop: Responsive grid (2-4 columns)
 * - Mobile: Native scroll-snap carousel
 * - Flexible column-span pattern
 * - 6 button variants + 6 badge variants
 * - Text position control (above/below carousel on mobile)
 */

namespace Travel\Blocks\Blocks\ACF;

use Travel\Blocks\Core\CarouselBlockBase;
use Travel\Blocks\Helpers\ContentQueryHelper;

class FlexibleGridCarousel extends CarouselBlockBase
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
        $this->name        = 'flexible-grid-carousel';
        $this->title       = __('Flexible Grid Carousel (Cards + Text Blocks)', 'travel-blocks');
        $this->description = __('Grid of cards with optional WYSIWYG text blocks', 'travel-blocks');
        $this->category    = 'travel';
        $this->icon        = 'grid-view';
        $this->keywords    = ['flexible', 'grid', 'carousel', 'cards', 'text', 'wysiwyg'];
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
    }

    /**
     * Register block and ACF fields.
     *
     * ✅ REFACTORED: Now uses parent::register() from BlockBase.
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
     * ✅ REFACTORED: Removed duplicate add_action() calls.
     * Assets now enqueued only via BlockBase pattern.
     *
     * @return void
     */
    public function enqueue_assets(): void
    {
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

    /**
     * Render the block output.
     *
     * ✅ REFACTORED v2.0.0:
     * - Renamed from render_block() to render()
     * - Method signature matches BlockBase
     *
     * @param array  $block      Block settings and attributes
     * @param string $content    Block content (unused)
     * @param bool   $is_preview Whether block is being previewed in editor
     * @param int    $post_id    Current post ID
     *
     * @return void
     */
        public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        // Get WordPress block attributes
        $block_wrapper_attributes = get_block_wrapper_attributes([
            'class' => 'flexible-grid-carousel-wrapper'
        ]);

        // Get ACF fields (ACF automatically knows the context in preview mode)
        $columns_desktop = get_field('columns_desktop') ?: 3;
        $text_position_mobile = get_field('text_position_mobile') ?: 'above';

        // ✅ REFACTORED: Use shared style settings from CarouselBlockBase
        $style_data = $this->get_style_data(true); // true = include alignments

        // Get card min height
        $card_min_height = (int)(get_field('card_min_height') ?: 450);

        // ✅ REFACTORED: Get dynamic content using shared method from CarouselBlockBase
        $dynamic_source = get_field('fgc_dynamic_source') ?: 'none';
        $items = $this->get_dynamic_content('fgc', $dynamic_source);

        // If no dynamic content, use manual content
        if (empty($items)) {
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

        // ✅ REFACTORED: Use shared carousel data method from CarouselBlockBase
        $total_cards = count($cards);
        $carousel_data = $this->get_carousel_data($total_cards, $columns_desktop);

        // Get Display Fields (control what to show in each card)
        $display_fields_packages = get_field('fgc_mat_dynamic_visible_fields') ?: [];
        $display_fields_posts = get_field('fgc_mat_dynamic_visible_fields') ?: [];

        // ✅ REFACTORED: Pass variables to template using shared data arrays
        $template_data = array_merge([
            'block_wrapper_attributes' => $block_wrapper_attributes,
            'items' => $items,
            'cards' => $cards,
            'text_blocks' => $text_blocks,
            'text_position_mobile' => $text_position_mobile,
            'display_fields_packages' => $display_fields_packages,
            'display_fields_posts' => $display_fields_posts,
            'card_min_height' => $card_min_height,
            'is_preview' => $is_preview,
        ], $style_data, $carousel_data); // Merge shared style and carousel data

        // Load template
        // FlexibleGridCarousel uses custom template location
        $template_file = TRAVEL_BLOCKS_PATH . 'src/Blocks/FlexibleGridCarousel/templates/flexible-grid.php';

        if (file_exists($template_file)) {
            extract($template_data);
            include $template_file;
        } else {
            echo '<p>' . esc_html__('Template not found: ', 'travel-blocks') . esc_html($template_file) . '</p>';
        }
    }

    /**
     * Get demo items (cards and text blocks).
     *
     * Returns demo data for preview mode when no items are added.
     * Includes 6 cards and 3 text blocks showcasing the flexible layout.
     *
     * @return array Array of demo items
     */
    private function get_demo_items(): array
    {
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
                    'url' => 'https://picsum.photos/400/300?random=402',
                    'sizes' => [
                        'medium' => 'https://picsum.photos/300/225?random=402'
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
                    'url' => 'https://picsum.photos/400/300?random=403',
                    'sizes' => [
                        'medium' => 'https://picsum.photos/300/225?random=403'
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
                    'url' => 'https://picsum.photos/400/300?random=404',
                    'sizes' => [
                        'medium' => 'https://picsum.photos/300/225?random=404'
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
                    'url' => 'https://picsum.photos/400/300?random=405',
                    'sizes' => [
                        'medium' => 'https://picsum.photos/300/225?random=405'
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
                    'url' => 'https://picsum.photos/400/300?random=406',
                    'sizes' => [
                        'medium' => 'https://picsum.photos/300/225?random=406'
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

    /**
     * Register ACF fields for Flexible Grid Carousel block.
     *
     * Defines fields for:
     * - General settings (columns, text position)
     * - Card styles (button color, badge color, alignment)
     * - Carousel controls (arrows, dots, autoplay)
     * - Dynamic content via ContentQueryHelper
     * - Manual items (flexible content: cards + text blocks)
     *
     * @return void
     */
    private function register_fields(): void
    {
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
                    'label' => __('🎨 Button Color', 'travel-blocks'),
                    'name' => 'button_color_variant',
                    'type' => 'select',
                    'required' => 0,
                    'choices' => [
                        'primary' => __('Primary - Pink (#E78C85)', 'travel-blocks'),
                        'secondary' => __('Secondary - Purple (#311A42)', 'travel-blocks'),
                        'white' => __('White with black text', 'travel-blocks'),
                        'gold' => __('Gold (#CEA02D)', 'travel-blocks'),
                        'dark' => __('Dark (#1A1A1A)', 'travel-blocks'),
                        'transparent' => __('Transparent with white border', 'travel-blocks'),
                        'read-more' => __('Text "Read More" (no background)', 'travel-blocks'),
                    ],
                    'default_value' => 'primary',
                    'ui' => 1,
                    'instructions' => __('Color applied to all card buttons', 'travel-blocks'),
                ],
                [
                    'key' => 'field_fgc_badge_color_variant',
                    'label' => __('🎨 Badge Color', 'travel-blocks'),
                    'name' => 'badge_color_variant',
                    'type' => 'select',
                    'required' => 0,
                    'choices' => [
                        'primary' => __('Primary - Pink (#E78C85)', 'travel-blocks'),
                        'secondary' => __('Secondary - Purple (#311A42)', 'travel-blocks'),
                        'white' => __('White with black text', 'travel-blocks'),
                        'gold' => __('Gold (#CEA02D)', 'travel-blocks'),
                        'dark' => __('Dark (#1A1A1A)', 'travel-blocks'),
                        'transparent' => __('Transparent with white border', 'travel-blocks'),
                    ],
                    'default_value' => 'secondary',
                    'ui' => 1,
                    'instructions' => __('Color applied to all badges', 'travel-blocks'),
                ],
                [
                    'key' => 'field_fgc_text_alignment',
                    'label' => __('📐 Text Alignment', 'travel-blocks'),
                    'name' => 'text_alignment',
                    'type' => 'select',
                    'required' => 0,
                    'choices' => [
                        'left' => __('Left', 'travel-blocks'),
                        'center' => __('Center', 'travel-blocks'),
                        'right' => __('Right', 'travel-blocks'),
                    ],
                    'default_value' => 'left',
                    'ui' => 1,
                    'instructions' => __('Text alignment (title, description, location, price)', 'travel-blocks'),
                ],
                [
                    'key' => 'field_fgc_button_alignment',
                    'label' => __('📍 Button Alignment', 'travel-blocks'),
                    'name' => 'button_alignment',
                    'type' => 'select',
                    'required' => 0,
                    'choices' => [
                        'left' => __('Left', 'travel-blocks'),
                        'center' => __('Center', 'travel-blocks'),
                        'right' => __('Right', 'travel-blocks'),
                    ],
                    'default_value' => 'left',
                    'ui' => 1,
                    'instructions' => __('Button/CTA alignment', 'travel-blocks'),
                ],
                [
                    'key' => 'field_fgc_card_min_height',
                    'label' => __('📏 Altura Mínima de Cards', 'travel-blocks'),
                    'name' => 'card_min_height',
                    'type' => 'range',
                    'instructions' => __('Altura mínima de las cards. Las cards crecerán si el contenido es mayor.', 'travel-blocks'),
                    'default_value' => 450,
                    'min' => 300,
                    'max' => 800,
                    'step' => 10,
                    'append' => 'px',
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
                                    'label' => __('🎨 Badge Color (Individual)', 'travel-blocks'),
                                    'name' => 'badge_color_variant',
                                    'type' => 'select',
                                    'required' => 0,
                                    'choices' => [
                                        '' => __('Use global setting', 'travel-blocks'),
                                        'primary' => __('Pink (#E78C85)', 'travel-blocks'),
                                        'secondary' => __('Purple (#311A42)', 'travel-blocks'),
                                        'white' => __('White', 'travel-blocks'),
                                        'gold' => __('Gold (#CEA02D)', 'travel-blocks'),
                                        'dark' => __('Dark (#1A1A1A)', 'travel-blocks'),
                                        'transparent' => __('Transparent with border', 'travel-blocks'),
                                    ],
                                    'default_value' => '',
                                    'allow_null' => 1,
                                    'ui' => 1,
                                    'instructions' => __('Override global badge color for this card only', 'travel-blocks'),
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
                                    'label' => __('📍 Location', 'travel-blocks'),
                                    'name' => 'location',
                                    'type' => 'text',
                                    'required' => 0,
                                    'maxlength' => 50,
                                    'placeholder' => __('e.g., Cusco, Peru', 'travel-blocks'),
                                    'instructions' => __('Location displayed below description', 'travel-blocks'),
                                ],
                                [
                                    'key' => 'field_fgc_card_price',
                                    'label' => __('💰 Price', 'travel-blocks'),
                                    'name' => 'price',
                                    'type' => 'text',
                                    'required' => 0,
                                    'maxlength' => 20,
                                    'placeholder' => __('e.g., $299', 'travel-blocks'),
                                    'instructions' => __('Price of tour/product', 'travel-blocks'),
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
                                    'label' => __('🔘 CTA Button Text', 'travel-blocks'),
                                    'name' => 'cta_text',
                                    'type' => 'text',
                                    'required' => 0,
                                    'maxlength' => 30,
                                    'default_value' => __('View More', 'travel-blocks'),
                                    'placeholder' => __('e.g., Explore, Read more', 'travel-blocks'),
                                    'instructions' => __('Text for the card button/link', 'travel-blocks'),
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

    /**
     * Get block-specific ACF fields (required by CarouselBlockBase).
     *
     * FlexibleGridCarousel's specific fields are already handled in register_fields().
     * This method exists to satisfy the abstract requirement from CarouselBlockBase.
     *
     * @param string $prefix Field key prefix ('fgc')
     * @return array Empty array (fields are handled elsewhere)
     */
    protected function get_block_specific_fields(string $prefix): array
    {
        // All FlexibleGrid-specific fields (flexible content, text position)
        // are already defined in register_fields() method above.
        // This method is required by CarouselBlockBase but not used in this implementation.
        return [];
    }
}
