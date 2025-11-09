<?php
/**
 * Taxonomy: Destinations
 *
 * Destinos turísticos principales (Cusco, Lima, Arequipa, Puno, etc.)
 * Esta taxonomía reemplaza el antiguo CPT "destination"
 * Jerárquica para permitir sub-destinos (ej: Cusco > Valle Sagrado)
 *
 * @package Aurora\ContentKit\Taxonomies
 * @since 1.0.0
 */

namespace Aurora\ContentKit\Taxonomies;

use Aurora\ContentKit\Core\TaxonomyType;

class Destinations extends TaxonomyType
{
    public function __construct()
    {
        parent::__construct('Destination', 'Destinations', 'destinations', ['location', 'post']); // Sin package
    }

    public function register(): void
    {
        add_action('init', function () {
            $labels = $this->labels('aurora-content-kit');

            $args = [
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => ['slug' => 'destination', 'with_front' => false],
                'show_in_rest'      => true,
            ];

            register_taxonomy($this->slug, $this->objectTypes, $args);
        }, 9);
    }
}
