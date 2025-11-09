<?php
/**
 * Plugin Name: Travel Reviews
 * Description: Muestra reseñas por red social desde la API de Valencia Travel Cusco.
 * Version: 1.0
 * Author: Rogger Palomino
 * Text Domain: travel-reviews
 */

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/vendor/autoload.php';

use Travel\Reviews\Plugin;

add_action('plugins_loaded', function() {
    (new Plugin())->init();
});
