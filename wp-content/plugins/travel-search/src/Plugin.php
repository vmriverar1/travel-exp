<?php

namespace TravelSearch;

use TravelSearch\Assets\Assets;
use TravelSearch\Shortcode\PackagesSearchShortcode;
use TravelSearch\Shortcode\PostsSearchShortcode;
use TravelSearch\Api\PackagesController;
use TravelSearch\Api\FiltersController;

if (!defined('ABSPATH')) {
    exit;
}

class Plugin
{
    public function init(): void
    {
        // Assets
        (new Assets())->init();

        // Shortcodes
        add_action('init', function () {
            (new PackagesSearchShortcode())->register();
            (new PostsSearchShortcode())->register();
        });

        // REST API
        add_action('rest_api_init', function () {
            (new PackagesController())->register_routes();
            (new FiltersController())->register_routes();
        });

        // Forzar plantilla search.php cuando hay destination o date
        add_filter('template_include', [$this, 'force_search_template']);

        // Modificar query principal para buscar solo packages
        add_action('pre_get_posts', [$this, 'modify_main_query']);
    }

    /**
     * Forzar plantilla search.php cuando hay filtros en la URL
     */
    public function force_search_template(string $template): string
    {
        $has_filters = $this->has_package_filters();

        if ($has_filters) {
            // Buscar search.php en el tema
            $search_template = locate_template('search.php');
            if ($search_template) {
                return $search_template;
            }
        }

        return $template;
    }

    /**
     * Modificar query principal para buscar solo packages cuando hay filtros
     */
    public function modify_main_query(\WP_Query $query): void
    {
        // Solo en el front-end y query principal
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        // Si es búsqueda nativa de WordPress (?s=), buscar en posts Y packages
        if ($query->is_search() && !empty($query->get('s')) && !$this->has_package_filters()) {
            $search_term = $query->get('s');
            $query->set('post_type', ['post', 'package']);
            $query->set('s', $search_term); // Asegurar que se mantenga el término de búsqueda
            return;
        }

        // Check if we have package filters
        if (!$this->has_package_filters()) {
            return;
        }

        // Get filters from URL
        $filters = $this->get_filters_from_url();

        // Set post type to package
        $query->set('post_type', 'package');
        $query->is_search = true;
        $query->is_home = false;

        // Si también hay parámetro s, aplicarlo
        if (!empty($_GET['s'])) {
            $search_term = sanitize_text_field(wp_unslash($_GET['s']));
            $query->set('s', $search_term);
        }

        // Build tax_query
        $tax_query = ['relation' => 'AND'];

        // Destination filter
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

        // Days filter
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

        // Apply tax_query if not empty
        if (count($tax_query) > 1) {
            $query->set('tax_query', $tax_query);
        }

        // Build meta_query
        $meta_query = ['relation' => 'AND'];

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

        // Apply meta_query if not empty
        if (count($meta_query) > 1) {
            $query->set('meta_query', $meta_query);
        }
    }

    /**
     * Check if URL has package filters
     */
    protected function has_package_filters(): bool
    {
        return !empty($_GET['destination']) ||
               !empty($_GET['interest']) ||
               !empty($_GET['date']) ||
               !empty($_GET['days']);
    }

    /**
     * Get filter values from URL
     */
    protected function get_filters_from_url(): array
    {
        return [
            'destination' => isset($_GET['destination']) ? sanitize_text_field(wp_unslash($_GET['destination'])) : '',
            'interest'    => isset($_GET['interest']) ? sanitize_text_field(wp_unslash($_GET['interest'])) : '',
            'date'        => isset($_GET['date']) ? sanitize_text_field(wp_unslash($_GET['date'])) : '',
            'days'        => isset($_GET['days']) ? sanitize_text_field(wp_unslash($_GET['days'])) : '',
        ];
    }
}
