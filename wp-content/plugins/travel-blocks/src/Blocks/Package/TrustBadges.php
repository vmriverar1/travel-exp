<?php
namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class TrustBadges
{
    private string $name = 'trust-badges';
    private string $title = 'Trust Badges';
    private string $description = 'Trust badges and certifications';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'shield-alt',
            'keywords' => ['trust', 'badges', 'certifications', 'awards'],
            'supports' => ['anchor' => true, 'html' => false],
            'render_callback' => [$this, 'render'],
            'show_in_rest' => true,
        ]);
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            wp_enqueue_style('trust-badges-style', TRAVEL_BLOCKS_URL . 'assets/blocks/trust-badges.css', [], TRAVEL_BLOCKS_VERSION);
        }
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            $badges = $is_preview ? $this->get_preview_data() : $this->get_post_data($post_id);
            if (empty($badges)) return '';

            $data = [
                'block_id' => 'trust-badges-' . uniqid(),
                'class_name' => 'trust-badges' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
                'badges' => $badges,
                'is_preview' => $is_preview,
            ];

            ob_start();
            $this->load_template('trust-badges', $data);
            return ob_get_clean();
        } catch (\Exception $e) {
            return defined('WP_DEBUG') && WP_DEBUG ? '<div style="padding:10px;background:#ffebee;">Error: ' . esc_html($e->getMessage()) . '</div>' : '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            ['icon' => 'shield-alt', 'label' => 'ATOL Protected', 'image' => ''],
            ['icon' => 'star-filled', 'label' => 'TripAdvisor 5★', 'image' => ''],
            ['icon' => 'awards', 'label' => 'Best Tour Operator 2024', 'image' => ''],
            ['icon' => 'shield', 'label' => 'ABTA Member', 'image' => ''],
        ];
    }

    private function get_post_data(int $post_id): array
    {
        $badges_raw = get_post_meta($post_id, 'trust_badges', true);

        if (!is_array($badges_raw) || empty($badges_raw)) {
            return $this->get_default_badges();
        }

        $badges = [];
        foreach ($badges_raw as $badge) {
            if (is_array($badge)) {
                $image_url = '';
                if (!empty($badge['image'])) {
                    if (is_numeric($badge['image'])) {
                        $image_url = wp_get_attachment_image_url($badge['image'], 'medium');
                    } else {
                        $image_url = $badge['image'];
                    }
                }

                $badges[] = [
                    'icon' => $badge['icon'] ?? 'shield-alt',
                    'label' => $badge['label'] ?? $badge['text'] ?? '',
                    'image' => $image_url,
                ];
            } elseif (is_string($badge)) {
                $badges[] = [
                    'icon' => 'shield-alt',
                    'label' => $badge,
                    'image' => '',
                ];
            }
        }

        return !empty($badges) ? $badges : $this->get_default_badges();
    }

    private function get_default_badges(): array
    {
        return [
            ['icon' => 'shield-alt', 'label' => __('Secure Booking', 'travel-blocks'), 'image' => ''],
            ['icon' => 'star-filled', 'label' => __('Top Rated', 'travel-blocks'), 'image' => ''],
            ['icon' => 'yes-alt', 'label' => __('Certified Operator', 'travel-blocks'), 'image' => ''],
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
