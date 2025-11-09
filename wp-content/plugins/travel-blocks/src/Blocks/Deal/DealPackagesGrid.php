<?php
/**
 * Deal Packages Grid Block
 *
 * Displays packages included in a deal as a grid of cards
 *
 * @package Travel\Blocks\Blocks\Deal
 * @since 1.3.0
 */

namespace Travel\Blocks\Blocks\Deal;

class DealPackagesGrid
{
    private string $name = 'deal-packages-grid';
    private string $title = 'Deal Packages Grid';
    private string $description = 'Displays packages included in this deal as a grid';

    /**
     * Register the block
     */
    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'travel',
            'icon' => 'grid-view',
            'keywords' => ['deal', 'packages', 'grid', 'tours'],
            'supports' => [
                'align' => ['wide', 'full'],
                'anchor' => true,
                'html' => false,
            ],
            'attributes' => [
                'columns' => [
                    'type' => 'number',
                    'default' => 2,
                ],
            ],
            'render_callback' => [$this, 'render'],
        ]);

        // Enqueue assets
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    /**
     * Enqueue block-specific assets
     */
    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            wp_enqueue_style(
                'deal-packages-grid-style',
                TRAVEL_BLOCKS_URL . 'assets/blocks/deal-packages-grid.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );
        }
    }

    /**
     * Render block content
     */
    public function render($attributes, $content, $block): string
    {
        // Get current post ID (should be a deal post)
        $post_id = get_the_ID();

        if (!$post_id || get_post_type($post_id) !== 'deal') {
            return $this->render_preview_fallback();
        }

        $packages = get_post_meta($post_id, 'packages', true);

        if (empty($packages) || !is_array($packages)) {
            return '<p class="deal-packages-grid__empty">' . esc_html__('No packages selected for this deal.', 'travel-blocks') . '</p>';
        }

        $data = [
            'packages' => $this->get_packages_data($packages),
            'columns'  => isset($attributes['columns']) ? intval($attributes['columns']) : 2,
        ];

        return $this->get_template('deal-packages-grid', $data);
    }

    /**
     * Get packages data for rendering
     */
    private function get_packages_data(array $package_ids): array
    {
        $packages = [];

        foreach ($package_ids as $package_id) {
            $package_id = intval($package_id);

            if (!$package_id || get_post_status($package_id) !== 'publish') {
                continue;
            }

            $packages[] = [
                'id'              => $package_id,
                'title'           => get_the_title($package_id),
                'url'             => get_permalink($package_id),
                'excerpt'         => get_the_excerpt($package_id),
                'thumbnail_id'    => get_post_thumbnail_id($package_id),
                'thumbnail_url'   => get_the_post_thumbnail_url($package_id, 'large'),
                'duration'        => get_post_meta($package_id, 'duration', true),
                'difficulty'      => get_post_meta($package_id, 'physical_difficulty', true),
                'origin'          => get_post_meta($package_id, 'departure', true),
                'price_from'      => $this->get_package_price($package_id),
                'promo_tag'       => get_post_meta($package_id, 'promo_tag', true),
                'promo_color'     => get_post_meta($package_id, 'promo_color', true),
            ];
        }

        return $packages;
    }

    /**
     * Get package lowest price from departures
     */
    private function get_package_price(int $package_id): ?float
    {
        $departures = get_post_meta($package_id, 'departures', true);

        if (empty($departures) || !is_array($departures)) {
            return null;
        }

        $prices = array_column($departures, 'price');
        return !empty($prices) ? min($prices) : null;
    }

    /**
     * Preview fallback
     */
    private function render_preview_fallback(): string
    {
        $data = [
            'packages' => [
                [
                    'id' => 1,
                    'title' => 'Machu Picchu Full Day Tour',
                    'url' => '#',
                    'excerpt' => 'Experience the wonder of Machu Picchu on this full-day guided tour.',
                    'thumbnail_url' => '',
                    'duration' => '1 day',
                    'difficulty' => 'Easy',
                    'origin' => 'Cusco',
                    'price_from' => 299,
                    'promo_tag' => 'BEST SELLER',
                    'promo_color' => '#ff6b6b',
                ],
                [
                    'id' => 2,
                    'title' => 'Rainbow Mountain Trek',
                    'url' => '#',
                    'excerpt' => 'Hike to the stunning Rainbow Mountain and witness nature\'s palette.',
                    'thumbnail_url' => '',
                    'duration' => '1 day',
                    'difficulty' => 'Moderate',
                    'origin' => 'Cusco',
                    'price_from' => 79,
                    'promo_tag' => 'POPULAR',
                    'promo_color' => '#4ecdc4',
                ],
            ],
            'columns' => 2,
        ];

        return $this->get_template('deal-packages-grid', $data);
    }

    /**
     * Load template and return HTML
     */
    private function get_template(string $template_name, array $data): string
    {
        $template_path = TRAVEL_BLOCKS_PATH . 'templates/' . $template_name . '.php';

        if (!file_exists($template_path)) {
            return '';
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $template_path;
        return ob_get_clean();
    }
}
