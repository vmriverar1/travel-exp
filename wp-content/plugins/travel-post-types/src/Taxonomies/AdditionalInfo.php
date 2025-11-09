<?php
namespace Aurora\ContentKit\Taxonomies;

use Aurora\ContentKit\Core\TaxonomyType;

class AdditionalInfo extends TaxonomyType
{
    public function __construct()
    {
        parent::__construct('Additional Info', 'Additional Info', 'additional_info', ['package']);
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
                'rewrite'           => ['slug' => 'additional-info', 'with_front' => false],
                'show_in_rest'      => true,
            ];

            register_taxonomy($this->slug, $this->objectTypes, $args);
        }, 9);
    }
}
