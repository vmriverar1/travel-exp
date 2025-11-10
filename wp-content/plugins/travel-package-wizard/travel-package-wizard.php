<?php
/**
 * Plugin Name: Travel Package Wizard
 * Plugin URI: https://machupicchuperu.com
 * Description: Mock data generator for travel packages and related content
 * Version: 2.0.0
 * Author: Rogger Palomino Gamboa
 * Author URI: https://machupicchuperu.com
 * Text Domain: travel-package-wizard
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('TRAVEL_PACKAGE_WIZARD_VERSION', '2.0.0');
define('TRAVEL_PACKAGE_WIZARD_PATH', plugin_dir_path(__FILE__));
define('TRAVEL_PACKAGE_WIZARD_URL', plugin_dir_url(__FILE__));
define('TRAVEL_PACKAGE_WIZARD_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Travel_Package_Wizard {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        // TODO: Load mock data generator classes here
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // TODO: Initialize plugin functionality
    }
}

/**
 * Initialize the plugin
 */
function travel_package_wizard() {
    return Travel_Package_Wizard::get_instance();
}

// Start the plugin
travel_package_wizard();
