<?php
/**
 * Plugin Name: Travel Category Banner
 * Description: Bloques Gutenberg (dinámico y estático) con Swiper para banners de categorías/terminos y carrusel de paquetes en oferta.
 * Version: 1.0.0
 * Author: Attach / Travel
 * Text Domain: travel-category-banner
 */

if (!defined('ABSPATH')) { exit; }

require_once __DIR__ . '/vendor/autoload.php';

// Fallback simple si no existe autoloader (por si Composer no se ejecutó)
spl_autoload_register(function($class){
    $prefix = 'Travel\\CategoryBanner\\';
    $base_dir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

add_action('plugins_loaded', function () {
    (new \Travel\CategoryBanner\Plugin())->init();
});

if (!defined('TRAVEL_CATEGORY_BANNER_FILE')) define('TRAVEL_CATEGORY_BANNER_FILE', __FILE__);
