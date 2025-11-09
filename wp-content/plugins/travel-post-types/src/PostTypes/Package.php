<?php
namespace Aurora\ContentKit\PostTypes;

use Aurora\ContentKit\Core\ContentType;

class Package extends ContentType
{
    public function __construct()
    {
        parent::__construct('Package', 'Packages', 'package');
    }

    public function register(): void
    {
        add_action('init', function () {
            $labels = $this->labels('aurora-content-kit');

            $args = [
                'labels'             => $labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => ['slug' => 'packages', 'with_front' => false],
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => 20,
                'menu_icon'          => 'dashicons-palmtree',
                'supports'           => ['title', 'thumbnail', 'custom-fields', 'revisions', 'author'],
                'show_in_rest'       => true,
                'taxonomies'         => [
                    'package_type',
                    'interest',
                    'locations',
                    'optional_renting',
                    'included_services',
                    'additional_info',
                    'tag_locations',
                ]
            ];

            register_post_type($this->slug, $args);
        }, 9);
    }
}
