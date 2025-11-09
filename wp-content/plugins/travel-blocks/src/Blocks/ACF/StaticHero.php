<?php

namespace Travel\Blocks\Blocks\ACF;

class StaticHero
{
    private string $name = 'acf-gbr-static-hero';
    public function register(): void
    {
        acf_register_block_type([
            'name' => $this->name,
            'title' => __('Static Hero', 'acf-gbr'),
            'description' => __('Bloque estático con título, subtítulo e imagen.', 'acf-gbr'),
            'category' => 'travel',
            'icon' => 'slides',
            'keywords' => ['hero', 'banner'],
            'render_callback' => [$this, 'render'],
            'supports' => [
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
            'enqueue_assets' => function () {
                wp_enqueue_style($this->name, TRAVEL_BLOCKS_URL . 'assets/blocks/StaticHero/style.css', [], TRAVEL_BLOCKS_VERSION);
            },
        ]);
    }
    public function render($block, $content = '', $is_preview = false, $post_id = 0): void
    {
        // Get WordPress block attributes
        $block_wrapper_attributes = get_block_wrapper_attributes([
            'class' => 'static-hero-wrapper'
        ]);

        if ($is_preview instanceof \WP_Block) {
            $post_id = $is_preview->context['postId'] ?? 0;
            $is_preview = false;
        }

        // Make block_wrapper_attributes available to template
        $GLOBALS['sh_block_wrapper_attributes'] = $block_wrapper_attributes;

        $template = TRAVEL_BLOCKS_PATH . 'src/Blocks/StaticHero/template.php';
        if (file_exists($template)) include $template;
    }
}
