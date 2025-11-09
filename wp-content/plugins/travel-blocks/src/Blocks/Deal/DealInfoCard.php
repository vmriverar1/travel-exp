<?php
/**
 * Deal Info Card Block
 *
 * Displays deal discount, validity dates, and CTA in a sidebar card
 *
 * @package Travel\Blocks\Blocks\Deal
 * @since 1.3.0
 */

namespace Travel\Blocks\Blocks\Deal;

class DealInfoCard
{
    private string $name = 'deal-info-card';
    private string $title = 'Deal Info Card';
    private string $description = 'Displays deal discount percentage, validity dates, and booking CTA';

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
            'icon' => 'tag',
            'keywords' => ['deal', 'discount', 'offer', 'promo'],
            'supports' => [
                'anchor' => true,
                'html' => false,
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
                'deal-info-card-style',
                TRAVEL_BLOCKS_URL . 'assets/blocks/deal-info-card.css',
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

        $data = $this->get_deal_data($post_id);

        if (empty($data)) {
            return '';
        }

        return $this->get_template('deal-info-card', $data);
    }

    /**
     * Get deal data for rendering
     */
    private function get_deal_data(int $post_id): array
    {
        $active = get_post_meta($post_id, 'active', true);
        $start_date = get_post_meta($post_id, 'start_date', true);
        $end_date = get_post_meta($post_id, 'end_date', true);
        $discount = get_post_meta($post_id, 'discount_percentage', true);

        // Calculate deal status
        $now = current_time('timestamp');
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);

        $is_active = false;
        $status = 'expired';

        if ($active && $start_timestamp && $end_timestamp) {
            if ($now < $start_timestamp) {
                $status = 'scheduled';
            } elseif ($now >= $start_timestamp && $now <= $end_timestamp) {
                $status = 'active';
                $is_active = true;
            } else {
                $status = 'expired';
            }
        }

        return [
            'discount_percentage' => $discount ? intval($discount) : 0,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'start_date_formatted' => $start_date ? date_i18n('M j, Y', strtotime($start_date)) : '',
            'end_date_formatted' => $end_date ? date_i18n('M j, Y', strtotime($end_date)) : '',
            'is_active' => $is_active,
            'status' => $status,
        ];
    }

    /**
     * Preview fallback when not on a deal post
     */
    private function render_preview_fallback(): string
    {
        $data = [
            'discount_percentage' => 20,
            'start_date' => current_time('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s', strtotime('+30 days')),
            'start_date_formatted' => date_i18n('M j, Y'),
            'end_date_formatted' => date_i18n('M j, Y', strtotime('+30 days')),
            'is_active' => true,
            'status' => 'active',
        ];

        return $this->get_template('deal-info-card', $data);
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
