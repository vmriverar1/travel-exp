<?php
namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class CTABanner
{
    private string $name = 'cta-banner';
    private string $title = 'CTA Banner';
    private string $description = 'Call-to-action banner with background';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'travel',
            'icon' => 'megaphone',
            'keywords' => ['cta', 'banner', 'call-to-action', 'button'],
            'supports' => ['anchor' => true, 'html' => false],
            'render_callback' => [$this, 'render'],
        ]);
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            wp_enqueue_style('cta-banner-style', TRAVEL_BLOCKS_URL . 'assets/blocks/cta-banner.css', [], TRAVEL_BLOCKS_VERSION);
            wp_enqueue_script('cta-banner-script', TRAVEL_BLOCKS_URL . 'assets/blocks/cta-banner.js', [], TRAVEL_BLOCKS_VERSION, true);
        }
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            $banner_data = $is_preview ? $this->get_preview_data() : $this->get_post_data($post_id);
            if (empty($banner_data['title'])) return '';

            $data = [
                'block_id' => 'cta-banner-' . uniqid(),
                'class_name' => 'cta-banner' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
                'banner' => $banner_data,
                'is_preview' => $is_preview,
            ];

            ob_start();
            $this->load_template('cta-banner', $data);
            return ob_get_clean();
        } catch (\Exception $e) {
            return defined('WP_DEBUG') && WP_DEBUG ? '<div style="padding:10px;background:#ffebee;">Error: ' . esc_html($e->getMessage()) . '</div>' : '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            'title' => 'Ready to Start Your Adventure?',
            'subtitle' => 'Book now and get 15% off your first trip',
            'button_text' => 'Book Now',
            'button_url' => '#contact',
            'background_image' => '',
            'background_color' => '#1a73e8',
        ];
    }

    private function get_post_data(int $post_id): array
    {
        // Try to get CTA settings from post meta
        $cta_title = get_post_meta($post_id, 'cta_title', true);
        $cta_subtitle = get_post_meta($post_id, 'cta_subtitle', true);
        $cta_button_text = get_post_meta($post_id, 'cta_button_text', true);
        $cta_button_url = get_post_meta($post_id, 'cta_button_url', true);

        // Fallback to default CTA
        if (empty($cta_title)) {
            $cta_title = __('Ready to Book This Adventure?', 'travel-blocks');
        }
        if (empty($cta_button_text)) {
            $cta_button_text = __('Contact Us', 'travel-blocks');
        }
        if (empty($cta_button_url)) {
            $cta_button_url = '#contact-form';
        }

        // Get background image
        $background_image = '';
        $bg_image_id = get_post_meta($post_id, 'cta_background_image', true);
        if ($bg_image_id) {
            $background_image = wp_get_attachment_image_url($bg_image_id, 'full');
        } else {
            // Fallback to featured image
            $featured_id = get_post_thumbnail_id($post_id);
            if ($featured_id) {
                $background_image = get_the_post_thumbnail_url($post_id, 'full');
            }
        }

        return [
            'title' => $cta_title,
            'subtitle' => $cta_subtitle,
            'button_text' => $cta_button_text,
            'button_url' => $cta_button_url,
            'background_image' => $background_image,
            'background_color' => get_post_meta($post_id, 'cta_background_color', true) ?: '#1a73e8',
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
