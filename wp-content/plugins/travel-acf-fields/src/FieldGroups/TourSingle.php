<?php
/**
 * Field Group: Tour Single
 *
 * ACF fields for individual tour posts: pricing, itinerary, details
 *
 * @package Aurora\ACFKit\FieldGroups
 * @since 1.0.0
 */

namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class TourSingle extends FieldGroup
{
    public function __construct()
    {
        $this->key   = 'group_tour_single';
        $this->title = __('Tour Details', 'travel');

        $this->fields = [
            // Tab: Pricing
            [
                'key' => 'field_tab_pricing',
                'label' => __('Pricing', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_tour_price',
                'label' => __('Regular Price', 'travel'),
                'name' => 'tour_price',
                'type' => 'number',
                'min' => 0,
                'step' => 0.01,
                'prepend' => '$',
                'wrapper' => ['width' => 33],
                'required' => 1,
            ],
            [
                'key' => 'field_tour_sale_price',
                'label' => __('Sale Price', 'travel'),
                'name' => 'tour_sale_price',
                'type' => 'number',
                'min' => 0,
                'step' => 0.01,
                'prepend' => '$',
                'instructions' => __('Leave empty if no discount', 'travel'),
                'wrapper' => ['width' => 33],
            ],
            [
                'key' => 'field_tour_currency',
                'label' => __('Currency', 'travel'),
                'name' => 'tour_currency',
                'type' => 'select',
                'choices' => [
                    'USD' => __('US Dollar (USD)', 'travel'),
                    'PEN' => __('Peruvian Sol (PEN)', 'travel'),
                    'EUR' => __('Euro (EUR)', 'travel'),
                ],
                'default_value' => 'USD',
                'wrapper' => ['width' => 34],
            ],

            // Tab: Tour Details
            [
                'key' => 'field_tab_details',
                'label' => __('Tour Details', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_tour_duration_days',
                'label' => __('Duration (Days)', 'travel'),
                'name' => 'tour_duration_days',
                'type' => 'number',
                'min' => 1,
                'step' => 1,
                'wrapper' => ['width' => 25],
            ],
            [
                'key' => 'field_tour_duration_nights',
                'label' => __('Duration (Nights)', 'travel'),
                'name' => 'tour_duration_nights',
                'type' => 'number',
                'min' => 0,
                'step' => 1,
                'wrapper' => ['width' => 25],
            ],
            [
                'key' => 'field_tour_min_age',
                'label' => __('Minimum Age', 'travel'),
                'name' => 'tour_min_age',
                'type' => 'number',
                'min' => 0,
                'step' => 1,
                'default_value' => 0,
                'wrapper' => ['width' => 25],
            ],
            [
                'key' => 'field_tour_max_group_size',
                'label' => __('Max Group Size', 'travel'),
                'name' => 'tour_max_group_size',
                'type' => 'number',
                'min' => 1,
                'step' => 1,
                'default_value' => 15,
                'wrapper' => ['width' => 25],
            ],
            [
                'key' => 'field_tour_meeting_point',
                'label' => __('Meeting Point', 'travel'),
                'name' => 'tour_meeting_point',
                'type' => 'textarea',
                'rows' => 2,
                'instructions' => __('Where does the tour start?', 'travel'),
            ],

            // Tab: Itinerary
            [
                'key' => 'field_tab_itinerary',
                'label' => __('Itinerary', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_tour_itinerary',
                'label' => __('Day-by-Day Itinerary', 'travel'),
                'name' => 'tour_itinerary',
                'type' => 'repeater',
                'button_label' => __('Add Day', 'travel'),
                'layout' => 'block',
                'sub_fields' => [
                    [
                        'key' => 'field_itinerary_day_title',
                        'label' => __('Day Title', 'travel'),
                        'name' => 'day_title',
                        'type' => 'text',
                        'placeholder' => __('e.g., Day 1: Arrival in Cusco', 'travel'),
                        'wrapper' => ['width' => 100],
                    ],
                    [
                        'key' => 'field_itinerary_day_description',
                        'label' => __('Description', 'travel'),
                        'name' => 'day_description',
                        'type' => 'wysiwyg',
                        'media_upload' => 0,
                        'toolbar' => 'basic',
                        'wrapper' => ['width' => 100],
                    ],
                    [
                        'key' => 'field_itinerary_day_meals',
                        'label' => __('Meals Included', 'travel'),
                        'name' => 'day_meals',
                        'type' => 'checkbox',
                        'choices' => [
                            'breakfast' => __('Breakfast', 'travel'),
                            'lunch' => __('Lunch', 'travel'),
                            'dinner' => __('Dinner', 'travel'),
                        ],
                        'layout' => 'horizontal',
                        'wrapper' => ['width' => 50],
                    ],
                    [
                        'key' => 'field_itinerary_day_accommodation',
                        'label' => __('Accommodation', 'travel'),
                        'name' => 'day_accommodation',
                        'type' => 'text',
                        'placeholder' => __('e.g., Hotel in Cusco', 'travel'),
                        'wrapper' => ['width' => 50],
                    ],
                ],
            ],

            // Tab: Inclusions & Exclusions
            [
                'key' => 'field_tab_inclusions',
                'label' => __('Inclusions & Exclusions', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_tour_inclusions',
                'label' => __('What\'s Included', 'travel'),
                'name' => 'tour_inclusions',
                'type' => 'repeater',
                'button_label' => __('Add Item', 'travel'),
                'layout' => 'table',
                'sub_fields' => [
                    [
                        'key' => 'field_inclusion_item',
                        'label' => __('Item', 'travel'),
                        'name' => 'item',
                        'type' => 'text',
                    ],
                ],
            ],
            [
                'key' => 'field_tour_exclusions',
                'label' => __('What\'s Not Included', 'travel'),
                'name' => 'tour_exclusions',
                'type' => 'repeater',
                'button_label' => __('Add Item', 'travel'),
                'layout' => 'table',
                'sub_fields' => [
                    [
                        'key' => 'field_exclusion_item',
                        'label' => __('Item', 'travel'),
                        'name' => 'item',
                        'type' => 'text',
                    ],
                ],
            ],

            // Tab: Gallery
            [
                'key' => 'field_tab_gallery',
                'label' => __('Gallery', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_tour_gallery',
                'label' => __('Tour Gallery', 'travel'),
                'name' => 'tour_gallery',
                'type' => 'gallery',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'min' => 3,
                'max' => 20,
                'instructions' => __('Upload 3-20 images showcasing this tour', 'travel'),
            ],

            // Tab: Additional Info
            [
                'key' => 'field_tab_additional',
                'label' => __('Additional Info', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_tour_important_info',
                'label' => __('Important Information', 'travel'),
                'name' => 'tour_important_info',
                'type' => 'wysiwyg',
                'media_upload' => 0,
                'toolbar' => 'basic',
                'instructions' => __('Health requirements, visa info, packing list, etc.', 'travel'),
            ],
            [
                'key' => 'field_tour_cancellation_policy',
                'label' => __('Cancellation Policy', 'travel'),
                'name' => 'tour_cancellation_policy',
                'type' => 'wysiwyg',
                'media_upload' => 0,
                'toolbar' => 'basic',
            ],
            [
                'key' => 'field_tour_featured',
                'label' => __('Featured Tour', 'travel'),
                'name' => 'tour_featured',
                'type' => 'true_false',
                'default_value' => 0,
                'ui' => 1,
                'instructions' => __('Mark as featured to highlight on homepage and listings', 'travel'),
            ],
        ];

        // Location: All tour posts
        $this->location = [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'tour',
                ],
            ],
        ];

        $this->settings = [
            'position' => 'acf_after_title',
            'style' => 'default',
            'active' => true,
            'show_in_rest' => 1,
            'label_placement' => 'top',
        ];
    }
}
