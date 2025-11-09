<?php
namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class RelatedPostsGrid
{
    private string $name = 'related-posts-grid';
    private string $title = 'Related Posts Grid';
    private string $description = 'Display related blog posts in a grid';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'grid-view',
            'keywords' => ['related', 'posts', 'blog', 'articles'],
            'supports' => ['anchor' => true, 'html' => false],
            'render_callback' => [$this, 'render'],
            'show_in_rest' => true,
        ]);
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            wp_enqueue_style('related-posts-grid-style', TRAVEL_BLOCKS_URL . 'assets/blocks/related-posts-grid.css', [], TRAVEL_BLOCKS_VERSION);
        }
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            $posts = $is_preview ? $this->get_preview_data() : $this->get_post_data($post_id);
            if (empty($posts)) return '';

            $data = [
                'block_id' => 'related-posts-grid-' . uniqid(),
                'class_name' => 'related-posts-grid' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
                'posts' => $posts,
                'section_title' => __('Take a look to this reading!', 'travel-blocks'),
                'is_preview' => $is_preview,
            ];

            ob_start();
            $this->load_template('related-posts-grid', $data);
            return ob_get_clean();
        } catch (\Exception $e) {
            return defined('WP_DEBUG') && WP_DEBUG ? '<div style="padding:10px;background:#ffebee;">Error: ' . esc_html($e->getMessage()) . '</div>' : '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Top 10 Travel Tips for South America',
                'permalink' => '#',
                'thumbnail' => '',
                'excerpt' => 'Discover the best tips for traveling through South America on a budget.',
                'categories' => [['name' => 'Travel Tips', 'slug' => 'travel-tips']],
                'date' => 'October 15, 2025',
            ],
            [
                'id' => 2,
                'title' => 'Hidden Gems in Peru',
                'permalink' => '#',
                'thumbnail' => '',
                'excerpt' => 'Explore lesser-known destinations in Peru beyond Machu Picchu.',
                'categories' => [['name' => 'Destinations', 'slug' => 'destinations']],
                'date' => 'October 10, 2025',
            ],
            [
                'id' => 3,
                'title' => 'Sustainable Tourism Guide',
                'permalink' => '#',
                'thumbnail' => '',
                'excerpt' => 'Learn how to travel responsibly and minimize your environmental impact.',
                'categories' => [['name' => 'Sustainability', 'slug' => 'sustainability']],
                'date' => 'October 5, 2025',
            ],
        ];
    }

    private function get_post_data(int $post_id): array
    {
        // Get related posts by destination taxonomy
        $destinations = wp_get_post_terms($post_id, 'destination', ['fields' => 'ids']);

        $args = [
            'post_type' => 'post',
            'posts_per_page' => 3,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        // If we have destinations, try to find posts with same destination
        if (!empty($destinations) && !is_wp_error($destinations)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'destination',
                    'field' => 'term_id',
                    'terms' => $destinations,
                ],
            ];
        }

        $query = new \WP_Query($args);
        $posts = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_data = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'excerpt' => get_the_excerpt(),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium_large'),
                    'date' => get_the_date(),
                    'categories' => [],
                ];

                $categories = get_the_category();
                if (!empty($categories)) {
                    foreach ($categories as $category) {
                        $post_data['categories'][] = [
                            'name' => $category->name,
                            'slug' => $category->slug,
                        ];
                    }
                }

                $posts[] = $post_data;
            }
            wp_reset_postdata();
        }

        return $posts;
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
