<?php
/**
 * Plugin Name: Travel Latest Posts Block
 * Description: Bloque Gutenberg (ACF Pro) para mostrar posts seleccionados/recientes. Grid en desktop y slider en móvil (Swiper, condicional).
 * Version: 1.0.0
 * Author: Travel
 * License: GPL-2.0-or-later
 */
if (!defined('ABSPATH')) exit;

define('TRAVEL_LP_PATH', plugin_dir_path(__FILE__));
define('TRAVEL_LP_URL', plugin_dir_url(__FILE__));

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
  require_once __DIR__ . '/vendor/autoload.php';
}

add_action('plugins_loaded', function () {
  if (!function_exists('acf_register_block_type')) {
    add_action('admin_notices', function () {
      echo '<div class="notice notice-error"><p><strong>Travel Latest Posts Block:</strong> Requiere ACF Pro activo.</p></div>';
    });
    return;
  }
  (new \Travel\Latest\Plugin())->init();
});
