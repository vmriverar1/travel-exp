<?php

namespace TravelSearch\Shortcode;

use WP_Query;
use TravelSearch\View\PackagesRenderer;

if (!defined('ABSPATH')) {
    exit;
}

class PackagesSearchShortcode
{
    public const TAG = 'packages_search';

    public function register(): void
    {
        add_shortcode(self::TAG, [$this, 'render']);
    }

    public function render(array $atts = [], ?string $content = null, string $tag = ''): string
    {
        // Ensure assets
        wp_enqueue_style('travel-search-front');
        wp_enqueue_style('travel-search-filters');
        wp_enqueue_script('travel-search-filters');
        wp_enqueue_script('travel-search-favorites');

        // Get filter values from URL
        $filters = $this->get_filters_from_url();

        // Build query
        $query = $this->build_query($filters);
        $renderer = new PackagesRenderer();

        ob_start();

        $rest_url = esc_url_raw(rest_url('travel-search/v1/packages'));

        // Siempre usar home_url para búsqueda, no la URL del single post
        // Esto evita que los filtros redirijan a /packages/nombre-paquete/
        $page_url = home_url('/');

        ?>
        <div class="ts-packages-wrapper"
             data-rest-url="<?php echo esc_attr($rest_url); ?>"
             data-page-url="<?php echo esc_attr($page_url); ?>">

            <?php $this->render_filters($filters); ?>

            <div class="ts-packages-results">
                <?php echo $renderer->render($query, 'Package', 'Packages'); ?>
            </div>
        </div>
        <?php

        return ob_get_clean();
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

        // Si hay parámetro s, agregarlo
        if (!empty($_GET['s'])) {
            $args['s'] = sanitize_text_field(wp_unslash($_GET['s']));
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
                // Necesitamos buscar dentro de ese string serializado
                // Agregamos quotes para mayor precisión: "january"
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

        // Debug: Ver qué query se está generando
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('=== TRAVEL SEARCH DEBUG ===');
            error_log('Filters: ' . print_r($filters, true));
            error_log('Args: ' . print_r($args, true));
        }

        $query = new WP_Query($args);

        // Debug: Ver resultados
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Found posts: ' . $query->found_posts);
            error_log('SQL: ' . $query->request);

            // Si no encuentra nada, ver qué paquetes hay disponibles
            if ($query->found_posts == 0) {
                error_log('--- Checking all packages ---');
                $all_packages = new WP_Query([
                    'post_type' => 'package',
                    'post_status' => 'publish',
                    'posts_per_page' => 5,
                ]);

                foreach ($all_packages->posts as $pkg) {
                    $months = get_post_meta($pkg->ID, 'months', true);
                    $departures = get_post_meta($pkg->ID, 'fixed_departures', true);
                    $interests = wp_get_post_terms($pkg->ID, 'interest', ['fields' => 'slugs']);

                    error_log('Package: ' . $pkg->post_title);
                    error_log('  Months (raw): ' . print_r($months, true));
                    error_log('  Departures (raw): ' . print_r($departures, true));
                    error_log('  Interests: ' . print_r($interests, true));
                }
            }
        }

        return $query;
    }

    /**
     * Render filters HTML
     */
    protected function render_filters(array $filters): void
    {
        // Get taxonomies
        $destinations = get_terms(['taxonomy' => 'destinations', 'hide_empty' => false]);
        $interests = get_terms(['taxonomy' => 'interest', 'hide_empty' => false]);
        $days_terms = get_terms(['taxonomy' => 'day', 'hide_empty' => false]);

        // Sort days: words first, then numbers
        $days = $this->sort_days_terms($days_terms);

        // Enqueue flatpickr if not already loaded
        if (!wp_script_is('flatpickr', 'enqueued')) {
            wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', [], '4.6.13');
            wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], '4.6.13', true);
        }

        ?>
        <div class="ts-filters-bar">
            <!-- Destination Filter -->
            <div class="ts-filter-group">
                <label for="ts-filter-destination"><?php esc_html_e('Destination', 'travel-search'); ?></label>
                <select id="ts-filter-destination" name="destination" class="ts-filter-select">
                    <option value=""><?php esc_html_e('All Destinations', 'travel-search'); ?></option>
                    <?php if (!empty($destinations) && !is_wp_error($destinations)) : ?>
                        <?php foreach ($destinations as $term) : ?>
                            <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($filters['destination'], $term->slug); ?>>
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Travel Style / Interest Filter -->
            <div class="ts-filter-group">
                <label for="ts-filter-interest"><?php esc_html_e('Travel Style', 'travel-search'); ?></label>
                <select id="ts-filter-interest" name="interest" class="ts-filter-select">
                    <option value=""><?php esc_html_e('All Styles', 'travel-search'); ?></option>
                    <?php if (!empty($interests) && !is_wp_error($interests)) : ?>
                        <?php foreach ($interests as $term) : ?>
                            <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($filters['interest'], $term->slug); ?>>
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Date Filter (Flatpickr) -->
            <div class="ts-filter-group">
                <label for="ts-filter-date"><?php esc_html_e('Date', 'travel-search'); ?></label>
                <input type="text" id="ts-filter-date" name="date" class="ts-filter-date" placeholder="Select a date..." value="<?php echo esc_attr($filters['date']); ?>" readonly />
            </div>

            <!-- Duration Filter -->
            <div class="ts-filter-group">
                <label for="ts-filter-days"><?php esc_html_e('Duration', 'travel-search'); ?></label>
                <select id="ts-filter-days" name="days" class="ts-filter-select">
                    <option value=""><?php esc_html_e('All Durations', 'travel-search'); ?></option>
                    <?php if (!empty($days) && !is_wp_error($days)) : ?>
                        <?php foreach ($days as $term) : ?>
                            <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($filters['days'], $term->slug); ?>>
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <?php
    }

    /**
     * Sort days terms: words first, then numbers
     */
    protected function sort_days_terms($terms): array
    {
        if (empty($terms) || is_wp_error($terms)) {
            return [];
        }

        $words = [];
        $numbers = [];

        foreach ($terms as $term) {
            // Check if name is ONLY digits (1, 2, 3, etc.)
            // "2 Hour" or "4 Hour" will be treated as words
            if (preg_match('/^\d+$/', $term->name)) {
                $numbers[] = $term;
            } else {
                $words[] = $term;
            }
        }

        // Sort numbers by numeric value
        usort($numbers, function($a, $b) {
            return (int) $a->name - (int) $b->name;
        });

        // Merge: words first, then numbers
        return array_merge($words, $numbers);
    }

    /**
     * Get ACF field choices
     */
    protected function get_acf_choices(string $field_name): array
    {
        if (!function_exists('get_field_object')) {
            return [];
        }

        $field = get_field_object($field_name);

        if (!empty($field) && !empty($field['choices'])) {
            return $field['choices'];
        }

        return [];
    }
}
