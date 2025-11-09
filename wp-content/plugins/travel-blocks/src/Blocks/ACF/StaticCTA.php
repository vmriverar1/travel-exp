<?php
/**
 * Block: Static CTA
 *
 * Call-to-action block with background image, title, subtitle, and button(s).
 *
 * @package Travel\Blocks\Blocks
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\ACF;

use Travel\Blocks\Core\BlockBase;

class StaticCTA extends BlockBase
{
    public function __construct()
    {
        $this->name        = 'static-cta';
        $this->title       = __('Static CTA', 'travel-blocks');
        $this->description = __('Call-to-action section with background and buttons', 'travel-blocks');
        $this->category    = 'travel';
        $this->icon        = 'megaphone';
        $this->keywords    = ['cta', 'call to action', 'banner', 'promo'];
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
     * @return void
     */
    public function register(): void
    {
        parent::register();

        // Register ACF fields for this block
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_block_static_cta',
                'title' => __('Static CTA Block', 'travel-blocks'),
                'fields' => [
                    [
                        'key' => 'field_cta_title',
                        'label' => __('Title', 'travel-blocks'),
                        'name' => 'title',
                        'type' => 'text',
                        'required' => 1,
                        'default_value' => __('Ready to Start Your Adventure?', 'travel-blocks'),
                    ],
                    [
                        'key' => 'field_cta_subtitle',
                        'label' => __('Subtitle', 'travel-blocks'),
                        'name' => 'subtitle',
                        'type' => 'textarea',
                        'rows' => 2,
                        'default_value' => __('Book your dream Peru tour today and create memories that will last a lifetime.', 'travel-blocks'),
                    ],
                    [
                        'key' => 'field_cta_background_type',
                        'label' => __('Background Type', 'travel-blocks'),
                        'name' => 'background_type',
                        'type' => 'radio',
                        'choices' => [
                            'image' => __('Image', 'travel-blocks'),
                            'color' => __('Solid Color', 'travel-blocks'),
                            'gradient' => __('Gradient', 'travel-blocks'),
                        ],
                        'default_value' => 'image',
                        'layout' => 'horizontal',
                    ],
                    [
                        'key' => 'field_cta_background_image',
                        'label' => __('Background Image', 'travel-blocks'),
                        'name' => 'background_image',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'large',
                        'library' => 'all',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_cta_background_type',
                                    'operator' => '==',
                                    'value' => 'image',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_cta_background_color',
                        'label' => __('Background Color', 'travel-blocks'),
                        'name' => 'background_color',
                        'type' => 'color_picker',
                        'default_value' => '#e74c3c',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_cta_background_type',
                                    'operator' => '==',
                                    'value' => 'color',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_cta_overlay_opacity',
                        'label' => __('Overlay Opacity', 'travel-blocks'),
                        'name' => 'overlay_opacity',
                        'type' => 'range',
                        'min' => 0,
                        'max' => 100,
                        'step' => 10,
                        'default_value' => 50,
                        'append' => '%',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_cta_background_type',
                                    'operator' => '==',
                                    'value' => 'image',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_cta_buttons',
                        'label' => __('Buttons', 'travel-blocks'),
                        'name' => 'buttons',
                        'type' => 'repeater',
                        'min' => 1,
                        'max' => 2,
                        'layout' => 'block',
                        'button_label' => __('Add Button', 'travel-blocks'),
                        'sub_fields' => [
                            [
                                'key' => 'field_cta_button_text',
                                'label' => __('Button Text', 'travel-blocks'),
                                'name' => 'text',
                                'type' => 'text',
                                'required' => 1,
                            ],
                            [
                                'key' => 'field_cta_button_url',
                                'label' => __('Button URL', 'travel-blocks'),
                                'name' => 'url',
                                'type' => 'url',
                                'required' => 1,
                            ],
                            [
                                'key' => 'field_cta_button_style',
                                'label' => __('Button Style', 'travel-blocks'),
                                'name' => 'style',
                                'type' => 'select',
                                'choices' => [
                                    'primary' => __('Primary', 'travel-blocks'),
                                    'secondary' => __('Secondary', 'travel-blocks'),
                                    'outline' => __('Outline', 'travel-blocks'),
                                ],
                                'default_value' => 'primary',
                            ],
                        ],
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/static-cta',
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * Render the block output.
     *
     * @param array  $block      Block settings
     * @param string $content    Block content
     * @param bool   $is_preview Whether in preview mode
     * @param int    $post_id    Current post ID
     *
     * @return void
     */
    public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        // Get field values
        $title = get_field('title');
        $subtitle = get_field('subtitle');
        $background_type = get_field('background_type') ?: 'image';
        $background_image = get_field('background_image');
        $background_color = get_field('background_color');
        $overlay_opacity = get_field('overlay_opacity') ?: 50;
        $buttons = get_field('buttons') ?: [];

        // Prepare template data
        $data = [
            'block'            => $block,
            'is_preview'       => $is_preview,
            'title'            => $title,
            'subtitle'         => $subtitle,
            'background_type'  => $background_type,
            'background_image' => $background_image,
            'background_color' => $background_color,
            'overlay_opacity'  => $overlay_opacity,
            'buttons'          => $buttons,
        ];

        // Load template
        $this->load_template('static-cta', $data);
    }

    /**
     * Enqueue block-specific assets.
     *
     * @return void
     */
    public function enqueue_assets(): void
    {
        wp_enqueue_style(
            'block-static-cta',
            TRAVEL_BLOCKS_URL . 'assets/blocks/static-cta.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );
    }
}
