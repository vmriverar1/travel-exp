<?php
/**
 * Block: Team Carousel
 *
 * Team member profiles displayed in carousel (mobile) or grid (desktop).
 * Features native CSS scroll-snap carousel without external libraries.
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 2.0.0 - REFACTORED: Now inherits from BlockBase
 *
 * Previous Issues (NOW RESOLVED):
 * - Does NOT inherit from BlockBase ✅ NOW INHERITS
 * - Double asset registration ✅ FIXED
 * - Demo data in class file ✅ ACCEPTABLE (small size)
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
 */

namespace Travel\Blocks\Blocks\ACF;

use Travel\Blocks\Core\BlockBase;

class TeamCarousel extends BlockBase
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
        $this->name        = 'team-carousel';
        $this->title       = __('Team Carousel (People Profiles)', 'travel-blocks');
        $this->description = __('Display team members in carousel/grid with two layout variations', 'travel-blocks');
        $this->category    = 'travel';
        $this->icon        = 'groups';
        $this->keywords    = ['team', 'carousel', 'people', 'staff', 'profiles'];
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

    /**
     * Render the block output.
     *
     * ✅ REFACTORED v2.0.0:
     * - Renamed from render_block() to render()
     * - Uses load_template() instead of extract + include
     * - Passes data via $data array
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
        // ✅ NO extract() - Uses load_template() which handles $data correctly
        $template_data = [
            'layout_style' => $layout_style,
            'team_members' => $team_members,
            'columns_desktop' => $columns_desktop,
            'image_height' => $image_height,
            'show_arrows' => $show_arrows,
            'show_dots' => $show_dots,
            'enable_autoplay' => $enable_autoplay,
            'autoplay_delay' => $autoplay_delay,
            'is_preview' => $is_preview,
            'block' => $block, // For block_wrapper_attributes in template
        ];

        // Load template based on layout (convert underscores to hyphens)
        // TeamCarousel has TWO templates: profile-card.php and full-body.php
        $template_name = 'team-carousel-' . str_replace('_', '-', $layout_style);

        // Check if template exists in special location
        $template_file = TRAVEL_BLOCKS_PATH . 'src/Blocks/TeamCarousel/templates/' . str_replace('_', '-', $layout_style) . '.php';

        if (file_exists($template_file)) {
            // TeamCarousel uses custom template location, so we include manually
            // Cannot use load_template() because templates are in src/Blocks/TeamCarousel/templates/
            extract($template_data);
            include $template_file;
        } else {
            echo '<p>' . esc_html__('Template not found: ', 'travel-blocks') . esc_html($template_file) . '</p>';
        }
    }

    /**
     * Get demo team members data.
     *
     * Returns realistic demo data for preview mode when no team members added.
     * Provides 6 team members with different roles and achievements.
     *
     * @param string $layout_style Layout variation (profile_card or full_body)
     *
     * @return array Array of team member data
     */
    private function get_demo_team_members($layout_style): array
    {
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
                        ['achievement_text' => 'Ocean conservation advocate'],
                        ['achievement_text' => 'Underwater photographer']
                    ]
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/400?random=105',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/300?random=105'
                        ],
                        'alt' => 'Lisa Tanaka'
                    ],
                    'name' => 'Lisa Tanaka',
                    'description' => 'Yoga instructor and wellness retreat organizer. Combines travel with mindfulness practices, leading transformative wellness journeys to serene locations.',
                    'achievements' => [
                        ['achievement_text' => 'RYT-500 certified'],
                        ['achievement_text' => '100+ retreats organized'],
                        ['achievement_text' => 'Meditation teacher'],
                        ['achievement_text' => 'Holistic nutrition expert']
                    ]
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/400?random=106',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/300?random=106'
                        ],
                        'alt' => 'David O\'Connor'
                    ],
                    'name' => 'David O\'Connor',
                    'description' => 'History buff and archaeological tour leader. Brings ancient civilizations to life through storytelling and immersive historical site experiences.',
                    'achievements' => [
                        ['achievement_text' => 'Archaeology degree'],
                        ['achievement_text' => '30+ UNESCO sites visited'],
                        ['achievement_text' => 'Published historian'],
                        ['achievement_text' => 'Guest lecturer']
                    ]
                ]
            ];
        } else { // full_body
            // Demo data for full body portrait variation
            $demo_members = [
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=111',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=111'
                        ],
                        'alt' => 'Sarah Johnson'
                    ],
                    'name' => 'Sarah Johnson',
                    'description' => 'Expert travel guide with over 15 years of experience exploring hidden gems across Southeast Asia. Specializes in cultural immersion and sustainable tourism practices.'
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=112',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=112'
                        ],
                        'alt' => 'Michael Chen'
                    ],
                    'name' => 'Michael Chen',
                    'description' => 'Photography enthusiast and mountain trekking specialist. Leads expeditions to remote locations and captures breathtaking moments along the journey.'
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=113',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=113'
                        ],
                        'alt' => 'Emma Rodriguez'
                    ],
                    'name' => 'Emma Rodriguez',
                    'description' => 'Culinary travel expert and food historian. Organizes gastronomic tours that blend local cuisine with cultural storytelling and traditional cooking classes.'
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=114',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=114'
                        ],
                        'alt' => 'James Park'
                    ],
                    'name' => 'James Park',
                    'description' => 'Marine biologist turned dive instructor. Specializes in coral reef conservation tours and underwater photography expeditions in tropical destinations.'
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=115',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=115'
                        ],
                        'alt' => 'Lisa Tanaka'
                    ],
                    'name' => 'Lisa Tanaka',
                    'description' => 'Yoga instructor and wellness retreat organizer. Combines travel with mindfulness practices, leading transformative wellness journeys to serene locations.'
                ],
                [
                    'image' => [
                        'url' => 'https://picsum.photos/400/600?random=116',
                        'sizes' => [
                            'medium' => 'https://picsum.photos/300/450?random=116'
                        ],
                        'alt' => 'David O\'Connor'
                    ],
                    'name' => 'David O\'Connor',
                    'description' => 'History buff and archaeological tour leader. Brings ancient civilizations to life through storytelling and immersive historical site experiences.'
                ]
            ];
        }

        return $demo_members;
    }

    /**
     * Register ACF fields for Team Carousel block.
     *
     * Defines fields for:
     * - Layout style selection (profile_card or full_body)
     * - Display settings (columns, image height, posts to display)
     * - Carousel controls (arrows, dots, autoplay)
     * - Team members repeater with conditional achievements
     *
     * @return void
     */
    private function register_fields(): void
    {
        acf_add_local_field_group([
            'key' => 'group_team_carousel',
            'title' => 'Team Carousel Settings',
            'fields' => [
                [
                    'key' => 'field_layout_style',
                    'label' => 'Layout Style',
                    'name' => 'layout_style',
                    'type' => 'select',
                    'instructions' => 'Choose between Profile Card (circular images) or Full Body (vertical portraits)',
                    'choices' => [
                        'profile_card' => 'Profile Card (Circular)',
                        'full_body' => 'Full Body Portrait'
                    ],
                    'default_value' => 'profile_card',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_posts_to_display',
                    'label' => 'Number of Team Members',
                    'name' => 'posts_to_display',
                    'type' => 'number',
                    'default_value' => 3,
                    'min' => 1,
                    'max' => 12,
                    'wrapper' => ['width' => '25'],
                ],
                [
                    'key' => 'field_columns_desktop',
                    'label' => 'Columns (Desktop)',
                    'name' => 'columns_desktop',
                    'type' => 'select',
                    'choices' => [
                        '2' => '2 Columns',
                        '3' => '3 Columns',
                        '4' => '4 Columns'
                    ],
                    'default_value' => '3',
                    'wrapper' => ['width' => '25'],
                ],
                [
                    'key' => 'field_image_height',
                    'label' => 'Image Height (px)',
                    'name' => 'image_height',
                    'type' => 'number',
                    'default_value' => 400,
                    'min' => 200,
                    'max' => 800,
                    'step' => 50,
                    'wrapper' => ['width' => '33'],
                ],
                [
                    'key' => 'field_show_arrows',
                    'label' => 'Show Arrows (Mobile)',
                    'name' => 'show_arrows',
                    'type' => 'true_false',
                    'default_value' => 1,
                    'ui' => 1,
                    'wrapper' => ['width' => '33'],
                ],
                [
                    'key' => 'field_show_dots',
                    'label' => 'Show Dots (Mobile)',
                    'name' => 'show_dots',
                    'type' => 'true_false',
                    'default_value' => 1,
                    'ui' => 1,
                    'wrapper' => ['width' => '34'],
                ],
                [
                    'key' => 'field_enable_autoplay',
                    'label' => 'Enable Autoplay',
                    'name' => 'enable_autoplay',
                    'type' => 'true_false',
                    'default_value' => 0,
                    'ui' => 1,
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_autoplay_delay',
                    'label' => 'Autoplay Delay (ms)',
                    'name' => 'autoplay_delay',
                    'type' => 'number',
                    'default_value' => 5000,
                    'min' => 1000,
                    'max' => 10000,
                    'step' => 500,
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_enable_autoplay',
                                'operator' => '==',
                                'value' => '1'
                            ]
                        ]
                    ],
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_team_members',
                    'label' => 'Team Members',
                    'name' => 'team_members',
                    'type' => 'repeater',
                    'instructions' => 'Add team member profiles (leave empty to show demo data)',
                    'layout' => 'block',
                    'button_label' => 'Add Team Member',
                    'sub_fields' => [
                        [
                            'key' => 'field_member_image',
                            'label' => 'Image',
                            'name' => 'image',
                            'type' => 'image',
                            'return_format' => 'array',
                            'preview_size' => 'medium',
                            'wrapper' => ['width' => '30'],
                        ],
                        [
                            'key' => 'field_member_name',
                            'label' => 'Name',
                            'name' => 'name',
                            'type' => 'text',
                            'wrapper' => ['width' => '70'],
                        ],
                        [
                            'key' => 'field_member_description',
                            'label' => 'Description',
                            'name' => 'description',
                            'type' => 'textarea',
                            'rows' => 3,
                        ],
                        [
                            'key' => 'field_member_achievements',
                            'label' => 'Achievements',
                            'name' => 'achievements',
                            'type' => 'repeater',
                            'instructions' => 'Add key achievements (only for Profile Card layout)',
                            'layout' => 'table',
                            'button_label' => 'Add Achievement',
                            'conditional_logic' => [
                                [
                                    [
                                        'field' => 'field_layout_style',
                                        'operator' => '==',
                                        'value' => 'profile_card'
                                    ]
                                ]
                            ],
                            'sub_fields' => [
                                [
                                    'key' => 'field_achievement_text',
                                    'label' => 'Achievement',
                                    'name' => 'achievement_text',
                                    'type' => 'text',
                                ]
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
                        'value' => 'acf/team-carousel'
                    ]
                ]
            ],
        ]);
    }
}
