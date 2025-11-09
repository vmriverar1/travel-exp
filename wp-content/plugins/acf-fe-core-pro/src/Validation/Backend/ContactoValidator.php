<?php
namespace ACF\FECP\Validation\Backend;

if (!defined('ABSPATH')) exit;

/**
 * Validador Backend para Formulario de Contacto (Travel)
 */
class ContactoValidator extends BaseValidator {

  public static function check($fields) {
    $f = self::sanitize($fields);
    $e = [];

    // Nombre
    if (empty($f['nombre'])) {
      $e['nombre'] = 'First name is required.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $f['nombre'])) {
      $e['nombre'] = 'Only letters allowed.';
    }

    // Apellido
    if (empty($f['apellido'])) {
      $e['apellido'] = 'Last name is required.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $f['apellido'])) {
      $e['apellido'] = 'Only letters allowed.';
    }

    // Email
    if (empty($f['email'])) {
      $e['email'] = 'Email is required.';
    } elseif (!is_email($f['email'])) {
      $e['email'] = 'Invalid email format.';
    }

    // Phone (opcional)
    if (!empty($f['phone']) && !preg_match('/^\+?\d{7,15}$/', $f['phone'])) {
      $e['phone'] = 'Invalid phone number.';
    }

    // Selects obligatorios
    foreach (['country', 'holiday', 'destination', 'package'] as $key) {
      if (empty($f[$key])) {
        $e[$key] = ucfirst($key) . ' is required.';
      }
    }

    // Mensaje
    if (empty($f['mensaje']) || strlen(trim($f['mensaje'])) < 3) {
      $e['mensaje'] = 'Message too short.';
    }

    // Radio (agent)
    if (empty($f['agent']) || !in_array($f['agent'], ['yes', 'no'], true)) {
      $e['agent'] = 'Please select if you are a travel agent.';
    }

    // Checkbox (privacy)
    if (empty($f['privacy']) || $f['privacy'] !== '1') {
      $e['privacy'] = 'You must accept the privacy policy.';
    }

    return $e;
  }
}
