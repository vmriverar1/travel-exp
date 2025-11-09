<?php
/**
 * Plugin Name: Travel Components Blocks
 * Description: ACF Gutenberg Blocks (Social Share, Downloadable Guide, Subscribe Form) - PSR-4 Autoload
 * Author: Rogger Palomino Gamboa
 * Version: 1.1.0
 */

if (!defined('ABSPATH')) exit;

/**
 * ==============================================
 * Constantes base del plugin
 * ==============================================
 */
if (!defined('TC_PLUGIN_FILE')) {
  define('TC_PLUGIN_FILE', __FILE__); // Archivo principal
}

if (!defined('TC_PLUGIN_PATH')) {
  define('TC_PLUGIN_PATH', plugin_dir_path(__FILE__)); // Ruta absoluta
}

if (!defined('TC_PLUGIN_URL')) {
  define('TC_PLUGIN_URL', plugin_dir_url(__FILE__)); // URL del plugin
}

/**
 * ==============================================
 * Cargar Autoload de Composer
 * ==============================================
 */
if (file_exists(TC_PLUGIN_PATH . 'vendor/autoload.php')) {
  require_once TC_PLUGIN_PATH . 'vendor/autoload.php';
}

/**
 * ==============================================
 * Inicializar el plugin
 * ==============================================
 */
use Travel\Components\Plugin;

$plugin = new Plugin();
$plugin->register();

/**
 * ==============================================
 * Hooks de activación / desactivación
 * ==============================================
 */
register_activation_hook(TC_PLUGIN_FILE, [Plugin::class, 'activate']);
register_deactivation_hook(TC_PLUGIN_FILE, [Plugin::class, 'deactivate']);
