<?php
/**
 * Block: Pricing Card
 *
 * Sticky sidebar card with:
 * - Duration with clock icon
 * - Price display (From $X)
 * - CTA button
 * - Best months to go (chips)
 * - Top inclusions (pictograms)
 * - Guarantee bullets
 * - Social share icons
 *
 * NATIVE WORDPRESS BLOCK - Does NOT use ACF
 * Gets data from Package post meta fields
 *
 * @package Travel\Blocks\Blocks\Package
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class PricingCard
{
    private string $name = 'pricing-card';
    private string $title = 'Pricing Card';
    private string $description = 'Tarjeta de precio sticky para sidebar con duración, precio, CTA, meses recomendados, inclusiones y garantías';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'money-alt',
            'keywords' => ['pricing', 'price', 'card', 'sidebar', 'cta', 'booking', 'package'],
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
            'pricing-card-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/pricing-card.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            if ($is_preview) {
                $data = $this->get_preview_data();
            } else {
                $data = $this->get_post_data($post_id);
            }

            $block_id = 'pricing-card-' . uniqid();
            $class_name = 'pricing-card';

            if (!empty($attributes['className'])) {
                $class_name .= ' ' . $attributes['className'];
            }

            $data['block_id'] = $block_id;
            $data['class_name'] = $class_name;
            $data['is_preview'] = $is_preview;

            ob_start();
            $this->load_template('pricing-card', $data);
            return ob_get_clean();

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return '<div style="padding: 10px; background: #ffebee; border: 1px solid #f44336;">' .
                       '<p>Error en Pricing Card: ' . esc_html($e->getMessage()) . '</p>' .
                       '</div>';
            }
            return '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            'price' => 450,
            'duration_number' => '4',
            'duration_text' => 'days / 3 nights',
            'accommodation' => '2 Nights hotel, 2 Nights camping',
            'meals' => [
                'breakfast' => 4,
                'lunch' => 3,
                'dinner' => 3,
            ],
        ];
    }

    private function get_post_data(int $post_id): array
    {
        // Price
        $price_offer = floatval(get_field('price_offer', $post_id));
        $price_from = floatval(get_field('price_from', $post_id));
        $price_normal = floatval(get_field('price_normal', $post_id));
        $price = $price_offer ?: ($price_from ?: $price_normal);

        // Duration - Get from 'days' field and calculate nights
        $days = intval(get_field('days', $post_id));
        $nights = $days > 0 ? $days - 1 : 0;

        $duration_number = $days > 0 ? (string)$days : '';
        $duration_text = '';
        if ($days > 0) {
            $duration_text = ($days === 1 ? 'day' : 'days');
            if ($nights > 0) {
                $duration_text .= ' / ' . $nights . ' ' . ($nights === 1 ? 'night' : 'nights');
            }
        }

        // Accommodation text
        $accommodation = get_field('accommodation', $post_id) ?: '';

        // Meals - Count from itinerary
        $meals = $this->count_meals_from_itinerary($post_id);

        return [
            'price' => $price,
            'duration_number' => $duration_number,
            'duration_text' => $duration_text,
            'accommodation' => $accommodation,
            'meals' => $meals,
        ];
    }

    private function count_meals_from_itinerary(int $post_id): array
    {
        $itinerary = get_field('itinerary', $post_id);
        $meals = ['breakfast' => 0, 'lunch' => 0, 'dinner' => 0];

        if (is_array($itinerary)) {
            foreach ($itinerary as $day) {
                // Skip inactive days
                if (isset($day['active']) && !$day['active']) {
                    continue;
                }

                // Count meals from items
                if (!empty($day['items']) && is_array($day['items'])) {
                    foreach ($day['items'] as $item) {
                        $type_service_id = $item['type_service'] ?? null;
                        if ($type_service_id) {
                            $term = get_term($type_service_id, 'type_service');
                            if ($term && !is_wp_error($term)) {
                                $service_name = strtolower($term->name);
                                if (strpos($service_name, 'breakfast') !== false || strpos($service_name, 'desayuno') !== false) {
                                    $meals['breakfast']++;
                                } elseif (strpos($service_name, 'lunch') !== false || strpos($service_name, 'almuerzo') !== false) {
                                    $meals['lunch']++;
                                } elseif (strpos($service_name, 'dinner') !== false || strpos($service_name, 'cena') !== false) {
                                    $meals['dinner']++;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $meals;
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
