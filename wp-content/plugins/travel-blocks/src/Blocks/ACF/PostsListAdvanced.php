<?php

namespace Travel\Blocks\Blocks\ACF;

class PostsListAdvanced
{
    private string $name = 'acf-gbr-posts-list-advanced';

    public function __construct()
    {
        add_action('acf/init', [$this, 'register']);
    }

    /**
     * Registro del bloque Gutenberg
     */
    public function register(): void
    {
        acf_register_block_type([
            'name'            => $this->name,
            'title'           => __('Posts List Advanced (SSR + Swiper Mobile)', 'acf-gbr'),
            'description'     => __('Render SSR optimizado para SEO con Swiper solo en mobile.', 'acf-gbr'),
            'category'        => 'travel',
            'icon'            => 'slides',
            'keywords'        => ['posts', 'slider', 'grid', 'responsive'],
            'render_callback' => [$this, 'render'],
            'supports'        => [
                'align' => ['wide', 'full'],
                'mode' => true,
                'jsx' => true,
                'spacing' => [
                    'margin' => true,
                    'padding' => true,
                    'blockGap' => true,
                ],
                'color' => [
                    'background' => true,
                    'text' => true,
                    'gradients' => true,
                ],
                'typography' => [
                    'fontSize' => true,
                    'lineHeight' => true,
                ],
                'anchor' => true,
                'customClassName' => true,
            ],
            'enqueue_assets'  => function () {
                // CSS base
                wp_enqueue_style(
                    "{$this->name}-base",
                    TRAVEL_BLOCKS_URL . 'assets/blocks/PostsListAdvanced/style.css',
                    [],
                    TRAVEL_BLOCKS_VERSION
                );
            },
        ]);
    }

    /**
     * Render SSR + estructura HTML
     */
    public function render($block, $content = '', $is_preview = false, $post_id = 0): void
    {
        // Get WordPress block attributes
        $block_wrapper_attributes = get_block_wrapper_attributes([
            'class' => 'posts-list-advanced-wrapper'
        ]);

        // Detectar si está habilitado el Swiper Mobile
        $enable_swiper_mobile = (bool) get_field('pla_enable_swiper_mobile');

        if ($enable_swiper_mobile) {
            // Swiper core (ligero)
            wp_enqueue_style(
                'swiper',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
                [],
                '11.0.0'
            );

            wp_enqueue_script(
                'swiper',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
                [],
                '11.0.0',
                true
            );
            wp_script_add_data('swiper', 'async', true);
            wp_script_add_data('swiper', 'defer', true);


            // Script de inicialización condicional
            wp_enqueue_script(
                "{$this->name}-view",
                TRAVEL_BLOCKS_URL . 'assets/blocks/PostsListAdvanced/view-swiper.js',
                ['swiper'],
                TRAVEL_BLOCKS_VERSION,
                true
            );

            wp_localize_script("{$this->name}-view", 'PLA_FLAGS', [
                'enableMobile' => true,
            ]);
        }

        // Make block_wrapper_attributes available to template
        $GLOBALS['pla_block_wrapper_attributes'] = $block_wrapper_attributes;

        $template = TRAVEL_BLOCKS_PATH . 'src/Blocks/PostsListAdvanced/templates/editorial-grid.php';
        if (file_exists($template)) {
            include $template;
        }
    }
}
