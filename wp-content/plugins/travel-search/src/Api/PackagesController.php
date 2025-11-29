<?php

namespace TravelSearch\Api;

use WP_Query;
use TravelSearch\View\PackagesRenderer;
use WP_REST_Request;
use WP_REST_Response;

if (!defined('ABSPATH')) {
    exit;
}

class PackagesController
{
    public function register_routes(): void
    {
        register_rest_route(
            'travel-search/v1',
            '/packages',
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'handle_packages'],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function handle_packages(WP_REST_Request $request): WP_REST_Response
    {
        // Get filter values from request
        $filters = $this->get_filters_from_request($request);

        // Build query
        $query = $this->build_query($filters);

        // Render HTML
        $renderer = new PackagesRenderer();
        $html = $renderer->render($query, 'Package', 'Packages');

        return new WP_REST_Response(
            [
                'html'  => $html,
                'found' => (int) $query->found_posts,
                'filters' => $filters, // For debugging
            ],
            200
        );
    }

    /**
     * Get filter values from REST request
     */
    protected function get_filters_from_request(WP_REST_Request $request): array
    {
        // Destination (single value)
        $destination = $request->get_param('destination');

        // Interest (single select)
        $interest = $request->get_param('interest');

        // Date (single value)
        $date = $request->get_param('date');

        // Days (single value)
        $days = $request->get_param('days');

        // Search term (single value)
        $s = $request->get_param('s');

        return [
            'destination' => !empty($destination) ? sanitize_text_field($destination) : '',
            'interest'    => !empty($interest) ? sanitize_text_field($interest) : '',
            'date'        => !empty($date) ? sanitize_text_field($date) : '',
            'days'        => !empty($days) ? sanitize_text_field($days) : '',
            's'           => !empty($s) ? sanitize_text_field($s) : '',
        ];
    }

    /**
     * Build WP_Query with filters
     */
    protected function build_query(array $filters): WP_Query
    {
        $args = [
            'post_type'      => 'package',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ];

        // Si hay parámetro s en los filtros, agregarlo
        if (!empty($filters['s'])) {
            $args['s'] = $filters['s'];
        }

        $tax_query = ['relation' => 'AND'];
        $meta_query = ['relation' => 'AND'];

        // Destination filter (single select)
        if (!empty($filters['destination'])) {
            $term = get_term_by('slug', $filters['destination'], 'destinations');
            if ($term && !is_wp_error($term)) {
                $tax_query[] = [
                    'taxonomy' => 'destinations',
                    'field'    => 'term_id',
                    'terms'    => (int) $term->term_id,
                ];
            }
        }

        // Interest filter (single select)
        if (!empty($filters['interest'])) {
            $term = get_term_by('slug', $filters['interest'], 'interest');
            if ($term && !is_wp_error($term)) {
                $tax_query[] = [
                    'taxonomy' => 'interest',
                    'field'    => 'term_id',
                    'terms'    => (int) $term->term_id,
                ];
            }
        }

        // Days filter (single select)
        if (!empty($filters['days'])) {
            $term = get_term_by('slug', $filters['days'], 'day');
            if ($term && !is_wp_error($term)) {
                $tax_query[] = [
                    'taxonomy' => 'day',
                    'field'    => 'term_id',
                    'terms'    => (int) $term->term_id,
                ];
            }
        }

        // Date filter (ACF months + fixed_departures con mejor compatibilidad)
        if (!empty($filters['date'])) {
            $timestamp = strtotime($filters['date']);
            if ($timestamp && $timestamp > 0) {
                $month_key   = strtolower(date('F', $timestamp));
                $weekday_key = strtolower(date('l', $timestamp));

                // ACF guarda valores como: a:2:{i:0;s:7:"january";i:1;s:8:"february";}
                // Agregamos quotes para buscar dentro del string serializado
                $month_search = '"' . $month_key . '"';
                $weekday_search = '"' . $weekday_key . '"';

                $meta_query[] = [
                    'key'     => 'months',
                    'value'   => $month_search,
                    'compare' => 'LIKE',
                ];

                $meta_query[] = [
                    'key'     => 'fixed_departures',
                    'value'   => $weekday_search,
                    'compare' => 'LIKE',
                ];
            }
        }

        // Apply queries if not empty
        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }

        if (count($meta_query) > 1) {
            $args['meta_query'] = $meta_query;
        }

        return new WP_Query($args);
    }
}
