<?php
/**
 * Block: Team Carousel
 *
 * Team member profiles displayed in carousel (mobile) or grid (desktop).
 * Features native CSS scroll-snap carousel without external libraries.
 *
 * Features:
 * - 2 layout variations: Profile Card (circular) and Full Body Portrait (vertical)
 * - Desktop: Static grid (2/3/4 columns, no carousel)
 * - Mobile: Native carousel with CSS scroll-snap (<1024px)
 * - Carousel controls: arrows, dots, autoplay, keyboard navigation
 * - Skeleton loader with shimmer animation
 * - Demo data with 6 realistic team members
 * - Sub-repeater for achievements (Profile Card layout)
 * - Conditional fields based on layout style
 *
 * ⚠️ Note: Does NOT inherit from BlockBase (simple standalone block).
 * Demo data (179 lines) could be moved to JSON file.
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 1.1.0 - Refactored: namespace fix, improved documentation
 */

namespace Travel\Blocks\Blocks\ACF;

class TeamCarousel {

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
                'name'              => 'team-carousel',
                'title'             => __('Team Carousel (People Profiles)', 'acf-gutenberg-rest-blocks'),
                'description'       => __('Display team members in carousel/grid with two layout variations', 'acf-gutenberg-rest-blocks'),
                'render_callback'   => [$this, 'render_block'],
                'category'          => 'travel',
                'icon'              => 'groups',
                'keywords'          => ['team', 'carousel', 'people', 'staff', 'profiles'],
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
            'team-carousel-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/TeamCarousel/style.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        wp_enqueue_script(
            'team-carousel-script',
            TRAVEL_BLOCKS_URL . 'assets/blocks/TeamCarousel/carousel.js',
            [],
            TRAVEL_BLOCKS_VERSION,
            true
        );
    }

    public function render_block($block, $content = '', $is_preview = false) {
        // Get WordPress block attributes
        $block_wrapper_attributes = get_block_wrapper_attributes([
            'class' => 'team-carousel-wrapper'
        ]);

        // Get ACF fields (ACF automatically knows the context in preview mode)
        $layout_style = get_field('layout_style') ?: 'profile_card';
        $posts_to_display = get_field('posts_to_display') ?: 3;
        $columns_desktop = get_field('columns_desktop') ?: 3;
        $image_height = get_field('image_height') ?: 400;
        $show_arrows = get_field('show_arrows');
        $show_dots = get_field('show_dots');
        $enable_autoplay = get_field('enable_autoplay');
        $autoplay_delay = get_field('autoplay_delay') ?: 5000;

        // Get team members (repeater)
        $team_members = get_field('team_members');

        // Si no hay team members, usar datos demo
        if (empty($team_members)) {
            $team_members = $this->get_demo_team_members($layout_style);
        } else {
            // Rellenar imágenes vacías con demo images
            foreach ($team_members as $index => &$member) {
                if (empty($member['image'])) {
                    $random_id = 100 + $index + 1;
                    if ($layout_style === 'profile_card') {
                        $member['image'] = [
                            'url' => 'https://picsum.photos/400/400?random=' . $random_id,
                            'sizes' => [
                                'medium' => 'https://picsum.photos/300/300?random=' . $random_id
                            ],
                            'alt' => $member['name'] ?? 'Team Member'
                        ];
                    } else {
                        $member['image'] = [
                            'url' => 'https://picsum.photos/400/600?random=' . $random_id,
                            'sizes' => [
                                'medium' => 'https://picsum.photos/300/450?random=' . $random_id
                            ],
                            'alt' => $member['name'] ?? 'Team Member'
                        ];
                    }
                }
            }
            unset($member); // Romper la referencia
        }

        // Limit to posts_to_display
        $team_members = array_slice($team_members, 0, $posts_to_display);

        // Pass variables to template
        $template_data = [
            'block_wrapper_attributes' => $block_wrapper_attributes,
            'layout_style' => $layout_style,
            'team_members' => $team_members,
            'columns_desktop' => $columns_desktop,
            'image_height' => $image_height,
            'show_arrows' => $show_arrows,
            'show_dots' => $show_dots,
            'enable_autoplay' => $enable_autoplay,
            'autoplay_delay' => $autoplay_delay,
            'is_preview' => $is_preview,
        ];

        // Load template based on layout (convert underscores to hyphens)
        $template_name = str_replace('_', '-', $layout_style);
        $template_file = TRAVEL_BLOCKS_PATH . 'src/Blocks/TeamCarousel/templates/' . $template_name . '.php';

        if (file_exists($template_file)) {
            extract($template_data);
            include $template_file;
        } else {
            echo '<p>Template not found: ' . esc_html($template_file) . '</p>';
        }
    }

    private function get_demo_team_members($layout_style) {
        $demo_members = [];

        if ($layout_style === 'profile_card') {
            // Demo data for profile card variation
            $demo_members = [
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/400?random=101',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/300?random=101'
                        ],
                        'alt' => 'Sarah Johnson'
                    ],
                    'name' => 'Sarah Johnson',
                    'description' => 'Expert travel guide with over 15 years of experience exploring hidden gems across Southeast Asia. Specializes in cultural immersion and sustainable tourism practices.',
                    'achievements' => [
                        ['achievement_text' => '15+ years experience'],
                        ['achievement_text' => 'Certified Adventure Guide'],
                        ['achievement_text' => '50+ countries visited'],
                        ['achievement_text' => 'Speaks 5 languages']
                    ]
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/400?random=102',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/300?random=102'
                        ],
                        'alt' => 'Michael Chen'
                    ],
                    'name' => 'Michael Chen',
                    'description' => 'Photography enthusiast and mountain trekking specialist. Leads expeditions to remote locations and captures breathtaking moments along the journey.',
                    'achievements' => [
                        ['achievement_text' => 'Summit 20+ peaks'],
                        ['achievement_text' => 'National Geographic contributor'],
                        ['achievement_text' => 'Wilderness First Aid certified'],
                        ['achievement_text' => 'Trail mapping expert']
                    ]
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/400?random=103',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/300?random=103'
                        ],
                        'alt' => 'Emma Rodriguez'
                    ],
                    'name' => 'Emma Rodriguez',
                    'description' => 'Culinary travel expert and food historian. Organizes gastronomic tours that blend local cuisine with cultural storytelling and traditional cooking classes.',
                    'achievements' => [
                        ['achievement_text' => 'Certified sommelier'],
                        ['achievement_text' => 'Published food writer'],
                        ['achievement_text' => '200+ restaurant reviews'],
                        ['achievement_text' => 'Michelin tour guide']
                    ]
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/400?random=104',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/300?random=104'
                        ],
                        'alt' => 'James Park'
                    ],
                    'name' => 'James Park',
                    'description' => 'Marine biologist turned dive instructor. Specializes in coral reef conservation tours and underwater photography expeditions in tropical destinations.',
                    'achievements' => [
                        ['achievement_text' => 'PADI Master Instructor'],
                        ['achievement_text' => '5000+ logged dives'],
                        ['achievement_text' => 'Marine conservation advocate'],
                        ['achievement_text' => 'Underwater photographer']
                    ]
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/400?random=105',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/300?random=105'
                        ],
                        'alt' => 'Lisa Thompson'
                    ],
                    'name' => 'Lisa Thompson',
                    'description' => 'Historical architecture enthusiast and certified tour guide. Leads heritage walks through ancient cities and UNESCO World Heritage sites.',
                    'achievements' => [
                        ['achievement_text' => 'Art History PhD'],
                        ['achievement_text' => 'Museum curator experience'],
                        ['achievement_text' => 'Published 3 guidebooks'],
                        ['achievement_text' => 'Heritage site consultant']
                    ]
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/400?random=106',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/300?random=106'
                        ],
                        'alt' => 'David Kumar'
                    ],
                    'name' => 'David Kumar',
                    'description' => 'Wildlife photographer and safari guide. Expert in tracking big cats and organizing ethical wildlife viewing experiences across African reserves.',
                    'achievements' => [
                        ['achievement_text' => '10+ years safari guide'],
                        ['achievement_text' => 'Wildlife tracking certified'],
                        ['achievement_text' => 'Conservation volunteer'],
                        ['achievement_text' => 'Award-winning photographer']
                    ]
                ]
            ];
        } else {
            // Demo data for full body variation
            $demo_members = [
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=201',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=201'
                        ],
                        'alt' => 'Sarah Johnson - Senior Tour Guide'
                    ],
                    'name' => 'Sarah Johnson',
                    'position' => 'Senior Tour Guide'
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=202',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=202'
                        ],
                        'alt' => 'Michael Chen - Mountain Expedition Leader'
                    ],
                    'name' => 'Michael Chen',
                    'position' => 'Mountain Expedition Leader'
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=203',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=203'
                        ],
                        'alt' => 'Emma Rodriguez - Culinary Travel Expert'
                    ],
                    'name' => 'Emma Rodriguez',
                    'position' => 'Culinary Travel Expert'
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=204',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=204'
                        ],
                        'alt' => 'James Park - Dive Master'
                    ],
                    'name' => 'James Park',
                    'position' => 'Dive Master'
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=205',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=205'
                        ],
                        'alt' => 'Lisa Thompson - Heritage Tour Specialist'
                    ],
                    'name' => 'Lisa Thompson',
                    'position' => 'Heritage Tour Specialist'
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=206',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=206'
                        ],
                        'alt' => 'David Kumar - Wildlife Safari Guide'
                    ],
                    'name' => 'David Kumar',
                    'position' => 'Wildlife Safari Guide'
                ]
            ];
        }

        return $demo_members;
    }

    public function register_fields() {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_team_carousel',
                'title' => 'Team Carousel Settings',
                'fields' => [
                    // Layout Style
                    [
                        'key' => 'field_tc_layout_style',
                        'label' => 'Layout Style',
                        'name' => 'layout_style',
                        'type' => 'select',
                        'instructions' => 'Choose how team members are displayed',
                        'required' => 0,
                        'choices' => [
                            'profile_card' => 'Profile Card (Photo + Description + Achievements)',
                            'full_body' => 'Full Body Portrait (Vertical Photo + Name + Position)',
                        ],
                        'default_value' => 'profile_card',
                        'ui' => 1,
                        'return_format' => 'value',
                    ],

                    // Columns Desktop
                    [
                        'key' => 'field_tc_columns_desktop',
                        'label' => 'Columns (Desktop)',
                        'name' => 'columns_desktop',
                        'type' => 'select',
                        'instructions' => 'Number of columns on desktop',
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

                    // Image Height (only for full_body)
                    [
                        'key' => 'field_tc_image_height',
                        'label' => 'Image Height (px)',
                        'name' => 'image_height',
                        'type' => 'number',
                        'instructions' => 'Height of the portrait image in pixels',
                        'required' => 0,
                        'default_value' => 400,
                        'min' => 200,
                        'max' => 800,
                        'step' => 50,
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_tc_layout_style',
                                    'operator' => '==',
                                    'value' => 'full_body',
                                ],
                            ],
                        ],
                    ],

                    // Posts to Display
                    [
                        'key' => 'field_tc_posts_to_display',
                        'label' => 'Team Members to Display',
                        'name' => 'posts_to_display',
                        'type' => 'number',
                        'instructions' => 'Maximum number of team members to show (1-20)',
                        'required' => 0,
                        'default_value' => 3,
                        'min' => 1,
                        'max' => 20,
                        'step' => 1,
                    ],

                    // Show Arrows
                    [
                        'key' => 'field_tc_show_arrows',
                        'label' => 'Show Navigation Arrows',
                        'name' => 'show_arrows',
                        'type' => 'true_false',
                        'instructions' => 'Display prev/next arrows',
                        'default_value' => 1,
                        'ui' => 1,
                    ],

                    // Show Dots
                    [
                        'key' => 'field_tc_show_dots',
                        'label' => 'Show Pagination Dots',
                        'name' => 'show_dots',
                        'type' => 'true_false',
                        'instructions' => 'Display pagination dots',
                        'default_value' => 1,
                        'ui' => 1,
                    ],

                    // Enable Autoplay
                    [
                        'key' => 'field_tc_enable_autoplay',
                        'label' => 'Enable Autoplay',
                        'name' => 'enable_autoplay',
                        'type' => 'true_false',
                        'instructions' => 'Automatically advance slides',
                        'default_value' => 0,
                        'ui' => 1,
                    ],

                    // Autoplay Delay
                    [
                        'key' => 'field_tc_autoplay_delay',
                        'label' => 'Autoplay Delay (ms)',
                        'name' => 'autoplay_delay',
                        'type' => 'number',
                        'instructions' => 'Delay between slides in milliseconds',
                        'required' => 0,
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_tc_enable_autoplay',
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

                    // Team Members Repeater
                    [
                        'key' => 'field_tc_team_members',
                        'label' => 'Team Members',
                        'name' => 'team_members',
                        'type' => 'repeater',
                        'instructions' => 'Add team members to display',
                        'required' => 0,
                        'layout' => 'block',
                        'button_label' => 'Add Team Member',
                        'sub_fields' => [
                            // Image
                            [
                                'key' => 'field_tc_member_image',
                                'label' => 'Photo',
                                'name' => 'image',
                                'type' => 'image',
                                'required' => 0,
                                'return_format' => 'array',
                                'preview_size' => 'medium',
                            ],

                            // Name
                            [
                                'key' => 'field_tc_member_name',
                                'label' => 'Name',
                                'name' => 'name',
                                'type' => 'text',
                                'required' => 0,
                                'default_value' => 'Team Member Name',
                                'placeholder' => 'John Doe',
                            ],

                            // Position (only for full_body)
                            [
                                'key' => 'field_tc_member_position',
                                'label' => 'Position/Title',
                                'name' => 'position',
                                'type' => 'text',
                                'instructions' => 'Only shown in Full Body layout',
                                'required' => 0,
                                'default_value' => 'Tour Guide',
                                'placeholder' => 'Tour Guide',
                                'conditional_logic' => [
                                    [
                                        [
                                            'field' => 'field_tc_layout_style',
                                            'operator' => '==',
                                            'value' => 'full_body',
                                        ],
                                    ],
                                ],
                            ],

                            // Description (only for profile_card)
                            [
                                'key' => 'field_tc_member_description',
                                'label' => 'Description',
                                'name' => 'description',
                                'type' => 'textarea',
                                'instructions' => 'Only shown in Profile Card layout',
                                'required' => 0,
                                'rows' => 3,
                                'default_value' => 'Expert travel guide with extensive experience in creating unforgettable journeys and cultural experiences.',
                                'placeholder' => 'Expert travel guide with 10+ years experience...',
                                'conditional_logic' => [
                                    [
                                        [
                                            'field' => 'field_tc_layout_style',
                                            'operator' => '==',
                                            'value' => 'profile_card',
                                        ],
                                    ],
                                ],
                            ],

                            // Achievements (only for profile_card)
                            [
                                'key' => 'field_tc_member_achievements',
                                'label' => 'Achievements/Awards',
                                'name' => 'achievements',
                                'type' => 'repeater',
                                'instructions' => 'List of achievements, awards, or highlights (small text)',
                                'required' => 0,
                                'layout' => 'table',
                                'button_label' => 'Add Achievement',
                                'conditional_logic' => [
                                    [
                                        [
                                            'field' => 'field_tc_layout_style',
                                            'operator' => '==',
                                            'value' => 'profile_card',
                                        ],
                                    ],
                                ],
                                'sub_fields' => [
                                    [
                                        'key' => 'field_tc_achievement_text',
                                        'label' => 'Achievement',
                                        'name' => 'achievement_text',
                                        'type' => 'text',
                                        'required' => 0,
                                        'placeholder' => '10+ years experience',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/team-carousel',
                        ],
                    ],
                ],
            ]);
        }
    }
}
