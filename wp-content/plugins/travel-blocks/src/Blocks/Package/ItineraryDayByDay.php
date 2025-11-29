<?php
/**
 * Block: Itinerary Day-by-Day
 *
 * Accordion-style itinerary display with expandable days
 *
 * NATIVE WORDPRESS BLOCK - Does NOT use ACF
 * Gets data from Package post meta fields
 *
 * @package Travel\Blocks\Blocks\Package
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class ItineraryDayByDay
{
    private string $name = 'itinerary-day-by-day';
    private string $title = 'Itinerary Day-by-Day';
    private string $description = 'Accordion-style day-by-day itinerary with activities, meals, and accommodation';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'list-view',
            'keywords' => ['itinerary', 'schedule', 'days', 'accordion', 'package'],
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
        // Enqueue Swiper from CDN
        wp_enqueue_style(
            'swiper-css',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
            [],
            '11.0.0'
        );

        wp_enqueue_script(
            'swiper-js',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
            [],
            '11.0.0',
            true
        );

        wp_enqueue_style(
            'itinerary-day-by-day-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/itinerary-day-by-day.css',
            ['swiper-css'],
            TRAVEL_BLOCKS_VERSION
        );

        // Accordion functionality (no dependencies on Swiper)
        wp_enqueue_script(
            'itinerary-day-by-day-script',
            TRAVEL_BLOCKS_URL . 'assets/blocks/itinerary-day-by-day.js',
            [],
            TRAVEL_BLOCKS_VERSION,
            true
        );

        // Swiper gallery initialization (depends on Swiper)
        wp_enqueue_script(
            'itinerary-swiper-script',
            TRAVEL_BLOCKS_URL . 'assets/blocks/itinerary-swiper.js',
            ['swiper-js'],
            TRAVEL_BLOCKS_VERSION,
            true
        );
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            if ($is_preview) {
                $days = $this->get_preview_data();
            } else {
                $days = $this->get_post_data($post_id);
            }

            if (empty($days)) {
                return '';
            }

            $block_id = 'itinerary-' . uniqid();
            $class_name = 'itinerary-day-by-day itinerary-day-by-day--default';

            if (!empty($attributes['className'])) {
                $class_name .= ' ' . $attributes['className'];
            }

            $data = [
                'block_id' => $block_id,
                'class_name' => $class_name,
                'days' => $days,
                'accordion_style' => 'default',
                'default_state' => 'first_open',
                'show_day_numbers' => true,
                'show_meals' => true,
                'show_accommodation' => true,
                'is_preview' => $is_preview,
            ];

            ob_start();
            $this->load_template('itinerary-day-by-day', $data);
            return ob_get_clean();

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return '<div style="padding: 10px; background: #ffebee; border: 1px solid #f44336;">' .
                       '<p>Error en Itinerary Day-by-Day: ' . esc_html($e->getMessage()) . '</p>' .
                       '</div>';
            }
            return '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            [
                'day_number' => 1,
                'day_title' => 'Arrival in Cusco',
                'day_description' => '<p>Welcome to Cusco! Upon arrival at the airport, our team will greet you and transfer you to your hotel. Spend the rest of the day acclimating to the altitude.</p>',
                'day_gallery' => [],
                'day_items' => [
                    ['order' => 1, 'type_service' => 'Transfer', 'text' => 'Airport to hotel transfer'],
                    ['order' => 2, 'type_service' => 'Accommodation', 'text' => 'Hotel check-in'],
                    ['order' => 3, 'type_service' => 'Briefing', 'text' => 'Welcome meeting with your guide'],
                ],
                'day_accommodation' => 'Hotel Casa Andina Premium',
                'day_altitude' => '3400',
                'day_limit' => '',
            ],
            [
                'day_number' => 2,
                'day_title' => 'Sacred Valley Tour',
                'day_description' => '<p>Full-day excursion to the Sacred Valley, visiting Pisac market and Ollantaytambo fortress.</p>',
                'day_gallery' => [],
                'day_items' => [
                    ['order' => 1, 'type_service' => 'Transport', 'text' => 'Private van to Sacred Valley'],
                    ['order' => 2, 'type_service' => 'Visit', 'text' => 'Pisac archaeological site and market'],
                    ['order' => 3, 'type_service' => 'Lunch', 'text' => 'Traditional Peruvian buffet'],
                    ['order' => 4, 'type_service' => 'Visit', 'text' => 'Ollantaytambo fortress'],
                ],
                'day_accommodation' => 'Sacred Valley Hotel',
                'day_altitude' => '2900',
                'day_limit' => '',
            ],
            [
                'day_number' => 3,
                'day_title' => 'Machu Picchu',
                'day_description' => '<p>Early morning train to Aguas Calientes, then bus up to Machu Picchu for a guided tour of the ancient Inca citadel.</p>',
                'day_gallery' => [],
                'day_items' => [
                    ['order' => 1, 'type_service' => 'Train', 'text' => 'Expedition train to Aguas Calientes'],
                    ['order' => 2, 'type_service' => 'Transport', 'text' => 'Bus to Machu Picchu'],
                    ['order' => 3, 'type_service' => 'Guide', 'text' => 'Guided tour of Machu Picchu (2.5 hours)'],
                    ['order' => 4, 'type_service' => 'Free Time', 'text' => 'Explore on your own'],
                ],
                'day_accommodation' => 'Hotel Casa Andina Premium',
                'day_altitude' => '2430',
                'day_limit' => '',
            ],
        ];
    }

    private function get_post_data(int $post_id): array
    {
        $itinerary = get_field('itinerary', $post_id);

        if (!is_array($itinerary) || empty($itinerary)) {
            return [];
        }

        $days = [];
        foreach ($itinerary as $index => $day) {
            // Skip inactive days
            if (isset($day['active']) && !$day['active']) {
                continue;
            }

            // Get day number from order field or index
            $day_number = !empty($day['order']) ? $day['order'] : ($index + 1);

            // Get gallery images
            $gallery = [];
            if (!empty($day['gallery']) && is_array($day['gallery'])) {
                foreach ($day['gallery'] as $image) {
                    // ACF returns full image array when return_format is 'array'
                    if (is_array($image) && !empty($image['url'])) {
                        $gallery[] = [
                            'url' => $image['url'],
                            'alt' => $image['alt'] ?? '',
                        ];
                    }
                    // Fallback for ID-only format
                    elseif (is_numeric($image)) {
                        $image_data = wp_get_attachment_image_src($image, 'large');
                        if ($image_data) {
                            $gallery[] = [
                                'url' => $image_data[0],
                                'alt' => get_post_meta($image, '_wp_attachment_image_alt', true),
                            ];
                        }
                    }
                }
            }

            // Get items/services for this day
            $items = [];
            if (!empty($day['items']) && is_array($day['items'])) {
                foreach ($day['items'] as $item) {
                    $type_service_id = $item['type_service'] ?? null;
                    $type_service_name = '';
                    if ($type_service_id) {
                        $term = get_term($type_service_id, 'type_service');
                        if ($term && !is_wp_error($term)) {
                            $type_service_name = $term->name;
                        }
                    }

                    $items[] = [
                        'order' => $item['order'] ?? 1,
                        'type_service' => $type_service_name,
                        'text' => $item['text'] ?? '',
                    ];
                }

                // Sort items by order
                usort($items, function($a, $b) {
                    return $a['order'] - $b['order'];
                });
            }

            $days[] = [
                'day_number' => $day_number,
                'day_title' => $day['title'] ?? '',
                'day_description' => $day['content'] ?? '',
                'day_gallery' => $gallery,
                'day_items' => $items,
                'day_accommodation' => $day['accommodation'] ?? '',
                'day_altitude' => $day['altitude'] ?? '',
                'day_limit' => $day['limit'] ?? '',
            ];
        }

        return $days;
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
