<?php
namespace Aurora\ContentKit\Taxonomies;

use Aurora\ContentKit\Core\TaxonomyType;

class Hotels extends TaxonomyType
{
    public function __construct()
    {
        parent::__construct('Hotel', 'Hotels', 'hotel', ['package']);
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
                'rewrite'           => ['slug' => 'hotel', 'with_front' => false],
                'show_in_rest'      => true,
                'meta_box_cb'       => false, // Disable default meta box (we'll use ACF field)
            ];

            register_taxonomy($this->slug, $this->objectTypes, $args);
        }, 9);
    }
}
