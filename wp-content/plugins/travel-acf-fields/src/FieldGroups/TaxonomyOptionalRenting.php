<?php
namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class TaxonomyOptionalRenting extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_taxonomy_optional_renting',
            'title' => 'Optional Renting Details',
            'fields' => [
                [
                    'key' => 'field_optional_renting_featured_image',
                    'label' => '🖼️ Imagen Destacada',
                    'name' => 'featured_image',
                    'type' => 'image',
                    'instructions' => 'Imagen destacada para este optional renting (opcional).',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],
                [
                    'key' => 'field_optional_renting_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'required' => 1,
                    'instructions' => 'Enter the title for this optional renting item',
                    'placeholder' => 'e.g., Sleeping Bag, Trekking Poles',
                ],
                [
                    'key' => 'field_optional_renting_content',
                    'label' => 'Content',
                    'name' => 'content',
                    'type' => 'wysiwyg',
                    'required' => 1,
                    'instructions' => 'Describe the optional renting item details',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ],
                [
                    'key' => 'field_optional_renting_price',
                    'label' => 'Price',
                    'name' => 'price',
                    'type' => 'number',
                    'required' => 0,
                    'instructions' => 'Enter the rental price (in USD)',
                    'default_value' => 0,
                    'placeholder' => '0.00',
                    'min' => 0,
                    'step' => 0.01,
                    'prepend' => '$',
                    'append' => 'USD',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'optional_renting',
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
