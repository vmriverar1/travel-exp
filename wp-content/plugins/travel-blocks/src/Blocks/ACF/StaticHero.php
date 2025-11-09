<?php
/**
 * Block: Static Hero
 *
 * Simple fullscreen hero with title, subtitle, and background image.
 * Minimal options, static layout only.
 *
 * ⚠️ DEPRECATION WARNING:
 * This block has CRITICAL architectural issues:
 * - Does NOT inherit from BlockBase (violates architecture consistency)
 * - Template violates MVC (calls get_field() directly)
 * - Uses $GLOBALS anti-pattern for data passing
 * - add_action('wp_head') inside template (severe anti-pattern)
 * - Background-image without proper escaping (XSS risk in template)
 * - ACF fields defined in JSON (less flexible than PHP)
 * - Functionality duplicated by HeroSection block (which is superior)
 *
 * ⚠️ RECOMMENDATION: Migrate content to HeroSection and deprecate this block.
 * HeroSection provides same functionality with better architecture and more options.
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 1.1.0 - Refactored: namespace fix, added docs, marked for deprecation
 *
 * @see HeroSection Better alternative with proper BlockBase inheritance
 */

namespace Travel\Blocks\ACF;

class StaticHero
{
    /**
     * Block name identifier.
     *
     * @var string
     */
    private string $name = 'acf-gbr-static-hero';

    /**
     * Register the ACF block type.
     *
     * Registers a simple static hero block with minimal configuration.
     * Does NOT inherit from BlockBase (architectural issue).
     *
     * ACF Fields (defined in JSON /acf-json/group_acfgbr_static_hero.json):
     * - sh_title: Hero title text
     * - sh_subtitle: Hero subtitle text
     * - sh_background: Background image
     *
     * ⚠️ Known Issues:
     * - Template violates MVC by calling get_field() directly
     * - Uses $GLOBALS for data passing (anti-pattern)
     * - Template has add_action('wp_head') inside (severe anti-pattern)
     * - Background-image lacks proper escaping in template (XSS risk)
     *
     * @return void
     */
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

    /**
     * Render the block output.
     *
     * Loads template that generates fullscreen hero section.
     * Passes block wrapper attributes via $GLOBALS (anti-pattern).
     *
     * ⚠️ Architectural Issues in this method:
     * - Uses $GLOBALS to pass data to template (should use $data array)
     * - Template calls get_field() directly (violates MVC)
     * - Template includes add_action('wp_head') (severe anti-pattern)
     * - Direct file include instead of load_template() method
     *
     * @param array  $block      Block settings and attributes
     * @param string $content    Block content (unused)
     * @param bool   $is_preview Whether block is being previewed in editor
     * @param int    $post_id    Current post ID
     *
     * @return void
     */
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
