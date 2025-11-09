<?php
namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class PackageBaseInfo extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_package_base_info',
            'title' => '⭐ Package - Base Info (Product Identity)',
            'fields' => [

                // ===== API INTEGRATION =====
                [
                    'key' => 'field_package_tour_id',
                    'label' => '🔗 Tour ID (API)',
                    'name' => 'tour_id',
                    'type' => 'number',
                    'instructions' => 'Tour ID for API integration (used for calendar/dates endpoint).',
                    'required' => 0,
                    'placeholder' => '125',
                    'wrapper' => ['width' => 50],
                ],
                [
                    'key' => 'field_package_booking_anchor_id',
                    'label' => '📍 Booking Anchor ID',
                    'name' => 'booking_anchor_id',
                    'type' => 'text',
                    'instructions' => 'ID del elemento al que se hará scroll cuando se haga click en fechas fixed_week. Example: #booking-form',
                    'required' => 0,
                    'placeholder' => '#booking-form',
                    'wrapper' => ['width' => 50],
                ],

                // ===== DURATION & CAPACITY =====
                [
                    'key' => 'field_package_duration',
                    'label' => '⏱️ Duration (text)',
                    'name' => 'duration',
                    'type' => 'text',
                    'instructions' => 'Package duration in readable format. Example: "5 days / 4 nights"',
                    'required' => 0,
                    'placeholder' => '5 days / 4 nights',
                    'wrapper' => ['width' => 50],
                ],
                [
                    'key' => 'field_package_group_size',
                    'label' => '👥 Group Size',
                    'name' => 'group_size',
                    'type' => 'number',
                    'instructions' => 'Maximum number of people per group.',
                    'required' => 0,
                    'default_value' => 12,
                    'min' => 1,
                    'max' => 50,
                    'wrapper' => ['width' => 50],
                ],

                // ===== DEPARTURE/ARRIVAL =====
                [
                    'key' => 'field_package_departure_point',
                    'label' => '📍 Departure Point',
                    'name' => 'departure',
                    'type' => 'text',
                    'instructions' => 'Starting location of the package (hotel, terminal, airport).',
                    'required' => 0,
                    'placeholder' => 'Plaza de Armas, Cusco',
                    'wrapper' => ['width' => 50],
                ],
                [
                    'key' => 'field_package_arrival_point',
                    'label' => '🏁 Arrival Point',
                    'name' => 'arrival',
                    'type' => 'text',
                    'instructions' => 'Ending location of the package.',
                    'required' => 0,
                    'placeholder' => 'Plaza de Armas, Cusco',
                    'wrapper' => ['width' => 50],
                ],
                [
                    'key' => 'field_package_departure_time',
                    'label' => '🕐 Departure Time',
                    'name' => 'departure_time',
                    'type' => 'text',
                    'instructions' => 'Starting time of the package.',
                    'required' => 0,
                    'placeholder' => '04:30 a.m.',
                    'wrapper' => ['width' => 50],
                ],
                [
                    'key' => 'field_package_return_time',
                    'label' => '🕐 Return Time',
                    'name' => 'return_time',
                    'type' => 'text',
                    'instructions' => 'Approximate return time.',
                    'required' => 0,
                    'placeholder' => '06:00 p.m.',
                    'wrapper' => ['width' => 50],
                ],

                // ===== AVAILABILITY =====
                [
                    'key' => 'field_package_availability',
                    'label' => '📆 Availability',
                    'name' => 'availability',
                    'type' => 'select',
                    'instructions' => 'Package frequency.',
                    'required' => 0,
                    'choices' => [
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'specific_dates' => 'Specific dates (see Fixed Departures)',
                        'on_request' => 'On request',
                    ],
                    'default_value' => 'daily',
                    'ui' => 1,
                ],

                // ===== AFFILIATE =====
                [
                    'key' => 'field_package_affiliate_url',
                    'label' => '🔗 Affiliate URL',
                    'name' => 'affiliate_url',
                    'type' => 'url',
                    'instructions' => 'External link if the package is booked through an affiliate or external agency.',
                    'required' => 0,
                ],

                // ===== RATINGS & REVIEWS =====
                [
                    'key' => 'field_package_tripadvisor_rating',
                    'label' => '🏆 TripAdvisor Rating',
                    'name' => 'tripadvisor_rating',
                    'type' => 'number',
                    'instructions' => 'TripAdvisor rating (0-5 stars).',
                    'required' => 0,
                    'default_value' => 0,
                    'min' => 0,
                    'max' => 5,
                    'step' => 0.1,
                    'prepend' => '⭐',
                ],
                [
                    'key' => 'field_package_tripadvisor_url',
                    'label' => '🔗 TripAdvisor URL',
                    'name' => 'tripadvisor_url',
                    'type' => 'url',
                    'instructions' => 'Link to this package on TripAdvisor.',
                    'required' => 0,
                    'placeholder' => 'https://www.tripadvisor.com/...',
                ],
                [
                    'key' => 'field_package_google_rating',
                    'label' => '🌟 Google Rating',
                    'name' => 'google_rating',
                    'type' => 'number',
                    'instructions' => 'Google Reviews rating (0-5 stars).',
                    'required' => 0,
                    'default_value' => 0,
                    'min' => 0,
                    'max' => 5,
                    'step' => 0.1,
                    'prepend' => '⭐',
                ],
                [
                    'key' => 'field_package_total_reviews',
                    'label' => '💬 Total Reviews',
                    'name' => 'total_reviews',
                    'type' => 'number',
                    'instructions' => 'Total number of reviews across all platforms.',
                    'required' => 0,
                    'default_value' => 0,
                    'min' => 0,
                    'step' => 1,
                ],
                [
                    'key' => 'field_package_show_rating_badge',
                    'label' => '🏅 Show Rating Badge',
                    'name' => 'show_rating_badge',
                    'type' => 'true_false',
                    'instructions' => 'Display rating badge on package cards and metadata.',
                    'default_value' => 1,
                    'ui' => 1,
                ],

                // ===== VIDEO EXTRAS =====
                [
                    'key' => 'field_package_video_thumbnail',
                    'label' => '🎬 Video Custom Thumbnail',
                    'name' => 'video_thumbnail',
                    'type' => 'image',
                    'instructions' => 'Custom thumbnail for the video (optional). If not set, YouTube/Vimeo default will be used.',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],
                [
                    'key' => 'field_package_video_label',
                    'label' => '🏷️ Video Label',
                    'name' => 'video_label',
                    'type' => 'text',
                    'instructions' => 'Label/ribbon text to display on video thumbnail. Example: "Start planning session", "Virtual Tour".',
                    'required' => 0,
                    'maxlength' => 50,
                    'placeholder' => 'Start planning session',
                ],

                // ===== QUICK HIGHLIGHTS =====
                [
                    'key' => 'field_package_highlights',
                    'label' => '✨ Quick Highlights',
                    'name' => 'highlights',
                    'type' => 'repeater',
                    'instructions' => 'Key highlights/facts about this package. Displayed with icons in the "Overview" section.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 15,
                    'layout' => 'table',
                    'button_label' => 'Add Highlight',
                    'sub_fields' => [
                        [
                            'key' => 'field_highlight_icon',
                            'label' => 'Icon',
                            'name' => 'icon',
                            'type' => 'select',
                            'instructions' => 'Select an icon for this highlight.',
                            'required' => 0,
                            'choices' => [
                                // Time & Calendar
                                'clock' => '🕒 Clock',
                                'calendar' => '📅 Calendar',
                                // People
                                'user' => '👤 User',
                                'users' => '👥 Users/Group',
                                // Travel & Location
                                'map-pin' => '📍 Map Pin',
                                'compass' => '🧭 Compass',
                                'plane' => '✈️ Plane',
                                'bus' => '🚌 Bus',
                                'home' => '🏠 Home',
                                // Accommodation & Meals
                                'bed' => '🛏️ Bed/Accommodation',
                                'utensils' => '🍴 Utensils/Meals',
                                // Activities
                                'backpack' => '🎒 Backpack/Trekking',
                                'camera' => '📷 Camera/Photography',
                                'heart' => '❤️ Heart/Favorite',
                                'star' => '⭐ Star/Rating',
                                // Status & Features
                                'check' => '✅ Check/Included',
                                'shield' => '🛡️ Shield/Protection',
                                'award' => '🏆 Award/Excellence',
                                'briefcase' => '💼 Briefcase/Business',
                                // Weather
                                'sun' => '☀️ Sun/Sunny',
                                'cloud' => '☁️ Cloud/Cloudy',
                                'snowflake' => '❄️ Snowflake/Cold',
                                'droplet' => '💧 Droplet/Rain',
                            ],
                            'default_value' => 'check',
                            'ui' => 1,
                            'wrapper' => ['width' => 35],
                        ],
                        [
                            'key' => 'field_highlight_text',
                            'label' => 'Text',
                            'name' => 'text',
                            'type' => 'text',
                            'instructions' => 'Highlight description.',
                            'required' => 0,
                            'maxlength' => 100,
                            'placeholder' => 'Best time to travel: May-September',
                            'wrapper' => ['width' => 65],
                        ],
                    ],
                ],

                // ===== GUARANTEE BULLETS =====
                [
                    'key' => 'field_package_show_reserve_later',
                    'label' => '💳 Show "Reserve now & pay later"',
                    'name' => 'show_reserve_later',
                    'type' => 'true_false',
                    'instructions' => 'Display "Reserve now & pay later" bullet in pricing card.',
                    'default_value' => 1,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_package_show_international_standards',
                    'label' => '✅ Show "International standards guarantee"',
                    'name' => 'show_international_standards',
                    'type' => 'true_false',
                    'instructions' => 'Display "International standards guarantee" bullet in pricing card.',
                    'default_value' => 1,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_package_custom_guarantees',
                    'label' => '🛡️ Custom Guarantees',
                    'name' => 'custom_guarantees',
                    'type' => 'repeater',
                    'instructions' => 'Additional custom guarantee bullets to display in pricing card.',
                    'required' => 0,
                    'min' => 0,
                    'max' => 5,
                    'layout' => 'table',
                    'button_label' => 'Add Guarantee',
                    'sub_fields' => [
                        [
                            'key' => 'field_guarantee_icon',
                            'label' => 'Icon',
                            'name' => 'icon',
                            'type' => 'select',
                            'instructions' => 'Select an icon.',
                            'required' => 0,
                            'choices' => [
                                'check' => '✅ Check',
                                'shield' => '🛡️ Shield',
                                'award' => '🏆 Award',
                                'heart' => '❤️ Heart',
                                'star' => '⭐ Star',
                                'lock' => '🔒 Lock/Secure',
                                'thumbs-up' => '👍 Thumbs Up',
                            ],
                            'default_value' => 'check',
                            'ui' => 1,
                            'wrapper' => ['width' => 30],
                        ],
                        [
                            'key' => 'field_guarantee_text',
                            'label' => 'Text',
                            'name' => 'text',
                            'type' => 'text',
                            'instructions' => 'Guarantee description.',
                            'required' => 0,
                            'maxlength' => 80,
                            'placeholder' => 'Free cancellation up to 24h',
                            'wrapper' => ['width' => 70],
                        ],
                    ],
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
            'menu_order' => 5,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ]);
    }
}
