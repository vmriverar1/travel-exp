<?php
/**
 * ACF Field Group: Taxonomy - FAQ
 *
 * Campos personalizados para los términos de la taxonomía FAQ
 * Incluye Título (identificador interno) y Pregunta (pregunta completa)
 * La Descripción nativa de WP se usa para la respuesta
 *
 * @package Aurora\ACFKit\FieldGroups
 * @since 1.0.0
 */

namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class TaxonomyFAQ extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_faq_taxonomy',
            'title' => 'FAQ Taxonomy Fields',
            'fields' => [

                // ===== FEATURED IMAGE =====
                [
                    'key' => 'field_faq_featured_image',
                    'label' => '🖼️ Imagen Destacada',
                    'name' => 'featured_image',
                    'type' => 'image',
                    'instructions' => 'Imagen destacada para este FAQ (opcional).',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],

                // ===== PREGUNTA COMPLETA =====
                [
                    'key' => 'field_faq_pregunta',
                    'label' => 'Pregunta Completa',
                    'name' => 'pregunta',
                    'type' => 'textarea',
                    'instructions' => 'La pregunta completa que se mostrará en el acordeón FAQ',
                    'required' => 1,
                    'rows' => 2,
                    'placeholder' => 'Ejemplo: ¿Cuáles son las opciones de pago disponibles?',
                ],

                // ===== RESPUESTA =====
                [
                    'key' => 'field_faq_respuesta',
                    'label' => 'Respuesta',
                    'name' => 'respuesta',
                    'type' => 'wysiwyg',
                    'instructions' => 'La respuesta completa a la pregunta (soporta formato, enlaces, listas, etc.)',
                    'required' => 1,
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],

            ],
            'location' => [
                [
                    [
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'faq',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'acf_after_title',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => ['description'],
            'active' => true,
            'description' => 'Campos personalizados para la taxonomía FAQ. Usa el campo Nombre para el título, el campo Pregunta para la pregunta completa, y el campo Descripción para la respuesta.',
            'show_in_rest' => 1,
        ]);
    }
}
