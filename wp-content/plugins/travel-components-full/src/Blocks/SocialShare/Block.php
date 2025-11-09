<?php
namespace Travel\Components\Blocks\SocialShare;

if (!defined('ABSPATH')) exit;

class Block {

  public function __construct() {
    add_action('init', [$this, 'register_block']);
  }

  public function register_block() {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
      'name'            => 'socialshare',
      'title'           => __('Social Share', 'travel-components'),
      'render_template' => plugin_dir_path(__FILE__) . 'render.php',
      'category'        => 'formatting',
      'icon'            => 'share',
      'keywords'        => ['acf', 'social', 'share', 'travel'],
      'enqueue_assets'  => function() {
        $handle = 'tc-socialshare';
        $dir = plugin_dir_url(__FILE__);
        $path = plugin_dir_path(__FILE__);

        // CSS
        if (file_exists($path . 'style.css')) {
          wp_enqueue_style($handle, $dir . 'style.css', [], '1.0.0');
        }

        // JS
        if (file_exists($path . 'script.js')) {
          wp_enqueue_script($handle, $dir . 'script.js', [], '1.0.0', true);
          wp_localize_script($handle, 'TC_SocialShare', [
            'restUrl' => esc_url_raw(rest_url('acf-blocks/v1')),
            'nonce'   => wp_create_nonce('wp_rest'),
          ]);
        }
      }
    ]);
  }
}
