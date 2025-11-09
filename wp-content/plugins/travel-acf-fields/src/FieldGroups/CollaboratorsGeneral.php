<?php
/**
 * ACF Field Group: Collaborators - General
 *
 * Campos para el CPT Collaborator (miembros del equipo)
 *
 * @package Aurora\ACFKit\FieldGroups
 * @since 1.0.0
 */

namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class CollaboratorsGeneral extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_collaborators_general',
            'title' => '👥 Collaborator - Information',
            'fields' => [

                // ===== LAST NAME =====
                [
                    'key' => 'field_collaborator_last_name',
                    'label' => 'Last Name',
                    'name' => 'last_name',
                    'type' => 'text',
                    'instructions' => 'Collaborator\'s last name.',
                    'required' => 1,
                ],

                // ===== JOB =====
                [
                    'key' => 'field_collaborator_job',
                    'label' => '💼 Job Title',
                    'name' => 'job',
                    'type' => 'text',
                    'instructions' => 'Current job title or position.',
                    'required' => 1,
                    'placeholder' => 'e.g., Travel Advisor, Tour Guide',
                ],

                // ===== DESCRIPTION =====
                [
                    'key' => 'field_collaborator_description',
                    'label' => '📝 Description',
                    'name' => 'description',
                    'type' => 'wysiwyg',
                    'instructions' => 'Professional description or bio.',
                    'required' => 1,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                ],

                // ===== HOBBIES =====
                [
                    'key' => 'field_collaborator_hobbies',
                    'label' => '🎨 Hobbies',
                    'name' => 'hobbies',
                    'type' => 'wysiwyg',
                    'instructions' => 'Personal hobbies and interests.',
                    'required' => 1,
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],

            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'collaborator',
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
