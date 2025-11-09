<?php
namespace Travel\Components;

use Dompdf\Dompdf;
use Dompdf\Options;

if (!defined('ABSPATH')) exit;

class RestRoutesPdf {
  public function register() {
    add_action('rest_api_init', [$this, 'register_routes']);
  }

  public function register_routes() {
    register_rest_route('travel/v1', '/generate-pdf', [
      'methods' => 'POST',
      'callback' => [$this, 'generate_pdf'],
      'permission_callback' => '__return_true'
    ]);
  }

  public function generate_pdf($req) {
    // Capturar contenido enviado desde JS
    $html = $req->get_param('html');

    // 🔹 LOG de depuración (se guarda en wp-content/debug.log)
    if (defined('WP_DEBUG') && WP_DEBUG) {
      error_log('🟢 [PDF GENERATE] HTML recibido: ' . substr(strip_tags($html), 0, 300)); // solo primeros 300 chars
    }

    // Verificar contenido
    if (empty($html)) {
      error_log('🔴 [PDF ERROR] No se recibió contenido HTML desde el fetch().');
      return new \WP_REST_Response(['error' => 'No content received from frontend'], 400);
    }

    // 🔹 Opciones DOMPDF
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');

    $dompdf = new Dompdf($options);

    // 🔹 Envolver HTML en una plantilla básica por si Gutenberg trae solo fragmentos
    $html_final = '
      <html>
      <head>
        <meta charset="UTF-8">
        <style>
          body { font-family: Helvetica, Arial, sans-serif; }
          img { max-width: 100%; height: auto; }
          h1,h2,h3,h4,h5,h6 { color: #222; margin-bottom: 8px; }
          p { margin-bottom: 10px; line-height: 1.4; }
        </style>
      </head>
      <body>' . $html . '</body></html>';

    // Cargar HTML en DOMPDF
    $dompdf->loadHtml($html_final);
    $dompdf->setPaper('A4', 'portrait');

    try {
      $dompdf->render();
    } catch (\Throwable $e) {
      error_log('🔴 [PDF RENDER ERROR] ' . $e->getMessage());
      return new \WP_REST_Response(['error' => 'Render failed: ' . $e->getMessage()], 500);
    }

    $pdf = $dompdf->output();

    // 🔹 Confirmar tamaño del PDF generado
    error_log('🟢 [PDF SUCCESS] Bytes generados: ' . strlen($pdf));

    // Forzar descarga directa
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="travel-guide.pdf"');
    echo $pdf;
    exit;
  }
}
