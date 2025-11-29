<?php

/**

 * Block: Hero Section

 *

 * Full-width hero banner with background image, overlay, and call-to-action.

 * Simple and clean implementation with proper BlockBase inheritance.

 *

 * Features:

 * - Background image with configurable overlay opacity

 * - Title and subtitle with sensible defaults

 * - Optional CTA button

 * - Configurable height (small/medium/large/full)

 * - Full-width and wide alignment support

 *

 * @package Travel\Blocks\ACF

 * @since 1.0.0

 * @version 1.1.0 - Refactored: namespace fix, added validation, improved error handling

 */



namespace Travel\Blocks\Blocks\ACF;



use Travel\Blocks\Core\BlockBase;



class HeroSection extends BlockBase

{

    public function __construct()

    {

        $this->name        = 'hero-section';

        $this->title       = __('Hero Section', 'travel-blocks');

        $this->description = __('Full-width hero banner with background image and call-to-action', 'travel-blocks');

        $this->category    = 'travel';

        $this->icon        = 'cover-image';

        $this->keywords    = ['hero', 'banner', 'header', 'cta'];

        $this->mode        = 'preview';



        $this->supports = [

            'align' => ['full', 'wide'],

            'mode'  => true,

            'multiple' => true,

        ];

    }



    /**

     * Register block and its ACF fields.

     *

     * Registers ACF block type and defines field group with:

     * - background_image: Required background image (recommended 1920x800px)

     * - overlay_opacity: Dark overlay for text readability (0-100%, default 40%)

     * - title: Required hero title

     * - subtitle: Optional hero subtitle

     * - cta_text: CTA button text

     * - cta_url: CTA button URL

     * - height: Hero height (small/medium/large/full)

     *

     * @return void

     */

    public function register(): void

    {

        parent::register();



        // Register ACF fields for this block

        if (function_exists('acf_add_local_field_group')) {

            acf_add_local_field_group([

                'key' => 'group_block_hero_section',

                'title' => __('Hero Section Block', 'travel-blocks'),

                'fields' => [

                    [

                        'key' => 'field_hero_bg_image',

                        'label' => __('Background Image', 'travel-blocks'),

                        'name' => 'background_image',

                        'type' => 'image',

                        'return_format' => 'array',

                        'preview_size' => 'large',

                        'library' => 'all',

                        'instructions' => __('Recommended size: 1920x800px', 'travel-blocks'),

                        'required' => 1,

                    ],

                    [

                        'key' => 'field_hero_overlay',

                        'label' => __('Overlay Opacity', 'travel-blocks'),

                        'name' => 'overlay_opacity',

                        'type' => 'range',

                        'min' => 0,

                        'max' => 100,

                        'step' => 10,

                        'default_value' => 40,

                        'append' => '%',

                        'instructions' => __('Dark overlay to improve text readability', 'travel-blocks'),

                    ],

                    [

                        'key' => 'field_hero_title',

                        'label' => __('Title', 'travel-blocks'),

                        'name' => 'title',

                        'type' => 'text',

                        'required' => 1,

                        'default_value' => __('Discover the Magic of Peru', 'travel-blocks'),

                    ],

                    [

                        'key' => 'field_hero_subtitle',

                        'label' => __('Subtitle', 'travel-blocks'),

                        'name' => 'subtitle',

                        'type' => 'textarea',

                        'rows' => 2,

                        'default_value' => __('Unforgettable tours to Machu Picchu and beyond', 'travel-blocks'),

                    ],

                    [

                        'key' => 'field_hero_cta_text',

                        'label' => __('Button Text', 'travel-blocks'),

                        'name' => 'cta_text',

                        'type' => 'text',

                        'default_value' => __('Explore Tours', 'travel-blocks'),

                    ],

                    [

                        'key' => 'field_hero_cta_url',

                        'label' => __('Button URL', 'travel-blocks'),

                        'name' => 'cta_url',

                        'type' => 'url',

                    ],

                    [

                        'key' => 'field_hero_height',

                        'label' => __('Hero Height', 'travel-blocks'),

                        'name' => 'height',

                        'type' => 'select',

                        'choices' => [

                            'small' => __('Small (400px)', 'travel-blocks'),

                            'medium' => __('Medium (600px)', 'travel-blocks'),

                            'large' => __('Large (800px)', 'travel-blocks'),

                            'full' => __('Full Screen', 'travel-blocks'),

                        ],

                        'default_value' => 'large',

                    ],

                ],

                'location' => [

                    [

                        [

                            'param' => 'block',

                            'operator' => '==',

                            'value' => 'acf/hero-section',

                        ],

                    ],

                ],

            ]);

        }

    }



    /**

     * Render the block output.

     *

     * Generates full-width hero banner with:

     * - Background image with configurable overlay

     * - Centered title and subtitle

     * - Optional CTA button

     * - Variable height options

     *

     * Validates required fields (background_image, title) before rendering.

     * Falls back to error message if critical fields are missing.

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

        try {

            // Get field values

            $background_image = get_field('background_image');

            $title = get_field('title');



            // Validate required fields

            if (!$background_image || !$title) {

                if ($is_preview && (defined('WP_DEBUG') && WP_DEBUG)) {

                    echo '<div style="padding: 20px; background: #fff3cd; border: 2px solid #856404; border-radius: 4px;">';

                    echo '<h3 style="margin: 0 0 10px; color: #856404;">Hero Section: Missing Required Fields</h3>';

                    echo '<p style="margin: 0;">Please configure both Background Image and Title fields.</p>';

                    echo '</div>';

                }

                return;

            }



            $overlay_opacity = get_field('overlay_opacity') ?: 40;

            $subtitle = get_field('subtitle');

            $cta_text = get_field('cta_text');

            $cta_url = get_field('cta_url');

            $height = get_field('height') ?: 'large';



            // Prepare template data

            $data = [

                'block'            => $block,

                'is_preview'       => $is_preview,

                'background_image' => $background_image,

                'overlay_opacity'  => $overlay_opacity,

                'title'            => $title,

                'subtitle'         => $subtitle,

                'cta_text'         => $cta_text,

                'cta_url'          => $cta_url,

                'height'           => $height,

            ];



            // Load template

            $this->load_template('hero-section', $data);



        } catch (\Exception $e) {

            // Error handling

            if (defined('WP_DEBUG') && WP_DEBUG) {

                echo '<div style="padding: 20px; background: #ffebee; border: 2px solid #f44336; border-radius: 4px;">';

                echo '<h3 style="margin: 0 0 10px; color: #c62828;">Error in Hero Section</h3>';

                echo '<p style="margin: 0; font-family: monospace; font-size: 13px;">' . esc_html($e->getMessage()) . '</p>';

                echo '</div>';

            }

        }

    }



    /**

     * Enqueue block-specific assets.

     *

     * Loads CSS for hero section styling including:

     * - Background image and overlay styles

     * - Height variants (small/medium/large/full)

     * - Responsive behavior

     * - CTA button styles

     *

     * @return void

     */

    public function enqueue_assets(): void

    {

        wp_enqueue_style(

            'block-hero-section',

            TRAVEL_BLOCKS_URL . 'assets/blocks/hero-section.css',

            [],

            TRAVEL_BLOCKS_VERSION

        );

    }

}

