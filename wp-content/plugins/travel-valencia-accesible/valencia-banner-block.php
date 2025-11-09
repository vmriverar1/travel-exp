<?php
/**
 * Plugin Name: Valencia Banner Block (v5)
 * Description: Bloque Gutenberg con imagen + 5 ítems superpuestos (ACF Pro) con condicionales, TinyMCE y diseño responsive.
 * Version: 1.5.0
 * Author: Attach / Rogger Palomino
 */

if (!defined('ABSPATH')) exit;

// === Registrar bloque ===
add_action('init', function () {
  register_block_type(__DIR__ . '/block.json');
});

// === Registrar campos ACF ===
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group(array(
    'key' => 'group_vtb_banner_v5',
    'title' => 'Team Banner (v5)',
    'fields' => array(
      // === Imagen de fondo ===
      array(
        'key' => 'field_bg_image',
        'label' => 'Imagen principal',
        'name' => 'background_image',
        'type' => 'image',
        'return_format' => 'array',
        'preview_size' => 'medium',
        'library' => 'all',
        'required' => 1
      ),
      array(
        'key' => 'field_bg_alt',
        'label' => 'Texto ALT',
        'name' => 'background_alt',
        'type' => 'text',
        'instructions' => 'Texto alternativo para la imagen (SEO/Accesibilidad).'
      ),

      // === Colores ===
      array(
        'key' => 'field_overlay_color',
        'label' => 'Color de fondo (overlay)',
        'name' => 'overlay_color',
        'type' => 'color_picker',
        'default_value' => '#2A2A2A'
      ),
      array(
        'key' => 'field_text_color',
        'label' => 'Color de texto',
        'name' => 'text_color',
        'type' => 'color_picker',
        'default_value' => '#FFFFFF'
      ),
      array(
        'key' => 'field_tint_opacity',
        'label' => 'Tinte sobre imagen (%)',
        'name' => 'tint_opacity',
        'type' => 'range',
        'min' => 0,
        'max' => 100,
        'default_value' => 50,
        'append' => '%'
      ),

      // === Repetidor de íconos ===
      array(
        'key' => 'field_icons',
        'label' => 'Íconos',
        'name' => 'icons',
        'type' => 'repeater',
        'layout' => 'row',
        'button_label' => 'Agregar ítem',
        'min' => 1,
        'max' => 5,
        'sub_fields' => array(

          // --- ICONO ---
          array(
            'key' => 'field_show_icon',
            'label' => '¿Mostrar icono?',
            'name' => 'show_icon',
            'type' => 'true_false',
            'ui' => 1,
            'default_value' => 1,
          ),
          array(
            'key' => 'field_icon',
            'label' => 'Icono',
            'name' => 'icon',
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'thumbnail',
            'library' => 'all',
            'conditional_logic' => array(
              array(
                array(
                  'field' => 'field_show_icon',
                  'operator' => '==',
                  'value' => '1'
                )
              )
            )
          ),

          // --- TÍTULO ---
          array(
            'key' => 'field_show_title',
            'label' => '¿Mostrar título?',
            'name' => 'show_title',
            'type' => 'true_false',
            'ui' => 1,
            'default_value' => 1,
          ),
          array(
            'key' => 'field_title',
            'label' => 'Título',
            'name' => 'title',
            'type' => 'text',
            'conditional_logic' => array(
              array(
                array(
                  'field' => 'field_show_title',
                  'operator' => '==',
                  'value' => '1'
                )
              )
            )
          ),

          // --- DESCRIPCIÓN (WYSIWYG) ---
          array(
            'key' => 'field_show_desc',
            'label' => '¿Mostrar descripción?',
            'name' => 'show_desc',
            'type' => 'true_false',
            'ui' => 1,
            'default_value' => 1,
          ),
          array(
            'key' => 'field_description',
            'label' => 'Descripción',
            'name' => 'description',
            'type' => 'wysiwyg',
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'delay' => 0,
            'conditional_logic' => array(
              array(
                array(
                  'field' => 'field_show_desc',
                  'operator' => '==',
                  'value' => '1'
                )
              )
            )
          )
        )
      )
    ),
    'location' => array(
      array(
        array(
          'param' => 'block',
          'operator' => '==',
          'value' => 'valencia/team-banner'
        )
      )
    )
  ));
});
