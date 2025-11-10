<?php
/**
 * Block: Static Hero
 *
 * Simple fullscreen hero with title, subtitle, and background image.
 * Minimal options, static layout only.
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 2.0.0 - REFACTORED: Now inherits BlockBase, security fixes, removed anti-patterns
 *
 * Previous Issues (NOW RESOLVED):
 * - Does NOT inherit from BlockBase ✅ NOW INHERITS
 * - Template violates MVC (calls get_field() directly) ✅ NOW USES $data
 * - Uses $GLOBALS anti-pattern ✅ ELIMINATED
 * - add_action('wp_head') inside template ✅ MOVED TO CLASS
 * - Background-image without proper escaping ✅ NOW ESCAPED
 *
 * @see HeroSection Alternative with more features
 */

namespace Travel\Blocks\Blocks\ACF;

use Travel\Blocks\Core\BlockBase;

class StaticHero extends BlockBase
{
    /**
     * Constructor - Initialize block properties.
     *
     * Sets up block configuration following BlockBase pattern.
     *
     * @return void
     */
    public function __construct()
    {
        $this->name        = 'acf-gbr-static-hero';
        $this->title       = __('Static Hero', 'travel-blocks');
        $this->description = __('Bloque estático con título, subtítulo e imagen.', 'travel-blocks');
        $this->category    = 'travel';
        $this->icon        = 'slides';
        $this->keywords    = ['hero', 'banner', 'estático'];
        $this->mode        = 'preview';

        $this->supports = [
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
        ];
    }

    /**
     * Render the block output.
     *
     * ✅ REFACTORED v2.0.0:
     * - Now processes data in class (MVC pattern)
     * - Passes data via $data array (no $GLOBALS)
     * - Registers preload link in class (not in template)
     * - Uses load_template() from BlockBase
     *
     * @param array  $block      Block settings and attributes
     * @param string $content    Block content (unused)
     * @param bool   $is_preview Whether block is being previewed in editor
     * @param int    $post_id    Current post ID
     *
     * @return void
     */
    public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        // Handle WP_Block object in context
        if ($is_preview instanceof \WP_Block) {
            $post_id = $is_preview->context['postId'] ?? 0;
            $is_preview = false;
        }

        // Get ACF field data
        $title = get_field('sh_title') ?: __('Título por defecto', 'travel-blocks');
        $subtitle = get_field('sh_subtitle') ?: __('Subtítulo por defecto', 'travel-blocks');
        $bg = get_field('sh_background');
        $bg_url = is_array($bg) && isset($bg['url']) ? esc_url($bg['url']) : '';

        // Register preload link for background image (performance optimization)
        // ✅ MOVED FROM TEMPLATE - Now in class where it belongs
        if ($bg_url && !$is_preview) {
            add_action('wp_head', function() use ($bg_url) {
                echo '<link rel="preload" as="image" href="' . esc_url($bg_url) . '" fetchpriority="high" importance="high">';
            }, 1);
        }

        // Prepare template data
        // ✅ NO $GLOBALS - Data passed directly to template
        $data = [
            'title'    => $title,
            'subtitle' => $subtitle,
            'bg_url'   => $bg_url,
            'block_id' => 'static-hero-' . ($block['id'] ?? uniqid()),
            'align'    => $block['align'] ?? 'wide',
        ];

        // Load template using BlockBase method
        // ✅ Uses load_template() instead of direct include
        $this->load_template('static-hero', $data);
    }

    /**
     * Enqueue block assets.
     *
     * Loads CSS for Static Hero block.
     *
     * @return void
     */
    public function enqueue_assets(): void
    {
        wp_enqueue_style(
            $this->name,
            TRAVEL_BLOCKS_URL . 'assets/blocks/StaticHero/style.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );
    }
}
