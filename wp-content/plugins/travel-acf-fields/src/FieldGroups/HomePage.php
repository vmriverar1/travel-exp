<?php
/**
 * Field Group: Home Page
 *
 * ACF fields for the homepage: hero, featured tours, dynamic sections
 *
 * @package Aurora\ACFKit\FieldGroups
 * @since 1.0.0
 */

namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class HomePage extends FieldGroup
{
    public function __construct()
    {
        $this->key   = 'group_home_page';
        $this->title = __('Home Page Settings', 'travel');

        $this->fields = [
            // Tab: Hero Section
            [
                'key' => 'field_tab_hero',
                'label' => __('Hero Section', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_hero_image',
                'label' => __('Hero Background Image', 'travel'),
                'name' => 'hero_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'large',
                'library' => 'all',
                'instructions' => __('Recommended size: 1920x800px', 'travel'),
            ],
            [
                'key' => 'field_hero_title',
                'label' => __('Hero Title', 'travel'),
                'name' => 'hero_title',
                'type' => 'text',
                'default_value' => __('Discover the Magic of Peru', 'travel'),
                'wrapper' => ['width' => 100],
            ],
            [
                'key' => 'field_hero_subtitle',
                'label' => __('Hero Subtitle', 'travel'),
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => __('Unforgettable tours to Machu Picchu and beyond', 'travel'),
                'wrapper' => ['width' => 100],
            ],
            [
                'key' => 'field_hero_cta_text',
                'label' => __('CTA Button Text', 'travel'),
                'name' => 'hero_cta_text',
                'type' => 'text',
                'default_value' => __('Explore Tours', 'travel'),
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_hero_cta_url',
                'label' => __('CTA Button URL', 'travel'),
                'name' => 'hero_cta_url',
                'type' => 'url',
                'wrapper' => ['width' => 50],
            ],

            // Tab: Featured Tours
            [
                'key' => 'field_tab_featured',
                'label' => __('Featured Tours', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_featured_title',
                'label' => __('Section Title', 'travel'),
                'name' => 'featured_title',
                'type' => 'text',
                'default_value' => __('Top Experiences', 'travel'),
            ],
            [
                'key' => 'field_featured_tours',
                'label' => __('Featured Tours', 'travel'),
                'name' => 'featured_tours',
                'type' => 'relationship',
                'post_type' => ['tour'],
                'filters' => ['search', 'taxonomy'],
                'return_format' => 'object',
                'min' => 3,
                'max' => 6,
                'instructions' => __('Select 3-6 tours to highlight on the homepage', 'travel'),
            ],

            // Tab: Dynamic Sections
            [
                'key' => 'field_tab_sections',
                'label' => __('Dynamic Sections', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_home_sections',
                'label' => __('Page Sections', 'travel'),
                'name' => 'home_sections',
                'type' => 'flexible_content',
                'button_label' => __('Add Section', 'travel'),
                'layouts' => [
                    // Layout: Text & Image
                    'text_image' => [
                        'key' => 'layout_text_image',
                        'name' => 'text_image',
                        'label' => __('Text & Image', 'travel'),
                        'display' => 'block',
                        'sub_fields' => [
                            [
                                'key' => 'field_ti_title',
                                'label' => __('Title', 'travel'),
                                'name' => 'title',
                                'type' => 'text',
                            ],
                            [
                                'key' => 'field_ti_content',
                                'label' => __('Content', 'travel'),
                                'name' => 'content',
                                'type' => 'wysiwyg',
                                'media_upload' => 0,
                                'toolbar' => 'basic',
                            ],
                            [
                                'key' => 'field_ti_image',
                                'label' => __('Image', 'travel'),
                                'name' => 'image',
                                'type' => 'image',
                                'return_format' => 'array',
                                'preview_size' => 'medium',
                            ],
                            [
                                'key' => 'field_ti_layout',
                                'label' => __('Layout', 'travel'),
                                'name' => 'layout',
                                'type' => 'select',
                                'choices' => [
                                    'image-left' => __('Image Left', 'travel'),
                                    'image-right' => __('Image Right', 'travel'),
                                ],
                                'default_value' => 'image-right',
                            ],
                        ],
                    ],

                    // Layout: Testimonials
                    'testimonials' => [
                        'key' => 'layout_testimonials',
                        'name' => 'testimonials',
                        'label' => __('Testimonials', 'travel'),
                        'display' => 'block',
                        'sub_fields' => [
                            [
                                'key' => 'field_test_title',
                                'label' => __('Section Title', 'travel'),
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => __('What Our Travelers Say', 'travel'),
                            ],
                            [
                                'key' => 'field_test_reviews',
                                'label' => __('Select Reviews', 'travel'),
                                'name' => 'reviews',
                                'type' => 'relationship',
                                'post_type' => ['review'],
                                'filters' => ['search'],
                                'return_format' => 'object',
                                'min' => 3,
                                'max' => 6,
                            ],
                        ],
                    ],

                    // Layout: Destinations Grid
                    'destinations' => [
                        'key' => 'layout_destinations',
                        'name' => 'destinations',
                        'label' => __('Destinations Grid', 'travel'),
                        'display' => 'block',
                        'sub_fields' => [
                            [
                                'key' => 'field_dest_title',
                                'label' => __('Section Title', 'travel'),
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => __('Explore Peru', 'travel'),
                            ],
                            [
                                'key' => 'field_dest_items',
                                'label' => __('Select Locations', 'travel'),
                                'name' => 'destinations',
                                'type' => 'relationship',
                                'post_type' => ['location'],
                                'filters' => ['search', 'taxonomy'],
                                'return_format' => 'object',
                                'min' => 3,
                                'max' => 8,
                            ],
                        ],
                    ],

                    // Layout: CTA Banner
                    'cta_banner' => [
                        'key' => 'layout_cta_banner',
                        'name' => 'cta_banner',
                        'label' => __('CTA Banner', 'travel'),
                        'display' => 'block',
                        'sub_fields' => [
                            [
                                'key' => 'field_cta_bg',
                                'label' => __('Background Image', 'travel'),
                                'name' => 'background_image',
                                'type' => 'image',
                                'return_format' => 'array',
                                'preview_size' => 'large',
                            ],
                            [
                                'key' => 'field_cta_title',
                                'label' => __('Title', 'travel'),
                                'name' => 'title',
                                'type' => 'text',
                            ],
                            [
                                'key' => 'field_cta_subtitle',
                                'label' => __('Subtitle', 'travel'),
                                'name' => 'subtitle',
                                'type' => 'textarea',
                                'rows' => 2,
                            ],
                            [
                                'key' => 'field_cta_button_text',
                                'label' => __('Button Text', 'travel'),
                                'name' => 'button_text',
                                'type' => 'text',
                            ],
                            [
                                'key' => 'field_cta_button_url',
                                'label' => __('Button URL', 'travel'),
                                'name' => 'button_url',
                                'type' => 'url',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Get the homepage
        $homepage_id = get_option('page_on_front');

        // Location: Only on the homepage
        $this->location = [
            [
                [
                    'param' => 'page_type',
                    'operator' => '==',
                    'value' => 'front_page',
                ],
            ],
        ];

        // If no homepage is set, fallback to page ID
        if ($homepage_id) {
            $this->location[] = [
                [
                    'param' => 'page',
                    'operator' => '==',
                    'value' => $homepage_id,
                ],
            ];
        }

        $this->settings = [
            'position' => 'acf_after_title',
            'style' => 'default',
            'active' => true,
            'show_in_rest' => 1,
        ];
    }
}
