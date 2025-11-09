<?php
namespace ACF\FECP\Validation\Backend;
if (!defined('ABSPATH')) exit;
abstract class BaseValidator {
  public static function sanitize($arr){
    $out=[]; foreach((array)$arr as $k=>$v){ if(is_array($v)) continue; $out[$k]=sanitize_text_field($v); } return $out;
  }
}
