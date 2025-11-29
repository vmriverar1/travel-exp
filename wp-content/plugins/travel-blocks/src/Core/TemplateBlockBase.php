<?php
/**
 * Template Block Base Class
 *
 * Base class for all Template Blocks with consistent preview/live rendering
 *
 * @package Travel\Blocks\Core
 * @since 2.0.0
 */

namespace Travel\Blocks\Core;

use Travel\Blocks\Helpers\EditorHelper;

abstract class TemplateBlockBase
{
    /**
     * Block name (slug)
     *
     * @var string
     */
    protected string $name;

    /**
     * Block title
     *
     * @var string
     */
    protected string $title;

    /**
     * Block description
     *
     * @var string
     */
    protected string $description;

    /**
     * Block category
     *
     * @var string
     */
    protected string $category = 'template-blocks';

    /**
     * Block icon (Dashicon)
     *
     * @var string
     */
    protected string $icon = 'layout';

    /**
     * Block keywords for search
     *
     * @var array
     */
    protected array $keywords = [];

    /**
     * Block supports
     *
     * @var array
     */
    protected array $supports = [
        'anchor' => true,
        'align' => false,
        'html' => false,
    ];

    /**
     * Register the block
     */
    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => $this->category,
            'icon' => $this->icon,
            'keywords' => $this->keywords,
            'supports' => $this->supports,
            'render_callback' => [$this, 'render'],
            'show_in_rest' => true,
        ]);

        // Enqueue assets
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    /**
     * Main render method - handles preview vs live mode
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
            $is_preview = EditorHelper::is_editor_mode($post_id);

            if ($is_preview) {
                return $this->render_preview($attributes);
            }

            return $this->render_live($post_id, $attributes);

        } catch (\Exception $e) {
            return $this->render_error($e);
        }
    }

    /**
     * Render block in preview/editor mode
     *
     * @param array $attributes Block attributes
     * @return string Rendered HTML with preview data
     */
    abstract protected function render_preview(array $attributes): string;

    /**
     * Render block in live/frontend mode
     *
     * @param int $post_id Current post ID
     * @param array $attributes Block attributes
     * @return string Rendered HTML with real data
     */
    abstract protected function render_live(int $post_id, array $attributes): string;

    /**
     * Load template file with data
     *
     * @param string $template_name Template file name (without .php)
     * @param array $data Data to extract into template
     * @return string Rendered template HTML
     */
    protected function load_template(string $template_name, array $data = []): string
    {
        $template_path = TRAVEL_BLOCKS_PATH . "templates/template/{$template_name}.php";

        if (!file_exists($template_path)) {
            return $this->render_error(
                new \Exception("Template not found: {$template_name}.php")
            );
        }

        ob_start();
        extract($data);
        include $template_path;
        return ob_get_clean();
    }

    /**
     * Render error message (only in debug mode)
     *
     * @param \Exception $e Exception object
     * @return string Error HTML or empty string
     */
    protected function render_error(\Exception $e): string
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return sprintf(
                '<div class="template-block-error" style="padding: 15px; background: #ffebee; border: 2px solid #f44336; border-radius: 4px; margin: 10px 0;">
                    <strong>Template Block Error (%s):</strong><br>
                    <code>%s</code>
                </div>',
                esc_html($this->name),
                esc_html($e->getMessage())
            );
        }

        return '';
    }

    /**
     * Enqueue block-specific assets
     * Override in child class if needed
     */
    public function enqueue_assets(): void
    {
        // Child classes can override to add CSS/JS
    }

    /**
     * Get block wrapper attributes
     *
     * @param array $attributes Block attributes
     * @param string $additional_classes Additional CSS classes
     * @return string HTML attributes string
     */
    protected function get_wrapper_attributes(array $attributes, string $additional_classes = ''): string
    {
        $classes = ['template-block', 'template-block-' . $this->name];

        if (!empty($attributes['className'])) {
            $classes[] = $attributes['className'];
        }

        if ($additional_classes) {
            $classes[] = $additional_classes;
        }

        $attrs = [
            'class' => implode(' ', $classes),
        ];

        if (!empty($attributes['anchor'])) {
            $attrs['id'] = $attributes['anchor'];
        }

        $output = [];
        foreach ($attrs as $key => $value) {
            $output[] = sprintf('%s="%s"', $key, esc_attr($value));
        }

        return implode(' ', $output);
    }
}
