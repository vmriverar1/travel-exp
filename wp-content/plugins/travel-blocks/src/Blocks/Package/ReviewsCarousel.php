<?php
namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class ReviewsCarousel
{
    private string $name = 'reviews-carousel';
    private string $title = 'Mini Reviews List';
    private string $description = 'Vertical list of customer reviews with ratings - NO Swiper';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'star-filled',
            'keywords' => ['reviews', 'testimonials', 'ratings', 'mini'],
            'supports' => ['anchor' => true, 'html' => false],
            'render_callback' => [$this, 'render'],
            'show_in_rest' => true,
        ]);
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            // NO Swiper - Simple vertical list
            wp_enqueue_style('reviews-carousel-style', TRAVEL_BLOCKS_URL . 'assets/blocks/reviews-carousel.css', [], TRAVEL_BLOCKS_VERSION);
        }
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);
            
            $reviews = $is_preview ? $this->get_preview_data() : $this->get_post_data($post_id);
            if (empty($reviews)) return '';

            $data = [
                'block_id' => 'reviews-carousel-' . uniqid(),
                'class_name' => 'reviews-carousel' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
                'reviews' => $reviews,
                'is_preview' => $is_preview,
            ];

            ob_start();
            $this->load_template('reviews-carousel', $data);
            return ob_get_clean();
        } catch (\Exception $e) {
            return defined('WP_DEBUG') && WP_DEBUG ? '<div style="padding:10px;background:#ffebee;">Error: ' . esc_html($e->getMessage()) . '</div>' : '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            ['author' => 'Sarah Johnson', 'rating' => 5, 'date' => '2024-12-15', 'content' => 'Amazing experience!', 'country' => 'USA'],
            ['author' => 'Michael Chen', 'rating' => 5, 'date' => '2024-11-28', 'content' => 'Highly recommend!', 'country' => 'Canada'],
        ];
    }

    private function get_post_data(int $post_id): array
    {
        $reviews = get_post_meta($post_id, 'reviews', true);
        if (!is_array($reviews)) return [];
        
        $formatted = [];
        foreach ($reviews as $review) {
            if (is_array($review)) {
                $formatted[] = [
                    'author' => $review['author'] ?? 'Anonymous',
                    'rating' => intval($review['rating'] ?? 5),
                    'date' => $review['date'] ?? '',
                    'content' => $review['content'] ?? $review['text'] ?? '',
                    'country' => $review['country'] ?? '',
                ];
            }
        }
        return $formatted;
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
