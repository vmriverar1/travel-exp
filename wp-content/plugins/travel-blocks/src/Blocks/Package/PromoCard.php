<?php
namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class PromoCard
{
    private string $name = 'promo-card';
    private string $title = 'Promo Card';
    private string $description = 'Promotional card with circular image';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'travel',
            'icon' => 'format-image',
            'keywords' => ['promo', 'card', 'promotional', 'offer'],
            'supports' => ['anchor' => true, 'html' => false],
            'render_callback' => [$this, 'render'],
        ]);
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            wp_enqueue_style('promo-card-style', TRAVEL_BLOCKS_URL . 'assets/blocks/promo-card.css', [], TRAVEL_BLOCKS_VERSION);
        }
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            $promo = $is_preview ? $this->get_preview_data() : $this->get_post_data($post_id);
            if (empty($promo['title'])) return '';

            $data = [
                'block_id' => 'promo-card-' . uniqid(),
                'class_name' => 'promo-card' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
                'promo' => $promo,
                'is_preview' => $is_preview,
            ];

            ob_start();
            $this->load_template('promo-card', $data);
            return ob_get_clean();
        } catch (\Exception $e) {
            return defined('WP_DEBUG') && WP_DEBUG ? '<div style="padding:10px;background:#ffebee;">Error: ' . esc_html($e->getMessage()) . '</div>' : '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            'title' => 'Special Offer',
            'subtitle' => 'Limited Time Only',
            'description' => 'Book before December 31st and save 20% on your adventure',
            'image' => '',
            'badge_text' => '20% OFF',
            'badge_color' => '#ff5722',
            'button_text' => 'Claim Offer',
            'button_url' => '#pricing',
        ];
    }

    private function get_post_data(int $post_id): array
    {
        $promo_title = get_post_meta($post_id, 'promo_title', true);
        $promo_subtitle = get_post_meta($post_id, 'promo_subtitle', true);
        $promo_description = get_post_meta($post_id, 'promo_description', true);
        $promo_button_text = get_post_meta($post_id, 'promo_button_text', true);
        $promo_button_url = get_post_meta($post_id, 'promo_button_url', true);

        if (empty($promo_title)) {
            $discount = get_post_meta($post_id, 'discount_percentage', true);
            if ($discount) {
                $promo_title = sprintf(__('%s%% Off Early Bird Special', 'travel-blocks'), $discount);
            }
        }

        $promo_image = '';
        $promo_image_id = get_post_meta($post_id, 'promo_image', true);
        if ($promo_image_id) {
            $promo_image = wp_get_attachment_image_url($promo_image_id, 'medium');
        } else {
            $featured_id = get_post_thumbnail_id($post_id);
            if ($featured_id) {
                $promo_image = get_the_post_thumbnail_url($post_id, 'medium');
            }
        }

        $badge_text = get_post_meta($post_id, 'promo_badge_text', true);
        if (empty($badge_text)) {
            $discount = get_post_meta($post_id, 'discount_percentage', true);
            if ($discount) {
                $badge_text = $discount . '% OFF';
            }
        }

        return [
            'title' => $promo_title,
            'subtitle' => $promo_subtitle,
            'description' => $promo_description,
            'image' => $promo_image,
            'badge_text' => $badge_text,
            'badge_color' => get_post_meta($post_id, 'promo_badge_color', true) ?: '#ff5722',
            'button_text' => $promo_button_text ?: __('Learn More', 'travel-blocks'),
            'button_url' => $promo_button_url ?: '#pricing-card',
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
