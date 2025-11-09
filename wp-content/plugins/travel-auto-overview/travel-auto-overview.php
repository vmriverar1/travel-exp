<?php
/**
 * Plugin Name: Travel Auto Overview
 * Plugin URI: https://attachdevs.example
 * Description: Genera un índice automático (Quick Overview) basado en H2...H7 del contenido, con jerarquía y scroll animado. Shortcode: [travel_toc title="Quick Overview"]
 * Version: 1.0.0
 * Author: Attach Devs
 * License: GPL-2.0-or-later
 * Text Domain: travel-auto-overview
 */

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/src/Plugin.php';

// Bootstrap
(new \Travel\Overview\Plugin())->register();
