<?php
/**
 * Block: Quick Facts
 *
 * Display quick facts and key information with icons
 *
 * NATIVE WORDPRESS BLOCK - Does NOT use ACF
 * Gets data from Package post meta fields
 *
 * @package Travel\Blocks\Blocks\Package
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class QuickFacts
{
    private string $name = 'quick-facts';
    private string $title = 'Quick Facts';
    private string $description = 'Display quick facts and key information with icons';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'travel',
            'icon' => 'list-view',
            'keywords' => ['facts', 'features', 'highlights', 'package'],
            'supports' => [
                'anchor' => true,
                'html' => false,
            ],
            'render_callback' => [$this, 'render'],
        ]);

        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            wp_enqueue_style(
                'quick-facts-style',
                TRAVEL_BLOCKS_URL . 'assets/blocks/quick-facts.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );
        }
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            if ($is_preview || !$post_id) {
                $facts = $this->get_preview_data();
            } else {
                $facts = $this->get_post_data($post_id);
            }

            if (empty($facts)) {
                return '';
            }

            $block_id = 'quick-facts-' . uniqid();
            $class_name = 'quick-facts quick-facts--grid-2 quick-facts--medium quick-facts--default';

            if (!empty($attributes['className'])) {
                $class_name .= ' ' . $attributes['className'];
            }

            $data = [
                'block_id' => $block_id,
                'class_name' => $class_name,
                'section_title' => '',
                'facts' => $facts,
                'layout' => 'grid-2',
                'icon_size' => 'medium',
                'icon_color' => '#4A90A4',
                'card_style' => 'default',
                'show_icons' => true,
                'is_preview' => $is_preview,
            ];

            ob_start();
            $this->load_template('quick-facts', $data);
            return ob_get_clean();

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return '<div style="padding: 10px; background: #ffebee; border: 1px solid #f44336;">' .
                       '<p>Error en Quick Facts: ' . esc_html($e->getMessage()) . '</p>' .
                       '</div>';
            }
            return '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            ['icon' => 'clock', 'label' => 'Duration', 'value' => '4 days / 3 nights'],
            ['icon' => 'users', 'label' => 'Group Size', 'value' => 'Small group (max 12)'],
            ['icon' => 'compass', 'label' => 'Difficulty', 'value' => 'Moderate'],
            ['icon' => 'map-pin', 'label' => 'Starting Point', 'value' => 'Cusco, Peru'],
            ['icon' => 'calendar', 'label' => 'Best Time', 'value' => 'May - September'],
            ['icon' => 'check', 'label' => 'Meals', 'value' => 'All included'],
        ];
    }

    private function get_post_data(int $post_id): array
    {
        // Get highlights from Package
        $highlights = get_post_meta($post_id, 'highlights', true);

        if (!is_array($highlights) || empty($highlights)) {
            // Fallback: create basic facts from package meta
            $facts = [];

            $duration = get_post_meta($post_id, 'duration', true);
            if ($duration) {
                $facts[] = ['icon' => 'clock', 'label' => 'Duration', 'value' => $duration];
            }

            $difficulty = get_post_meta($post_id, 'physical_difficulty', true);
            if ($difficulty) {
                $facts[] = ['icon' => 'compass', 'label' => 'Difficulty', 'value' => ucfirst($difficulty)];
            }

            $service_type = get_post_meta($post_id, 'service_type', true);
            if ($service_type) {
                $facts[] = ['icon' => 'users', 'label' => 'Type', 'value' => ucfirst($service_type)];
            }

            $departure = get_post_meta($post_id, 'departure', true);
            if ($departure) {
                $facts[] = ['icon' => 'map-pin', 'label' => 'Starting Point', 'value' => $departure];
            }

            return $facts;
        }

        // Transform highlights format
        $facts = [];
        foreach ($highlights as $highlight) {
            if (is_array($highlight)) {
                // Highlight is already an array with icon/label/value
                $facts[] = [
                    'icon' => $highlight['icon'] ?? 'check',
                    'label' => '',
                    'value' => $highlight['text'] ?? $highlight['label'] ?? $highlight['value'] ?? '',
                ];
            } elseif (is_string($highlight) && !empty($highlight)) {
                // Highlight is a simple string
                $facts[] = [
                    'icon' => 'check',
                    'label' => '',
                    'value' => $highlight,
                ];
            }
        }

        return $facts;
    }

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

        extract($data, EXTR_SKIP);
        include $template_path;
    }
}
