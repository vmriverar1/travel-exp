<?php
/**
 * Block: Breadcrumb (Migas de Pan)
 *
 * Muestra la ruta de navegación automáticamente
 * Inicio > Archivo/Categoría > Página actual
 *
 * @package Travel\Blocks\Blocks
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\ACF;

use Travel\Blocks\Core\BlockBase;

class Breadcrumb extends BlockBase
{
    public function __construct()
    {
        $this->name        = 'breadcrumb';
        $this->title       = __('Breadcrumb (Migas de Pan)', 'travel-blocks');
        $this->description = __('Muestra la ruta de navegación automáticamente', 'travel-blocks');
        $this->category    = 'travel';
        $this->icon        = 'admin-home';
        $this->keywords    = ['breadcrumb', 'migas', 'navegacion', 'ruta'];
        $this->mode        = 'preview';

        $this->supports = [
            'align' => false,
            'mode'  => false,
            'multiple' => true,
            'anchor' => false,
        ];
    }

    /**
     * Enqueue block-specific assets.
     */
    public function enqueue_assets(): void
    {
        // Enqueue CSS
        wp_enqueue_style(
            'breadcrumb-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/breadcrumb.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );
    }

    /**
     * Register block and its ACF fields.
     */
    public function register(): void
    {
        parent::register();

        // Register ACF fields for this block
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_block_breadcrumb',
                'title' => __('Breadcrumb Settings', 'travel-blocks'),
                'fields' => [
                    [
                        'key' => 'field_breadcrumb_show_home',
                        'label' => __('Mostrar Inicio', 'travel-blocks'),
                        'name' => 'show_home',
                        'type' => 'true_false',
                        'instructions' => __('Mostrar enlace a la página de inicio', 'travel-blocks'),
                        'default_value' => 1,
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_breadcrumb_separator',
                        'label' => __('Separador', 'travel-blocks'),
                        'name' => 'separator',
                        'type' => 'select',
                        'instructions' => __('Símbolo separador entre items', 'travel-blocks'),
                        'choices' => [
                            '>' => '> (Mayor que)',
                            '/' => '/ (Slash)',
                            '→' => '→ (Flecha)',
                            '»' => '» (Doble mayor)',
                            '·' => '· (Punto medio)',
                        ],
                        'default_value' => '>',
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_breadcrumb_text_color',
                        'label' => __('Color del Texto', 'travel-blocks'),
                        'name' => 'text_color',
                        'type' => 'select',
                        'instructions' => __('Color del breadcrumb', 'travel-blocks'),
                        'choices' => [
                            'default' => __('Por defecto (gris)', 'travel-blocks'),
                            'primary' => __('Primario (rosa)', 'travel-blocks'),
                            'secondary' => __('Secundario (morado)', 'travel-blocks'),
                            'dark' => __('Oscuro', 'travel-blocks'),
                        ],
                        'default_value' => 'default',
                        'ui' => 1,
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/breadcrumb',
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * Render block content.
     */
    public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        try {
            // Get settings
            $show_home = get_field('show_home') ?? true;
            $separator = get_field('separator') ?: '>';
            $text_color = get_field('text_color') ?: 'default';

            // Block attributes
            $block_id = 'breadcrumb-' . ($block['id'] ?? uniqid());

            // Generate breadcrumb items
            $breadcrumb_items = $this->get_breadcrumb_items($show_home);

            // Pass data to template
            $data = [
                'block_id' => $block_id,
                'show_home' => $show_home,
                'separator' => $separator,
                'text_color' => $text_color,
                'items' => $breadcrumb_items,
                'is_preview' => $is_preview,
            ];

            $this->load_template('breadcrumb', $data);

        } catch (\Exception $e) {
            // Error handling
            if (defined('WP_DEBUG') && WP_DEBUG) {
                echo '<div style="padding: 10px; background: #ffebee; border: 1px solid #f44336;">';
                echo '<p>Error en Breadcrumb: ' . esc_html($e->getMessage()) . '</p>';
                echo '</div>';
            }
        }
    }

    /**
     * Get breadcrumb items based on current page context
     */
    private function get_breadcrumb_items(bool $show_home = true): array
    {
        $items = [];

        // Home
        if ($show_home) {
            $items[] = [
                'title' => __('Inicio', 'travel-blocks'),
                'url' => home_url('/'),
                'current' => is_front_page(),
            ];
        }

        // Single post/package
        if (is_singular()) {
            global $post;

            // Get post type
            $post_type = get_post_type();
            $post_type_object = get_post_type_object($post_type);

            // Add post type archive (if not 'post')
            if ($post_type !== 'post' && $post_type_object && $post_type_object->has_archive) {
                $items[] = [
                    'title' => $post_type_object->labels->name,
                    'url' => get_post_type_archive_link($post_type),
                    'current' => false,
                ];
            }

            // Add categories for posts
            if ($post_type === 'post') {
                $categories = get_the_category();
                if (!empty($categories)) {
                    $category = $categories[0];
                    $items[] = [
                        'title' => $category->name,
                        'url' => get_category_link($category->term_id),
                        'current' => false,
                    ];
                }
            }

            // Add taxonomies for custom post types (like package)
            if ($post_type !== 'post') {
                $taxonomies = get_object_taxonomies($post_type, 'objects');
                foreach ($taxonomies as $taxonomy) {
                    if ($taxonomy->public && $taxonomy->show_ui) {
                        $terms = get_the_terms($post->ID, $taxonomy->name);
                        if ($terms && !is_wp_error($terms)) {
                            $term = array_shift($terms);
                            $items[] = [
                                'title' => $term->name,
                                'url' => get_term_link($term),
                                'current' => false,
                            ];
                            break; // Only show first taxonomy
                        }
                    }
                }
            }

            // Current page
            $items[] = [
                'title' => get_the_title(),
                'url' => get_permalink(),
                'current' => true,
            ];
        }
        // Archive pages
        elseif (is_archive()) {
            if (is_post_type_archive()) {
                $post_type_object = get_queried_object();
                $items[] = [
                    'title' => $post_type_object->labels->name,
                    'url' => get_post_type_archive_link($post_type_object->name),
                    'current' => true,
                ];
            } elseif (is_category() || is_tag() || is_tax()) {
                $term = get_queried_object();
                $items[] = [
                    'title' => $term->name,
                    'url' => get_term_link($term),
                    'current' => true,
                ];
            }
        }
        // Search
        elseif (is_search()) {
            $items[] = [
                'title' => __('Resultados de búsqueda para: ', 'travel-blocks') . get_search_query(),
                'url' => '',
                'current' => true,
            ];
        }
        // 404
        elseif (is_404()) {
            $items[] = [
                'title' => __('Página no encontrada', 'travel-blocks'),
                'url' => '',
                'current' => true,
            ];
        }

        return $items;
    }
}
