<?php
namespace Travel\Components\Blocks\DownloadableGuide;

if (!defined('ABSPATH')) exit;

class Block {
  public function __construct() {
    add_action('init', [$this, 'register_block']);
  }

  public function register_block() {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
      'name'            => strtolower('DownloadableGuide'),
      'title'           => __('Downloadable Guide', 'block-travel'),
      'render_template' => plugin_dir_path(__FILE__) . 'render.php',
      'category'        => 'formatting',
      'icon'            => 'download',
      'keywords'        => ['acf','downloadableguide','travel'],
      'enqueue_assets'  => function() {
        $handle = 'tc-downloadableguide';
        $dir    = plugin_dir_url(__FILE__);
        $path   = plugin_dir_path(__FILE__);

        // Encolar CSS si existe
        $css_file = $path . 'style.css';
        if (file_exists($css_file)) {
          wp_enqueue_style($handle, $dir . 'style.css', [], '1.0.0');
        }

        // Encolar JS si existe
        $js_file = $path . 'script.js';
        if (file_exists($js_file)) {
          wp_enqueue_script($handle, $dir . 'script.js', [], '1.0.0', true);

          // Pasar variables al JS
          wp_localize_script($handle, 'TC_DownloadableGuide', [
            'restUrl' => esc_url_raw(rest_url('travel/v1/generate-pdf')), // endpoint PDF
            'pluginUrl' => TC_PLUGIN_URL, // URL raíz del plugin, por si la necesitas en JS
            'nonce'   => wp_create_nonce('wp_rest'),
          ]);
        }
      }
    ]);
  }
}
