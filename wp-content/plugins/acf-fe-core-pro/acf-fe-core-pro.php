<?php
/**
 * Plugin Name: Travel Form Pro
 * Description: ACF block + templates (contacto, cotizacion), realtime JS validation (animated), REST (nonce), DB + CRUD (prepare). JSON only loaded (no duplicates).
 * Version: 1.3.3
 * Author: Rogger Andrés Palomino
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit;

define('ACF_FECP_PATH', plugin_dir_path(__FILE__));
define('ACF_FECP_URL',  plugin_dir_url(__FILE__));

/**
 * ==============================================
 * AUTOLOADER (Composer o fallback PSR-4 manual)
 * ==============================================
 */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
  require __DIR__ . '/vendor/autoload.php';
} else {
  spl_autoload_register(function ($class) {
    $prefix = 'ACF\\FECP\\';
    $base   = __DIR__ . '/src/';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
    $file = $base . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (file_exists($file)) require $file;
  });
}

/**
 * ==============================================
 * ACF JSON (guardar / cargar desde acf-json/)
 * ==============================================
 */
add_filter('acf/settings/save_json', function ($path) {
  $new_path = ACF_FECP_PATH . 'acf-json';
  error_log('🟢 [ACF JSON SAVE] ' . $new_path);
  return $new_path;
});

add_filter('acf/settings/load_json', function ($paths) {
  $paths[] = ACF_FECP_PATH . 'acf-json';
  error_log('🟣 [ACF JSON LOAD] ' . ACF_FECP_PATH . 'acf-json');
  return $paths;
});

/**
 * ==============================================
 * INSTALACIÓN DE BASE DE DATOS
 * ==============================================
 */
register_activation_hook(__FILE__, function () {
  (new ACF\FECP\DB\Installer())->install();
});

/**
 * ==============================================
 * REGISTRO DE ENDPOINT REST (💥 punto clave)
 * ==============================================
 */
add_action('rest_api_init', function () {
  error_log('📡 Hook rest_api_init → Registrando rutas REST');
  (new ACF\FECP\REST\EndpointHandler())->register();
});

/**
 * ==============================================
 * BOOTSTRAP GENERAL
 * ==============================================
 */
add_action('plugins_loaded', function () {

  // Admin
  if (is_admin()) {
    (new ACF\FECP\Admin\LeadEmailAction())->register();
    (new ACF\FECP\Admin\Menu())->register();
  }

  // Frontend blocks
  add_action('init', function () {
    (new ACF\FECP\Blocks\FormEngineBlock())->register();
  });

  // Assets JS/CSS
  add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('fecp-validator', ACF_FECP_URL . 'assets/css/validator.css', [], '1.3.3');

    // Handler principal del formulario
    wp_register_script('fecp-form-handler', ACF_FECP_URL . 'assets/js/fecc-form-handler.js', [], '1.3.3', true);
    wp_localize_script('fecp-form-handler', 'FECP', [
      'restUrl' => esc_url_raw(rest_url('acf-fecp/v1')),
      'nonce'   => wp_create_nonce('wp_rest'),
    ]);
    wp_enqueue_script('fecp-form-handler');

    // Validadores opcionales
    wp_register_script('fecp-val-contacto', ACF_FECP_URL . 'assets/js/validators/contacto.js', ['fecp-form-handler'], '1.3.3', true);
    wp_register_script('fecp-val-cotizacion', ACF_FECP_URL . 'assets/js/validators/cotizacion.js', ['fecp-form-handler'], '1.3.3', true);
  });

  // Servicios (mailer, etc.)
  (new ACF\FECP\Services\MailerService())->register();
});
