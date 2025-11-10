<?php
namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class PackageMedia extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_package_media',
            'title' => '🖼️ Package - Media (Images)',
            'fields' => [

                // ===== MAIN IMAGE (uses WordPress Featured Image) =====
                [
                    'key' => 'field_package_main_image_note',
                    'label' => '📸 Main Image',
                    'name' => '',
                    'type' => 'message',
                    'message' => 'The main image is configured using WordPress native <strong>Featured Image</strong> function (see right sidebar).',
                ],

                // ===== GALLERY =====
                [
                    'key' => 'field_package_gallery',
                    'label' => '🖼️ Image Gallery',
                    'name' => 'gallery',
                    'type' => 'gallery',
                    'instructions' => 'Photo gallery of the package. These images will be displayed on the single package page.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 30,
                    'insert' => 'append',
                    'library' => 'all',
                    'min_width' => 800,
                    'min_height' => 600,
                    'preview_size' => 'medium',
                ],

                // ===== MAP IMAGE =====
                [
                    'key' => 'field_package_map_image',
                    'label' => '🗺️ Map Image',
                    'name' => 'map_image',
                    'type' => 'image',
                    'instructions' => 'Static map image showing the route or destinations of the package (displayed in sidebar).',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],

            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'package',
                    ],
                ],
            ],
            'menu_order' => 30,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ]);
    }
}
