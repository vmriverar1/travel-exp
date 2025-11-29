<?php
/**
 * Block: Dates and Prices (Manual)
 *
 * Manual version of the Dates and Prices block with ACF repeater fields.
 * Allows manual input of departure dates, prices, and availability.
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\ACF;

use Travel\Blocks\Core\BlockBase;

class DatesAndPricesManual extends BlockBase
{
    public function __construct()
    {
        $this->name        = 'dates-and-prices-manual';
        $this->title       = __('Dates and Prices (Manual)', 'travel-blocks');
        $this->description = __('Calendario de fechas y precios administrable manualmente con ACF', 'travel-blocks');
        $this->category    = 'travel';
        $this->icon        = 'calendar-alt';
        $this->keywords    = ['dates', 'prices', 'departures', 'calendar', 'manual', 'acf'];
        $this->mode        = 'preview';

        $this->supports = [
            'align' => false,
            'mode'  => true,
            'multiple' => true,
            'anchor' => true,
        ];
    }

    /**
     * Enqueue block-specific assets.
     */
    public function enqueue_assets(): void
    {
        // Enqueue CSS (same as original block)
        wp_enqueue_style(
            'dates-and-prices-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/dates-and-prices.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        // Enqueue JS (same as original block)
        wp_enqueue_script(
            'dates-and-prices-script',
            TRAVEL_BLOCKS_URL . 'assets/blocks/dates-and-prices.js',
            [],
            TRAVEL_BLOCKS_VERSION,
            true
        );
    }

    /**
     * Register block and its ACF fields.
     */
    public function register(): void
    {
        parent::register();

        // Register ACF fields for this block
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_block_dates_and_prices_manual',
                'title' => __('Dates and Prices (Manual)', 'travel-blocks'),
                'fields' => [
                    // Tours (repeater)
                    [
                        'key' => 'field_dapm_tours',
                        'label' => __('🎯 Tours', 'travel-blocks'),
                        'name' => 'tours',
                        'type' => 'repeater',
                        'instructions' => __('Agrega uno o más tours. El primero se mostrará por defecto. Los usuarios podrán seleccionar entre ellos con radio buttons.', 'travel-blocks'),
                        'required' => 1,
                        'min' => 1,
                        'max' => 10,
                        'layout' => 'table',
                        'button_label' => __('Agregar Tour', 'travel-blocks'),
                        'sub_fields' => [
                            [
                                'key' => 'field_dapm_tour_id',
                                'label' => __('Tour ID', 'travel-blocks'),
                                'name' => 'tour_id',
                                'type' => 'number',
                                'required' => 1,
                                'min' => 1,
                                'step' => 1,
                                'wrapper' => [
                                    'width' => '50',
                                ],
                            ],
                            [
                                'key' => 'field_dapm_tour_name',
                                'label' => __('Nombre del Tour', 'travel-blocks'),
                                'name' => 'tour_name',
                                'type' => 'text',
                                'required' => 1,
                                'placeholder' => __('Ej: Classic Inca Trail - 4 Days', 'travel-blocks'),
                                'wrapper' => [
                                    'width' => '50',
                                ],
                            ],
                        ],
                    ],
                    // Call Us Anchor
                    [
                        'key' => 'field_dapm_callus_anchor',
                        'label' => __('🔗 Call Us Anchor ID', 'travel-blocks'),
                        'name' => 'callus_anchor',
                        'type' => 'text',
                        'instructions' => __('ID del ancla para scroll cuando se hace clic en "CALL US" (ejemplo: #contact-form o #booking-form). Si se deja vacío, se usará #booking-form por defecto.', 'travel-blocks'),
                        'required' => 0,
                        'default_value' => '#booking-form',
                        'placeholder' => '#booking-form',
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/dates-and-prices-manual',
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * Render the block output.
     */
    public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        try {
            // Get tours and callus_anchor from ACF fields
            // For ACF blocks, we need to manually parse the repeater data from block['data']
            $tours = [];
            $callus_anchor = '#booking-form';

            if (!empty($block['data'])) {
                // Get callus_anchor - check both possible field names
                $callus_anchor = $block['data']['callus_anchor']
                    ?? $block['data']['field_dapm_callus_anchor']
                    ?? '#booking-form';

                // Parse repeater field 'tours' from block data
                // ACF can send data in different formats depending on context

                // Format 1: Nested array format (what we're seeing now)
                if (isset($block['data']['field_dapm_tours']) && is_array($block['data']['field_dapm_tours'])) {
                    foreach ($block['data']['field_dapm_tours'] as $row_key => $row) {
                        $tour_id = $row['field_dapm_tour_id'] ?? '';
                        $tour_name = $row['field_dapm_tour_name'] ?? '';

                        if (!empty($tour_id)) {
                            $tours[] = [
                                'tour_id' => $tour_id,
                                'tour_name' => $tour_name,
                            ];
                        }
                    }
                }
                // Format 2: Flat format (what we had before)
                elseif (isset($block['data']['tours'])) {
                    $tour_count = intval($block['data']['tours']);

                    for ($i = 0; $i < $tour_count; $i++) {
                        $tour_id_key = "tours_{$i}_tour_id";
                        $tour_name_key = "tours_{$i}_tour_name";

                        $tour_id = $block['data'][$tour_id_key] ?? '';
                        $tour_name = $block['data'][$tour_name_key] ?? '';

                        if (!empty($tour_id)) {
                            $tours[] = [
                                'tour_id' => $tour_id,
                                'tour_name' => $tour_name,
                            ];
                        }
                    }
                }
            }

            // If no tours, show empty state
            if (empty($tours) || !is_array($tours)) {
                $debug_info = '';
                if ($is_preview) {
                    $debug_info = '<div style="background:#fff3cd;padding:15px;border:2px solid #ffc107;margin:10px 0;font-family:monospace;font-size:12px;">';
                    $debug_info .= '<strong style="color:#856404;">⚠️ DEBUG - Tours not parsed correctly</strong><br><br>';
                    $debug_info .= '<strong>Raw block data:</strong><br>';
                    $debug_info .= '<pre style="background:#fff;padding:5px;font-size:11px;overflow:auto;">';
                    $debug_info .= htmlspecialchars(print_r($block['data'] ?? [], true));
                    $debug_info .= '</pre>';
                    $debug_info .= '<strong>Tours parsed:</strong> ' . htmlspecialchars(print_r($tours, true)) . '<br>';
                    $debug_info .= '<strong>Tour count from data:</strong> ' . ($block['data']['tours'] ?? 'not set') . '<br>';
                    $debug_info .= '</div>';
                }

                $data = [
                    'block_id' => 'booking-' . uniqid(),
                    'class_name' => 'dates-and-prices booking booking--empty' . (!empty($block['className']) ? ' ' . esc_attr($block['className']) : ''),
                    'grouped_dates' => [],
                    'is_preview' => $is_preview,
                    'tours' => [],
                    'debug_info' => $debug_info,
                ];
                $this->load_template('dates-and-prices-manual', $data);
                return;
            }

            // Get data ONLY for the first tour (default) to avoid timeout
            // Other tours will be loaded via AJAX when user switches to them
            $all_tours_data = [];
            foreach ($tours as $index => $tour) {
                $tour_id = intval($tour['tour_id']);
                $tour_name = $tour['tour_name'];

                // Fetch API data for first 3 tours maximum to avoid timeout
                // Tours beyond index 2 won't be loaded (user needs to refresh)
                if ($index < 3) {
                    // Debug: Log before API call
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log("DatesAndPricesManual: Fetching data for tour_id={$tour_id} (tour index {$index})");
                    }

                    $dates = $this->get_api_data($tour_id, $callus_anchor);
                    $grouped_dates = $this->group_dates_by_year_month($dates);

                    // Debug: Log after API call
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log("DatesAndPricesManual: Got " . count($dates) . " dates for tour_id={$tour_id}");
                    }
                } else {
                    // For tours beyond index 2, don't load data (to prevent timeout)
                    $dates = [];
                    $grouped_dates = [];

                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log("DatesAndPricesManual: Skipping tour_id={$tour_id} (index {$index} >= 3) to prevent timeout");
                    }
                }

                $all_tours_data[] = [
                    'tour_id' => $tour_id,
                    'tour_name' => $tour_name,
                    'dates' => $dates,
                    'grouped_dates' => $grouped_dates,
                    'is_default' => ($index === 0),
                ];
            }

            // Use first tour as default
            $default_tour = $all_tours_data[0];
            $dates = $default_tour['dates'];
            $grouped_dates = $default_tour['grouped_dates'];

            // If grouped_dates is empty, show debug info in preview mode
            if (empty($grouped_dates) && $is_preview) {
                $debug_info = '<div style="background:#fff3cd;padding:15px;border:2px solid #ffc107;margin:10px 0;font-family:monospace;font-size:12px;">';
                $debug_info .= '<strong style="color:#856404;">⚠️ API returned no calendar data</strong><br><br>';
                $debug_info .= '<strong>Tours configured:</strong><br>';
                foreach ($all_tours_data as $idx => $tour) {
                    $debug_info .= sprintf('• Tour %d: ID=%s, Name="%s", Dates count=%d<br>',
                        $idx + 1,
                        $tour['tour_id'],
                        $tour['tour_name'],
                        count($tour['dates'])
                    );
                }
                $debug_info .= '<br><strong>Possible solutions:</strong><br>';
                $debug_info .= '• Verify the Tour IDs exist in the API<br>';
                $debug_info .= '• Try tour IDs: 243, 242, 240, 239<br>';
                $debug_info .= '• Check that the API endpoint is accessible<br>';
                $debug_info .= '</div>';

                $data = [
                    'block_id' => 'booking-' . uniqid(),
                    'class_name' => 'dates-and-prices booking booking--empty' . (!empty($block['className']) ? ' ' . esc_attr($block['className']) : ''),
                    'grouped_dates' => [],
                    'is_preview' => $is_preview,
                    'tours' => [],
                    'debug_info' => $debug_info,
                ];
                $this->load_template('dates-and-prices-manual', $data);
                return;
            }

            // Extract available years from default tour
            $available_years = array_keys($grouped_dates);

            // Determine initial year/month
            $current_year = !empty($available_years) ? (string)$available_years[0] : (string)date('Y');
            $current_month = (string)date('m');

            // If current month doesn't have dates in the first year, use first available month
            if (isset($grouped_dates[$current_year]) && is_array($grouped_dates[$current_year])) {
                if (!isset($grouped_dates[$current_year][$current_month])) {
                    $months = array_keys($grouped_dates[$current_year]);
                    $current_month = !empty($months) ? $months[0] : '01';
                }
            } else {
                // If year doesn't exist, use first available month from first available year
                $current_month = '01';
            }

            $data = [
                'block_id' => 'booking-' . uniqid(),
                'class_name' => 'dates-and-prices booking' . (!empty($block['className']) ? ' ' . esc_attr($block['className']) : ''),
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
                'package_id' => $default_tour['tour_id'],
                'tours' => $all_tours_data,
            ];

            $this->load_template('dates-and-prices-manual', $data);

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                echo '<div style="padding: 20px; background: #ffebee; border: 2px solid #f44336; border-radius: 4px;">';
                echo '<h3 style="margin: 0 0 10px; color: #c62828;">Error en Dates and Prices (Manual)</h3>';
                echo '<p style="margin: 0; font-family: monospace; font-size: 13px;">' . esc_html($e->getMessage()) . '</p>';
                echo '</div>';
            }
        }
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

            // Sort dates within each month
            foreach ($grouped[$year] as $month => $month_dates) {
                usort($grouped[$year][$month], function($a, $b) {
                    return strcmp($a['date'], $b['date']);
                });
            }
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
        // Try to get from cache first (cache for 1 hour)
        $cache_key = "tour_calendar_{$tour_id}_{$year}_{$month}";
        $cached_data = get_transient($cache_key);

        if ($cached_data !== false) {
            return $cached_data;
        }

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
                error_log('DatesAndPricesManual: wp_remote_get failed, trying file_get_contents: ' . $response->get_error_message());
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
                    error_log('DatesAndPricesManual: file_get_contents also failed for ' . $url);
                }
                return [];
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('DatesAndPricesManual API Error: Invalid JSON response');
                }
                return [];
            }

            $result = is_array($data) ? $data : [];

            // Cache the result for 1 hour
            set_transient($cache_key, $result, HOUR_IN_SECONDS);

            return $result;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("DatesAndPricesManual API Error: HTTP {$status_code} for tour_id {$tour_id}");
            }
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('DatesAndPricesManual API Error: Invalid JSON response');
            }
            return [];
        }

        $result = is_array($data) ? $data : [];

        // Cache the result for 1 hour
        set_transient($cache_key, $result, HOUR_IN_SECONDS);

        return $result;
    }

    /**
     * Transform API data to the format expected by the template
     *
     * @param array $api_data Raw API data (dates as keys)
     * @param int $duration Package duration in days (default 4)
     * @param string $anchor_id Anchor ID for fixed_week scroll action
     * @return array Transformed dates array
     */
    private function transform_api_data_to_dates(array $api_data, int $duration = 4, string $anchor_id = '#booking-form'): array
    {
        $dates = [];
        $today = strtotime('today');

        foreach ($api_data as $date_str => $date_info) {
            $type = $date_info['type'] ?? '';
            // If price is empty string or 0, use a default price of 500
            $price_raw = $date_info['price'] ?? '';
            $price = (!empty($price_raw) && $price_raw !== '') ? floatval($price_raw) : 500;
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

            // Apply logic by type (simplified version - you can expand this)
            if ($type === 'spots_api') {
                if ($days_until <= 10) {
                    $date_entry['availability'] = 'sold_out';
                    $date_entry['button_text'] = 'SOLD OUT';
                } elseif ($spots > 50) {
                    $date_entry['availability'] = 'available';
                    $date_entry['button_text'] = 'CONTACT US';
                    $date_entry['button_action'] = 'scroll_to_anchor';
                } else {
                    $date_entry['availability'] = 'available';
                    $date_entry['button_text'] = 'BOOK NOW';
                    $date_entry['button_action'] = 'open_purchase_aside';
                }
            } elseif ($type === 'fidex_week') {
                if ($spots <= 20) {
                    $date_entry['availability'] = 'sold_out';
                    $date_entry['button_text'] = 'SOLD OUT';
                } else {
                    $date_entry['availability'] = 'available';
                    $date_entry['button_text'] = 'BOOK NOW';
                    $date_entry['button_action'] = 'open_purchase_aside';
                }

                if ($offer && $offer < $price) {
                    $date_entry['has_deal'] = true;
                    $date_entry['original_price'] = $price;
                    $date_entry['price'] = $offer;
                    $date_entry['discount_percentage'] = round((($price - $offer) / $price) * 100);
                    $date_entry['deal_label'] = $date_entry['discount_percentage'] . '% Off';
                    $date_entry['row_class'] = 'booking-row--promo-fixed-week';
                }
            } elseif ($type === 'fixed_dates') {
                if ($spots <= 5) {
                    $date_entry['availability'] = 'sold_out';
                    $date_entry['button_text'] = 'SOLD OUT';
                } else {
                    $date_entry['availability'] = 'available';
                    $date_entry['button_text'] = 'BOOK NOW';
                    $date_entry['button_action'] = 'open_purchase_aside';
                }

                $date_entry['row_class'] = 'booking-row--promo-fixed-dates';
                if ($offer && $offer < $price) {
                    $date_entry['has_deal'] = true;
                    $date_entry['original_price'] = $price;
                    $date_entry['price'] = $offer;
                    $date_entry['discount_percentage'] = round((($price - $offer) / $price) * 100);
                    $date_entry['deal_label'] = $date_entry['discount_percentage'] . '% Off';
                }
            } elseif ($type === 'no_program') {
                $date_entry['availability'] = 'sold_out';
                $date_entry['button_text'] = 'SOLD OUT';
            }

            $dates[] = $date_entry;
        }

        return $dates;
    }

    /**
     * Get dates from API for a tour_id
     *
     * @param int $tour_id Tour ID for API
     * @param string $anchor_id Anchor ID for CALL US scroll action
     * @return array Dates array in template format
     */
    private function get_api_data(int $tour_id, string $anchor_id = '#booking-form'): array
    {
        $all_dates = [];
        $api_calls_made = 0;
        $api_calls_with_data = 0;

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
                $api_calls_made++;

                if (!empty($api_data)) {
                    $api_calls_with_data++;
                    $transformed = $this->transform_api_data_to_dates($api_data, 4, $anchor_id);
                    $all_dates = array_merge($all_dates, $transformed);
                }
            }
        }

        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf(
                'DatesAndPricesManual get_api_data: tour_id=%d, api_calls=%d, calls_with_data=%d, total_dates=%d',
                $tour_id,
                $api_calls_made,
                $api_calls_with_data,
                count($all_dates)
            ));
        }

        return $all_dates;
    }
}
