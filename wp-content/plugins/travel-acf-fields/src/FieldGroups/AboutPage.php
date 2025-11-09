<?php
namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class AboutPage extends FieldGroup
{
    public function __construct()
    {
        $this->key   = 'group_aurora_about_page';
        $this->title = __('About Page Fields', 'aurora-acf-kit');

        $this->fields = [
            [
                'key' => 'field_about_mission',
                'label' => __('Mission Statement', 'aurora-acf-kit'),
                'name' => 'about_mission',
                'type' => 'textarea',
                'instructions' => __('Write the mission statement of the company.', 'aurora-acf-kit'),
                'wrapper' => ['width' => 100],
            ],
            [
                'key' => 'field_about_image',
                'label' => __('Team Image', 'aurora-acf-kit'),
                'name' => 'about_team_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'instructions' => __('Upload an image of the team for the About page.', 'aurora-acf-kit'),
            ],
        ];

        $about_page = get_page_by_path('about');

        // Mostrar solo en la página con slug 'about' si existe
        if ($about_page) {
            $this->location = [
                [
                    [
                        'param' => 'page',
                        'operator' => '==',
                        'value'    => $about_page->ID,
                    ]
                ],
            ];
        } else {
            // Fallback: show on all pages with slug containing 'about'
            $this->location = [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ],
                ],
            ];
        }

        $this->settings = [
            'position' => 'acf_after_title',
            'style' => 'default',
            'active' => true,
            'show_in_rest' => 1,
        ];
    }
}
