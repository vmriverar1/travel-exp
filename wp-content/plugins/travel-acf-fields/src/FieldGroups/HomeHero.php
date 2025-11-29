<?php
namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;
use Aurora\ACFKit\Setup\Settings;

class HomeHero extends FieldGroup
{
    public function __construct()
    {
        $this->key   = 'group_aurora_home_hero';
        $this->title = __('Home Hero', 'aurora-acf-kit');

        $this->fields = [
            [
                'key' => 'field_hero_title',
                'label' => __('Hero Title', 'aurora-acf-kit'),
                'name' => 'hero_title',
                'type' => 'text',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_hero_subtitle',
                'label' => __('Hero Subtitle', 'aurora-acf-kit'),
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_hero_cta_text',
                'label' => __('CTA Text', 'aurora-acf-kit'),
                'name' => 'hero_cta_text',
                'type' => 'text',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_hero_cta_url',
                'label' => __('CTA URL', 'aurora-acf-kit'),
                'name' => 'hero_cta_url',
                'type' => 'url',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_hero_bg',
                'label' => __('Background Image', 'aurora-acf-kit'),
                'name' => 'hero_background_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ],
        ];

        $ids = Settings::get_home_hero_ids();

        $location = [];
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $location[] = [
                    [
                        'param' => 'post_id',
                        'operator' => '==',
                        'value' => (string) $id,
                    ],
                ];
            }
        } else {
            // Default: show on all pages (post_type == page)
            $location[] = [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ]
            ];
        }

        $this->location = $location;

        $this->settings = [
            'position' => 'acf_after_title',
            'style' => 'default',
            'active' => true,
            'show_in_rest' => 1,
        ];
    }
}
