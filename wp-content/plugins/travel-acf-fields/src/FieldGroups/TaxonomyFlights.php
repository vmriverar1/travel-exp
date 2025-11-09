<?php
/**
 * ACF Field Group: Taxonomy - Flights
 *
 * Campos para los términos de la taxonomía Flights
 *
 * @package Aurora\ACFKit\FieldGroups
 * @since 1.0.0
 */

namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class TaxonomyFlights extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_taxonomy_flights',
            'title' => '✈️ Flight - Additional Fields',
            'fields' => [

                // ===== FEATURED IMAGE =====
                [
                    'key' => 'field_flight_featured_image',
                    'label' => '🖼️ Imagen Destacada',
                    'name' => 'featured_image',
                    'type' => 'image',
                    'instructions' => 'Imagen destacada para este flight (opcional).',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],

                // ===== PRICE =====
                [
                    'key' => 'field_flight_price',
                    'label' => '💵 Price',
                    'name' => 'price',
                    'type' => 'number',
                    'instructions' => 'Flight price in USD.',
                    'required' => 1,
                    'min' => 0,
                    'step' => 0.01,
                    'prepend' => '$',
                ],

                // ===== ACTIVE STATUS =====
                [
                    'key' => 'field_flight_active',
                    'label' => '✅ Active',
                    'name' => 'active',
                    'type' => 'true_false',
                    'instructions' => 'Activate or deactivate this flight option.',
                    'default_value' => 1,
                    'ui' => 1,
                    'ui_on_text' => 'Active',
                    'ui_off_text' => 'Inactive',
                ],

            ],
            'location' => [
                [
                    [
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'flights',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'left',
            'instruction_placement' => 'label',
            'active' => true,
        ]);
    }
}
