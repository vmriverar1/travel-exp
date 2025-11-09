<?php
/**
 * Block: Metadata Line
 *
 * Displays package metadata line with icons:
 * - Origin (departure city)
 * - Difficulty level
 * - Duration (days/nights)
 * - Service type (shared/private)
 *
 * NATIVE WORDPRESS BLOCK - Does NOT use ACF
 * Gets data from Package post meta fields
 *
 * @package Travel\Blocks\Blocks\Package
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class MetadataLine
{
    private string $name = 'metadata-line';
    private string $title = 'Metadata Line';
    private string $description = 'Muestra línea de metadata del paquete con iconos (origen, dificultad, duración, tipo)';

    /**
     * Register the block
     */
    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'travel',
            'icon' => 'info',
            'keywords' => ['metadata', 'package', 'info', 'duration', 'difficulty'],
            'supports' => [
                'anchor' => true,
                'html' => false,
            ],
            'render_callback' => [$this, 'render'],
            'show_in_rest' => true,
        ]);

        // Enqueue assets
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    /**
     * Enqueue block-specific assets
     */
    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            wp_enqueue_style(
                'metadata-line-style',
                TRAVEL_BLOCKS_URL . 'assets/blocks/metadata-line.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );
        }
    }

    /**
     * Render block content
     *
     * @param array $attributes Block attributes
     * @param string $content Block content
     * @param object $block Block object
     * @return string Rendered block HTML
     */
    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();

            // Detectar si estamos en el editor (preview mode)
            $is_preview = EditorHelper::is_editor_mode($post_id);

            // Obtener datos
            if ($is_preview || !$post_id) {
                // Datos de ejemplo para el editor
                $package_data = $this->get_preview_data();
            } else {
                // Datos reales del post
                $package_data = $this->get_post_data($post_id);
            }

            // Block attributes
            $block_id = 'metadata-line-' . uniqid();
            $class_name = 'metadata-line';

            if (!empty($attributes['className'])) {
                $class_name .= ' ' . $attributes['className'];
            }

            // Preparar datos para el template
            $data = [
                'block_id' => $block_id,
                'class_name' => $class_name,
                'package_data' => $package_data,
                'is_preview' => $is_preview,
                'metadata_color' => 'default',
            ];

            // Renderizar template
            ob_start();
            $this->load_template('metadata-line', $data);
            return ob_get_clean();

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return '<div style="padding: 10px; background: #ffebee; border: 1px solid #f44336;">' .
                       '<p>Error en Metadata Line: ' . esc_html($e->getMessage()) . '</p>' .
                       '</div>';
            }
            return '';
        }
    }

    /**
     * Get preview data (for editor)
     */
    private function get_preview_data(): array
    {
        return [
            'origin' => 'Cusco',
            'difficulty' => 'Moderate',
            'type' => 'Shared',
            'group_size' => 'Max 12 people',
            'languages' => 'English, Spanish',
        ];
    }

    /**
     * Get package metadata from post meta fields
     *
     * @param int $post_id Package post ID
     * @return array Package metadata
     */
    private function get_post_data(int $post_id): array
    {
        // Get group size and languages from quick_facts
        $quick_facts = get_post_meta($post_id, 'quick_facts', true);
        $group_size = '';
        $languages = '';

        if (is_array($quick_facts)) {
            foreach ($quick_facts as $fact) {
                if (is_array($fact)) {
                    $label = strtolower($fact['label'] ?? '');
                    if (strpos($label, 'group') !== false || strpos($label, 'size') !== false) {
                        $group_size = $fact['value'] ?? '';
                    }
                    if (strpos($label, 'language') !== false || strpos($label, 'idioma') !== false) {
                        $languages = $fact['value'] ?? '';
                    }
                }
            }
        }

        return [
            'origin' => get_post_meta($post_id, 'departure', true) ?: get_post_meta($post_id, 'origin', true) ?: '',
            'difficulty' => get_post_meta($post_id, 'physical_difficulty', true) ?: get_post_meta($post_id, 'difficulty', true) ?: '',
            'type' => get_post_meta($post_id, 'service_type', true) ?: get_post_meta($post_id, 'type', true) ?: '',
            'group_size' => $group_size,
            'languages' => $languages,
        ];
    }

    /**
     * Load block template with extracted variables
     *
     * @param string $template_name Template file name (without .php extension)
     * @param array  $data          Data to extract and pass to template
     */
    protected function load_template(string $template_name, array $data = []): void
    {
        $template_path = TRAVEL_BLOCKS_PATH . 'templates/' . $template_name . '.php';

        if (!file_exists($template_path)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                echo '<div style="padding:1rem;background:#fff3cd;border-left:4px solid #ffc107;">';
                echo '<strong>Template not found:</strong> ' . esc_html($template_name . '.php');
                echo '</div>';
            }
            return;
        }

        // Extract data to make variables available in template
        extract($data, EXTR_SKIP);

        include $template_path;
    }
}
