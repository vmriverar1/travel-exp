<?php
namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class PackageAvailability extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_package_availability',
            'title' => '📆 Package - Availability & Departures',
            'fields' => [

                // ===== DEPARTURE DATES =====
                [
                    'key' => 'field_package_fixed_departures',
                    'label' => 'Departure Dates',
                    'name' => 'manual_departure_dates',
                    'type' => 'repeater',
                    'instructions' => 'Add specific departure dates to override automatic generation or add special dates. Leave empty to use only automatic generation based on Months + Weekdays.',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'layout' => 'table',
                    'button_label' => 'Add Departure Date',
                    'sub_fields' => [
                        [
                            'key' => 'field_departure_date',
                            'label' => 'Departure Date',
                            'name' => 'date',
                            'type' => 'date_picker',
                            'instructions' => 'Select the departure date.',
                            'required' => 1,
                            'display_format' => 'F j, Y',
                            'return_format' => 'Y-m-d',
                            'first_day' => 1,
                            'wrapper' => ['width' => 25],
                        ],
                        [
                            'key' => 'field_departure_status',
                            'label' => 'Status',
                            'name' => 'status',
                            'type' => 'select',
                            'instructions' => 'Departure availability status.',
                            'required' => 1,
                            'choices' => [
                                'available' => 'Available',
                                'few_spots' => 'Few Spots Left',
                                'sold_out' => 'Sold Out',
                                'guaranteed' => 'Guaranteed Departure',
                            ],
                            'default_value' => 'available',
                            'ui' => 1,
                            'wrapper' => ['width' => 20],
                        ],
                        [
                            'key' => 'field_departure_spots_available',
                            'label' => 'Spots Available',
                            'name' => 'spots_available',
                            'type' => 'number',
                            'instructions' => 'Number of available spots.',
                            'required' => 0,
                            'min' => 0,
                            'max' => 50,
                            'wrapper' => ['width' => 15],
                        ],
                        [
                            'key' => 'field_departure_price',
                            'label' => 'Price (USD)',
                            'name' => 'price',
                            'type' => 'number',
                            'instructions' => 'Specific price for this departure (optional).',
                            'required' => 0,
                            'min' => 0,
                            'prepend' => '$',
                            'wrapper' => ['width' => 20],
                        ],
                        [
                            'key' => 'field_departure_notes',
                            'label' => 'Notes',
                            'name' => 'notes',
                            'type' => 'text',
                            'instructions' => 'Additional information for this departure.',
                            'required' => 0,
                            'maxlength' => 200,
                            'placeholder' => 'Special event, holiday, etc.',
                            'wrapper' => ['width' => 20],
                        ],
                    ],
                ],

                // ===== MONTH AVAILABILITY SELECTOR =====
                [
                    'key' => 'field_package_available_months',
                    'label' => '📅 Available Months',
                    'name' => 'available_months',
                    'type' => 'checkbox',
                    'instructions' => 'Select the months when this package is generally available.',
                    'required' => 0,
                    'choices' => [
                        'january' => 'January',
                        'february' => 'February',
                        'march' => 'March',
                        'april' => 'April',
                        'may' => 'May',
                        'june' => 'June',
                        'july' => 'July',
                        'august' => 'August',
                        'september' => 'September',
                        'october' => 'October',
                        'november' => 'November',
                        'december' => 'December',
                    ],
                    'default_value' => [],
                    'layout' => 'horizontal',
                ],

                // ===== BEST TIME TO VISIT =====
                [
                    'key' => 'field_package_best_months',
                    'label' => '⭐ Best Time to Visit',
                    'name' => 'best_months',
                    'type' => 'checkbox',
                    'instructions' => 'Highlight the best months for this package (weather, events, etc.).',
                    'required' => 0,
                    'choices' => [
                        'january' => 'January',
                        'february' => 'February',
                        'march' => 'March',
                        'april' => 'April',
                        'may' => 'May',
                        'june' => 'June',
                        'july' => 'July',
                        'august' => 'August',
                        'september' => 'September',
                        'october' => 'October',
                        'november' => 'November',
                        'december' => 'December',
                    ],
                    'default_value' => [],
                    'layout' => 'horizontal',
                ],

                // ===== FREE SPOT CALENDAR INTEGRATION =====
                [
                    'key' => 'field_package_calendar_enabled',
                    'label' => '📊 Enable Free Spot Calendar',
                    'name' => 'calendar_enabled',
                    'type' => 'true_false',
                    'instructions' => 'Enable real-time availability calendar integration.',
                    'default_value' => 0,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_package_calendar_api_url',
                    'label' => '🔗 Calendar API URL',
                    'name' => 'calendar_api_url',
                    'type' => 'url',
                    'instructions' => 'API endpoint for calendar data (Google Calendar, iCal, or custom API).',
                    'required' => 0,
                    'placeholder' => 'https://api.example.com/calendar/package-123',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_package_calendar_enabled',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_package_calendar_type',
                    'label' => '📅 Calendar Type',
                    'name' => 'calendar_type',
                    'type' => 'select',
                    'instructions' => 'Type of calendar integration.',
                    'required' => 0,
                    'choices' => [
                        'google' => 'Google Calendar',
                        'ical' => 'iCal Feed',
                        'custom' => 'Custom API',
                        'manual' => 'Manual (Use Fixed Departures)',
                    ],
                    'default_value' => 'manual',
                    'ui' => 1,
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_package_calendar_enabled',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_package_calendar_api_key',
                    'label' => '🔑 API Key',
                    'name' => 'calendar_api_key',
                    'type' => 'text',
                    'instructions' => 'API key or authentication token (if required).',
                    'required' => 0,
                    'placeholder' => 'Enter API key',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_package_calendar_enabled',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],

                // ===== MINIMUM ADVANCE BOOKING =====
                [
                    'key' => 'field_package_min_booking_days',
                    'label' => '⏰ Minimum Advance Booking',
                    'name' => 'min_booking_days',
                    'type' => 'number',
                    'instructions' => 'Minimum number of days required to book in advance.',
                    'required' => 0,
                    'default_value' => 2,
                    'min' => 0,
                    'max' => 365,
                    'append' => 'days',
                ],

            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'package',
                    ],
                ],
            ],
            'menu_order' => 50,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ]);
    }
}
