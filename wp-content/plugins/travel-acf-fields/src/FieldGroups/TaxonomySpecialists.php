<?php
namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class TaxonomySpecialists extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_taxonomy_specialists',
            'title' => 'Specialist Information',
            'fields' => [
                [
                    'key' => 'field_specialist_fullname',
                    'label' => 'Full Name',
                    'name' => 'fullname',
                    'type' => 'text',
                    'required' => 1,
                    'instructions' => 'Enter the specialist\'s full name',
                    'placeholder' => 'e.g., John Smith',
                ],
                [
                    'key' => 'field_specialist_content',
                    'label' => 'Content',
                    'name' => 'content',
                    'type' => 'wysiwyg',
                    'required' => 1,
                    'instructions' => 'Enter the specialist\'s bio, expertise, and description',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ],
                [
                    'key' => 'field_specialist_email',
                    'label' => 'Email',
                    'name' => 'email',
                    'type' => 'email',
                    'required' => 1,
                    'instructions' => 'Enter the specialist\'s contact email',
                    'placeholder' => 'specialist@example.com',
                ],
                [
                    'key' => 'field_specialist_calendly',
                    'label' => 'Calendly Link',
                    'name' => 'calendly',
                    'type' => 'url',
                    'required' => 0,
                    'instructions' => 'Enter the Calendly booking URL (optional)',
                    'placeholder' => 'https://calendly.com/specialist-name',
                ],
                [
                    'key' => 'field_specialist_thumbnail',
                    'label' => 'Thumbnail',
                    'name' => 'thumbnail',
                    'type' => 'image',
                    'required' => 1,
                    'instructions' => 'Upload the specialist\'s photo',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'min_width' => 400,
                    'min_height' => 400,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'specialists',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'active' => true,
        ]);
    }
}
