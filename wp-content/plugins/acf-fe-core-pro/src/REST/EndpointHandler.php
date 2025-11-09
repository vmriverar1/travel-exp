<?php

namespace ACF\FECP\REST;

use ACF\FECP\Services\LeadService;

if (!defined('ABSPATH')) exit;

class EndpointHandler
{
  /**
   * Registra el endpoint REST en el hook correcto.
   * Este método debe ejecutarse dentro del hook rest_api_init
   */
  public function register()
  {
    error_log('✅ EndpointHandler->register() ejecutado');

    register_rest_route('acf-fecp/v1', '/submit', [
      'methods'             => 'POST',
      'callback'            => [$this, 'submit'],
      'permission_callback' => '__return_true',
    ]);

    error_log('✅ Ruta /acf-fecp/v1/submit registrada');
  }

  /**
   * Callback principal del endpoint /acf-fecp/v1/submit
   */
  public function submit($req)
  {
    error_log('🚀 Entró a submit()');

    // Obtener campos del body
    $fields = (array) $req->get_param('fields');

    // Asegurar campo type_lead
    if (empty($fields['type_lead'])) {
      $fields['type_lead'] = !empty($fields['agent']) ? 1 : 0;
    }

    // Procesar con LeadService
    $service = new LeadService();
    $res = $service->handle($fields);

    error_log('📬 FECP response: ' . print_r($res, true));

    return rest_ensure_response($res);
  }
}
