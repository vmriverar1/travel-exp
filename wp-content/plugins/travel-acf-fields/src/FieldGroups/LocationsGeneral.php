<?php
/**
 * ACF Field Group: Locations - General
 *
 * Campos para el CPT Location (ubicaciones específicas de tours)
 *
 * @package Aurora\ACFKit\FieldGroups
 * @since 1.0.0
 */

namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class LocationsGeneral extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_locations_general',
            'title' => '📍 Location - General Information',
            'fields' => [

                // ===== SUBTITLE =====
                [
                    'key' => 'field_location_subtitle',
                    'label' => 'Subtitle',
                    'name' => 'subtitle',
                    'type' => 'text',
                    'instructions' => 'Short tagline or description for this location.',
                    'placeholder' => 'e.g., The Lost City of the Incas',
                ],

                // ===== IMAGE =====
                [
                    'key' => 'field_location_image',
                    'label' => '🖼️ Main Image',
                    'name' => 'location_image',
                    'type' => 'image',
                    'instructions' => 'Main featured image for this location (different from thumbnail).',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],

                // ===== ACTIVE STATUS =====
                [
                    'key' => 'field_location_active',
                    'label' => '✅ Active',
                    'name' => 'active',
                    'type' => 'true_false',
                    'instructions' => 'Activate or deactivate this location (deactivated locations won\'t appear on frontend).',
                    'default_value' => 1,
                    'ui' => 1,
                    'ui_on_text' => 'Active',
                    'ui_off_text' => 'Inactive',
                ],

                // ===== SEO FIELDS =====
                [
                    'key' => 'field_location_seo_title',
                    'label' => 'SEO Title',
                    'name' => 'seo_title',
                    'type' => 'text',
                    'instructions' => 'Custom SEO title (leave empty to use post title).',
                    'placeholder' => '',
                ],
                [
                    'key' => 'field_location_seo_description',
                    'label' => 'SEO Description',
                    'name' => 'seo_description',
                    'type' => 'textarea',
                    'instructions' => 'Meta description for search engines (155-160 characters recommended).',
                    'rows' => 3,
                    'maxlength' => 160,
                ],
                [
                    'key' => 'field_location_seo_keywords',
                    'label' => 'SEO Keywords',
                    'name' => 'seo_keywords',
                    'type' => 'text',
                    'instructions' => 'Comma-separated keywords (optional, mostly for internal use).',
                    'placeholder' => 'machu picchu, inca, peru tours',
                ],

            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'location',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'left',
            'instruction_placement' => 'label',
            'active' => true,
        ]);
    }
}
