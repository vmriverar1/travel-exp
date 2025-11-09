<?php

namespace ValenciaTravel\Theme\App;

class Enqueue {

  public function register_hooks() {
    add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
  }

  public function enqueue_styles() {

    // === Helper para resolver rutas ===
    $resolve_path = function($relative) {
      $primary   = get_template_directory() . '/assets/' . $relative;
      $secondary = get_template_directory() . '/src/assets/' . $relative;

      if (file_exists($primary)) {
        return [
          'path' => $primary,
          'uri'  => get_template_directory_uri() . '/assets/' . $relative,
        ];
      } elseif (file_exists($secondary)) {
        return [
          'path' => $secondary,
          'uri'  => get_template_directory_uri() . '/src/assets/' . $relative,
        ];
      }

      return false;
    };

    // === CSS global ===
    $global = $resolve_path('css/global.min.css');
    if ($global) {
      wp_enqueue_style(
        'valenciatravel-global',
        $global['uri'],
        [],
        filemtime($global['path'])
      );
    }

    // === CSS por plantilla ===
    if (is_page()) {
      global $post;
      $slug = $post->post_name;
      $slug_css = $resolve_path("css/{$slug}.min.css");

      if ($slug_css) {
        wp_enqueue_style(
          "valenciatravel-{$slug}",
          $slug_css['uri'],
          ['valenciatravel-global'],
          filemtime($slug_css['path'])
        );
      }
    }

    // === CSS para home ===
    if (is_front_page() || is_home()) {
      $home = $resolve_path('css/home.min.css');
      if ($home) {
        wp_enqueue_style(
          'valenciatravel-home',
          $home['uri'],
          ['valenciatravel-global'],
          filemtime($home['path'])
        );
      }
    }
  }
}
