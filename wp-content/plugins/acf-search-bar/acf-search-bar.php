<?php
/**
 * Plugin Name: ACF Search Bar (Destinations)
 * Description: Bloque ACF + Gutenberg: buscador de destinos (taxonomy "destinations") con Select2 y Flatpickr. Envía a ?s= de WordPress.
 * Version: 1.0.0
 * Author: ChatGPT
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) { exit; }

// Composer autoload (si existe)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Autoloader PSR-4 mínimo por si no usan composer install (fallback).
spl_autoload_register(function($class) {
    $prefix = 'AcfSearchBar\\';
    $base_dir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

add_action('plugins_loaded', function() {
    if (!function_exists('acf_register_block_type')) {
        // ACF Pro es requerido para ACF blocks
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p><strong>ACF Search Bar:</strong> Requiere ACF PRO para registrar bloques.</p></div>';
        });
        return;
    }
    (new AcfSearchBar\Plugin())->init();
});
