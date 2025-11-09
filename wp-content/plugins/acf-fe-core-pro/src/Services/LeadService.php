<?php
// src/Services/LeadService.php
namespace ACF\FECP\Services;

use ACF\FECP\DB\LeadsRepository;
use ACF\FECP\Services\SpamAndRatingService;

class LeadService
{
  public function handle(array $fields)
  {
    global $wpdb;

    $post_id = intval($fields['post_id'] ?? 0);
    $base_uri = $fields['base_uri'];
    $endpoint = $fields['endpoint'];

    // Detectar si es agente y tipo de lead
    $is_agent = isset($fields['travel_agent']) && $fields['travel_agent'] === '1';
    $type_lead = $is_agent ? 1 : 0;

    // Construir repositorio según tipo
    $repo = new LeadsRepository($type_lead);

    $spamRatingService = new SpamAndRatingService();
    $risk = $spamRatingService->analyze($fields);

    $base_uri = $base_uri ?: (defined('FECP_BASE_URI') ? FECP_BASE_URI : '');
    $endpoint = $endpoint ?: '';

    // Normalizar nombres para tabla local
    $data = [
      'first_name'           => $fields['nombre'] ?? '',
      'last_name'            => $fields['apellido'] ?? '',
      'email'                => $fields['email'] ?? '',
      'phone'                => $fields['phone'] ?? '',
      'country_code'         => $fields['country'] ?? '',
      'holiday_type'         => $fields['holiday'] ?? '',
      'destination_interes'  => $fields['destination'] ?? '',
      'package'              => $fields['package'] ?? '',
      'package_link'         => $fields['package_link'],
      'description'          => $fields['mensaje'] ?? '',
      'travel_agent'         => $is_agent ? 1 : 0,
      'company'              => $fields['company'] ?? '',
      'type_lead'            => $type_lead,
      'endpoint'             => $endpoint,
      'base_uri'             => $base_uri,
      'rating'               => $risk['rating'],
      'score_spam'           => $risk['score_spam'],
    ];

    // Guardar en tabla local
    $id = $repo->insert($data);

    // Enviar al endpoint externo

    return ['ok' => true, 'id' => $id];
  }

}
