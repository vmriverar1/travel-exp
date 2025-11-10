<?php
namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class PackageSEO extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_package_seo',
            'title' => '🔍 Package - SEO & Meta',
            'fields' => [

                // ===== META TITLE =====
                [
                    'key' => 'field_package_seo_title',
                    'label' => '📝 SEO Title',
                    'name' => 'seo_title',
                    'type' => 'text',
                    'instructions' => 'Custom SEO title (60-70 characters recommended). Leave empty to use default post title.',
                    'required' => 0,
                    'maxlength' => 70,
                    'placeholder' => 'Amazing 5-Day Machu Picchu Tour | Book Now',
                ],

                // ===== META DESCRIPTION =====
                [
                    'key' => 'field_package_seo_description',
                    'label' => '📄 Meta Description',
                    'name' => 'seo_description',
                    'type' => 'textarea',
                    'instructions' => 'SEO meta description (150-160 characters recommended). Shown in search results.',
                    'required' => 0,
                    'rows' => 3,
                    'maxlength' => 160,
                    'placeholder' => 'Discover the wonders of Machu Picchu with our 5-day guided tour. Includes accommodations, meals, and expert guides. Book your adventure today!',
                ],

                // ===== FOCUS KEYWORDS =====
                [
                    'key' => 'field_package_seo_keywords',
                    'label' => '🔑 Focus Keywords',
                    'name' => 'seo_keywords',
                    'type' => 'text',
                    'instructions' => 'Primary keywords for this package (comma-separated). Example: "machu picchu tour, cusco travel, peru vacation"',
                    'required' => 0,
                    'maxlength' => 200,
                    'placeholder' => 'machu picchu tour, cusco travel, peru vacation',
                ],

                // ===== CANONICAL URL =====
                [
                    'key' => 'field_package_seo_canonical',
                    'label' => '🔗 Canonical URL',
                    'name' => 'seo_canonical',
                    'type' => 'url',
                    'instructions' => 'Canonical URL (optional). Use only if this package is duplicated elsewhere.',
                    'required' => 0,
                    'placeholder' => 'https://example.com/packages/original-package',
                ],

                // ===== OPEN GRAPH IMAGE =====
                [
                    'key' => 'field_package_seo_og_image',
                    'label' => '🖼️ Open Graph Image',
                    'name' => 'seo_og_image',
                    'type' => 'image',
                    'instructions' => 'Custom image for social media sharing (Facebook, LinkedIn). Recommended: 1200x630px. Leave empty to use featured image.',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'min_width' => 1200,
                    'min_height' => 630,
                ],

                // ===== NOINDEX/NOFOLLOW =====
                [
                    'key' => 'field_package_seo_robots',
                    'label' => '🤖 Search Engine Visibility',
                    'name' => 'seo_robots',
                    'type' => 'select',
                    'instructions' => 'Control how search engines index this package.',
                    'required' => 0,
                    'choices' => [
                        'index_follow' => 'Index, Follow (Default)',
                        'noindex_follow' => 'No Index, Follow',
                        'index_nofollow' => 'Index, No Follow',
                        'noindex_nofollow' => 'No Index, No Follow',
                    ],
                    'default_value' => 'index_follow',
                    'ui' => 1,
                ],

                // ===== SCHEMA MARKUP =====
                [
                    'key' => 'field_package_seo_schema',
                    'label' => '📊 Schema Type',
                    'name' => 'seo_schema',
                    'type' => 'select',
                    'instructions' => 'Structured data schema type for rich snippets.',
                    'required' => 0,
                    'choices' => [
                        'product' => 'Product (eCommerce)',
                        'tour' => 'Tour/Activity',
                        'event' => 'Event',
                        'local_business' => 'Local Business',
                        'article' => 'Article',
                    ],
                    'default_value' => 'tour',
                    'ui' => 1,
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
            'menu_order' => 70,
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ]);
    }
}
