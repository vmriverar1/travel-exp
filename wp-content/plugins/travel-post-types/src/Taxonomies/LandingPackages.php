<?php
/**
 * Taxonomy: Landing Packages
 *
 * Marcador para paquetes que tienen landing pages personalizadas
 * Las landings se diseñan con Gutenberg
 *
 * @package Aurora\ContentKit\Taxonomies
 * @since 1.0.0
 */

namespace Aurora\ContentKit\Taxonomies;

use Aurora\ContentKit\Core\TaxonomyType;

class LandingPackages extends TaxonomyType
{
    public function __construct()
    {
        parent::__construct('Landing Package', 'Landing Packages', 'landing_packages', ['package']);
    }

    public function register(): void
    {
        add_action('init', function () {
            $labels = $this->labels('aurora-content-kit');

            $args = [
                'hierarchical'      => false,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => ['slug' => 'landing-package', 'with_front' => false],
                'show_in_rest'      => true,
            ];

            register_taxonomy($this->slug, $this->objectTypes, $args);
        }, 9);
    }
}
