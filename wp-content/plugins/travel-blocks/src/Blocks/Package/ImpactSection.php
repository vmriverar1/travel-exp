<?php
namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class ImpactSection
{
    private string $name = 'impact-section';
    private string $title = 'Impact Section';
    private string $description = 'Social responsibility messaging';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'heart',
            'keywords' => ['impact', 'sustainability', 'responsibility', 'social'],
            'supports' => ['anchor' => true, 'html' => false],
            'render_callback' => [$this, 'render'],
            'show_in_rest' => true,
        ]);
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            wp_enqueue_style('impact-section-style', TRAVEL_BLOCKS_URL . 'assets/blocks/impact-section.css', [], TRAVEL_BLOCKS_VERSION);
        }
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            $impact_data = $is_preview ? $this->get_preview_data() : $this->get_post_data($post_id);
            if (empty($impact_data['title'])) return '';

            $data = [
                'block_id' => 'impact-section-' . uniqid(),
                'class_name' => 'impact-section' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
                'impact' => $impact_data,
                'is_preview' => $is_preview,
            ];

            ob_start();
            $this->load_template('impact-section', $data);
            return ob_get_clean();
        } catch (\Exception $e) {
            return defined('WP_DEBUG') && WP_DEBUG ? '<div style="padding:10px;background:#ffebee;">Error: ' . esc_html($e->getMessage()) . '</div>' : '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            'title' => 'Guides. Guardians. Bridges.',
            'message' => 'We believe in responsible tourism that benefits local communities and preserves our planet for future generations.',
            'background_image' => '',
            'tiles' => [
                [
                    'icon' => '',
                    'title' => 'Local Communities',
                    'text' => 'We support local guides and businesses, ensuring tourism benefits the communities we visit.',
                ],
                [
                    'icon' => '',
                    'title' => 'Environmental Protection',
                    'text' => 'We minimize our environmental footprint and contribute to conservation efforts.',
                ],
                [
                    'icon' => '',
                    'title' => 'Cultural Preservation',
                    'text' => 'We respect and help preserve the cultural heritage of the places we explore.',
                ],
            ],
            'button_text' => 'Learn More About Our Impact',
            'button_url' => '#',
        ];
    }

    private function get_post_data(int $post_id): array
    {
        $impact_title = get_post_meta($post_id, 'impact_title', true);
        $impact_message = get_post_meta($post_id, 'impact_message', true);

        // Get background image
        $background_image = '';
        $bg_image_id = get_post_meta($post_id, 'impact_background_image', true);
        if ($bg_image_id) {
            $background_image = wp_get_attachment_image_url($bg_image_id, 'full');
        }

        // Get tiles
        $tiles = [];
        for ($i = 1; $i <= 3; $i++) {
            $tile_title = get_post_meta($post_id, "impact_tile_{$i}_title", true);
            $tile_text = get_post_meta($post_id, "impact_tile_{$i}_text", true);
            $tile_icon_id = get_post_meta($post_id, "impact_tile_{$i}_icon", true);

            if ($tile_title || $tile_text) {
                $icon_url = '';
                if ($tile_icon_id) {
                    $icon_url = wp_get_attachment_image_url($tile_icon_id, 'thumbnail');
                }

                $tiles[] = [
                    'icon' => $icon_url,
                    'title' => $tile_title,
                    'text' => $tile_text,
                ];
            }
        }

        return [
            'title' => $impact_title,
            'message' => $impact_message,
            'background_image' => $background_image,
            'tiles' => $tiles,
            'button_text' => get_post_meta($post_id, 'impact_button_text', true) ?: __('Learn More About Our Impact', 'travel-blocks'),
            'button_url' => get_post_meta($post_id, 'impact_button_url', true) ?: '#',
        ];
    }

    protected function load_template(string $template_name, array $data = []): void
    {
        $template_path = TRAVEL_BLOCKS_PATH . 'templates/' . $template_name . '.php';
        if (!file_exists($template_path)) {
            if (defined('WP_DEBUG') && WP_DEBUG) echo '<div style="padding:1rem;background:#fff3cd;">Template not found: ' . esc_html($template_name . '.php') . '</div>';
            return;
        }
        extract($data, EXTR_SKIP);
        include $template_path;
    }
}
