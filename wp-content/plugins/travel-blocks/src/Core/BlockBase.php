<?php
/**
 * Abstract Block Base Class
 *
 * Base class for all Travel Blocks
 *
 * @package Travel\Blocks
 * @since 1.0.0
 */

namespace Travel\Blocks\Core;

abstract class BlockBase
{
    /**
     * Block name (slug).
     *
     * @var string
     */
    protected $name;

    /**
     * Block title.
     *
     * @var string
     */
    protected $title;

    /**
     * Block description.
     *
     * @var string
     */
    protected $description;

    /**
     * Block category.
     *
     * @var array
     */
    protected $category;

    /**
     * Block supports.
     *
     * @var array
     */
    protected $supports;

    /**
     * Block icon.
     *
     * @var string
     */
    protected $icon;

    /**
     * Block keywords.
     *
     * @var array
     */
    protected $keywords;

    /**
     * Block mode (auto, preview, edit).
     *
     * @var string
     */
    protected $mode;

    /**
     * Constructor.
     *
     * @param string $name        Block name (slug).
     * @param string $title       Block title.
     * @param string $description Block description.
     * @param array  $category    Block category.
     * @param array  $supports    Block supports.
     */
    public function __construct(
        string $name,
        string $title,
        string $description,
        array $category = ['travel'],
        array $supports = []
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->description = $description;
        $this->category = $category;
        $this->supports = array_merge([
            'align' => false,
            'mode' => false,
            'jsx' => false,
        ], $supports);
    }

    /**
     * Register the block.
     */
    public function register(): void
    {
        // Enqueue assets for both frontend and editor
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);

        // Also enqueue for block editor specifically
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_assets']);

        // Register ACF block
        if (function_exists('acf_register_block_type')) {
            $block_config = [
                'name' => $this->name,
                'title' => $this->title,
                'description' => $this->description,
                'category' => is_array($this->category) ? $this->category[0] : ($this->category ?? 'common'),
                'icon' => $this->icon ?? 'palmtree',
                'keywords' => $this->keywords ?? ['travel', 'package', 'tour'],
                'supports' => $this->supports,
                'render_callback' => [$this, 'render'],
                'enqueue_assets' => [$this, 'enqueue_assets'],
                'api_version' => 2,
            ];

            // Add mode if set
            if (!empty($this->mode)) {
                $block_config['mode'] = $this->mode;
            }

            acf_register_block_type($block_config);
        }

        // Register ACF fields if method exists
        if (method_exists($this, 'get_fields')) {
            add_action('acf/init', function() {
                $fields = $this->get_fields();

                if (!empty($fields)) {
                    acf_add_local_field_group([
                        'key' => 'group_' . $this->name,
                        'title' => $this->title . ' - Settings',
                        'fields' => $fields,
                        'location' => [
                            [
                                [
                                    'param' => 'block',
                                    'operator' => '==',
                                    'value' => 'acf/' . $this->name,
                                ],
                            ],
                        ],
                        'menu_order' => 0,
                        'position' => 'normal',
                        'style' => 'default',
                        'label_placement' => 'top',
                        'instruction_placement' => 'label',
                    ]);
                }
            });
        }
    }

    /**
     * Enqueue block assets (must be implemented by child classes).
     */
    abstract public function enqueue_assets(): void;

    /**
     * Render block callback (must be implemented by child classes).
     *
     * @param array  $block      Block settings.
     * @param string $content    Block content.
     * @param bool   $is_preview Whether block is being previewed.
     * @param int    $post_id    Current post ID.
     */
    abstract public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void;

    /**
     * Get ACF fields configuration (optional, can be implemented by child classes).
     *
     * @return array
     */
    protected function get_fields(): array
    {
        return [];
    }

    /**
     * Get block name.
     *
     * @return string
     */
    public function get_name(): string
    {
        return $this->name;
    }

    /**
     * Get block title.
     *
     * @return string
     */
    public function get_title(): string
    {
        return $this->title;
    }

    /**
     * Get block description.
     *
     * @return string
     */
    public function get_description(): string
    {
        return $this->description;
    }

    /**
     * Load block template with extracted variables.
     *
     * @param string $template_name Template file name (without .php extension).
     * @param array  $data          Data to extract and pass to template.
     * @return void
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
