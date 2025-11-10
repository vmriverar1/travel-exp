<?php
/**
 * API Import Admin Page
 *
 * Admin interface for importing packages from Valencia Travel API.
 * Phase 1: Basic UI structure with inputs, progress bar, and logs.
 *
 * @package Travel\Blocks\Admin
 */

namespace Travel\Blocks\Admin;

defined('ABSPATH') || exit;

class ApiImportAdmin
{
    /**
     * Menu slug for the admin page.
     */
    private const MENU_SLUG = 'travel-api-import';

    /**
     * Capability required to access this page.
     */
    private const REQUIRED_CAPABILITY = 'manage_options';

    /**
     * Initialize the admin page.
     */
    public static function init(): void
    {
        add_action('admin_menu', [__CLASS__, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    /**
     * Register the admin menu page.
     */
    public static function register_admin_menu(): void
    {
        add_submenu_page(
            'edit.php?post_type=package',
            __('Importar desde API Valencia', 'travel-blocks'),
            __('Importar API', 'travel-blocks'),
            self::REQUIRED_CAPABILITY,
            self::MENU_SLUG,
            [__CLASS__, 'render_page']
        );
    }

    /**
     * Enqueue CSS and JavaScript assets for the admin page.
     */
    public static function enqueue_assets(string $hook): void
    {
        // Only load on our admin page
        if (strpos($hook, self::MENU_SLUG) === false) {
            return;
        }

        // Enqueue CSS
        wp_enqueue_style(
            'travel-api-import',
            TRAVEL_BLOCKS_URL . 'assets/css/admin/api-import.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        // Enqueue JavaScript
        wp_enqueue_script(
            'travel-api-import',
            TRAVEL_BLOCKS_URL . 'assets/js/admin/api-import.js',
            ['jquery'],
            TRAVEL_BLOCKS_VERSION,
            true
        );

        // Pass data to JavaScript
        wp_localize_script('travel-api-import', 'travelApiImport', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('travel_api_import'),
            'i18n' => [
                'processing' => __('Procesando...', 'travel-blocks'),
                'success' => __('Importación completada', 'travel-blocks'),
                'error' => __('Error en la importación', 'travel-blocks'),
                'validating' => __('Validando IDs...', 'travel-blocks'),
                'noIds' => __('Por favor ingresa al menos un ID de tour', 'travel-blocks'),
                'invalidIds' => __('Algunos IDs no son válidos. Por favor verifica.', 'travel-blocks'),
            ]
        ]);
    }

    /**
     * Render the admin page HTML.
     */
    public static function render_page(): void
    {
        if (!current_user_can(self::REQUIRED_CAPABILITY)) {
            wp_die(__('No tienes permisos para acceder a esta página.', 'travel-blocks'));
        }

        ?>
        <div class="wrap travel-api-import-wrap">
            <h1><?php _e('Importar Paquetes desde API Valencia', 'travel-blocks'); ?></h1>

            <div class="travel-api-import-container">
                <!-- Left Panel: Form -->
                <div class="travel-api-import-panel travel-api-import-form-panel">
                    <div class="travel-api-card">
                        <h2><?php _e('IDs de Tours a Importar', 'travel-blocks'); ?></h2>
                        <p class="description">
                            <?php _e('Ingresa los IDs de tours separados por comas, espacios o saltos de línea. Ejemplo: 125, 126, 127', 'travel-blocks'); ?>
                        </p>

                        <form id="travel-api-import-form" method="post">
                            <div class="form-field">
                                <label for="tour_ids">
                                    <?php _e('IDs de Tours', 'travel-blocks'); ?>
                                    <span class="required">*</span>
                                </label>
                                <textarea
                                    id="tour_ids"
                                    name="tour_ids"
                                    rows="8"
                                    class="large-text"
                                    placeholder="125, 126, 127&#10;128&#10;129, 130"
                                    required
                                ></textarea>
                                <p class="tour-ids-preview" style="display: none;">
                                    <span class="dashicons dashicons-info"></span>
                                    <span class="preview-text"></span>
                                </p>
                            </div>

                            <div class="form-field">
                                <label>
                                    <input type="checkbox" id="update_existing" name="update_existing" value="1" checked>
                                    <?php _e('Actualizar paquetes existentes si ya existen', 'travel-blocks'); ?>
                                </label>
                                <p class="description">
                                    <?php _e('Si está marcado, se actualizarán los paquetes que ya existen. Si no, solo se crearán nuevos.', 'travel-blocks'); ?>
                                </p>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="button button-primary button-large" id="start_import_btn">
                                    <span class="dashicons dashicons-download"></span>
                                    <?php _e('Iniciar Importación', 'travel-blocks'); ?>
                                </button>
                                <button type="button" class="button button-secondary button-large" id="stop_import_btn" style="display: none;">
                                    <span class="dashicons dashicons-no"></span>
                                    <?php _e('Detener', 'travel-blocks'); ?>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Import Stats -->
                    <div class="travel-api-card travel-api-stats" style="display: none;">
                        <h3><?php _e('Estadísticas de Importación', 'travel-blocks'); ?></h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-label"><?php _e('Total', 'travel-blocks'); ?></span>
                                <span class="stat-value" id="stat_total">0</span>
                            </div>
                            <div class="stat-item stat-success">
                                <span class="stat-label"><?php _e('Exitosos', 'travel-blocks'); ?></span>
                                <span class="stat-value" id="stat_success">0</span>
                            </div>
                            <div class="stat-item stat-error">
                                <span class="stat-label"><?php _e('Errores', 'travel-blocks'); ?></span>
                                <span class="stat-value" id="stat_errors">0</span>
                            </div>
                            <div class="stat-item stat-skipped">
                                <span class="stat-label"><?php _e('Omitidos', 'travel-blocks'); ?></span>
                                <span class="stat-value" id="stat_skipped">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Progress & Logs -->
                <div class="travel-api-import-panel travel-api-import-logs-panel">
                    <div class="travel-api-card">
                        <h2><?php _e('Progreso y Registros', 'travel-blocks'); ?></h2>

                        <!-- Progress Bar -->
                        <div class="progress-container" style="display: none;">
                            <div class="progress-info">
                                <span class="progress-text"><?php _e('Preparando...', 'travel-blocks'); ?></span>
                                <span class="progress-percentage">0%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                            <p class="progress-details">
                                <span id="progress_current">0</span> / <span id="progress_total">0</span>
                                <?php _e('tours procesados', 'travel-blocks'); ?>
                            </p>
                        </div>

                        <!-- Logs Area -->
                        <div class="logs-container">
                            <div class="logs-header">
                                <h3><?php _e('Registro de Actividad', 'travel-blocks'); ?></h3>
                                <button type="button" class="button button-small" id="clear_logs_btn">
                                    <span class="dashicons dashicons-trash"></span>
                                    <?php _e('Limpiar', 'travel-blocks'); ?>
                                </button>
                            </div>
                            <div class="logs-content" id="import_logs">
                                <div class="log-entry log-info">
                                    <span class="log-time"><?php echo date('H:i:s'); ?></span>
                                    <span class="log-icon dashicons dashicons-info"></span>
                                    <span class="log-message"><?php _e('Sistema listo. Ingresa IDs de tours para comenzar.', 'travel-blocks'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
