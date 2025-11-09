<?php
namespace Travel\Latest;

use Travel\Latest\Blocks\LatestPostsBlock;

class Plugin {
  public function init() {
    // ACF JSON save/load inside plugin
    add_filter('acf/settings/save_json', function ($path) { return TRAVEL_LP_PATH . 'acf-json'; });
    add_filter('acf/settings/load_json', function ($paths) { $paths[] = TRAVEL_LP_PATH . 'acf-json'; return $paths; });

    add_action('init', [$this, 'register_block']);
    add_action('init', [$this, 'register_assets']);
  }

  public function register_block() {
    if (!function_exists('acf_register_block_type')) return;

    \acf_register_block_type([
      'name'            => 'travel-latest-posts',
      'title'           => __('Travel Latest Posts', 'travel-lp'),
      'description'     => __('Cards de posts con slider móvil y grid desktop', 'travel-lp'),
      'category'        => 'widgets',
      'icon'            => 'format-gallery',
      'keywords'        => ['travel','posts','slider','grid'],
      'render_callback' => [new LatestPostsBlock(), 'render'],
      'enqueue_assets'  => function () {
        // CSS del bloque
        wp_enqueue_style('travel-lp-css');

        // Swiper sólo si no existe ya en la página
        if (!wp_script_is('swiper', 'registered') && !wp_script_is('swiper', 'enqueued')) {
          wp_register_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', [], '10.3.0', true);
          wp_register_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css', [], '10.3.0');
        }
        if (wp_style_is('swiper', 'registered') && !wp_style_is('swiper', 'enqueued')) {
          wp_enqueue_style('swiper');
        }

        // JS del bloque (depende de Swiper sólo si está registrado)
        $deps = [];
        if (wp_script_is('swiper', 'registered')) $deps[] = 'swiper';
        wp_enqueue_script('travel-lp-js', TRAVEL_LP_URL . 'assets/js/block.js', $deps, '1.0.0', true);
      },
      'supports'        => [
        'anchor' => true,
        'align'  => ['wide','full'],
      ],
      'mode'            => 'preview',
    ]);
  }

  public function register_assets() {
    wp_register_style('travel-lp-css', TRAVEL_LP_URL . 'assets/css/block.css', [], '1.0.0');
  }
}
