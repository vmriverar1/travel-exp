<?php
/**
 * ACF Field Group: Taxonomy - Destinations
 *
 * Campos para los términos de la taxonomía Destinations
 * Esta taxonomía reemplaza el antiguo CPT "destination"
 *
 * @package Aurora\ACFKit\FieldGroups
 * @since 1.0.0
 */

namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class TaxonomyDestinations extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_taxonomy_destinations',
            'title' => '🗺️ Destination - Additional Fields',
            'fields' => [

                // ===== SUBTITLE =====
                [
                    'key' => 'field_destination_subtitle',
                    'label' => 'Subtitle',
                    'name' => 'subtitle',
                    'type' => 'text',
                    'instructions' => 'Short tagline for this destination.',
                    'placeholder' => 'e.g., The Imperial City',
                ],

                // ===== IMAGE =====
                [
                    'key' => 'field_destination_image',
                    'label' => '🖼️ Main Image',
                    'name' => 'image',
                    'type' => 'image',
                    'instructions' => 'Main featured image for this destination.',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],

                // ===== THUMBNAIL =====
                [
                    'key' => 'field_destination_thumbnail',
                    'label' => '📷 Thumbnail',
                    'name' => 'thumbnail',
                    'type' => 'image',
                    'instructions' => 'Smaller thumbnail for destination listings.',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                    'library' => 'all',
                ],

                // ===== CONTENT (WYSIWYG) =====
                [
                    'key' => 'field_destination_content',
                    'label' => '📝 Content',
                    'name' => 'content',
                    'type' => 'wysiwyg',
                    'instructions' => 'Rich content description for this destination.',
                    'required' => 1,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                ],

                // ===== COUNTRY (TAXONOMY RELATIONSHIP) =====
                [
                    'key' => 'field_destination_country',
                    'label' => '🌍 Country',
                    'name' => 'country',
                    'type' => 'taxonomy',
                    'instructions' => 'Select the country this destination belongs to.',
                    'required' => 1,
                    'taxonomy' => 'countries',
                    'field_type' => 'select',
                    'allow_null' => 0,
                    'add_term' => 0,
                    'save_terms' => 0,
                    'load_terms' => 0,
                    'return_format' => 'id',
                ],

                // ===== ACTIVE STATUS =====
                [
                    'key' => 'field_destination_active',
                    'label' => '✅ Active',
                    'name' => 'active',
                    'type' => 'true_false',
                    'instructions' => 'Activate or deactivate this destination.',
                    'default_value' => 1,
                    'ui' => 1,
                    'ui_on_text' => 'Active',
                    'ui_off_text' => 'Inactive',
                ],

                // ===== SEO FIELDS =====
                [
                    'key' => 'field_destination_seo_title',
                    'label' => 'SEO Title',
                    'name' => 'seo_title',
                    'type' => 'text',
                    'instructions' => 'Custom SEO title (leave empty to use term name).',
                ],
                [
                    'key' => 'field_destination_seo_description',
                    'label' => 'SEO Description',
                    'name' => 'seo_description',
                    'type' => 'textarea',
                    'instructions' => 'Meta description for search engines (155-160 characters).',
                    'rows' => 3,
                    'maxlength' => 160,
                ],
                [
                    'key' => 'field_destination_seo_keywords',
                    'label' => 'SEO Keywords',
                    'name' => 'seo_keywords',
                    'type' => 'text',
                    'instructions' => 'Comma-separated keywords.',
                    'placeholder' => 'cusco, peru, inca, tours',
                ],

            ],
            'location' => [
                [
                    [
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'destinations',
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
