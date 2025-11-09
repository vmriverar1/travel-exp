<?php
namespace Travel\Overview;

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/Shortcode/TocShortcode.php';
use Travel\Overview\Shortcode\TocShortcode;

class Plugin {
  public function register() {
    add_action('init', [$this, 'init_shortcode']);
    add_action('wp_enqueue_scripts', [$this, 'register_assets']);
  }

  public function init_shortcode() {
    (new TocShortcode())->register();
  }

  public function register_assets() {
    $base_url = plugin_dir_url(__FILE__);
    wp_register_script('travel-toc-scroll', $base_url . 'Assets/js/scroll.js', [], '1.2.0', true);
    wp_register_style('travel-toc-style', $base_url . 'Assets/css/style.css', [], '1.2.0');
  }
}
