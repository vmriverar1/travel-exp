<?php

namespace Travel\Reviews;

use Travel\Reviews\Shortcode\ReviewsShortcode;

class Plugin
{
  private const VERSION = '2.6';

  public function init(): void
  {
    add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
    (new ReviewsShortcode())->register();
  }

  public function enqueueAssets(): void
  {
    $url = plugin_dir_url(__FILE__) . 'Assets/';

    // Swiper CSS
    wp_enqueue_style(
      'swiper-css',
      'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
      [],
      '11.1'
    );

    // Swiper JS
    wp_enqueue_script(
      'swiper-js',
      'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
      [],
      '11.1',
      true
    );

    // Plugin CSS
    wp_enqueue_style(
      'travel-reviews',
      $url . 'reviews.css',
      ['swiper-css'],
      self::VERSION
    );

    // Plugin JS
    wp_enqueue_script(
      'travel-reviews',
      $url . 'reviews.js',
      ['swiper-js'],
      self::VERSION,
      true
    );
  }
}
