<?php
/**
 * Plugin Name: Travel Auto Overview
 * Description: TOC automático basado en H2..H7 con scroll suave. Shortcode: [travel_toc title="Quick Overview"]
 * Version: 1.2.0
 * Author: Attach Devs
 * Text Domain: travel-auto-overview
 */

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/src/Plugin.php';
(new \Travel\Overview\Plugin())->register();
