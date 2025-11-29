<?php

/**

 * Template: Dates and Prices Block - Booking Interface

 *

 * Available variables:

 * @var string $block_id           Unique block ID

 * @var string $class_name          Block CSS classes

 * @var array  $grouped_dates       Dates grouped by [year][month]

 * @var array  $all_dates           Flat array of all dates

 * @var array  $available_years     Array of years with dates

 * @var string $current_year        Initial year to display

 * @var string $current_month       Initial month to display (01-12)

 * @var string $currency_symbol     Currency symbol (e.g., "USD $")

 * @var string $button_text         CTA button text

 * @var string $alert_message       Alert box message

 * @var string $alert_emphasis      Emphasized part of alert

 * @var bool   $is_preview          Whether in preview mode

 */



if (empty($grouped_dates)) {

    ?>

    <section

        id="<?php echo esc_attr($block_id); ?>"

        class="<?php echo esc_attr($class_name); ?>"

    >

        <div class="booking-empty">

            <svg class="booking-empty__icon" width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">

                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>

                <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>

                <circle cx="8" cy="14" r="1" fill="currentColor"/>

                <circle cx="12" cy="14" r="1" fill="currentColor"/>

                <circle cx="16" cy="14" r="1" fill="currentColor"/>

                <circle cx="8" cy="18" r="1" fill="currentColor"/>

                <circle cx="12" cy="18" r="1" fill="currentColor"/>

            </svg>

            <h3 class="booking-empty__title"><?php _e('No Departure Dates Available', 'travel-blocks'); ?></h3>

            <p class="booking-empty__message">

                <?php _e('Departure dates for this package have not been configured yet. Please check back later or contact us for more information.', 'travel-blocks'); ?>

            </p>

            <?php if ($is_preview): ?>

                <p class="booking-empty__hint">

                    <strong><?php _e('Editor Note:', 'travel-blocks'); ?></strong>

                    <?php _e('Add departure dates in the "departures" custom field to display the booking calendar.', 'travel-blocks'); ?>

                </p>

            <?php endif; ?>

        </div>

    </section>

    <?php

    return;

}



// Month names for display

$month_names = [

    '01' => __('January', 'travel-blocks'),

    '02' => __('February', 'travel-blocks'),

    '03' => __('March', 'travel-blocks'),

    '04' => __('April', 'travel-blocks'),

    '05' => __('May', 'travel-blocks'),

    '06' => __('June', 'travel-blocks'),

    '07' => __('July', 'travel-blocks'),

    '08' => __('August', 'travel-blocks'),

    '09' => __('September', 'travel-blocks'),

    '10' => __('October', 'travel-blocks'),

    '11' => __('November', 'travel-blocks'),

    '12' => __('December', 'travel-blocks'),

];



// Day names for display

$day_names = [

    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'

];

?>



<section

    id="<?php echo esc_attr($block_id); ?>"

    class="<?php echo esc_attr($class_name); ?>"

    aria-labelledby="<?php echo esc_attr($block_id); ?>-title"

    data-current-year="<?php echo esc_attr($current_year); ?>"

    data-current-month="<?php echo esc_attr($current_month); ?>"

