<?php

namespace TravelSearch\Shortcode;

use WP_Query;
use TravelSearch\View\PackagesRenderer;

if (!defined('ABSPATH')) {
    exit;
}

class PostsSearchShortcode
{
    public const TAG = 'posts_search';

    public function register(): void
    {
        add_shortcode(self::TAG, [$this, 'render']);
    }

    public function render(array $atts = [], ?string $content = null, string $tag = ''): string
    {
        wp_enqueue_style('travel-search-front');

        $text_search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

        ob_start();
        ?>
        <div class="ts-posts-results">
            <?php
            // Solo buscar si hay texto de búsqueda
            if (!empty($text_search)) {
                $args = [
                    'post_type'      => 'post',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    's'              => $text_search,
                ];

                $query    = new WP_Query($args);
                $renderer = new PackagesRenderer();

                echo $renderer->render($query, 'Post', 'Posts');
            } else {
                // No hay búsqueda activa
                echo '<p>' . esc_html__('Use the search bar to find posts.', 'travel-search') . '</p>';
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
