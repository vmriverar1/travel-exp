<?php
/**
 * Taxonomy: FAQ (Frequently Asked Questions)
 *
 * Preguntas frecuentes relacionadas con packages
 * Permite categorizar FAQs para diferentes tipos de packages o temas
 * Jerárquica para permitir sub-categorías
 *
 * @package Aurora\ContentKit\Taxonomies
 * @since 1.0.0
 */

namespace Aurora\ContentKit\Taxonomies;

use Aurora\ContentKit\Core\TaxonomyType;

class FAQ extends TaxonomyType
{
    public function __construct()
    {
        parent::__construct('FAQ', 'FAQs', 'faq', ['package']);
    }

    public function register(): void
    {
        add_action('init', function () {
            $labels = $this->labels('aurora-content-kit');

            $args = [
                'hierarchical'      => false, // No necesita jerarquía (sin parent/hijo)
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => ['slug' => 'faq', 'with_front' => false],
                'show_in_rest'      => true, // Habilita Gutenberg/REST API
                'public'            => true,
                'publicly_queryable' => true,
                'show_in_nav_menus' => true,
                'show_tagcloud'     => false,
            ];

            register_taxonomy($this->slug, $this->objectTypes, $args);
        }, 9);
    }
}
