<?php
/**
 * ACF Field Group: Deal
 *
 * Campos para el Custom Post Type Deal (Ofertas)
 *
 * @package Travel\ACFFields\FieldGroups
 * @since 1.0.0
 */

namespace Travel\ACFFields\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class Deal extends FieldGroup
{
    /**
     * Register Deal field group
     */
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_deal',
            'title' => '🏷️ Deal Settings',
            'fields' => $this->get_fields(),
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'deal',
                    ],
                ],
            ],
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 1,
        ]);

        // Filter packages relationship to show only packages with promo_enabled
        add_filter('acf/fields/relationship/query/key=field_deal_packages', [$this, 'filter_packages_with_promo'], 10, 3);
    }

    /**
     * Filter packages relationship to show only packages with promo_enabled = 1
     */
    public function filter_packages_with_promo($args, $field, $post_id)
    {
        // Add meta query to filter by promo_enabled
        $args['meta_query'] = [
            [
                'key' => 'promo_enabled',
                'value' => '1',
                'compare' => '='
            ]
        ];

        return $args;
    }

    /**
     * Get field definitions
     */
    private function get_fields(): array
    {
        return [
            // Active checkbox
            [
                'key' => 'field_deal_active',
                'label' => __('Active', 'travel-acf-fields'),
                'name' => 'active',
                'type' => 'true_false',
                'instructions' => __('Mark this deal as active. Deal will only show if active AND current date is within start/end dates.', 'travel-acf-fields'),
                'required' => 0,
                'default_value' => 1,
                'ui' => 1,
                'ui_on_text' => __('Yes', 'travel-acf-fields'),
                'ui_off_text' => __('No', 'travel-acf-fields'),
            ],

            // Start date
            [
                'key' => 'field_deal_start_date',
                'label' => __('Start Date', 'travel-acf-fields'),
                'name' => 'start_date',
                'type' => 'date_time_picker',
                'instructions' => __('When does this deal start being available?', 'travel-acf-fields'),
                'required' => 1,
                'display_format' => 'F j, Y g:i a',
                'return_format' => 'Y-m-d H:i:s',
                'first_day' => 1,
            ],

            // End date
            [
                'key' => 'field_deal_end_date',
                'label' => __('End Date', 'travel-acf-fields'),
                'name' => 'end_date',
                'type' => 'date_time_picker',
                'instructions' => __('When does this deal expire? Must be after start date.', 'travel-acf-fields'),
                'required' => 1,
                'display_format' => 'F j, Y g:i a',
                'return_format' => 'Y-m-d H:i:s',
                'first_day' => 1,
            ],

            // Banner image
            [
                'key' => 'field_deal_banner',
                'label' => __('Banner Image', 'travel-acf-fields'),
                'name' => 'banner',
                'type' => 'image',
                'instructions' => __('Main banner image for this deal. Recommended size: 1920x1080px (16:9 ratio)', 'travel-acf-fields'),
                'required' => 0,
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
            ],

            // Discount percentage (optional)
            [
                'key' => 'field_deal_discount_percentage',
                'label' => __('Discount Percentage', 'travel-acf-fields'),
                'name' => 'discount_percentage',
                'type' => 'number',
                'instructions' => __('Discount percentage (e.g., 15 for 15% off). Used for display purposes.', 'travel-acf-fields'),
                'required' => 0,
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'append' => '%',
            ],

            // Packages relationship - ONLY shows packages with active_promotion = 1
            [
                'key' => 'field_deal_packages',
                'label' => __('Packages', 'travel-acf-fields'),
                'name' => 'packages',
                'type' => 'relationship',
                'instructions' => __('Select packages included in this deal. Only packages with active promotions are shown.', 'travel-acf-fields'),
                'required' => 0,
                'post_type' => ['package'],
                'filters' => ['search'],
                'elements' => ['featured_image'],
                'min' => 0,
                'max' => '',
                'return_format' => 'id',
            ],

            // Deal description
            [
                'key' => 'field_deal_description',
                'label' => __('Deal Description', 'travel-acf-fields'),
                'name' => 'description',
                'type' => 'wysiwyg',
                'instructions' => __('Describe the deal, terms and conditions, what makes it special, etc.', 'travel-acf-fields'),
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
            ],

            // Deal Terms & Conditions
            [
                'key' => 'field_deal_terms',
                'label' => __('Terms & Conditions', 'travel-acf-fields'),
                'name' => 'terms',
                'type' => 'textarea',
                'instructions' => __('Legal terms, restrictions, blackout dates, etc.', 'travel-acf-fields'),
                'required' => 0,
                'rows' => 4,
            ],
        ];
    }
}
