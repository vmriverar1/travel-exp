<?php
/**
 * Plugin Name: Travel Package Wizard
 * Plugin URI: https://machupicchuperu.com
 * Description: Modern wizard-style interface for creating and editing travel packages
 * Version: 1.0.0
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
define('TRAVEL_PACKAGE_WIZARD_VERSION', '1.0.0');
define('TRAVEL_PACKAGE_WIZARD_PATH', plugin_dir_path(__FILE__));
define('TRAVEL_PACKAGE_WIZARD_URL', plugin_dir_url(__FILE__));
define('TRAVEL_PACKAGE_WIZARD_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Aurora_Package_Builder {

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
        require_once TRAVEL_PACKAGE_WIZARD_PATH . 'includes/class-wizard-controller.php';
        require_once TRAVEL_PACKAGE_WIZARD_PATH . 'includes/class-mock-data-generator.php';
        require_once TRAVEL_PACKAGE_WIZARD_PATH . 'includes/class-mock-data-admin.php';

        // Load WP-CLI commands if available
        if (defined('WP_CLI') && WP_CLI) {
            require_once TRAVEL_PACKAGE_WIZARD_PATH . 'includes/class-wp-cli-commands.php';
        }
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Initialize wizard controller
        add_action('plugins_loaded', [$this, 'init_wizard']);

        // Initialize mock data admin
        add_action('plugins_loaded', [$this, 'init_mock_admin']);

        // Add settings link to plugins page
        add_filter('plugin_action_links_' . TRAVEL_PACKAGE_WIZARD_BASENAME, [$this, 'add_settings_link']);
    }

    /**
     * Initialize wizard controller
     */
    public function init_wizard() {
        if (class_exists('Aurora_Wizard_Controller')) {
            Aurora_Wizard_Controller::get_instance();
        }
    }

    /**
     * Initialize mock data admin
     */
    public function init_mock_admin() {
        if (class_exists('Aurora_Mock_Data_Admin')) {
            Aurora_Mock_Data_Admin::get_instance();
        }
    }

    /**
     * Add settings link to plugins page
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('edit.php?post_type=package') . '">' . __('Packages', 'travel-package-wizard') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}

/**
 * Initialize the plugin
 */
function aurora_package_builder() {
    return Aurora_Package_Builder::get_instance();
}

// Start the plugin
aurora_package_builder();
