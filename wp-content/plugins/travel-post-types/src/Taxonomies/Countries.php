<?php
/**
 * Taxonomy: Countries
 *
 * Países donde se ofrecen tours
 * Jerárquica para permitir organización
 *
 * @package Aurora\ContentKit\Taxonomies
 * @since 1.0.0
 */

namespace Aurora\ContentKit\Taxonomies;

use Aurora\ContentKit\Core\TaxonomyType;

class Countries extends TaxonomyType
{
    public function __construct()
    {
        parent::__construct('Country', 'Countries', 'countries', ['location']); // Solo location
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
                'rewrite'           => ['slug' => 'country', 'with_front' => false],
                'show_in_rest'      => true,
            ];

            register_taxonomy($this->slug, $this->objectTypes, $args);
        }, 9);
    }
}
