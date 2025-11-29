<?php

namespace TravelSearch\Api;

use WP_REST_Request;
use WP_REST_Response;

if (!defined('ABSPATH')) {
    exit;
}

class FiltersController
{
    public function register_routes(): void
    {
        register_rest_route(
            'travel-search/v1',
            '/filters',
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'handle_filters'],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function handle_filters(WP_REST_Request $request): WP_REST_Response
    {
        // Destinations taxonomy
        $destinations = get_terms([
            'taxonomy'   => 'destinations',
            'hide_empty' => false,
        ]);

        $dest_data = [];
        if (!empty($destinations) && !is_wp_error($destinations)) {
            foreach ($destinations as $term) {
                $dest_data[] = [
                    'slug' => $term->slug,
                    'name' => $term->name,
                    'id'   => (int) $term->term_id,
                ];
            }
        }

        // ACF choices: months & fixed_departures (if ACF is active)
        $months = [];
        $weekdays = [];

        if (function_exists('get_field_object')) {
            $months_field = get_field_object('field_package_months');
            if (!empty($months_field) && !empty($months_field['choices'])) {
                foreach ($months_field['choices'] as $value => $label) {
                    $months[] = [
                        'value' => $value,
                        'label' => $label,
                    ];
                }
            }

            $fixed_field = get_field_object('field_package_fixed_departures_v2');
            if (!empty($fixed_field) && !empty($fixed_field['choices'])) {
                foreach ($fixed_field['choices'] as $value => $label) {
                    $weekdays[] = [
                        'value' => $value,
                        'label' => $label,
                    ];
                }
            }
        }

        return new WP_REST_Response(
            [
                'destinations' => $dest_data,
                'months'       => $months,
                'weekdays'     => $weekdays,
            ],
            200
        );
    }
}
