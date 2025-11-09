<?php
namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class PackagePricing extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_package_pricing',
            'title' => '💰 Package - Pricing',
            'fields' => [

                // ===== PROMOTION =====
                [
                    'key' => 'field_package_promo_enabled',
                    'label' => '🏷️ Active Promotion',
                    'name' => 'promo_enabled',
                    'type' => 'true_false',
                    'instructions' => 'Activate special promotion for this package.',
                    'default_value' => 0,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_package_promo_tag',
                    'label' => '🏷️ Promotion Text',
                    'name' => 'promo_tag',
                    'type' => 'text',
                    'instructions' => 'Promotional tag text. Example: "Top Seller", "50% OFF", "New!"',
                    'required' => 0,
                    'maxlength' => 20,
                    'placeholder' => 'Top Seller',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_package_promo_enabled',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_package_promo_tag_color',
                    'label' => '🎨 Promotion Color',
                    'name' => 'promo_tag_color',
                    'type' => 'color_picker',
                    'instructions' => 'Background color of the promotional tag.',
                    'required' => 0,
                    'default_value' => '#E78C85',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_package_promo_enabled',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],

                // ===== BASE PRICES =====
                [
                    'key' => 'field_package_price_from',
                    'label' => '💵 Price From (USD)',
                    'name' => 'price_from',
                    'type' => 'number',
                    'instructions' => 'Base price "From $X". This is the lowest reference price.',
                    'required' => 0,
                    'min' => 0,
                    'step' => 1,
                    'prepend' => '$',
                ],
                [
                    'key' => 'field_package_price_normal',
                    'label' => '💵 Normal Price (USD)',
                    'name' => 'price_normal',
                    'type' => 'number',
                    'instructions' => 'Standard price without discount.',
                    'required' => 0,
                    'min' => 0,
                    'step' => 1,
                    'prepend' => '$',
                ],
                [
                    'key' => 'field_package_price_offer',
                    'label' => '🎁 Offer Price (USD)',
                    'name' => 'price_offer',
                    'type' => 'number',
                    'instructions' => 'Discounted price. If defined, normal price will be shown crossed out and this as current.',
                    'required' => 0,
                    'min' => 0,
                    'step' => 1,
                    'prepend' => '$',
                ],
                [
                    'key' => 'field_package_price_per_person',
                    'label' => '👤 Price per Person',
                    'name' => 'price_per_person',
                    'type' => 'true_false',
                    'instructions' => 'Check if the price is per person (instead of per group).',
                    'default_value' => 1,
                    'ui' => 1,
                ],

                // ===== ADDITIONAL PRICES =====
                [
                    'key' => 'field_package_price_single_supplement',
                    'label' => '🛏️ Single Supplement (USD)',
                    'name' => 'price_single_supplement',
                    'type' => 'number',
                    'instructions' => 'Additional charge for single occupancy.',
                    'required' => 0,
                    'min' => 0,
                    'step' => 1,
                    'prepend' => '$',
                ],
                [
                    'key' => 'field_package_price_child',
                    'label' => '👶 Child Price (USD)',
                    'name' => 'price_child',
                    'type' => 'number',
                    'instructions' => 'Special price for children/minors.',
                    'required' => 0,
                    'min' => 0,
                    'step' => 1,
                    'prepend' => '$',
                ],

                // ===== GROUP PRICES (DYNAMIC TABLE) =====
                [
                    'key' => 'field_package_price_tiers',
                    'label' => '📊 Tiered Group Pricing',
                    'name' => 'price_tiers',
                    'type' => 'repeater',
                    'instructions' => 'Define prices based on minimum number of passengers.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 20,
                    'layout' => 'table',
                    'button_label' => 'Add Price',
                    'sub_fields' => [
                        [
                            'key' => 'field_tier_min_passengers',
                            'label' => 'Min. Passengers',
                            'name' => 'min_passengers',
                            'type' => 'number',
                            'required' => 0,
                            'min' => 1,
                            'wrapper' => ['width' => 25],
                        ],
                        [
                            'key' => 'field_tier_price',
                            'label' => 'Price (USD)',
                            'name' => 'price',
                            'type' => 'number',
                            'required' => 0,
                            'min' => 0,
                            'prepend' => '$',
                            'wrapper' => ['width' => 35],
                        ],
                        [
                            'key' => 'field_tier_offer',
                            'label' => 'Offer (USD)',
                            'name' => 'offer',
                            'type' => 'number',
                            'required' => 0,
                            'min' => 0,
                            'prepend' => '$',
                            'wrapper' => ['width' => 35],
                        ],
                    ],
                ],

                // ===== AVAILABILITY & SPOTS =====
                [
                    'key' => 'field_package_default_spots',
                    'label' => '👥 Default Available Spots',
                    'name' => 'default_spots',
                    'type' => 'number',
                    'instructions' => 'Default number of available spots/vacancies per departure. Can be overridden for specific dates below.',
                    'required' => 0,
                    'default_value' => 12,
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'wrapper' => ['width' => 50],
                ],

                // ===== FIXED DEPARTURES (SPECIFIC DATES WITH CUSTOM PRICING/SPOTS) =====
                [
                    'key' => 'field_package_departure_exceptions',
                    'label' => '📅 Fixed Departures',
                    'name' => 'departure_exceptions',
                    'type' => 'repeater',
                    'instructions' => 'Add specific departure dates with custom pricing and availability. These dates will override automatic schedules if they match, or add new departure dates not in the regular calendar.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 100,
                    'layout' => 'table',
                    'button_label' => 'Add Fixed Departure',
                    'sub_fields' => [
                        [
                            'key' => 'field_exception_date',
                            'label' => 'Date',
                            'name' => 'date',
                            'type' => 'date_picker',
                            'required' => 0,
                            'display_format' => 'F j, Y',
                            'return_format' => 'Y-m-d',
                            'first_day' => 1,
                            'wrapper' => ['width' => 25],
                        ],
                        [
                            'key' => 'field_exception_spots',
                            'label' => 'Spots',
                            'name' => 'spots',
                            'type' => 'number',
                            'instructions' => 'Available spots for this specific date',
                            'required' => 0,
                            'min' => 0,
                            'max' => 100,
                            'wrapper' => ['width' => 15],
                        ],
                        [
                            'key' => 'field_exception_price_regular',
                            'label' => 'Regular Price',
                            'name' => 'price_regular',
                            'type' => 'number',
                            'instructions' => 'Regular price (optional, uses default if empty)',
                            'required' => 0,
                            'min' => 0,
                            'prepend' => '$',
                            'wrapper' => ['width' => 25],
                        ],
                        [
                            'key' => 'field_exception_price_offer',
                            'label' => 'Offer Price',
                            'name' => 'price_offer',
                            'type' => 'number',
                            'instructions' => 'Special offer price (optional)',
                            'required' => 0,
                            'min' => 0,
                            'prepend' => '$',
                            'wrapper' => ['width' => 25],
                        ],
                    ],
                ],

            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'package',
                    ],
                ],
            ],
            'menu_order' => 20,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ]);
    }
}
