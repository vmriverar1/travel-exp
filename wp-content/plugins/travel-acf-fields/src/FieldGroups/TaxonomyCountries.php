<?php
/**
 * ACF Field Group: Taxonomy - Countries
 *
 * Campos para los términos de la taxonomía Countries
 *
 * @package Aurora\ACFKit\FieldGroups
 * @since 1.0.0
 */

namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class TaxonomyCountries extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_taxonomy_countries',
            'title' => '🌍 Country - Additional Fields',
            'fields' => [

                // ===== BANNER IMAGE =====
                [
                    'key' => 'field_country_banner',
                    'label' => '🖼️ Banner Image',
                    'name' => 'banner',
                    'type' => 'image',
                    'instructions' => 'Large banner image for country pages.',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],

                // ===== THUMBNAIL =====
                [
                    'key' => 'field_country_thumbnail',
                    'label' => '📷 Thumbnail',
                    'name' => 'thumbnail',
                    'type' => 'image',
                    'instructions' => 'Smaller thumbnail image for country listings.',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                    'library' => 'all',
                ],

                // ===== CONTENT (WYSIWYG) =====
                [
                    'key' => 'field_country_content',
                    'label' => '📝 Content',
                    'name' => 'content',
                    'type' => 'wysiwyg',
                    'instructions' => 'Rich content description for this country.',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                ],

                // ===== ACTIVE STATUS =====
                [
                    'key' => 'field_country_active',
                    'label' => '✅ Active',
                    'name' => 'active',
                    'type' => 'true_false',
                    'instructions' => 'Activate or deactivate this country.',
                    'default_value' => 1,
                    'ui' => 1,
                    'ui_on_text' => 'Active',
                    'ui_off_text' => 'Inactive',
                ],

            ],
            'location' => [
                [
                    [
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'countries',
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
