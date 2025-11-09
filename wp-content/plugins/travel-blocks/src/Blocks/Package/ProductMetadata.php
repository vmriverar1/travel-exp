<?php
/**
 * Block: Product Metadata
 *
 * Displays product identity information:
 * - TripAdvisor rating badge
 * - Metadata line with: origin • difficulty • duration • type
 *
 * NATIVE WORDPRESS BLOCK - Does NOT use ACF
 * Gets data from Package post meta fields
 *
 * @package Travel\Blocks\Blocks\Package
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class ProductMetadata
{
    private string $name = 'product-metadata';
    private string $title = 'Product Metadata';
    private string $description = 'Muestra rating de TripAdvisor y metadata del producto (origen, dificultad, duración, tipo)';

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
            'icon' => 'star-filled',
            'keywords' => ['product', 'metadata', 'rating', 'tripadvisor', 'package'],
            'supports' => [
                'anchor' => true,
                'html' => false,
            ],
            'render_callback' => [$this, 'render'],
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
                'product-metadata-style',
                TRAVEL_BLOCKS_URL . 'assets/blocks/product-metadata.css',
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
            if ($is_preview) {
                // Datos de ejemplo para el editor
                $package_data = $this->get_preview_data();
            } else {
                // Datos reales del post
                $package_data = $this->get_post_data($post_id);
            }

            // Block attributes
            $block_id = 'product-metadata-' . uniqid();
            $class_name = 'product-metadata';

            if (!empty($attributes['className'])) {
                $class_name .= ' ' . $attributes['className'];
            }

            // Preparar datos para el template
            $data = [
                'block_id' => $block_id,
                'class_name' => $class_name,
                'package_data' => $package_data,
                'is_preview' => $is_preview,
                'show_tripadvisor' => true,
                'show_metadata' => true,
                'metadata_color' => 'default',
                'package_title' => $is_preview ? 'Package Title Preview' : get_the_title($post_id),
            ];

            // Renderizar template
            ob_start();
            $this->load_template('product-metadata', $data);
            return ob_get_clean();

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return '<div style="padding: 10px; background: #ffebee; border: 1px solid #f44336;">' .
                       '<p>Error en Product Metadata: ' . esc_html($e->getMessage()) . '</p>' .
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
            // TripAdvisor data
            'tripadvisor_rating' => 4.9,
            'tripadvisor_url' => '#',
            'total_reviews' => 1250,
            'show_rating_badge' => true,

            // Metadata line items
            'origin' => 'Cusco',
            'difficulty' => 'Moderate',
            'duration' => '4 days / 3 nights',
            'type' => 'Small Group',
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
        return [
            // TripAdvisor data
            'tripadvisor_rating' => floatval(get_post_meta($post_id, 'tripadvisor_rating', true)) ?: 0,
            'tripadvisor_url' => get_post_meta($post_id, 'tripadvisor_url', true) ?: '',
            'total_reviews' => intval(get_post_meta($post_id, 'total_reviews', true)) ?: 0,
            'show_rating_badge' => get_post_meta($post_id, 'show_rating_badge', true) !== '0',

            // Metadata line items
            'origin' => get_post_meta($post_id, 'departure', true) ?: get_post_meta($post_id, 'origin', true) ?: '',
            'difficulty' => get_post_meta($post_id, 'physical_difficulty', true) ?: get_post_meta($post_id, 'difficulty', true) ?: '',
            'duration' => get_post_meta($post_id, 'duration', true) ?: (get_post_meta($post_id, 'days', true) . ' days'),
            'type' => get_post_meta($post_id, 'service_type', true) ?: get_post_meta($post_id, 'type', true) ?: '',
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
