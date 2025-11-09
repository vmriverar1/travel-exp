<?php

namespace Travel\Components;

use Travel\Components\RestRoutesPdf;
use Travel\Components\RestRoutesPackagePdf;
use Travel\Components\Database\PackagePdfLeadsTable;
use Travel\Components\Blocks\SubscribeForm\Admin\AdminPageSubscribers;
use Travel\Components\Blocks\SubscribeForm\RestSubscribe;

if (!defined('ABSPATH')) exit;

class Plugin
{
  public function register()
  {
    /**
     * ==============================================
     * ACF JSON
     * ==============================================
     */
    add_action('plugins_loaded', function () {
      add_filter('acf/settings/load_json', [$this, 'load_json_path']);
      add_filter('acf/settings/save_json', [$this, 'save_json_path']);
    });

    /**
     * ==============================================
     * Bloques y Rutas REST
     * ==============================================
     */
    add_action('acf/init', [$this, 'register_blocks']);
    add_action('rest_api_init', [$this, 'register_rest']);

    /**
     * ==============================================
     * Página Admin de suscriptores
     * ==============================================
     */
    add_action('admin_init', function () {
      if (class_exists(AdminPageSubscribers::class)) {
        $admin = new AdminPageSubscribers();
        $admin->register();
      } else {
        error_log('⚠️ Clase AdminPageSubscribers no encontrada');
      }
    });

    /**
     * ==============================================
     * Ruta PDF (REST personalizada)
     * ==============================================
     */
    add_action('init', function () {
      if (class_exists(RestRoutesPdf::class)) {
        $restPdf = new RestRoutesPdf();
        $restPdf->register();
      } else {
        error_log('⚠️ Clase RestRoutesPdf no encontrada');
      }
    });

    /**
     * ==============================================
     * Ruta PDF Package (REST para packages)
     * ==============================================
     */
    add_action('rest_api_init', function () {
      if (class_exists(RestRoutesPackagePdf::class)) {
        $packagePdfRoutes = new RestRoutesPackagePdf();
        $packagePdfRoutes->register_routes();
      } else {
        error_log('⚠️ Clase RestRoutesPackagePdf no encontrada');
      }
    });

    /**
     * ==============================================
     * Enqueue PDF Modal Assets
     * ==============================================
     */
    add_action('wp_enqueue_scripts', function () {
      // Only load if promo cards block is being used
      wp_enqueue_style(
        'tc-pdf-modal',
        TC_PLUGIN_URL . 'assets/css/pdf-download-modal.css',
        [],
        filemtime(TC_PLUGIN_PATH . 'assets/css/pdf-download-modal.css')
      );

      wp_enqueue_script(
        'tc-pdf-modal',
        TC_PLUGIN_URL . 'assets/js/pdf-download-modal.js',
        [],
        filemtime(TC_PLUGIN_PATH . 'assets/js/pdf-download-modal.js'),
        true
      );
    });
  }

  /**
   * Cargar solo el JSON global
   */
  public function load_json_path($paths)
  {
    $paths[] = TC_PLUGIN_PATH . 'acf-json';
    return $paths;
  }

  /**
   * Guardar los JSON globalmente
   */
  public function save_json_path($path)
  {
    return TC_PLUGIN_PATH . 'acf-json';
  }

  /**
   * Registrar bloques
   */
  public function register_blocks()
  {
    $blocks = [
      'Travel\\Components\\Blocks\\SocialShare\\Block',
      'Travel\\Components\\Blocks\\DownloadableGuide\\Block',
      'Travel\\Components\\Blocks\\SubscribeForm\\Block',
    ];

    foreach ($blocks as $class) {
      if (class_exists($class)) {
        new $class();
      } else {
        error_log('⚠️ Clase no encontrada: ' . $class);
      }
    }
  }

  /**
   * Registrar rutas REST
   */
  public function register_rest()
  {
    if (class_exists(RestSubscribe::class)) {
      $subscribe_rest = new RestSubscribe();
      $subscribe_rest->register();
    } else {
      error_log('⚠️ Clase RestSubscribe no encontrada');
    }
  }

  /**
   * Activación: crear tablas necesarias
   */
  public static function activate()
  {
    global $wpdb;

    // Tabla de suscriptores
    $table = $wpdb->prefix . 'subscribers';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100),
      email VARCHAR(150),
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    // Tabla de PDF leads
    if (class_exists(PackagePdfLeadsTable::class)) {
      $leads_table = new PackagePdfLeadsTable();
      $leads_table->create_table();
    }
  }

  public static function deactivate() {}
}
