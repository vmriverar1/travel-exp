<?php

namespace Travel\ACFFields\FieldGroups;

class PostFeatured
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_post_featured',
            'title' => '⭐ Featured / Destacado',
            'fields' => [
                [
                    'key' => 'field_is_featured',
                    'label' => 'Featured Post',
                    'name' => 'is_featured',
                    'type' => 'true_false',
                    'instructions' => 'Mark this post/package as featured to highlight it in listings and carousels',
                    'default_value' => 0,
                    'ui' => 1,
                    'ui_on_text' => '⭐ Featured',
                    'ui_off_text' => 'Not Featured',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
                    ],
                ],
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'package',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ]);
    }
}
