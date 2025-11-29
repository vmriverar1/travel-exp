<?php
/**
 * Calendar Helper Functions
 *
 * Funciones auxiliares para generar la vista de calendario del bloque Dates and Prices
 *
 * @package Travel\Blocks
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Build calendar grid for a specific year and month
 *
 * @param int $year Year (YYYY)
 * @param string $month Month (01-12)
 * @param array $dates Array of dates from API
 * @return array Calendar grid with 35-42 cells
 */
function travel_blocks_build_calendar_grid($year, $month, $dates) {
    $first_day = mktime(0, 0, 0, intval($month), 1, intval($year));
    $days_in_month = date('t', $first_day);
    $day_of_week = date('w', $first_day); // 0 (Sunday) to 6 (Saturday)

    // Create lookup array for dates by day number
    $dates_by_day = [];
    foreach ($dates as $date) {
        $date_timestamp = strtotime($date['date']);
        $date_year = date('Y', $date_timestamp);
        $date_month = date('m', $date_timestamp);
        $date_day = date('j', $date_timestamp);

        if ($date_year == $year && $date_month == $month) {
            $dates_by_day[$date_day] = $date;
        }
    }

    $calendar_grid = [];

    // Add empty cells for days before the first day of month
    for ($i = 0; $i < $day_of_week; $i++) {
        $calendar_grid[] = [
            'is_empty' => true,
            'day_number' => null,
            'date' => null,
            'data' => null,
        ];
    }

    // Add cells for each day of the month
    for ($day = 1; $day <= $days_in_month; $day++) {
        $date_string = sprintf('%04d-%02d-%02d', $year, intval($month), $day);
        $has_data = isset($dates_by_day[$day]);

        $calendar_grid[] = [
            'is_empty' => false,
            'day_number' => $day,
            'date' => $date_string,
            'data' => $has_data ? $dates_by_day[$day] : null,
        ];
    }

    // Add empty cells to complete the last week (make it 35 or 42 cells total)
    $total_cells = count($calendar_grid);
    $cells_needed = (ceil($total_cells / 7) * 7) - $total_cells;

    for ($i = 0; $i < $cells_needed; $i++) {
        $calendar_grid[] = [
            'is_empty' => true,
            'day_number' => null,
            'date' => null,
            'data' => null,
        ];
    }

    return $calendar_grid;
}

/**
 * Get day status information (class, text, action)
 *
 * @param array|null $date_data Date data from API
 * @return array Status information [class, text, action, has_tooltip]
 */
function travel_blocks_get_day_status($date_data) {
    if (!$date_data) {
        return [
            'class' => 'closed',
            'text' => __('CLOSED', 'travel-blocks'),
            'action' => 'none',
            'has_tooltip' => false,
        ];
    }

    $availability = $date_data['availability'] ?? 'available';
    $has_deal = !empty($date_data['has_deal']);
    $button_action = $date_data['button_action'] ?? 'default';

    // SOLD OUT / CLOSED
    if ($availability === 'sold_out') {
        return [
            'class' => 'closed',
            'text' => __('CLOSED', 'travel-blocks'),
            'action' => 'none',
            'has_tooltip' => false,
        ];
    }

    // CALL US (scroll to anchor)
    if ($button_action === 'scroll_to_anchor') {
        return [
            'class' => 'callus',
            'text' => __('CALL US', 'travel-blocks'),
            'action' => 'scroll',
            'has_tooltip' => true,
        ];
    }

    // DEAL (has offer/discount)
    if ($has_deal) {
        return [
            'class' => 'deal',
            'text' => __('DEAL', 'travel-blocks'),
            'action' => 'book',
            'has_tooltip' => true,
        ];
    }

    // BOOK (available, normal)
    return [
        'class' => 'book',
        'text' => __('BOOK', 'travel-blocks'),
        'action' => 'book',
        'has_tooltip' => true,
    ];
}

/**
 * Get tooltip content for a day
 *
 * @param array $date_data Date data from API
 * @param string $currency_symbol Currency symbol
 * @return string|null Tooltip HTML or null
 */
function travel_blocks_get_tooltip_content($date_data, $currency_symbol) {
    if (!$date_data) {
        return null;
    }

    $has_deal = !empty($date_data['has_deal']);
    $price = $date_data['price'] ?? 0;

    $tooltip = '';

    if ($has_deal) {
        $tooltip .= '<span class="tooltip__label tooltip__label--deal">' . esc_html__('Top Deal', 'travel-blocks') . '</span>';
        $tooltip .= '<span class="tooltip__price tooltip__price--deal">$ ' . esc_html(number_format($price, 0)) . '</span>';
    } else {
        $tooltip .= '<span class="tooltip__label">' . esc_html__('Price', 'travel-blocks') . '</span>';
        $tooltip .= '<span class="tooltip__price">$ ' . esc_html(number_format($price, 0)) . '</span>';
    }

    return $tooltip;
}

/**
 * Get weekday headers (short names)
 *
 * @return array Array of short weekday names
 */
function travel_blocks_get_weekday_headers() {
    return [
        __('Su', 'travel-blocks'),
        __('Mo', 'travel-blocks'),
        __('Tu', 'travel-blocks'),
        __('We', 'travel-blocks'),
        __('Th', 'travel-blocks'),
        __('Fr', 'travel-blocks'),
        __('Sa', 'travel-blocks'),
    ];
}
