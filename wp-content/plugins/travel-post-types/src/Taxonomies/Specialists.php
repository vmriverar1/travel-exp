<?php
namespace Aurora\ContentKit\Taxonomies;

use Aurora\ContentKit\Core\TaxonomyType;

class Specialists extends TaxonomyType
{
    public function __construct()
    {
        parent::__construct('Specialist', 'Specialists', 'specialists', ['package']);
    }

    public function register(): void
    {
        add_action('init', function () {
            $labels = $this->labels('aurora-content-kit');

            $args = [
                'hierarchical'      => false,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => ['slug' => 'specialists', 'with_front' => false],
                'show_in_rest'      => true,
            ];

            register_taxonomy($this->slug, $this->objectTypes, $args);
        }, 9);
    }
}
