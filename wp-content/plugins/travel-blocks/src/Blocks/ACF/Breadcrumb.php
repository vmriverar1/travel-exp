<?php

/**

 * Block: Breadcrumb (Migas de Pan)

 *

 * Generates automatic breadcrumb navigation based on page context.

 * Supports singular pages, archives, taxonomies, search, and 404 pages.

 * Provides contextual navigation trail: Home > Archive/Category > Current Page

 *

 * @package Travel\Blocks\ACF

 * @since 1.0.0

 * @version 1.1.0 - Refactored: divided long methods, improved architecture

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

     *

     * Loads CSS styles for breadcrumb navigation.

     *

     * @return void

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

     *

     * Registers ACF block type and defines field group with settings:

     * - show_home: Toggle home link visibility

     * - separator: Choose breadcrumb separator symbol

     * - text_color: Select breadcrumb color scheme

     *

     * @return void

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

     *

     * Generates and displays breadcrumb navigation based on current page context.

     * Automatically detects if user is on singular page, archive, search, or 404

     * and builds appropriate breadcrumb trail.

     *

     * @param array $block Block settings and attributes

     * @param string $content Block content (unused)

     * @param bool $is_preview Whether block is being previewed in editor

     * @param int $post_id Current post ID

     * @return void

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

     * Get breadcrumb items based on current page context.

     *

     * Generates hierarchical breadcrumb trail based on WordPress

     * query context (singular, archive, search, 404).

     *

     * @param bool $show_home Whether to include home link

     * @return array Array of breadcrumb items with title, url, and current status

     */

    private function get_breadcrumb_items(bool $show_home = true): array

    {

        $items = [];



        // Add home link

        if ($show_home) {

            $items[] = $this->get_home_item();

        }



        // Add context-specific breadcrumbs

        if (is_singular()) {

            $items = array_merge($items, $this->get_singular_breadcrumbs());

        } elseif (is_archive()) {

            $items = array_merge($items, $this->get_archive_breadcrumbs());

        } elseif (is_search()) {

            $items[] = $this->get_search_breadcrumb();

        } elseif (is_404()) {

            $items[] = $this->get_404_breadcrumb();

        }



        return $items;

    }



    /**

     * Get home breadcrumb item.

     *

     * @return array Home breadcrumb data

     */

    private function get_home_item(): array

    {

        return [

            'title' => __('Inicio', 'travel-blocks'),

            'url' => home_url('/'),

            'current' => is_front_page(),

        ];

    }



    /**

     * Get breadcrumbs for singular pages (posts, pages, custom post types).

     *

     * @return array Array of breadcrumb items

     */

    private function get_singular_breadcrumbs(): array

    {

        global $post;

        $items = [];



        $post_type = get_post_type();

        $post_type_object = get_post_type_object($post_type);



        // Add post type archive (if exists and not 'post')

        if ($post_type !== 'post' && $post_type_object && $post_type_object->has_archive) {

            $items[] = [

                'title' => $post_type_object->labels->name,

                'url' => get_post_type_archive_link($post_type),

                'current' => false,

            ];

        }



        // Add category for regular posts

        if ($post_type === 'post') {

            $category_item = $this->get_post_category_breadcrumb();

            if ($category_item) {

                $items[] = $category_item;

            }

        } else {

            // Add taxonomy term for custom post types

            $taxonomy_item = $this->get_first_taxonomy_breadcrumb($post_type, $post->ID);

            if ($taxonomy_item) {

                $items[] = $taxonomy_item;

            }

        }



        // Add current page

        $items[] = [

            'title' => get_the_title(),

            'url' => get_permalink(),

            'current' => true,

        ];



        return $items;

    }



    /**

     * Get breadcrumbs for archive pages.

     *

     * @return array Array of breadcrumb items

     */

    private function get_archive_breadcrumbs(): array

    {

        $items = [];



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



        return $items;

    }



    /**

     * Get search results breadcrumb.

     *

     * @return array Search breadcrumb data

     */

    private function get_search_breadcrumb(): array

    {

        return [

            'title' => __('Resultados de búsqueda para: ', 'travel-blocks') . get_search_query(),

            'url' => '',

            'current' => true,

        ];

    }



    /**

     * Get 404 error breadcrumb.

     *

     * @return array 404 breadcrumb data

     */

    private function get_404_breadcrumb(): array

    {

        return [

            'title' => __('Página no encontrada', 'travel-blocks'),

            'url' => '',

            'current' => true,

        ];

    }



    /**

     * Get category breadcrumb for regular posts.

     *

     * @return array|null Category breadcrumb or null if no category

     */

    private function get_post_category_breadcrumb(): ?array

    {

        $categories = get_the_category();



        if (empty($categories)) {

            return null;

        }



        $category = $categories[0];

        return [

            'title' => $category->name,

            'url' => get_category_link($category->term_id),

            'current' => false,

        ];

    }



    /**

     * Get first public taxonomy term breadcrumb for custom post types.

     *

     * Uses early returns to reduce nesting and improve readability.

     *

     * @param string $post_type The post type

     * @param int $post_id The post ID

     * @return array|null Taxonomy breadcrumb or null if no taxonomy found

     */

    private function get_first_taxonomy_breadcrumb(string $post_type, int $post_id): ?array

    {

        $taxonomies = get_object_taxonomies($post_type, 'objects');



        foreach ($taxonomies as $taxonomy) {

            // Skip non-public or hidden taxonomies

            if (!$taxonomy->public || !$taxonomy->show_ui) {

                continue;

            }



            $terms = get_the_terms($post_id, $taxonomy->name);



            // Skip if no terms or error

            if (!$terms || is_wp_error($terms)) {

                continue;

            }



            // Return first valid term

            $term = array_shift($terms);

            return [

                'title' => $term->name,

                'url' => get_term_link($term),

                'current' => false,

            ];

        }



        return null;

    }

}