>

    <h2 id="<?php echo esc_attr($block_id); ?>-title" class="sr-only">

        <?php _e('Select your travel date', 'travel-blocks'); ?>

    </h2>



    <!-- YEAR TABS (floating on top border) -->

    <nav class="year-tabs" aria-label="<?php esc_attr_e('Select year', 'travel-blocks'); ?>">

        <?php foreach ($available_years as $index => $year): ?>

            <button

                class="year-tab<?php echo ($year == $current_year) ? ' is-active' : ''; ?>"

                data-year="<?php echo esc_attr($year); ?>"

                aria-selected="<?php echo ($year == $current_year) ? 'true' : 'false'; ?>"

                type="button"

            >

                <?php echo esc_html($year); ?>

            </button>

        <?php endforeach; ?>

    </nav>



    <!-- MONTH NAVIGATION -->

    <header class="month-nav">

        <div class="month-nav__group">

            <button

                class="icon-btn icon-btn--prev"

                aria-label="<?php esc_attr_e('Previous month', 'travel-blocks'); ?>"

                type="button"

            >

                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">

                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

                </svg>

            </button>



            <div class="month-label">

                <span class="month-label__text"></span>

            </div>



            <button

                class="icon-btn icon-btn--next"

                aria-label="<?php esc_attr_e('Next month', 'travel-blocks'); ?>"

                type="button"

            >

                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">

                    <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

                </svg>

            </button>

        </div>



        <button

            class="icon-btn icon-btn--select"

            id="btn-month-select-<?php echo esc_attr($block_id); ?>"

            aria-label="<?php esc_attr_e('Select month', 'travel-blocks'); ?>"

            aria-haspopup="listbox"

            aria-expanded="false"

            type="button"

        >

            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">

                <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

            </svg>

        </button>



        <!-- MONTH POPOVER -->

        <ul

            id="month-popover-<?php echo esc_attr($block_id); ?>"

            class="month-popover"

            role="listbox"

            hidden

        >

            <?php foreach ($month_names as $month_num => $month_name): ?>

                <li

                    class="month-popover__item"

                    role="option"

                    data-month="<?php echo esc_attr($month_num); ?>"

                >

                    <?php echo esc_html($month_name); ?>

                </li>

            <?php endforeach; ?>

        </ul>

    </header>



    <ul class="trip-list" role="list">

        <?php foreach ($grouped_dates as $year => $months): ?>

            <?php foreach ($months as $month => $dates): ?>

                <?php foreach ($dates as $date):

                    // Parse dates

                    $departure_ts = strtotime($date['date']);

                    $return_ts = !empty($date['return_date']) ? strtotime($date['return_date']) : null;



                    // Format dates

                    $departure_day = $day_names[date('w', $departure_ts)];

                    $departure_date = date('jS M, Y', $departure_ts);



                    $return_day = $return_ts ? $day_names[date('w', $return_ts)] : '';

                    $return_date = $return_ts ? date('jS M, Y', $return_ts) : '';



                    // Determine card classes and states

                    $availability = $date['availability'] ?? 'available';

                    $has_deal = !empty($date['has_deal']);

                    $is_sold_out = ($availability === 'sold_out');



                    $card_classes = ['trip-card'];

                    if ($has_deal) $card_classes[] = 'trip-card--deal';

                    if ($is_sold_out) $card_classes[] = 'trip-card--soldout';



                    // Add API-specific row class if present

                    if (!empty($date['row_class'])) {

                        $card_classes[] = $date['row_class'];

                    }



                    // Show cards for current year/month by default (before JS loads)

                    if ($year == $current_year && $month == $current_month) {

                        $card_classes[] = 'trip-card--visible';

                    }



                    // Availability status text

                    $status_text = '';

                    if ($availability === 'limited') {

                        $status_text = sprintf(__('Only %d left', 'travel-blocks'), $date['spaces_left']);

                    } elseif ($availability === 'sold_out') {

                        $status_text = __('SOLD OUT', 'travel-blocks');

                    } else {

                        $status_text = __('Available', 'travel-blocks');

                    }

                ?>

                    <li

                        class="<?php echo esc_attr(implode(' ', $card_classes)); ?>"

                        data-year="<?php echo esc_attr($year); ?>"

                        data-month="<?php echo esc_attr($month); ?>"

                        <?php if ($is_sold_out): ?>aria-disabled="true"<?php endif; ?>

                    >

                        <!-- DATES COLUMN -->

                        <div class="trip-dates">

                            <div class="trip-dates__item">

                                <div class="trip-dates__line">

                                    <strong class="trip-dates__label"><?php _e('FROM:', 'travel-blocks'); ?></strong>

                                    <span class="trip-dates__day"><?php echo esc_html($departure_day); ?></span>

                                </div>

                                <div class="trip-dates__date"><?php echo esc_html($departure_date); ?></div>

                            </div>



                            <?php if ($return_ts): ?>

                                <!-- Arrow separator -->

                                <svg class="trip-dates__arrow" xmlns="http://www.w3.org/2000/svg" width="15" height="23" viewBox="0 0 15 23" fill="none">

                                    <g clip-path="url(#clip0_333_75)">

                                        <path d="M5.39062 17.7119L10.1634 11.5288L5.39063 5.34574" stroke="#666666" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>

                                    </g>

                                    <defs>

                                        <clipPath id="clip0_333_75">

                                            <rect width="22.6712" height="15" fill="white" transform="matrix(0 -1 1 0 0 22.6712)"/>

                                        </clipPath>

                                    </defs>

                                </svg>



                                <div class="trip-dates__item">

                                    <div class="trip-dates__line">

                                        <strong class="trip-dates__label"><?php _e('TO:', 'travel-blocks'); ?></strong>

                                        <span class="trip-dates__day"><?php echo esc_html($return_day); ?></span>

                                    </div>

                                    <div class="trip-dates__date"><?php echo esc_html($return_date); ?></div>

                                </div>

                            <?php endif; ?>

                        </div>



                        <!-- PRICE COLUMN -->

                        <?php if (!$is_sold_out): ?>

                            <div class="trip-price">

                                <div class="trip-price__amount">

                                    <span class="trip-price__currency"><?php echo esc_html($currency_symbol); ?></span>

                                    <strong class="trip-price__value"><?php echo esc_html(number_format($date['price'], 0)); ?></strong>

                                </div>

                                <?php if (!$has_deal): ?>

                                    <!-- Only show "price per person" if NO deal -->

                                    <div class="trip-price__meta"><?php _e('price per person', 'travel-blocks'); ?></div>

                                <?php else: ?>

                                    <!-- If deal: show old price below current price -->

                                    <?php if (!empty($date['original_price'])): ?>

                                        <div class="trip-deal__old-price">

                                            <?php printf(__('%s%s', 'travel-blocks'), esc_html($currency_symbol), esc_html(number_format($date['original_price'], 0))); ?>

                                        </div>

                                    <?php endif; ?>

                                <?php endif; ?>

                            </div>



                            <!-- DEAL COLUMN (always present, empty if no deal) -->

                            <div class="trip-deal">

                                <?php if ($has_deal && !empty($date['discount_percentage'])): ?>

                                    <!-- Badge only -->

                                    <div class="badge badge--deal">

                                        <div class="badge__percentage"><?php echo esc_html($date['discount_percentage']); ?>%</div>

                                        <div class="badge__text">Off</div>

                                    </div>

                                <?php endif; ?>

                            </div>



                            <!-- CTA COLUMN -->

                            <div class="trip-cta">

                                <button

                                    class="btn btn-primary"

                                    data-departure-date="<?php echo esc_attr($date['date']); ?>"

                                    <?php if ($return_ts): ?>data-return-date="<?php echo esc_attr($date['return_date']); ?>"<?php endif; ?>

                                    data-action="<?php echo esc_attr($date['button_action'] ?? 'default'); ?>"

                                    <?php if (!empty($date['anchor_id'])): ?>data-anchor="<?php echo esc_attr($date['anchor_id']); ?>"<?php endif; ?>

                                    data-price="<?php echo esc_attr($date['price']); ?>"

                                    data-single-supp="<?php echo esc_attr($date['single_supp'] ?? '0'); ?>"

                                    type="button"

                                >

                                    <?php echo esc_html($date['button_text'] ?? $button_text); ?>

                                </button>

                            </div>

                        <?php else: ?>

                            <!-- SOLD OUT: Keep same structure as normal rows -->

                            <div class="trip-price">

                                <!-- Status in price column position -->

                                <div class="trip-status">

                                    <?php echo esc_html($status_text); ?>

                                </div>

                            </div>



                            <div class="trip-deal">

                                <!-- Empty deal column -->

                            </div>



                            <div class="trip-cta">

                                <!-- Empty CTA column -->

                            </div>

                        <?php endif; ?>

                    </li>

                <?php endforeach; ?>

            <?php endforeach; ?>

        <?php endforeach; ?>

    </ul>



    <!-- LEGEND CHIPS (floating on bottom border) -->

    <div class="legend-chips" aria-hidden="true">

        <span class="chip chip--soldout"><?php _e('Sold out', 'travel-blocks'); ?></span>

        <span class="chip chip--available"><?php _e('Available spots', 'travel-blocks'); ?></span>

        <span class="chip chip--deal"><?php _e('Deal + Few spots', 'travel-blocks'); ?></span>

    </div>



    <!-- Hidden data for JavaScript -->

    <script type="application/json" class="booking-data">

        <?php echo wp_json_encode([

            'grouped_dates' => $grouped_dates,

            'month_names' => $month_names,

            'current_year' => $current_year,

            'current_month' => $current_month,

            'package_id' => $package_id ?? null,

        ]); ?>

    </script>

</section>



<!-- BOOKING ALERT (outside container) -->

<div class="booking-alert" role="note">

    <svg class="icon-bell" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">

        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

    </svg>

    <span>

        <?php echo esc_html($alert_message); ?>

        <strong><?php echo esc_html($alert_emphasis); ?></strong>

    </span>

</div>

