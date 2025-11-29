<?php
/**
 * Block: Inclusions & Exclusions
 *
 * Display what's included and not included in the package
 *
 * NATIVE WORDPRESS BLOCK - Does NOT use ACF
 * Gets data from Package post meta fields
 *
 * @package Travel\Blocks\Blocks\Package
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class InclusionsExclusions
{
    private string $name = 'inclusions-exclusions';
    private string $title = 'Inclusions & Exclusions';
    private string $description = 'Display what\'s included and not included in the package';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'yes-alt',
            'keywords' => ['inclusions', 'exclusions', 'included', 'package', 'features'],
            'supports' => [
                'anchor' => true,
                'html' => false,
            ],
            'render_callback' => [$this, 'render'],
            'show_in_rest' => true,
        ]);

        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        wp_enqueue_style(
            'inclusions-exclusions-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/inclusions-exclusions.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        wp_enqueue_script(
            'inclusions-exclusions-script',
            TRAVEL_BLOCKS_URL . 'assets/blocks/inclusions-exclusions.js',
            [],
            TRAVEL_BLOCKS_VERSION,
            true
        );
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            if ($is_preview || !$post_id) {
                $data = $this->get_preview_data();
            } else {
                $data = $this->get_post_data($post_id);
            }

            if (empty($data['inclusions']) && empty($data['exclusions'])) {
                return '';
            }

            $block_id = 'inclusions-exclusions-' . uniqid();
            $class_name = 'inclusions-exclusions inclusions-exclusions--two-column inclusions-exclusions--default';

            if (!empty($attributes['className'])) {
                $class_name .= ' ' . $attributes['className'];
            }

            $data['block_id'] = $block_id;
            $data['class_name'] = $class_name;
            $data['is_preview'] = $is_preview;

            ob_start();
            $this->load_template('inclusions-exclusions', $data);
            return ob_get_clean();

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return '<div style="padding: 10px; background: #ffebee; border: 1px solid #f44336;">' .
                       '<p>Error en Inclusions & Exclusions: ' . esc_html($e->getMessage()) . '</p>' .
                       '</div>';
            }
            return '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            'inclusions' => [
                ['icon' => 'check', 'text' => 'Professional bilingual guide'],
                ['icon' => 'check', 'text' => 'All entrance fees to archaeological sites'],
                ['icon' => 'check', 'text' => 'Round-trip train tickets'],
                ['icon' => 'check', 'text' => 'Hotel accommodation (3 nights)'],
                ['icon' => 'check', 'text' => 'Daily breakfast and 2 lunches'],
                ['icon' => 'check', 'text' => 'Airport transfers'],
            ],
            'exclusions' => [
                ['icon' => 'x', 'text' => 'International flights'],
                ['icon' => 'x', 'text' => 'Travel insurance'],
                ['icon' => 'x', 'text' => 'Dinners (except day 1)'],
                ['icon' => 'x', 'text' => 'Personal expenses'],
                ['icon' => 'x', 'text' => 'Tips for guides and drivers'],
            ],
            'layout' => 'two-column',
            'style' => 'default',
            'inclusions_title' => 'What\'s Included',
            'exclusions_title' => 'What\'s NOT Included',
            'show_icons' => true,
        ];
    }

    private function get_post_data(int $post_id): array
    {
        // Get inclusions - try 'included' first (from wizard), then fallbacks
        $inclusions_raw = get_post_meta($post_id, 'included', true);
        if (empty($inclusions_raw)) {
            $inclusions_raw = get_post_meta($post_id, 'inclusions_full', true);
        }
        if (empty($inclusions_raw)) {
            $inclusions_raw = get_post_meta($post_id, 'inclusions', true);
        }
        $inclusions = $this->transform_items($inclusions_raw, 'check');

        // Get exclusions - try 'not_included' first (from wizard), then fallback
        $exclusions_raw = get_post_meta($post_id, 'not_included', true);
        if (empty($exclusions_raw)) {
            $exclusions_raw = get_post_meta($post_id, 'exclusions', true);
        }
        $exclusions = $this->transform_items($exclusions_raw, 'x');

        return [
            'inclusions' => $inclusions,
            'exclusions' => $exclusions,
            'layout' => 'two-column',
            'style' => 'default',
            'inclusions_title' => __('What\'s Included', 'travel-blocks'),
            'exclusions_title' => __('What\'s NOT Included', 'travel-blocks'),
            'show_icons' => true,
        ];
    }

    private function transform_items($items, string $default_icon): array
    {
        // If it's a string (HTML from WYSIWYG field), convert to array
        if (is_string($items)) {
            return $this->parse_html_to_items($items, $default_icon);
        }

        if (!is_array($items) || empty($items)) {
            return [];
        }

        $transformed = [];

        foreach ($items as $item) {
            if (is_string($item)) {
                // Simple text item
                $transformed[] = [
                    'icon' => $default_icon,
                    'text' => $item,
                ];
            } elseif (is_array($item)) {
                // Array with possible icon and text
                $text = $item['text'] ?? $item['item'] ?? $item['label'] ?? '';
                if (!empty($text)) {
                    $transformed[] = [
                        'icon' => $item['icon'] ?? $default_icon,
                        'text' => $text,
                    ];
                }
            }
        }

        return $transformed;
    }

    /**
     * Parse HTML content (from WYSIWYG fields) to array of items
     */
    private function parse_html_to_items(string $html, string $default_icon): array
    {
        if (empty(trim($html))) {
            return [];
        }

        $items = [];

        // Remove HTML tags but preserve line breaks
        $html = str_replace(['</li>', '</p>', '<br>', '<br/>'], "\n", $html);
        $text = strip_tags($html);

        // Split by newlines and clean up
        $lines = explode("\n", $text);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines
            if (empty($line)) {
                continue;
            }

            // Remove common list markers (bullets, numbers, dashes, etc.)
            $line = preg_replace('/^[\*\-\•\◦\▪\▫\→\⇒\➔\✓\✔\×\✕\d+\.\)]\s*/', '', $line);
            $line = trim($line);

            if (!empty($line)) {
                $items[] = [
                    'icon' => $default_icon,
                    'text' => $line,
                ];
            }
        }

        return $items;
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
