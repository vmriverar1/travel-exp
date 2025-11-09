<?php
namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

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
                [
                    'key' => 'field_rp_tab_content',
                    'label' => '🔍 Contenido Dinámico',
                    'type' => 'tab',
                ],
                [
                    'key' => 'field_rp_post_type',
                    'label' => 'Post Type',
                    'name' => 'post_type',
                    'type' => 'select',
                    'instructions' => 'Select which post type to display',
                    'choices' => [
                        'package' => 'Packages',
                        'post' => 'Blog Posts',
                    ],
                    'default_value' => 'package',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_rp_posts_per_page',
                    'label' => 'Number of Items',
                    'name' => 'posts_per_page',
                    'type' => 'number',
                    'instructions' => 'How many items to display',
                    'default_value' => 3,
                    'min' => 1,
                    'max' => 12,
                ],
                [
                    'key' => 'field_rp_order_by',
                    'label' => 'Order By',
                    'name' => 'order_by',
                    'type' => 'select',
                    'instructions' => 'How to order the items',
                    'choices' => [
                        'date' => 'Date Published',
                        'modified' => 'Date Modified',
                        'title' => 'Title',
                        'rand' => 'Random',
                        'featured' => '⭐ Most Popular',
                        'menu_order' => 'Menu Order',
                    ],
                    'default_value' => 'date',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_rp_order',
                    'label' => 'Order',
                    'name' => 'order',
                    'type' => 'select',
                    'instructions' => 'Ascending or descending order',
                    'choices' => [
                        'DESC' => 'Descending',
                        'ASC' => 'Ascending',
                    ],
                    'default_value' => 'DESC',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_rp_filter_by_taxonomy',
                    'label' => 'Filter by Related Taxonomy',
                    'name' => 'filter_by_taxonomy',
                    'type' => 'true_false',
                    'instructions' => 'Show posts with similar categories/taxonomies to the current post',
                    'default_value' => 1,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_rp_specific_taxonomy',
                    'label' => 'Specific Taxonomy',
                    'name' => 'specific_taxonomy',
                    'type' => 'select',
                    'instructions' => 'Filter by a specific taxonomy (leave empty for automatic detection)',
                    'choices' => [
                        '' => 'Auto-detect from current post',
                        'destinations' => 'Destinations',
                        'package_category' => 'Package Category',
                        'category' => 'Blog Category',
                        'post_tag' => 'Blog Tags',
                    ],
                    'allow_null' => 1,
                    'ui' => 1,
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_rp_filter_by_taxonomy',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_rp_specific_terms',
                    'label' => 'Specific Terms',
                    'name' => 'specific_terms',
                    'type' => 'text',
                    'instructions' => 'Comma-separated term IDs to filter by (leave empty to use current post terms)',
                    'placeholder' => 'e.g., 12,34,56',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_rp_filter_by_taxonomy',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_rp_display_fields_package',
                    'label' => '👁️ Display Fields (Packages)',
                    'name' => 'display_fields',
                    'type' => 'checkbox',
                    'instructions' => 'Select which fields to display on package cards',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_rp_post_type',
                                'operator' => '==',
                                'value' => 'package',
                            ],
                        ],
                    ],
                    'choices' => [
                        'featured_image' => '🖼️ Featured Image',
                        'destination' => '🏷️ Destination Badge',
                        'title' => '📝 Title',
                        'excerpt' => '📄 Description/Excerpt',
                        'location' => '📍 Location',
                        'duration' => '⏱️ Duration (e.g., "7 Days")',
                        'price' => '💰 Price (e.g., "From $1,145")',
                        'button' => '🔘 View Details Button',
                    ],
                    'default_value' => ['featured_image', 'destination', 'title', 'excerpt', 'location', 'duration', 'price', 'button'],
                    'layout' => 'vertical',
                    'toggle' => 1,
                ],
                [
                    'key' => 'field_rp_display_fields_post',
                    'label' => '👁️ Display Fields (Blog Posts)',
                    'name' => 'display_fields',
                    'type' => 'checkbox',
                    'instructions' => 'Select which fields to display on blog post cards',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_rp_post_type',
                                'operator' => '==',
                                'value' => 'post',
                            ],
                        ],
                    ],
                    'choices' => [
                        'featured_image' => '🖼️ Featured Image',
                        'destination' => '🏷️ Category Badge',
                        'title' => '📝 Title',
                        'excerpt' => '📄 Excerpt/Description',
                        'duration' => '📅 Publication Date',
                        'button' => '🔘 View Details Button',
                    ],
                    'default_value' => ['featured_image', 'destination', 'title', 'excerpt', 'duration', 'button'],
                    'layout' => 'vertical',
                    'toggle' => 1,
                ],

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

            // Get display fields selection
            $display_fields = get_field('display_fields');
            if (empty($display_fields) || !is_array($display_fields)) {
                $display_fields = ['featured_image', 'destination', 'title', 'excerpt', 'location', 'duration', 'price', 'button'];
            }

            // Convert to boolean flags
            $acf_show_image = in_array('featured_image', $display_fields);
            $acf_show_destination = in_array('destination', $display_fields);
            $acf_show_title = in_array('title', $display_fields);
            $acf_show_excerpt = in_array('excerpt', $display_fields);
            $acf_show_location = in_array('location', $display_fields);
            $acf_show_duration = in_array('duration', $display_fields);
            $acf_show_price = in_array('price', $display_fields);
            $acf_show_button = in_array('button', $display_fields);

            $acf_post_type = get_field('post_type') ?: 'package';
            $acf_posts_per_page = get_field('posts_per_page') ?: 3;
            $acf_order_by = get_field('order_by') ?: 'date';
            $acf_order = get_field('order') ?: 'DESC';
            $acf_filter_by_taxonomy = get_field('filter_by_taxonomy');
            if ($acf_filter_by_taxonomy === null) {
                $acf_filter_by_taxonomy = true;
            }
            $acf_specific_taxonomy = get_field('specific_taxonomy') ?: '';
            $acf_specific_terms = get_field('specific_terms') ?: '';

            // Parse specific terms if provided
            $specific_terms_array = [];
            if (!empty($acf_specific_terms)) {
                $specific_terms_array = array_map('intval', array_map('trim', explode(',', $acf_specific_terms)));
            }

            $config = [
                'post_type' => $acf_post_type,
                'posts_per_page' => intval($acf_posts_per_page),
                'order_by' => $acf_order_by,
                'order' => $acf_order,
                'filter_by_taxonomy' => (bool)$acf_filter_by_taxonomy,
                'specific_taxonomy' => $acf_specific_taxonomy,
                'specific_terms' => $specific_terms_array,
            ];

            $packages = $is_preview ? $this->get_preview_data($acf_post_type) : $this->get_post_data($post_id, $config);

            if (empty($packages)) {
                // Show helpful message in editor when no results found
                if ($is_preview) {
                    $post_type_label = $acf_post_type === 'post' ? __('blog posts', 'travel-blocks') : __('packages', 'travel-blocks');
                    $filter_info = '';

                    if ($acf_order_by === 'featured') {
                        $filter_info = sprintf(
                            __('No featured %s found. Mark some %s as featured to display them here.', 'travel-blocks'),
                            $post_type_label,
                            $post_type_label
                        );
                    } else {
                        $filter_info = sprintf(__('No %s found matching the current filters.', 'travel-blocks'), $post_type_label);
                    }

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

    private function get_post_data(int $post_id, array $config): array
    {
        $post_type = $config['post_type'] ?? 'package';
        $posts_per_page = $config['posts_per_page'] ?? 3;
        $order_by = $config['order_by'] ?? 'date';
        $order = $config['order'] ?? 'DESC';
        $filter_by_taxonomy = $config['filter_by_taxonomy'] ?? true;
        $specific_taxonomy = $config['specific_taxonomy'] ?? '';
        $specific_terms = $config['specific_terms'] ?? [];

        // Build query args
        $args = [
            'post_type' => $post_type,
            'posts_per_page' => $posts_per_page,
            'post_status' => 'publish',
            'orderby' => $order_by === 'featured' ? 'date' : $order_by,
            'order' => $order,
        ];

        // Exclude current post only if we have a valid post ID
        if ($post_id > 0) {
            $args['post__not_in'] = [$post_id];
        }

        // Filter by featured posts if selected
        if ($order_by === 'featured') {
            $args['meta_query'] = [
                [
                    'key' => 'is_featured',
                    'value' => '1',
                    'compare' => '='
                ]
            ];
        }

        // Handle taxonomy filtering
        if ($filter_by_taxonomy && $post_id > 0) {
            $tax_query = ['relation' => 'OR'];
            $has_tax_filters = false;

            // If specific terms are provided, use them
            if (!empty($specific_terms) && !empty($specific_taxonomy)) {
                $tax_query[] = [
                    'taxonomy' => $specific_taxonomy,
                    'field' => 'term_id',
                    'terms' => $specific_terms,
                ];
                $has_tax_filters = true;
            } else {
                // Auto-detect taxonomies based on post type and current post
                $taxonomies_to_check = [];

                if ($post_type === 'package') {
                    $taxonomies_to_check = ['destinations', 'package_category'];
                } elseif ($post_type === 'post') {
                    $taxonomies_to_check = ['category', 'post_tag'];
                }

                // If specific taxonomy is set, only use that one
                if (!empty($specific_taxonomy)) {
                    $taxonomies_to_check = [$specific_taxonomy];
                }

                // Get terms from current post for each taxonomy
                foreach ($taxonomies_to_check as $taxonomy) {
                    $terms = wp_get_post_terms($post_id, $taxonomy, ['fields' => 'ids']);
                    if (!is_wp_error($terms) && !empty($terms)) {
                        $tax_query[] = [
                            'taxonomy' => $taxonomy,
                            'field' => 'term_id',
                            'terms' => $terms,
                        ];
                        $has_tax_filters = true;
                    }
                }
            }

            if ($has_tax_filters) {
                $args['tax_query'] = $tax_query;
            }
        }

        // Execute query
        $query = new \WP_Query($args);
        $items = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $item_id = get_the_ID();

                // Build item data based on post type
                if ($post_type === 'package') {
                    $items[] = $this->build_package_item($item_id);
                } else {
                    $items[] = $this->build_post_item($item_id);
                }
            }
            wp_reset_postdata();
        }

        return $items;
    }

    private function build_package_item(int $package_id): array
    {
        // Get price with fallbacks
        $price = get_field('price_offer', $package_id);
        if (empty($price)) $price = get_field('price_from', $package_id);
        if (empty($price)) $price = get_field('price_normal', $package_id);

        // Get destination
        $destination_name = '';
        $destinations = wp_get_post_terms($package_id, 'destinations', ['fields' => 'all']);
        if (!is_wp_error($destinations) && !empty($destinations)) {
            $destination_name = $destinations[0]->name;
        }

        // Get location/starting point
        $location = get_field('starting_point', $package_id) ?: '';

        return [
            'id' => $package_id,
            'title' => get_the_title(),
            'permalink' => get_permalink(),
            'featured_image' => get_the_post_thumbnail_url($package_id, 'large'),
            'price' => floatval($price),
            'duration' => get_field('duration', $package_id),
            'destination' => $destination_name,
            'location' => $location,
            'excerpt' => get_the_excerpt(),
        ];
    }

    private function build_post_item(int $post_id): array
    {
        // Get category
        $category_name = '';
        $categories = get_the_category($post_id);
        if (!empty($categories)) {
            $category_name = $categories[0]->name;
        }

        return [
            'id' => $post_id,
            'title' => get_the_title(),
            'permalink' => get_permalink(),
            'featured_image' => get_the_post_thumbnail_url($post_id, 'large'),
            'price' => 0, // Blog posts don't have prices
            'duration' => get_the_date('', $post_id), // Use publish date instead
            'destination' => $category_name, // Use category as "destination"
            'excerpt' => get_the_excerpt(),
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
