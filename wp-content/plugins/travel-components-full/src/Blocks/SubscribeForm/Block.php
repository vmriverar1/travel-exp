<?php
namespace Travel\Components\Blocks\SubscribeForm;
if (!defined('ABSPATH')) exit;
class Block {
  public function __construct() {
    add_action('init', [$this, 'register_block']);
  }
  public function register_block() {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
      'name'            => strtolower('SubscribeForm'),
      'title'           => __('Subscribe Form', 'travel-components'),
      'render_template' => plugin_dir_path(__FILE__) . 'render.php',
      'category'        => 'formatting',
      'icon'            => 'email',
      'keywords'        => ['acf','subscribeform','travel'],
      'enqueue_assets'  => function() {
        $handle = 'tc-subscribeform';
        $dir = plugin_dir_url(__FILE__);
        if (file_exists(plugin_dir_path(__FILE__) . 'style.css')) {
          wp_enqueue_style($handle, $dir . 'style.css', [], '1.0.1');
        }
        if (file_exists(plugin_dir_path(__FILE__) . 'script.js')) {
          wp_enqueue_script($handle, $dir . 'script.js', [], '1.0.1', true);
          wp_localize_script($handle, 'TC_SubscribeForm', [
            'restUrl' => esc_url_raw( rest_url('acf-blocks/v1') ),
            'nonce'   => wp_create_nonce('wp_rest'),
          ]);
        }
      }
    ]);
  }
}
