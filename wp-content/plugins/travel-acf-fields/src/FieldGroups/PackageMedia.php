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
            'title' => '🖼️ Package - Media (Images and Banners)',
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

                // ===== BANNERS/SLIDES =====
                [
                    'key' => 'field_package_banners',
                    'label' => '🎠 Banners / Slides',
                    'name' => 'banners',
                    'type' => 'repeater',
                    'instructions' => 'Banners or slides for main package carousel.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 10,
                    'layout' => 'block',
                    'button_label' => 'Add Banner',
                    'collapsed' => 'field_banner_title',
                    'sub_fields' => [
                        [
                            'key' => 'field_banner_order',
                            'label' => 'Order',
                            'name' => 'order',
                            'type' => 'number',
                            'required' => 0, // Changed to optional
                            'default_value' => 1,
                            'min' => 1,
                            'wrapper' => ['width' => 20],
                        ],
                        [
                            'key' => 'field_banner_image',
                            'label' => 'Image',
                            'name' => 'image',
                            'type' => 'image',
                            'required' => 0, // Changed to optional - if you add a banner row, just fill it when ready
                            'return_format' => 'array',
                            'preview_size' => 'medium',
                            'library' => 'all',
                            'min_width' => 1920,
                            'min_height' => 800,
                            'wrapper' => ['width' => 80],
                        ],
                        [
                            'key' => 'field_banner_orientation',
                            'label' => 'Orientation',
                            'name' => 'orientation',
                            'type' => 'select',
                            'required' => 0,
                            'choices' => [
                                'horizontal' => 'Horizontal (16:9)',
                                'vertical' => 'Vertical (9:16)',
                                'square' => 'Square (1:1)',
                            ],
                            'default_value' => 'horizontal',
                            'ui' => 1,
                            'wrapper' => ['width' => 50],
                        ],
                        [
                            'key' => 'field_banner_title',
                            'label' => 'Slide Title',
                            'name' => 'title',
                            'type' => 'text',
                            'required' => 0,
                            'maxlength' => 100,
                            'placeholder' => 'Descriptive banner title',
                            'wrapper' => ['width' => 50],
                        ],
                    ],
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
