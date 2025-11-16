<?php
namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;
use Travel\Blocks\Helpers\ContentQueryHelper;

class RelatedPackages
{
    private string $name = 'related-packages';
    private string $title = 'Related Packages';
    private string $description = 'Display related travel packages';

    public function register(): void
    {
        if (!function_exists('acf_register_block_type')) {
            return;
        }

        // Register ACF fields first
        $this->register_acf_fields();

        // Then register the block
        acf_register_block_type([
            'name' => $this->name,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'grid-view',
            'keywords' => ['related', 'packages', 'tours', 'recommendations', 'posts', 'blog'],
            'supports' => [
                'anchor' => true,
                'html' => false,
            ],
            'render_callback' => [$this, 'render'],
        ]);

        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function register_acf_fields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_related_packages_block',
            'title' => 'Related Packages Block Settings',
            'fields' => [
                // ========================================
                // TAB 1: 🎨 Estilos y Apariencia
                // ========================================
                [
                    'key' => 'field_rp_tab_styles',
                    'label' => '🎨 Estilos y Apariencia',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_rp_section_title',
                    'label' => 'Section Title',
                    'name' => 'section_title',
                    'type' => 'text',
                    'instructions' => 'Optional title for the section',
                    'placeholder' => 'You might also like...',
                ],
                [
                    'key' => 'field_rp_layout',
                    'label' => 'Layout Style',
                    'name' => 'layout',
                    'type' => 'select',
                    'instructions' => 'Choose between vertical cards or horizontal cards',
                    'choices' => [
                        'vertical' => 'Vertical Cards (Image top, content bottom)',
                        'horizontal' => 'Horizontal Cards (Image left, content right)',
                    ],
                    'default_value' => 'vertical',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_rp_button_color',
                    'label' => '🎨 Button Color Variant',
                    'name' => 'button_color',
                    'type' => 'select',
                    'instructions' => 'Choose the button color style',
                    'choices' => [
                        'primary' => 'Primary (Coral Pink)',
                        'secondary' => 'Secondary (Deep Purple)',
                        'gold' => 'Gold',
                        'dark' => 'Dark',
                        'white' => 'White',
                        'transparent' => 'Transparent',
                        'outline-primary' => 'Outline Primary',
                        'outline-white' => 'Outline White',
                        'text-black' => 'Text Only Black (Read More style)',
                    ],
                    'default_value' => 'primary',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_rp_badge_color',
                    'label' => '🏷️ Badge Color Variant',
                    'name' => 'badge_color',
                    'type' => 'select',
                    'instructions' => 'Choose the badge/label color style',
                    'choices' => [
                        'primary' => 'Primary (Coral Pink)',
                        'secondary' => 'Secondary (Deep Purple)',
                        'gold' => 'Gold',
                        'dark' => 'Dark',
                        'white' => 'White',
                        'transparent' => 'Transparent',
                    ],
                    'default_value' => 'primary',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_rp_button_text',
                    'label' => '🔘 Button Text',
                    'name' => 'button_text',
                    'type' => 'text',
                    'instructions' => 'Customize the button text (default: "View Details")',
                    'default_value' => 'View Details',
                    'maxlength' => 30,
                    'placeholder' => 'View Details',
                ],
                [
                    'key' => 'field_rp_text_alignment',
                    'label' => '📝 Text Alignment',
                    'name' => 'text_alignment',
                    'type' => 'select',
                    'instructions' => 'Align card text content',
                    'choices' => [
                        'left' => 'Left',
                        'center' => 'Center',
                        'right' => 'Right',
                    ],
                    'default_value' => 'left',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_rp_button_alignment',
                    'label' => '🔘 Button Alignment',
                    'name' => 'button_alignment',
                    'type' => 'select',
                    'instructions' => 'Align the CTA button',
                    'choices' => [
                        'left' => 'Left',
                        'center' => 'Center',
                        'right' => 'Right',
                    ],
                    'default_value' => 'left',
                    'ui' => 1,
                ],

                // ========================================
                // TAB 2: 📐 Layout y Dimensiones
                // ========================================
                [
                    'key' => 'field_rp_tab_layout',
                    'label' => '📐 Layout y Dimensiones',
                    'type' => 'tab',
                ],
                [
                    'key' => 'field_rp_card_min_height',
                    'label' => '📏 Card Minimum Height',
                    'name' => 'card_min_height',
                    'type' => 'number',
                    'instructions' => 'Set the minimum height for cards in pixels. Increase if content is getting cut off. Cards will grow taller if needed.',
                    'default_value' => 350,
                    'min' => 300,
                    'max' => 800,
                    'step' => 10,
                    'append' => 'px',
                ],
                [
                    'key' => 'field_rp_grid_width',
                    'label' => '📐 Card Width (Columns)',
                    'name' => 'grid_width',
                    'type' => 'select',
                    'instructions' => 'Set how much width each card occupies. This controls the number of columns.',
                    'choices' => [
                        '100' => '100% - 1 Column (Full Width)',
                        '90' => '90% - 1 Column (Narrow)',
                        '80' => '80% - 1 Column (More Narrow)',
                        '50' => '50% - 2 Columns',
                        '33.333' => '33% - 3 Columns',
                        '25' => '25% - 4 Columns',
                        '20' => '20% - 5 Columns',
                    ],
                    'default_value' => '33.333',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_rp_card_gap',
                    'label' => '↔️ Card Gap (Spacing)',
                    'name' => 'card_gap',
                    'type' => 'range',
                    'instructions' => 'Set the spacing between cards in pixels',
                    'default_value' => 24,
                    'min' => 8,
                    'max' => 64,
                    'step' => 4,
                    'append' => 'px',
                ],
                [
                    'key' => 'field_rp_hover_effect',
                    'label' => '✨ Hover Effect',
                    'name' => 'hover_effect',
                    'type' => 'select',
                    'instructions' => 'Choose the hover effect for cards',
                    'choices' => [
                        'lift' => 'Lift (Move up with shadow)',
                        'scale' => 'Scale (Zoom in)',
                        'none' => 'None',
                    ],
                    'default_value' => 'lift',
                    'ui' => 1,
                ],

                // ========================================
                // TAB 3: 🔍 Contenido Dinámico
                // ========================================
                // Use ContentQueryHelper standard fields
                ...ContentQueryHelper::get_dynamic_content_fields('rp'),
                ...ContentQueryHelper::get_filter_fields('rp'),

                // ========================================
                // TAB 4: ⚙️ Slider (Mobile)
                // ========================================
                [
                    'key' => 'field_rp_tab_slider',
                    'label' => '⚙️ Slider (Mobile)',
                    'type' => 'tab',
                ],
                [
                    'key' => 'field_rp_slider_info',
                    'label' => '',
                    'type' => 'message',
                    'message' => 'ℹ️ <strong>Mobile Slider Settings</strong><br>On mobile devices (≤768px), cards automatically convert to a swipeable slider. Configure autoplay, navigation, and pagination below.',
                    'new_lines' => 'wpautop',
                    'esc_html' => 0,
                ],
                [
                    'key' => 'field_rp_slider_autoplay',
                    'label' => '▶️ Autoplay',
                    'name' => 'slider_autoplay',
                    'type' => 'true_false',
                    'instructions' => 'Automatically advance slides',
                    'default_value' => 0,
                    'ui' => 1,
                    'ui_on_text' => 'On',
                    'ui_off_text' => 'Off',
                ],
                [
                    'key' => 'field_rp_slider_autoplay_delay',
                    'label' => '⏱️ Autoplay Delay',
                    'name' => 'slider_autoplay_delay',
                    'type' => 'range',
                    'instructions' => 'Time between slides in milliseconds',
                    'default_value' => 5000,
                    'min' => 2000,
                    'max' => 10000,
                    'step' => 1000,
                    'append' => 'ms',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_rp_slider_autoplay',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_rp_slider_speed',
                    'label' => '⚡ Slider Speed',
                    'name' => 'slider_speed',
                    'type' => 'range',
                    'instructions' => 'Transition speed in milliseconds',
                    'default_value' => 300,
                    'min' => 200,
                    'max' => 1000,
                    'step' => 100,
                    'append' => 'ms',
                ],
                [
                    'key' => 'field_rp_slider_show_arrows',
                    'label' => '◀️ ▶️ Show Navigation Arrows',
                    'name' => 'slider_show_arrows',
                    'type' => 'true_false',
                    'instructions' => 'Display prev/next arrows for manual navigation',
                    'default_value' => 1,
                    'ui' => 1,
                    'ui_on_text' => 'Show',
                    'ui_off_text' => 'Hide',
                ],
                [
                    'key' => 'field_rp_slider_arrows_position',
                    'label' => '📍 Arrows Position',
                    'name' => 'slider_arrows_position',
                    'type' => 'select',
                    'instructions' => 'Where to position navigation arrows',
                    'choices' => [
                        'sides' => 'Sides (Left/Right of cards)',
                        'bottom' => 'Bottom (Below cards)',
                    ],
                    'default_value' => 'sides',
                    'ui' => 1,
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_rp_slider_show_arrows',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_rp_slider_show_dots',
                    'label' => '⚫ Show Pagination Dots',
                    'name' => 'slider_show_dots',
                    'type' => 'true_false',
                    'instructions' => 'Display dot indicators for each slide',
                    'default_value' => 1,
                    'ui' => 1,
                    'ui_on_text' => 'Show',
                    'ui_off_text' => 'Hide',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/related-packages',
                    ],
                ],
            ],
        ]);
    }

    public function enqueue_assets(): void
    {
        wp_enqueue_style('related-packages-style', TRAVEL_BLOCKS_URL . 'assets/blocks/related-packages.css', [], TRAVEL_BLOCKS_VERSION);
        wp_enqueue_script('related-packages-script', TRAVEL_BLOCKS_URL . 'assets/blocks/related-packages.js', [], TRAVEL_BLOCKS_VERSION, true);
    }

    public function render($block, $content = '', $is_preview = false, $post_id = 0): void
    {
        try {
            // Get current post ID if not provided
            if (!$post_id) {
                $post_id = get_the_ID();
            }
            if (!$post_id) {
                $post_id = 0;
            }

            // Check if we're in preview/editor mode
            $is_preview = $is_preview || EditorHelper::is_editor_mode($post_id);

            // Get ACF field values - ACF handles block context automatically
            $acf_section_title = get_field('section_title') ?: '';
            $acf_layout = get_field('layout') ?: 'vertical';
            $acf_button_color = get_field('button_color') ?: 'primary';
            $acf_badge_color = get_field('badge_color') ?: 'primary';
            $acf_button_text = get_field('button_text') ?: 'View Details';
            $acf_text_alignment = get_field('text_alignment') ?: 'left';
            $acf_button_alignment = get_field('button_alignment') ?: 'left';
            $acf_card_min_height = get_field('card_min_height') ?: 350;
            $acf_grid_width = get_field('grid_width') ?: '33.333';
            $acf_card_gap = get_field('card_gap') ?: 24;
            $acf_hover_effect = get_field('hover_effect') ?: 'lift';
            $acf_slider_autoplay = get_field('slider_autoplay') ?: false;
            $acf_slider_autoplay_delay = get_field('slider_autoplay_delay') ?: 5000;
            $acf_slider_speed = get_field('slider_speed') ?: 300;
            $acf_slider_show_arrows = get_field('slider_show_arrows');
            if ($acf_slider_show_arrows === null) {
                $acf_slider_show_arrows = true;
            }
            $acf_slider_arrows_position = get_field('slider_arrows_position') ?: 'sides';
            $acf_slider_show_dots = get_field('slider_show_dots');
            if ($acf_slider_show_dots === null) {
                $acf_slider_show_dots = true;
            }

            // Get content using ContentQueryHelper
            $dynamic_source = get_field('rp_dynamic_source') ?: 'package';

            if ($is_preview) {
                $packages = $this->get_preview_data($dynamic_source);
            } elseif ($dynamic_source === 'package') {
                $packages = ContentQueryHelper::get_content('rp', 'package');
            } elseif ($dynamic_source === 'post') {
                $packages = ContentQueryHelper::get_content('rp', 'post');
            } elseif ($dynamic_source === 'deal') {
                $deal_id = get_field('rp_deal_selector');
                $packages = $deal_id ? ContentQueryHelper::get_deal_packages($deal_id, 'rp') : [];
            } else {
                $packages = [];
            }

            // Get display fields from ContentQueryHelper standard field
            $display_fields = get_field('rp_dynamic_visible_fields') ?: [];

            // Convert ContentQueryHelper field names to boolean flags for template compatibility
            $acf_show_image = in_array('image', $display_fields);
            $acf_show_destination = in_array('category', $display_fields);
            $acf_show_title = in_array('title', $display_fields);
            $acf_show_excerpt = in_array('description', $display_fields);
            $acf_show_location = in_array('location', $display_fields);
            $acf_show_duration = in_array('duration', $display_fields);
            $acf_show_price = in_array('price', $display_fields);
            $acf_show_button = true; // Always show button (controlled by template)

            $acf_post_type = $dynamic_source;

            if (empty($packages)) {
                // Show helpful message in editor when no results found
                if ($is_preview) {
                    $post_type_label = $dynamic_source === 'post' ? __('blog posts', 'travel-blocks') : __('packages', 'travel-blocks');
                    $filter_info = sprintf(__('No %s found matching the current filters.', 'travel-blocks'), $post_type_label);

                    echo '<div style="padding: 20px; background: #f0f0f0; border-left: 4px solid #ff9800; margin: 20px 0;">';
                    echo '<p style="margin: 0; color: #333;"><strong>⚠️ ' . __('No results', 'travel-blocks') . ':</strong> ' . esc_html($filter_info) . '</p>';
                    echo '</div>';
                }
                return;
            }

            $data = [
                'block_id' => 'related-packages-' . uniqid(),
                'class_name' => 'related-packages' . (!empty($block['className']) ? ' ' . $block['className'] : ''),
                'packages' => $packages,
                'section_title' => $acf_section_title,
                'layout' => $acf_layout,
                'button_color' => $acf_button_color,
                'badge_color' => $acf_badge_color,
                'button_text' => $acf_button_text,
                'text_alignment' => $acf_text_alignment,
                'button_alignment' => $acf_button_alignment,
                'card_min_height' => $acf_card_min_height,
                'grid_width' => $acf_grid_width,
                'card_gap' => $acf_card_gap,
                'hover_effect' => $acf_hover_effect,
                'slider_autoplay' => $acf_slider_autoplay,
                'slider_autoplay_delay' => $acf_slider_autoplay_delay,
                'slider_speed' => $acf_slider_speed,
                'slider_show_arrows' => $acf_slider_show_arrows,
                'slider_arrows_position' => $acf_slider_arrows_position,
                'slider_show_dots' => $acf_slider_show_dots,
                'show_image' => $acf_show_image,
                'show_destination' => $acf_show_destination,
                'show_title' => $acf_show_title,
                'show_excerpt' => $acf_show_excerpt,
                'show_location' => $acf_show_location,
                'show_duration' => $acf_show_duration,
                'show_price' => $acf_show_price,
                'show_button' => $acf_show_button,
                'is_preview' => $is_preview,
                'post_type' => $acf_post_type,
            ];

            $this->load_template('related-packages', $data);

        } catch (\Exception $e) {
            $error_msg = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
            error_log('Related Packages Block Error: ' . $error_msg);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                echo '<div style="padding:10px;background:#ffebee;">Error: ' . esc_html($error_msg) . '</div>';
            }
        }
    }

    private function get_preview_data(string $post_type = 'package'): array
    {
        if ($post_type === 'post') {
            // Preview data for Blog Posts
            return [
                [
                    'id' => 1,
                    'title' => '10 Essential Tips for Peru Travel',
                    'permalink' => '#',
                    'featured_image' => 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=800',
                    'price' => 0,
                    'duration' => 'January 15, 2025',
                    'destination' => 'Travel Tips',
                    'excerpt' => 'Discover the best travel tips for exploring Peru, from packing essentials to local customs and must-see attractions.',
                ],
                [
                    'id' => 2,
                    'title' => 'Best Time to Visit South America',
                    'permalink' => '#',
                    'featured_image' => 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=800',
                    'price' => 0,
                    'duration' => 'January 10, 2025',
                    'destination' => 'Destinations',
                    'excerpt' => 'Learn about the ideal seasons to visit different South American countries and make the most of your adventure.',
                ],
                [
                    'id' => 3,
                    'title' => 'Trekking Guide: Machu Picchu',
                    'permalink' => '#',
                    'featured_image' => 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=800',
                    'price' => 0,
                    'duration' => 'December 28, 2024',
                    'destination' => 'Guides',
                    'excerpt' => 'Everything you need to know before trekking to Machu Picchu, including permits, routes, and preparation tips.',
                ],
            ];
        }

        // Preview data for Packages (default)
        return [
            [
                'id' => 1,
                'title' => 'Machu Picchu Explorer',
                'permalink' => '#',
                'featured_image' => 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=800',
                'price' => 1299,
                'duration' => '7 Days',
                'destination' => 'Peru',
                'location' => 'Cusco, Peru',
                'excerpt' => 'Explore the ancient Inca citadel and surrounding Sacred Valley.',
            ],
            [
                'id' => 2,
                'title' => 'Amazon Rainforest Adventure',
                'permalink' => '#',
                'featured_image' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800',
                'price' => 999,
                'duration' => '5 Days',
                'destination' => 'Brazil',
                'location' => 'Manaus, Brazil',
                'excerpt' => 'Immerse yourself in the worlds largest tropical rainforest.',
            ],
            [
                'id' => 3,
                'title' => 'Patagonia Trekking',
                'permalink' => '#',
                'featured_image' => 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=800',
                'price' => 1599,
                'duration' => '10 Days',
                'destination' => 'Chile',
                'location' => 'Torres del Paine, Chile',
                'excerpt' => 'Trek through stunning glaciers and mountain landscapes.',
            ],
        ];
    }


    protected function load_template(string $template_name, array $data = []): void
    {
        $template_path = TRAVEL_BLOCKS_PATH . 'templates/' . $template_name . '.php';
        if (!file_exists($template_path)) {
            if (defined('WP_DEBUG') && WP_DEBUG) echo '<div style="padding:1rem;background:#fff3cd;">Template not found: ' . esc_html($template_name . '.php') . '</div>';
            return;
        }
        extract($data, EXTR_SKIP);
        include $template_path;
    }
}
