<?php
namespace ACF\FECP\Validation\Backend;
if (!defined('ABSPATH')) exit;
class CotizacionValidator extends BaseValidator {
  public static function check($fields){
    $f=self::sanitize($fields); $e=[];
    if (empty($f['nombre']) || !preg_match('/^[a-zA-Z\s]+$/',$f['nombre'])) $e['name']='Only letters allowed';
    if (isset($f['phone']) && $f['phone']!=='' && !preg_match('/^\+?\d{7,15}$/',$f['phone'])) $e['phone']='Invalid phone number';
    if (empty($f['modelo'])) $e['model']='Model is required';
    if (empty($f['version'])) $e['version']='Version is required';
    return $e;
  }
}
