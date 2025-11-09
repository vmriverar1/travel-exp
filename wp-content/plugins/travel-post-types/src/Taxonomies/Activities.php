<?php
namespace Aurora\ContentKit\Taxonomies;

use Aurora\ContentKit\Core\TaxonomyType;

class Activities extends TaxonomyType
{
    public function __construct()
    {
        parent::__construct('Activity', 'Activities', 'activity', ['package']);
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
                'rewrite'           => ['slug' => 'activity', 'with_front' => false],
                'show_in_rest'      => true,
            ];

            register_taxonomy($this->slug, $this->objectTypes, $args);
        }, 9);
    }
}
