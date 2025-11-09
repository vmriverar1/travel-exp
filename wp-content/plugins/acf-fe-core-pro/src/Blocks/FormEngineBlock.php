<?php

namespace ACF\FECP\Blocks;

use ACF\FECP\Services\TemplateRegistry;

if (!defined('ABSPATH')) exit;

class FormEngineBlock
{
  public function register()
  {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
      'name'            => 'fecp-form',
      'title'           => __('Form Engine Core', 'fecp'),
      'description'     => __('Bloque de formularios con templates y validación.', 'fecp'),
      'render_callback' => [$this, 'render'],
      'category'        => 'widgets',
      'icon'            => 'feedback',
      'supports'        => ['align' => false],

      // 🔹 Carga de assets solo en frontend
      'enqueue_assets'  => function () {
        if (is_admin()) return;

        $form_type = get_field('form_type') ?: 'contacto';
        $base_path = plugin_dir_path(__DIR__) . '../assets/css/';
        $base_url  = plugin_dir_url(__DIR__) . '../assets/css/';

        // CSS base
        $global_css = $base_path . 'validator.css';
        if (file_exists($global_css)) {
          wp_enqueue_style('fecp-validator', $base_url . 'validator.css', [], filemtime($global_css));
        }

        // CSS específico del tipo de formulario
        $type_css = "{$base_path}{$form_type}.css";
        if (file_exists($type_css)) {
          wp_enqueue_style("fecp-form-{$form_type}", "{$base_url}{$form_type}.css", ['fecp-validator'], filemtime($type_css));
        }

        // JS global del form handler
        $handler_path = plugin_dir_path(__DIR__) . '../assets/js/fecc-form-handler.js';
        $handler_url  = plugin_dir_url(__DIR__) . '../assets/js/fecc-form-handler.js';

        if (file_exists($handler_path)) {
          wp_enqueue_script('fecp-form-handler', $handler_url, [], filemtime($handler_path), true);

          // 🔸 Variables iniciales (sin base_uri todavía)
          $rest_url = esc_url_raw(trailingslashit(home_url()) . 'wp-json/acf-fecp/v1/submit');
          $keys = [
            'recaptcha_site_key' => defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : '',
            'rest_url' => $rest_url,
            'nonce'    => wp_create_nonce('wp_rest'),
            'post_id'  => get_the_ID(),
          ];

          wp_localize_script('fecp-form-handler', 'FECP_KEYS', $keys);

          // if (defined('WP_DEBUG') && WP_DEBUG) {
          //   error_log('[FECP DEBUG] FECP_KEYS iniciales: ' . print_r($keys, true));
          // }
        }

        // reCAPTCHA externo
        if (defined('RECAPTCHA_SITE_KEY') && RECAPTCHA_SITE_KEY) {
          wp_enqueue_script('google-recaptcha-v3', 'https://www.google.com/recaptcha/api.js?render=' . RECAPTCHA_SITE_KEY, [], null, true);
        }
      },
    ]);

    // Carga dinámica de opciones del select "form_type"
    add_filter('acf/load_field/name=form_type', function ($field) {
      $field['choices'] = TemplateRegistry::list_form_types();
      if (empty($field['default_value'])) {
        $keys = array_keys($field['choices']);
        $field['default_value'] = reset($keys);
      }
      return $field;
    }, 11);
  }

  /**
   * Render del bloque (inyecta base_uri + endpoint sin sobrescribir FECP_KEYS)
   */
  public function render($block, $content = '', $is_preview = false, $post_id = 0)
  {
    $form_type = get_field('form_type') ?: 'contacto';
    $endpoint  = get_field('endpoint') ?: '';
    $base_uri  = get_field('base_uri') ?: '';

    // Encolar validadores según tipo
    if ($form_type === 'contacto') {
      wp_enqueue_script('fecp-val-contacto');
    } elseif ($form_type === 'cotizacion') {
      wp_enqueue_script('fecp-val-cotizacion');
    }

    // 🔸 Mezclar base_uri y endpoint con los datos ya existentes
    if (!is_admin() && wp_script_is('fecp-form-handler', 'enqueued')) {
      $wp_scripts = wp_scripts();
      $data = $wp_scripts->get_data('fecp-form-handler', 'data');
      $existing = [];

      if (!empty($data)) {
        // Extraer JSON del bloque "var FECP_KEYS = {...};"
        $decoded = json_decode(str_replace(['var FECP_KEYS = ', ';'], '', trim($data)), true);
        if (is_array($decoded)) {
          $existing = $decoded;
        }
      }

      // Fusionar con los nuevos valores
      $merged = array_merge($existing, [
        'base_uri' => $base_uri,
        'endpoint' => $endpoint,
      ]);

      wp_localize_script('fecp-form-handler', 'FECP_KEYS', $merged);

      // if (defined('WP_DEBUG') && WP_DEBUG) {
      //   error_log('[FECP DEBUG] FECP_KEYS fusionado final: ' . print_r($merged, true));
      // }

      // (Opcional) también puedes imprimir en consola para debug
      // add_action('wp_footer', function () use ($merged) {
      //   echo "<script>console.log('%c[FECP Debug]%c FECP_KEYS:', 'color: #4CAF50; font-weight:bold;', 'color: #2196F3;', " . wp_json_encode($merged) . ");</script>";
      // });
    }

    // CSS en el editor (modo preview)
    $base_path = plugin_dir_path(__DIR__) . '../assets/css/';
    $base_url  = plugin_dir_url(__DIR__) . '../assets/css/';
    $global_css = $base_path . 'validator.css';
    $type_css   = "{$base_path}{$form_type}.css";

    if ($is_preview) {
      if (file_exists($global_css)) {
        printf('<link rel="stylesheet" href="%s" />', esc_url("{$base_url}validator.css"));
      }
      if (file_exists($type_css)) {
        printf('<link rel="stylesheet" href="%s" />', esc_url("{$base_url}{$form_type}.css"));
      }
    }

    // Render final del template
    $template = ACF_FECP_PATH . 'templates/' . sanitize_file_name($form_type) . '.php';
    if (!file_exists($template)) {
      echo '<p style="color:#d63638">Template not found for: ' . esc_html($form_type) . '</p>';
      return;
    }

    printf(
      '<div class="fecp-form-wrap" data-endpoint="%s" data-form-type="%s">',
      esc_attr($endpoint),
      esc_attr($form_type)
    );

    include $template;
    echo '</div>';
  }
}
