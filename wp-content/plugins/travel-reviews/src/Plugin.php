<?php
namespace Travel\Reviews;

use Travel\Reviews\Shortcode\ReviewsShortcode;

class Plugin {
  public function init() {
    add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
    (new ReviewsShortcode())->register();
  }

  public function enqueueAssets() {
  $url = plugin_dir_url(__FILE__) . 'Assets/';

  // 🌀 Encola Swiper (desde CDN)
  wp_enqueue_style(
    'swiper-css',
    'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
    [],
    '11.1'
  );
  wp_enqueue_script(
    'swiper-js',
    'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
    [],
    '11.1',
    true
  );

  // 🧩 Tus assets del plugin
  wp_enqueue_style('travel-reviews', $url . 'reviews.css', ['swiper-css'], '1.0');
  wp_enqueue_script('travel-reviews', $url . 'reviews.js', ['swiper-js'], '1.0', true);

  wp_localize_script('travel-reviews', 'TRAVEL_REVIEWS', [
    'api' => '    '
  ]);
}

}
