<?php
/**
 * Block: Traveler Reviews
 *
 * Grid grande de reviews con filtros por plataforma
 * Native WordPress block - NO ACF
 *
 * @package Travel\Blocks\Blocks\Package
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class TravelerReviews
{
    private string $name = 'traveler-reviews';
    private string $title = 'Traveler Reviews';
    private string $description = 'Large grid of traveler reviews with platform filters';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'star-filled',
            'keywords' => ['reviews', 'testimonials', 'travelers', 'grid', 'ratings'],
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
        if (!is_admin()) {
            wp_enqueue_style(
                'traveler-reviews-style',
                TRAVEL_BLOCKS_URL . 'assets/blocks/traveler-reviews.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );

            wp_enqueue_script(
                'traveler-reviews-script',
                TRAVEL_BLOCKS_URL . 'assets/blocks/traveler-reviews.js',
                [],
                TRAVEL_BLOCKS_VERSION,
                true
            );
        }
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            if ($is_preview || !$post_id) {
                $reviews_data = $this->get_preview_data();
            } else {
                $reviews_data = $this->get_post_data($post_id);
            }

            if (empty($reviews_data['reviews'])) {
                return '';
            }

            // Get unique platforms for filter
            $platforms = [];
            foreach ($reviews_data['reviews'] as $review) {
                if (!empty($review['platform']) && !in_array($review['platform'], $platforms)) {
                    $platforms[] = $review['platform'];
                }
            }

            $data = [
                'block_id' => 'traveler-reviews-' . uniqid(),
                'class_name' => 'traveler-reviews' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
                'section_title' => $reviews_data['section_title'],
                'section_subtitle' => $reviews_data['section_subtitle'],
                'reviews' => $reviews_data['reviews'],
                'platforms' => $platforms,
                'show_platform_filter' => $reviews_data['show_platform_filter'],
                'reviews_per_page' => $reviews_data['reviews_per_page'],
                'grid_columns' => $reviews_data['grid_columns'],
                'pagination_type' => $reviews_data['pagination_type'],
                'is_preview' => $is_preview,
                'schema' => $this->generate_review_schema($reviews_data['reviews']),
            ];

            ob_start();
            $this->load_template('traveler-reviews', $data);
            return ob_get_clean();

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return '<div style="padding: 10px; background: #ffebee; border: 1px solid #f44336;">' .
                       '<p>Error en Traveler Reviews: ' . esc_html($e->getMessage()) . '</p>' .
                       '</div>';
            }
            return '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            'section_title' => 'Traveler Stories & Reviews',
            'section_subtitle' => 'What our adventurers say about their experiences',
            'show_platform_filter' => true,
            'reviews_per_page' => 9,
            'grid_columns' => 3,
            'pagination_type' => 'show_more',
            'reviews' => [
                [
                    'author' => 'Sarah Johnson',
                    'origin' => 'New York, USA',
                    'traveler_type' => 'Solo traveler',
                    'rating' => 5,
                    'date' => '2025-09-15',
                    'content' => 'Absolutely incredible experience! The Inca Trail trek exceeded all my expectations. Our guide was knowledgeable and the views were breathtaking.',
                    'platform' => 'tripadvisor',
                    'avatar' => '',
                ],
                [
                    'author' => 'Michael Chen',
                    'origin' => 'Vancouver, Canada',
                    'traveler_type' => 'Couple',
                    'rating' => 5,
                    'date' => '2025-08-28',
                    'content' => 'Best trip of our lives! Everything was perfectly organized. The team went above and beyond to make our honeymoon special.',
                    'platform' => 'google',
                    'avatar' => '',
                ],
                [
                    'author' => 'Emma Williams',
                    'origin' => 'London, UK',
                    'traveler_type' => 'Family',
                    'rating' => 4,
                    'date' => '2025-07-10',
                    'content' => 'Great family adventure! Our kids loved every moment. The itinerary was well-paced and suitable for all ages.',
                    'platform' => 'tripadvisor',
                    'avatar' => '',
                ],
                [
                    'author' => 'David Martinez',
                    'origin' => 'Barcelona, Spain',
                    'traveler_type' => 'Friends',
                    'rating' => 5,
                    'date' => '2025-06-22',
                    'content' => 'Unforgettable experience with friends. The guides were amazing and the food was delicious. Highly recommend!',
                    'platform' => 'facebook',
                    'avatar' => '',
                ],
                [
                    'author' => 'Lisa Anderson',
                    'origin' => 'Sydney, Australia',
                    'traveler_type' => 'Solo traveler',
                    'rating' => 5,
                    'date' => '2025-05-18',
                    'content' => 'As a solo female traveler, I felt completely safe and welcomed. Made lifelong friends on this trip!',
                    'platform' => 'google',
                    'avatar' => '',
                ],
                [
                    'author' => 'James Brown',
                    'origin' => 'Toronto, Canada',
                    'traveler_type' => 'Couple',
                    'rating' => 4,
                    'date' => '2025-04-05',
                    'content' => 'Excellent value for money. The accommodations were comfortable and the scenery was stunning.',
                    'platform' => 'tripadvisor',
                    'avatar' => '',
                ],
            ],
        ];
    }

    private function get_post_data(int $post_id): array
    {
        $reviews_raw = get_post_meta($post_id, 'traveler_reviews', true);

        if (!is_array($reviews_raw)) {
            $reviews_raw = [];
        }

        // Transform reviews to expected format
        $reviews = [];
        foreach ($reviews_raw as $review) {
            if (is_array($review) && !empty($review['author']) && !empty($review['content'])) {
                $reviews[] = [
                    'author' => $review['author'],
                    'origin' => $review['origin'] ?? '',
                    'traveler_type' => $review['traveler_type'] ?? '',
                    'rating' => intval($review['rating'] ?? 5),
                    'date' => $review['date'] ?? '',
                    'content' => $review['content'],
                    'platform' => $review['platform'] ?? 'tripadvisor',
                    'avatar' => $review['avatar'] ?? '',
                ];
            }
        }

        return [
            'section_title' => get_post_meta($post_id, 'traveler_reviews_title', true) ?: __('Traveler Stories & Reviews', 'travel-blocks'),
            'section_subtitle' => get_post_meta($post_id, 'traveler_reviews_subtitle', true),
            'show_platform_filter' => get_post_meta($post_id, 'traveler_reviews_show_filter', true) !== 'no',
            'reviews_per_page' => intval(get_post_meta($post_id, 'traveler_reviews_per_page', true)) ?: 9,
            'grid_columns' => intval(get_post_meta($post_id, 'traveler_reviews_columns', true)) ?: 3,
            'pagination_type' => get_post_meta($post_id, 'traveler_reviews_pagination', true) ?: 'show_more',
            'reviews' => $reviews,
        ];
    }

    /**
     * Generate schema.org Review markup for SEO
     */
    private function generate_review_schema(array $reviews): string
    {
        if (empty($reviews)) {
            return '';
        }

        $schema_reviews = [];
        foreach ($reviews as $review) {
            if (empty($review['author']) || empty($review['content'])) {
                continue;
            }

            $schema_reviews[] = [
                '@type' => 'Review',
                'author' => [
                    '@type' => 'Person',
                    'name' => $review['author'],
                ],
                'reviewRating' => [
                    '@type' => 'Rating',
                    'ratingValue' => $review['rating'],
                    'bestRating' => 5,
                ],
                'reviewBody' => wp_strip_all_tags($review['content']),
                'datePublished' => !empty($review['date']) ? $review['date'] : date('Y-m-d'),
            ];
        }

        if (empty($schema_reviews)) {
            return '';
        }

        return wp_json_encode($schema_reviews, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
