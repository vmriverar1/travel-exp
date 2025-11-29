<?php
/**
 * Plugin Name: Travel Components - Breadcrumb Block
 * Description: ACF Gutenberg block for dynamic breadcrumbs with repeater items, color and font controls.
 * Version: 1.1.0
 * Author: Travel Components
 */

if (!defined('ABSPATH')) exit;

// Forzar carga de ACF JSON local
add_filter('acf/settings/load_json', function($paths) {
  $paths[] = plugin_dir_path(__FILE__) . 'acf-json';
  return $paths;
});

/**
 * Registra el bloque Breadcrumb
 */
add_action('acf/init', function() {
  if (!function_exists('acf_register_block_type')) return;

  acf_register_block_type([
    'name'            => 'breadcrumb',
    'title'           => __('Breadcrumb', 'travel-components'),
    'description'     => __('Dynamic breadcrumb navigation block', 'travel-components'),
    'render_template' => __DIR__ . '/src/Blocks/Breadcrumb/render.php',
    'category'        => 'formatting',
    'icon'            => 'menu',
    'keywords'        => ['breadcrumb', 'navigation'],
    'enqueue_assets'  => function () {
      wp_enqueue_style('tc-breadcrumb', plugin_dir_url(__FILE__) . 'src/Blocks/Breadcrumb/style.css', [], '1.1.0');
    },
    'supports' => [
      'align' => false,
      'multiple' => true,
    ],
  ]);
});
