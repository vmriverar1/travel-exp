<?php
namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class DatesAndPrices
{
    private string $name = 'dates-and-prices';
    private string $title = 'Dates and Prices';
    private string $description = 'Display departure dates with pricing';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'calendar-alt',
            'keywords' => ['dates', 'prices', 'departures', 'calendar'],
            'supports' => ['anchor' => true, 'html' => false],
            'render_callback' => [$this, 'render'],
            'show_in_rest' => true,
            'uses_context' => ['postId', 'postType'],
        ]);
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        // Enqueue styles for both frontend and editor
        wp_enqueue_style('dates-and-prices-style', TRAVEL_BLOCKS_URL . 'assets/blocks/dates-and-prices.css', [], TRAVEL_BLOCKS_VERSION);

        // Enqueue scripts only on frontend
        if (!is_admin()) {
            wp_enqueue_script('dates-and-prices-script', TRAVEL_BLOCKS_URL . 'assets/blocks/dates-and-prices.js', [], TRAVEL_BLOCKS_VERSION, true);
        }
    }

    public function render($attributes, $content, $block): string
    {
        try {
            // Get post ID from block context or current post
            $post_id = null;

            // Check if block is WP_Block object (WordPress 5.5+)
            if (is_object($block) && isset($block->context['postId'])) {
                $post_id = $block->context['postId'];
            } elseif (is_array($block) && isset($block['postId'])) {
                $post_id = $block['postId'];
            }

            // Fallback to current post
            if (empty($post_id)) {
                $post_id = get_the_ID();
            }

            // Determine if we're in preview/editor mode
            $is_preview = empty($post_id) || EditorHelper::is_editor_mode($post_id);

            // Get dates: use preview data if no post_id or in editor mode
            $dates = $is_preview ? $this->get_preview_data() : $this->get_post_data($post_id);

            // If no dates, show empty state instead of nothing
            if (empty($dates)) {
                $data = [
                    'block_id' => 'booking-' . uniqid(),
                    'class_name' => 'dates-and-prices booking booking--empty' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
                    'grouped_dates' => [],
                    'is_preview' => $is_preview,
                ];
                ob_start();
                $this->load_template('dates-and-prices', $data);
                return ob_get_clean();
            }

            // Group dates by year/month
            $grouped_dates = $this->group_dates_by_year_month($dates);

            // Extract available years
            $available_years = array_keys($grouped_dates);

            // Determine initial year/month
            $current_year = !empty($available_years) ? (string)$available_years[0] : (string)date('Y');
            $current_month = (string)date('m');

            // If current month doesn't have dates in the first year, use first available month
            if (!isset($grouped_dates[$current_year][$current_month])) {
                $months = array_keys($grouped_dates[$current_year]);
                $current_month = !empty($months) ? $months[0] : '01';
            }

            $data = [
                'block_id' => 'booking-' . uniqid(),
                'class_name' => 'dates-and-prices booking' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
                'grouped_dates' => $grouped_dates,
                'all_dates' => $dates,
                'available_years' => $available_years,
                'current_year' => $current_year,
                'current_month' => $current_month,
                'currency_symbol' => 'USD $',
                'button_text' => __('BOOK NOW', 'travel-blocks'),
                'alert_message' => __('Secure your spot on the trip now with our real-time availability information.', 'travel-blocks'),
                'alert_emphasis' => __('Act quickly—these spots sell out fast!', 'travel-blocks'),
                'is_preview' => $is_preview,
                'package_id' => $post_id,
            ];

            ob_start();
            $this->load_template('dates-and-prices', $data);
            return ob_get_clean();
        } catch (\Exception $e) {
            return defined('WP_DEBUG') && WP_DEBUG ? '<div style="padding:10px;background:#ffebee;">Error: ' . esc_html($e->getMessage()) . '</div>' : '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            // ========== OCTUBRE 2025 (última semana) ==========
            [
                'date' => '2025-10-29',
                'return_date' => '2025-11-01',
                'price' => 450,
                'availability' => 'available',
                'spaces_left' => 10,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-10-31',
                'return_date' => '2025-11-03',
                'price' => 450,
                'availability' => 'limited',
                'spaces_left' => 4,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],

            // ========== NOVIEMBRE 2025 (8 fechas - 2 por semana) ==========
            [
                'date' => '2025-11-03',
                'return_date' => '2025-11-06',
                'price' => 450,
                'availability' => 'available',
                'spaces_left' => 12,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-11-07',
                'return_date' => '2025-11-10',
                'price' => 450,
                'availability' => 'available',
                'spaces_left' => 15,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-11-10',
                'return_date' => '2025-11-13',
                'price' => 450,
                'availability' => 'limited',
                'spaces_left' => 5,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-11-14',
                'return_date' => '2025-11-17',
                'price' => 450,
                'availability' => 'available',
                'spaces_left' => 10,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-11-17',
                'return_date' => '2025-11-20',
                'price' => 450,
                'availability' => 'available',
                'spaces_left' => 14,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-11-21',
                'return_date' => '2025-11-24',
                'price' => 730,
                'availability' => 'available',
                'spaces_left' => 8,
                'has_deal' => true,
                'original_price' => 1499,
                'discount_percentage' => 45,
                'deal_label' => '45% Off',
            ],
            [
                'date' => '2025-11-24',
                'return_date' => '2025-11-27',
                'price' => 450,
                'availability' => 'available',
                'spaces_left' => 11,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-11-28',
                'return_date' => '2025-12-01',
                'price' => 450,
                'availability' => 'limited',
                'spaces_left' => 3,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],

            // ========== DICIEMBRE 2025 (8 fechas - 2 por semana) ==========
            [
                'date' => '2025-12-01',
                'return_date' => '2025-12-04',
                'price' => 550,
                'availability' => 'available',
                'spaces_left' => 15,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-12-05',
                'return_date' => '2025-12-08',
                'price' => 550,
                'availability' => 'available',
                'spaces_left' => 12,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-12-08',
                'return_date' => '2025-12-11',
                'price' => 650,
                'availability' => 'available',
                'spaces_left' => 8,
                'has_deal' => true,
                'original_price' => 1200,
                'discount_percentage' => 30,
                'deal_label' => '30% Off',
            ],
            [
                'date' => '2025-12-12',
                'return_date' => '2025-12-15',
                'price' => 550,
                'availability' => 'available',
                'spaces_left' => 14,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-12-15',
                'return_date' => '2025-12-18',
                'price' => 550,
                'availability' => 'limited',
                'spaces_left' => 3,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-12-19',
                'return_date' => '2025-12-22',
                'price' => 550,
                'availability' => 'available',
                'spaces_left' => 10,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-12-22',
                'return_date' => '2025-12-25',
                'price' => 550,
                'availability' => 'limited',
                'spaces_left' => 4,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2025-12-29',
                'return_date' => '2026-01-01',
                'price' => 650,
                'availability' => 'sold_out',
                'spaces_left' => 0,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],

            // ========== ENERO 2026 (8 fechas - 2 por semana) ==========
            [
                'date' => '2026-01-02',
                'return_date' => '2026-01-05',
                'price' => 495,
                'availability' => 'available',
                'spaces_left' => 10,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-01-05',
                'return_date' => '2026-01-08',
                'price' => 495,
                'availability' => 'available',
                'spaces_left' => 14,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-01-09',
                'return_date' => '2026-01-12',
                'price' => 495,
                'availability' => 'available',
                'spaces_left' => 12,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-01-12',
                'return_date' => '2026-01-15',
                'price' => 495,
                'availability' => 'available',
                'spaces_left' => 11,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-01-16',
                'return_date' => '2026-01-19',
                'price' => 399,
                'availability' => 'limited',
                'spaces_left' => 4,
                'has_deal' => true,
                'original_price' => 799,
                'discount_percentage' => 50,
                'deal_label' => '50% Off',
            ],
            [
                'date' => '2026-01-19',
                'return_date' => '2026-01-22',
                'price' => 495,
                'availability' => 'available',
                'spaces_left' => 13,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-01-23',
                'return_date' => '2026-01-26',
                'price' => 495,
                'availability' => 'available',
                'spaces_left' => 9,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-01-26',
                'return_date' => '2026-01-29',
                'price' => 495,
                'availability' => 'available',
                'spaces_left' => 10,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],

            // ========== FEBRERO 2026 ==========
            [
                'date' => '2026-02-04',
                'return_date' => '2026-02-07',
                'price' => 520,
                'availability' => 'available',
                'spaces_left' => 15,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-02-11',
                'return_date' => '2026-02-14',
                'price' => 520,
                'availability' => 'available',
                'spaces_left' => 11,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-02-18',
                'return_date' => '2026-02-21',
                'price' => 450,
                'availability' => 'limited',
                'spaces_left' => 2,
                'has_deal' => true,
                'original_price' => 899,
                'discount_percentage' => 40,
                'deal_label' => '40% Off',
            ],
            [
                'date' => '2026-02-25',
                'return_date' => '2026-02-28',
                'price' => 520,
                'availability' => 'available',
                'spaces_left' => 13,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],

            // ========== MARZO 2026 ==========
            [
                'date' => '2026-03-04',
                'return_date' => '2026-03-07',
                'price' => 545,
                'availability' => 'available',
                'spaces_left' => 14,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-03-11',
                'return_date' => '2026-03-14',
                'price' => 545,
                'availability' => 'available',
                'spaces_left' => 10,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-03-18',
                'return_date' => '2026-03-21',
                'price' => 545,
                'availability' => 'sold_out',
                'spaces_left' => 0,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-03-25',
                'return_date' => '2026-03-28',
                'price' => 545,
                'availability' => 'available',
                'spaces_left' => 8,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],

            // ========== ABRIL 2026 ==========
            [
                'date' => '2026-04-01',
                'return_date' => '2026-04-04',
                'price' => 575,
                'availability' => 'available',
                'spaces_left' => 12,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-04-08',
                'return_date' => '2026-04-11',
                'price' => 475,
                'availability' => 'limited',
                'spaces_left' => 3,
                'has_deal' => true,
                'original_price' => 950,
                'discount_percentage' => 35,
                'deal_label' => '35% Off',
            ],
            [
                'date' => '2026-04-15',
                'return_date' => '2026-04-18',
                'price' => 575,
                'availability' => 'available',
                'spaces_left' => 16,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-04-22',
                'return_date' => '2026-04-25',
                'price' => 575,
                'availability' => 'available',
                'spaces_left' => 9,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-04-29',
                'return_date' => '2026-05-02',
                'price' => 575,
                'availability' => 'available',
                'spaces_left' => 11,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],

            // ========== MAYO 2026 ==========
            [
                'date' => '2026-05-06',
                'return_date' => '2026-05-09',
                'price' => 599,
                'availability' => 'available',
                'spaces_left' => 15,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-05-13',
                'return_date' => '2026-05-16',
                'price' => 499,
                'availability' => 'available',
                'spaces_left' => 12,
                'has_deal' => true,
                'original_price' => 999,
                'discount_percentage' => 50,
                'deal_label' => '50% Off',
            ],
            [
                'date' => '2026-05-20',
                'return_date' => '2026-05-23',
                'price' => 599,
                'availability' => 'limited',
                'spaces_left' => 4,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
            [
                'date' => '2026-05-27',
                'return_date' => '2026-05-30',
                'price' => 599,
                'availability' => 'available',
                'spaces_left' => 10,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ],
        ];
    }

    /**
     * Get departure dates by combining automatic generation + manual overrides
     * 
     * UNIVERSAL DATA STRUCTURE:
     * - Months (months field): Determines which months are active
     * - Weekdays (fixed_departures field): Determines which days of week
     * - Start Day (free_spot_start_day field): Starting day of month
     * - Departure Dates (departure_dates repeater): Override/add specific dates
     * 
     * @param int $post_id Package post ID
     * @return array Array of date entries
     */
    private function get_post_data(int $post_id): array
    {
        // Check if package has tour_id for API integration
        $tour_id = get_field('tour_id', $post_id);

        if (!empty($tour_id)) {
            // Use API data if tour_id is configured
            return $this->get_api_data($post_id, intval($tour_id));
        }

        // FALLBACK: Continue with original logic if no tour_id
        // Get schedule configuration
        $months = get_field('months', $post_id) ?: [];
        $weekdays = get_field('fixed_departures', $post_id) ?: [];
        $start_day = intval(get_field('free_spot_start_day', $post_id) ?: 1);
        $duration = intval(get_field('days', $post_id) ?: 1);
        
        // Get default values
        $default_spots = intval(get_field('default_spots', $post_id) ?: 12);
        $price_from = floatval(get_field('price_from', $post_id) ?: 0);
        $price_normal = floatval(get_field('price_normal', $post_id) ?: 0);
        
        // Generate automatic dates (3 years ahead)
        $automatic_dates = $this->generate_automatic_dates($months, $weekdays, $start_day, 3);
        
        // Initialize with automatic dates
        $all_dates = [];
        foreach ($automatic_dates as $date_str) {
            $all_dates[$date_str] = [
                'date' => $date_str,
                'return_date' => date('Y-m-d', strtotime("+{$duration} days", strtotime($date_str))),
                'spots' => $default_spots,
                'price' => $price_from,
                'status' => 'available',
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
            ];
        }
        
        // Override/add exception dates
        $exception_dates = $this->get_departure_dates($post_id);
        foreach ($exception_dates as $date_key => $exception_entry) {
            $all_dates[$date_key] = $exception_entry;
        }

        // Convert to final format with availability logic
        $dates = [];
        foreach ($all_dates as $entry) {
            $availability = $this->calculate_availability($entry['status'], $entry['spots']);

            $dates[] = [
                'date' => $entry['date'],
                'return_date' => $entry['return_date'],
                'price' => $entry['price'],
                'availability' => $availability,
                'spaces_left' => $entry['spots'],
                'has_deal' => $entry['has_deal'] ?? false,
                'original_price' => $entry['original_price'] ?? null,
                'discount_percentage' => $entry['discount_percentage'] ?? 0,
                'deal_label' => $entry['deal_label'] ?? '',
            ];
        }
        
        // Sort by date ascending
        usort($dates, function($a, $b) {
            return strcmp($a['date'], $b['date']);
        });
        
        return $dates;
    }

    /**
     * Get departure exceptions from repeater field
     *
     * Departure exceptions can:
     * 1. Override existing automatic dates (if date matches)
     * 2. Add new dates not in the automatic calendar
     *
     * @param int $post_id
     * @return array
     */
    private function get_departure_dates(int $post_id): array
    {
        $departure_dates = [];
        $duration = intval(get_field('days', $post_id) ?: 1);
        $default_price = floatval(get_field('price_from', $post_id) ?: 0);

        // Get repeater data from departure_exceptions field
        $repeater_data = get_field('departure_exceptions', $post_id);

        if (empty($repeater_data) || !is_array($repeater_data)) {
            return $departure_dates;
        }

        foreach ($repeater_data as $row) {
            $date = $row['date'] ?? '';

            if (empty($date)) {
                continue;
            }

            // Skip past dates
            $date_timestamp = strtotime($date . ' 00:00:00');
            $today_timestamp = strtotime('today 00:00:00');
            if ($date_timestamp < $today_timestamp) {
                continue;
            }

            // Get exception values
            $spots = intval($row['spots'] ?? 12);
            $price_regular = floatval($row['price_regular'] ?? 0);
            $price_offer = floatval($row['price_offer'] ?? 0);

            // Determine final price and deal status
            $has_deal = false;
            $final_price = $default_price;
            $original_price = null;
            $discount_percentage = 0;
            $deal_label = '';

            // If has offer price
            if ($price_offer > 0) {
                $final_price = $price_offer;

                // If also has regular price, calculate discount
                if ($price_regular > 0 && $price_regular > $price_offer) {
                    $has_deal = true;
                    $original_price = $price_regular;
                    $discount_percentage = round((($price_regular - $price_offer) / $price_regular) * 100);
                    $deal_label = $discount_percentage . '% Off';
                }
            }
            // If only has regular price (no offer)
            elseif ($price_regular > 0) {
                $final_price = $price_regular;
            }

            // Determine status based on spots
            $status = 'available';
            if ($spots === 0) {
                $status = 'sold_out';
            } elseif ($spots <= 5) {
                $status = 'few_spots';
            }

            $departure_dates[$date] = [
                'date' => $date,
                'return_date' => date('Y-m-d', strtotime("+{$duration} days", strtotime($date))),
                'spots' => $spots,
                'price' => $final_price,
                'status' => $status,
                'has_deal' => $has_deal,
                'original_price' => $original_price,
                'discount_percentage' => $discount_percentage,
                'deal_label' => $deal_label,
            ];
        }

        return $departure_dates;
    }

    /**
     * Calculate availability state based on status and spots
     * 
     * @param string $status
     * @param int $spots
     * @return string available|limited|sold_out
     */
    private function calculate_availability(string $status, int $spots): string
    {
        if ($status === 'sold_out' || $spots === 0) {
            return 'sold_out';
        }
        
        if ($status === 'few_spots' || $spots <= 5) {
            return 'limited';
        }
        
        return 'available';
    }

    /**
     * Generate automatic departure dates based on months, weekdays, and start day
     * 
     * @param array $months Array of month names (e.g., ['january', 'february'])
     * @param array $weekdays Array of weekday names (e.g., ['monday', 'wednesday', 'friday'])
     * @param int $start_day Day of month to start (1-31)
     * @param int $years_ahead Number of years to generate ahead (default: 3)
     * @return array Array of date strings in Y-m-d format
     */
    private function generate_automatic_dates(array $months, array $weekdays, int $start_day, int $years_ahead = 3): array
    {
        if (empty($months) || empty($weekdays)) {
            return [];
        }
        
        $dates = [];
        $current_year = intval(date('Y'));
        $current_month = intval(date('n'));
        $today = strtotime('today');
        
        // Month name to number mapping
        $month_map = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12,
        ];
        
        // Weekday name to number mapping (1=Monday, 7=Sunday)
        $weekday_map = [
            'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4,
            'friday' => 5, 'saturday' => 6, 'sunday' => 7,
        ];
        
        // Convert weekday names to numbers once
        $target_weekdays = [];
        foreach ($weekdays as $weekday_name) {
            if (isset($weekday_map[$weekday_name])) {
                $target_weekdays[] = $weekday_map[$weekday_name];
            }
        }
        
        if (empty($target_weekdays)) {
            return [];
        }
        
        // Generate dates for current year + years ahead
        for ($year = $current_year; $year <= $current_year + $years_ahead; $year++) {
            foreach ($months as $month_name) {
                if (!isset($month_map[$month_name])) continue;
                
                $month_num = $month_map[$month_name];
                
                // Skip past months in current year
                if ($year == $current_year && $month_num < $current_month) {
                    continue;
                }
                
                // Use DateTime for better performance
                $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month_num, $year);
                $first_day = new \DateTime(sprintf('%04d-%02d-%02d', $year, $month_num, min($start_day, $days_in_month)));
                $last_day = new \DateTime(sprintf('%04d-%02d-%02d', $year, $month_num, $days_in_month));
                
                // Iterate through each day
                $interval = new \DateInterval('P1D');
                $period = new \DatePeriod($first_day, $interval, $last_day->modify('+1 day'));
                
                foreach ($period as $date) {
                    // Skip if date is in the past
                    if ($date->getTimestamp() < $today) {
                        continue;
                    }
                    
                    // Check if this day matches any of the selected weekdays
                    $day_of_week = intval($date->format('N'));
                    
                    if (in_array($day_of_week, $target_weekdays, true)) {
                        $dates[] = $date->format('Y-m-d');
                    }
                }
            }
        }
        
        return $dates;
    }

    /**
     * Group dates by year and month
     *
     * @param array $dates Array of date entries
     * @return array Grouped dates [year][month][dates]
     */
    private function group_dates_by_year_month(array $dates): array
    {
        $grouped = [];

        foreach ($dates as $date) {
            if (empty($date['date'])) continue;

            $timestamp = strtotime($date['date']);
            if ($timestamp === false) continue;

            $year = date('Y', $timestamp);
            $month = date('m', $timestamp);

            if (!isset($grouped[$year])) {
                $grouped[$year] = [];
            }

            if (!isset($grouped[$year][$month])) {
                $grouped[$year][$month] = [];
            }

            $grouped[$year][$month][] = $date;
        }

        // Sort years ascending
        ksort($grouped);

        // Sort months within each year
        foreach ($grouped as $year => $months) {
            ksort($grouped[$year]);
        }

        return $grouped;
    }

    /**
     * Fetch calendar data from API for a specific tour, year, and month
     *
     * @param int $tour_id Tour ID
     * @param int $year Year (YYYY)
     * @param int $month Month (1-12)
     * @return array API response data or empty array on error
     */
    private function fetch_api_calendar(int $tour_id, int $year, int $month): array
    {
        $url = sprintf(
            'https://cms.valenciatravelcusco.com/packages/tours/%d/calendar?year=%d&month=%d',
            $tour_id,
            $year,
            $month
        );

        // Try wp_remote_get first
        $response = wp_remote_get($url, [
            'timeout' => 15,
            'sslverify' => false,
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
            ],
        ]);

        // If wp_remote_get fails, try file_get_contents as fallback
        if (is_wp_error($response)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('DatesAndPrices: wp_remote_get failed, trying file_get_contents: ' . $response->get_error_message());
            }

            // Try with file_get_contents
            $context = stream_context_create([
                'http' => [
                    'timeout' => 15,
                    'header' => "Accept: application/json\r\n",
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);

            $body = @file_get_contents($url, false, $context);

            if ($body === false) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('DatesAndPrices: file_get_contents also failed for ' . $url);
                }
                return [];
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('DatesAndPrices API Error: Invalid JSON response');
                }
                return [];
            }

            return is_array($data) ? $data : [];
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("DatesAndPrices API Error: HTTP {$status_code} for tour_id {$tour_id}");
            }
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('DatesAndPrices API Error: Invalid JSON response');
            }
            return [];
        }

        return is_array($data) ? $data : [];
    }

    /**
     * Transform API data to the format expected by the template
     *
     * @param array $api_data Raw API data (dates as keys)
     * @param int $duration Package duration in days
     * @param bool $promo_active Whether the package has an active promotion
     * @param string $anchor_id Anchor ID for fixed_week scroll action
     * @return array Transformed dates array
     */
    private function transform_api_data_to_dates(array $api_data, int $duration, bool $promo_active, string $anchor_id): array
    {
        $dates = [];
        $today = strtotime('today');

        foreach ($api_data as $date_str => $date_info) {
            $type = $date_info['type'] ?? '';
            $price = floatval($date_info['price'] ?? 0);
            $offer = isset($date_info['offer']) ? floatval($date_info['offer']) : null;
            $spots = intval($date_info['spots'] ?? 0);
            $single_supp = $date_info['singleSupp'] ?? '';

            // Calculate days until departure
            $departure_timestamp = strtotime($date_str);
            $days_until = intval(($departure_timestamp - $today) / 86400);

            // Calculate return date based on duration
            $return_date = date('Y-m-d', strtotime("+{$duration} days", strtotime($date_str)));

            // Initialize date entry
            $date_entry = [
                'date' => $date_str,
                'return_date' => $return_date,
                'price' => $price,
                'availability' => 'available',
                'spaces_left' => $spots,
                'has_deal' => false,
                'original_price' => null,
                'discount_percentage' => 0,
                'deal_label' => '',
                'type' => $type,
                'button_action' => 'default',
                'button_text' => null,
                'row_class' => '',
                'single_supp' => $single_supp,
                'anchor_id' => $anchor_id,
            ];

            // ========== APPLY LOGIC BY TYPE ==========

            if ($type === 'spots_api') {
                // spots_api: Días normales sin grupo asegurado
                if ($days_until <= 10) {
                    // Menos de 10 días → SOLD OUT
                    $date_entry['availability'] = 'sold_out';
                    $date_entry['button_text'] = 'SOLD OUT';
                } elseif ($spots > 50) {
                    // Más de 50 spots y más de 10 días → Contact Us (scroll al formulario)
                    $date_entry['availability'] = 'available';
                    $date_entry['button_text'] = 'CONTACT US';
                    $date_entry['button_action'] = 'scroll_to_anchor';
                } else {
                    // 50 o menos spots y más de 10 días → Book Now (abre aside)
                    $date_entry['availability'] = 'available';
                    $date_entry['button_text'] = 'BOOK NOW';
                    $date_entry['button_action'] = 'open_purchase_aside';
                }

            } elseif ($type === 'fidex_week') {
                // fidex_week: Salidas fijas semanales
                if ($spots <= 20) {
                    // 20 o menos espacios → SOLD OUT
                    $date_entry['availability'] = 'sold_out';
                    $date_entry['button_text'] = 'SOLD OUT';
                } elseif ($days_until > 30 && $spots > 100) {
                    // Más de 30 días y más de 100 spots → Book Now (abre aside)
                    $date_entry['availability'] = 'available';
                    $date_entry['button_text'] = 'BOOK NOW';
                    $date_entry['button_action'] = 'open_purchase_aside';
                } elseif ($days_until > 30 && $spots > 50) {
                    // Más de 30 días y más de 50 spots → Contact Us (scroll al formulario)
                    $date_entry['availability'] = 'limited';
                    $date_entry['button_text'] = 'CONTACT US';
                    $date_entry['button_action'] = 'scroll_to_anchor';
                } elseif ($days_until > 10 && $days_until <= 30 && $spots > 20) {
                    // Entre 10 y 30 días y más de 20 spots → Contact Us (scroll al formulario)
                    $date_entry['availability'] = 'limited';
                    $date_entry['button_text'] = 'CONTACT US';
                    $date_entry['button_action'] = 'scroll_to_anchor';
                } else {
                    // Otros casos → SOLD OUT
                    $date_entry['availability'] = 'sold_out';
                    $date_entry['button_text'] = 'SOLD OUT';
                }

                // MODO OFERTA: Solo si promo está activo
                if ($promo_active && $offer && $offer < $price) {
                    $date_entry['has_deal'] = true;
                    $date_entry['original_price'] = $price;
                    $date_entry['price'] = $offer;
                    $date_entry['discount_percentage'] = round((($price - $offer) / $price) * 100);
                    $date_entry['deal_label'] = $date_entry['discount_percentage'] . '% Off';
                    $date_entry['row_class'] = 'booking-row--promo-fixed-week';
                }

            } elseif ($type === 'fixed_dates') {
                // fixed_dates: Fechas aseguradas con pasajeros
                if ($spots <= 5) {
                    // 5 o menos espacios → SOLD OUT
                    $date_entry['availability'] = 'sold_out';
                    $date_entry['button_text'] = 'SOLD OUT';
                } elseif ($days_until > 10 && $spots > 50) {
                    // Más de 10 días y más de 50 spots → Book Now (abre aside)
                    $date_entry['availability'] = 'available';
                    $date_entry['button_text'] = 'BOOK NOW';
                    $date_entry['button_action'] = 'open_purchase_aside';
                } elseif ($days_until > 10 && $spots > 10) {
                    // Más de 10 días y más de 10 spots → Contact Us (scroll al formulario)
                    $date_entry['availability'] = 'limited';
                    $date_entry['button_text'] = 'CONTACT US';
                    $date_entry['button_action'] = 'scroll_to_anchor';
                } else {
                    // Otros casos → SOLD OUT
                    $date_entry['availability'] = 'sold_out';
                    $date_entry['button_text'] = 'SOLD OUT';
                }

                // SIEMPRE fondo amarillo y MODO OFERTA
                $date_entry['row_class'] = 'booking-row--promo-fixed-dates';
                if ($offer && $offer < $price) {
                    $date_entry['has_deal'] = true;
                    $date_entry['original_price'] = $price;
                    $date_entry['price'] = $offer;
                    $date_entry['discount_percentage'] = round((($price - $offer) / $price) * 100);
                    $date_entry['deal_label'] = $date_entry['discount_percentage'] . '% Off';
                }

            } elseif ($type === 'no_program') {
                // no_program: No programada → SOLD OUT
                $date_entry['availability'] = 'sold_out';
                $date_entry['button_text'] = 'SOLD OUT';
            }

            $dates[] = $date_entry;
        }

        return $dates;
    }

    /**
     * Get dates from API for a package with tour_id
     *
     * @param int $post_id Package post ID
     * @param int $tour_id Tour ID for API
     * @return array Dates array in template format
     */
    private function get_api_data(int $post_id, int $tour_id): array
    {
        // Get package configuration
        $duration = intval(get_field('days', $post_id) ?: 4);
        $promo_active = (bool)(get_field('promo', $post_id) ?? false);
        $anchor_id = get_field('booking_anchor_id', $post_id) ?: '#booking-form';

        $all_dates = [];

        // Fetch data for 2 years (current year + next year)
        $current_year = intval(date('Y'));
        $end_year = $current_year + 1;

        for ($year = $current_year; $year <= $end_year; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                // Skip past months in current year
                if ($year === $current_year && $month < intval(date('m'))) {
                    continue;
                }

                $api_data = $this->fetch_api_calendar($tour_id, $year, $month);

                if (!empty($api_data)) {
                    $transformed = $this->transform_api_data_to_dates($api_data, $duration, $promo_active, $anchor_id);
                    $all_dates = array_merge($all_dates, $transformed);
                }
            }
        }

        return $all_dates;
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
