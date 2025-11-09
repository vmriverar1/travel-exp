<?php
/**
 * ACF Field Group: Taxonomy - Roles
 *
 * Campos para los términos de la taxonomía Roles
 *
 * @package Aurora\ACFKit\FieldGroups
 * @since 1.0.0
 */

namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class TaxonomyRoles extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_taxonomy_roles',
            'title' => '💼 Role - Additional Fields',
            'fields' => [

                // ===== FEATURED IMAGE =====
                [
                    'key' => 'field_role_featured_image',
                    'label' => '🖼️ Imagen Destacada',
                    'name' => 'featured_image',
                    'type' => 'image',
                    'instructions' => 'Imagen destacada para este role (opcional).',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],

                // ===== DESCRIPTION (WYSIWYG) =====
                [
                    'key' => 'field_role_description',
                    'label' => '📝 Description',
                    'name' => 'description',
                    'type' => 'wysiwyg',
                    'instructions' => 'Detailed description of this role.',
                    'required' => 1,
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],

                // ===== ACTIVE STATUS =====
                [
                    'key' => 'field_role_active',
                    'label' => '✅ Active',
                    'name' => 'active',
                    'type' => 'true_false',
                    'instructions' => 'Activate or deactivate this role.',
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
                        'value' => 'roles',
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
