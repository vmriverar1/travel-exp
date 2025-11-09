<?php
namespace Aurora\ContentKit\Taxonomies;

use Aurora\ContentKit\Core\TaxonomyType;

class Days extends TaxonomyType
{
    public function __construct()
    {
        parent::__construct('Day', 'Days', 'day', ['package']);
    }

    public function register(): void
    {
        add_action('init', function () {
            $labels = $this->labels('aurora-content-kit');

            $args = [
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => ['slug' => 'day', 'with_front' => false],
                'show_in_rest'      => true,
                'meta_box_cb'       => false, // usaremos ACF
            ];

            register_taxonomy($this->slug, $this->objectTypes, $args);

            // Crear los términos ordenados
            $this->maybe_create_day_terms();
        }, 9);
    }

    /**
     * Crea los términos en orden:
     * 2 Hours, 4 Hours, Half Day, Full Day, 1–38
     */
    private function maybe_create_day_terms(): void
    {
        $taxonomy = $this->slug;

        // === 1️⃣ Duraciones especiales ===
        $specials = [
            '2-hours'  => '2 Hours',
            '4-hours'  => '4 Hours',
            'half-day' => 'Half Day',
            'full-day' => 'Full Day',
        ];

        foreach ($specials as $slug => $name) {
            if (!term_exists($slug, $taxonomy)) {
                wp_insert_term($name, $taxonomy, ['slug' => $slug]);
            }
        }

        // === 2️⃣ Días numéricos 1–38 ===
        for ($i = 1; $i <= 38; $i++) {
            $name = (string) $i;
            $slug = 'day-' . $i;

            if (!term_exists($slug, $taxonomy)) {
                wp_insert_term($name, $taxonomy, ['slug' => $slug]);
            }
        }
    }
}
