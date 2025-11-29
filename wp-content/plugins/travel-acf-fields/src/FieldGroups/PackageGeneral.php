<?php

namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class PackageGeneral extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_package_general',
            'title' => '📋 Package - Basic Information',
            'fields' => [

                // ===== SUBTITLE / TAGLINE =====
                [
                    'key' => 'field_package_subtitle',
                    'label' => '📝 Subtitle / Tagline',
                    'name' => 'subtitle',
                    'type' => 'text',
                    'instructions' => 'Short tagline or description for this package. Example: "The Inca Trail Express for those who are short on time."',
                    'required' => 0,
                    'maxlength' => 150,
                    'placeholder' => 'A memorable one-liner about this package...',
                ],

                // ===== #1 - SERVICE TYPE * =====
                [
                    'key' => 'field_package_service_type',
                    'label' => '🎯 Service Type',
                    'name' => 'service_type',
                    'type' => 'select',
                    'instructions' => 'Define if the package is shared or private.',
                    'required' => 1,
                    'choices' => [
                        'shared' => 'Shared',
                        'private' => 'Private',
                    ],
                    'default_value' => 'shared',
                    'ui' => 1,
                    'wrapper' => ['width' => 50],
                ],

                // Shared Package (conditional - appears when private)
                [
                    'key' => 'field_package_shared_package',
                    'label' => 'Shared Package',
                    'name' => 'shared_package',
                    'type' => 'post_object',
                    'instructions' => 'Select the shared package this private package is based on.',
                    'post_type' => ['package'],
                    'return_format' => 'id',
                    'ui' => 1,
                    'required' => 0,
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_package_service_type',
                                'operator' => '==',
                                'value' => 'private',
                            ],
                        ],
                    ],
                ],

                // ===== #2 - RATING =====
                [
                    'key' => 'field_package_rating',
                    'label' => '⭐ Rating',
                    'name' => 'rating',
                    'type' => 'number',
                    'instructions' => 'Package rating (promedio de estrellas). Puede ser cualquier número.',
                    'required' => 0,
                    'default_value' => 0,
                    'min' => 0,
                    'step' => 0.1,
                    'wrapper' => ['width' => 25],
                ],

                // ===== #3 - STARS =====
                [
                    'key' => 'field_package_stars',
                    'label' => '⭐ Stars',
                    'name' => 'stars',
                    'type' => 'number',
                    'instructions' => 'Stars rating (1-5).',
                    'required' => 0,
                    'min' => 1,
                    'max' => 5,
                    'step' => 1,
                    'wrapper' => ['width' => 25],
                ],

                // ===== #4 - SUMMARY * =====
                [
                    'key' => 'field_package_summary',
                    'label' => '📝 Short Summary',
                    'name' => 'summary',
                    'type' => 'textarea',
                    'instructions' => 'Brief description of the package (max 200 characters). Shown on cards/listings.',
                    'required' => 1,
                    'rows' => 3,
                    'maxlength' => 200,
                ],

                // ===== #5 - DESCRIPTION * =====
                [
                    'key' => 'field_package_description',
                    'label' => '📄 Detailed Description',
                    'name' => 'description',
                    'type' => 'wysiwyg',
                    'instructions' => 'Complete description of the package: history, attractions, recommendations.',
                    'required' => 1,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                ],

                // ===== #6 - WHATS INCLUDED =====
                [
                    'key' => 'field_package_included',
                    'label' => '✅ What is Included',
                    'name' => 'included',
                    'type' => 'wysiwyg',
                    'instructions' => 'List of included services: transport, meals, entrance fees, guide, etc.',
                    'required' => 0,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 0,
                ],

                // ===== #7 - WHATS NOT INCLUDED =====
                [
                    'key' => 'field_package_not_included',
                    'label' => '❌ What is NOT Included',
                    'name' => 'not_included',
                    'type' => 'wysiwyg',
                    'instructions' => 'Exclusions: drinks, insurance, tips, etc.',
                    'required' => 0,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 0,
                ],

                // ===== #8 - VIDEO URL =====
                [
                    'key' => 'field_package_video_url',
                    'label' => '🎥 Video URL',
                    'name' => 'video_url',
                    'type' => 'url',
                    'instructions' => 'Link to YouTube or Vimeo video of the package.',
                    'required' => 0,
                    'placeholder' => 'https://www.youtube.com/watch?v=...',
                ],

                // ===== #9 - PACKAGE TYPE * =====
                [
                    'key' => 'field_package_type',
                    'label' => '📦 Package Type',
                    'name' => 'package_type',
                    'type' => 'taxonomy',
                    'instructions' => 'Select the package type.',
                    'taxonomy' => 'package_type',
                    'field_type' => 'select',
                    'required' => 1,
                    'return_format' => 'id',
                    'ui' => 1,
                    'wrapper' => ['width' => 50],
                ],

                // ===== #10 - INTEREST * =====
                [
                    'key' => 'field_package_interest',
                    'label' => '🎯 Interests',
                    'name' => 'interest',
                    'type' => 'taxonomy',
                    'instructions' => 'Select package interests (multiple selection allowed).',
                    'taxonomy' => 'interest',
                    'field_type' => 'multi_select',
                    'required' => 1,
                    'return_format' => 'id',
                    'ui' => 1,
                    'wrapper' => ['width' => 50],
                ],

                // ===== #11 - MONTH * =====
                [
                    'key' => 'field_package_months',
                    'label' => '📅 Months',
                    'name' => 'months',
                    'type' => 'select',
                    'instructions' => 'Select the months when this package is available.',
                    'required' => 1,
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
                    'multiple' => 1,
                    'ui' => 1,
                    'allow_null' => 0,
                    'wrapper' => ['width' => 50],
                ],

                // ===== #12 - FIXED DEPARTURES =====
                [
                    'key' => 'field_package_fixed_departures_v2',
                    'label' => '📆 Fixed Departures',
                    'name' => 'fixed_departures',
                    'type' => 'select',
                    'instructions' => 'Select the days of the week for fixed departures.',
                    'required' => 0,
                    'choices' => [
                        'monday' => 'Monday',
                        'tuesday' => 'Tuesday',
                        'wednesday' => 'Wednesday',
                        'thursday' => 'Thursday',
                        'friday' => 'Friday',
                        'saturday' => 'Saturday',
                        'sunday' => 'Sunday',
                    ],
                    'default_value' => [],
                    'multiple' => 1,
                    'ui' => 1,
                    'ajax' => 0,
                    'return_format' => 'value',
                    'allow_null' => 0,
                    'wrapper' => ['width' => 50],
                ],

                // ===== #13 - FREE SPOT CALENDAR * =====
                [
                    'key' => 'field_package_spot_calendar',
                    'label' => '📅 Free Spot Calendar',
                    'name' => 'free_spot_calendar',
                    'type' => 'taxonomy',
                    'instructions' => 'Select the spot calendar for availability management.',
                    'taxonomy' => 'spot_calendar',
                    'field_type' => 'select',
                    'required' => 1,
                    'return_format' => 'id',
                    'ui' => 1,
                    'wrapper' => ['width' => 50],
                ],

                // ===== #14 - FREE SPOT START DAY * =====
                [
                    'key' => 'field_package_spot_start_day',
                    'label' => '📅 Free Spot Start Day',
                    'name' => 'free_spot_start_day',
                    'type' => 'number',
                    'instructions' => 'Day of the month when free spots start (1-31).',
                    'required' => 1,
                    'min' => 1,
                    'max' => 31,
                    'wrapper' => ['width' => 50],
                ],

                // ===== #15 - SPECIALIST * =====
                [
                    'key' => 'field_package_specialist',
                    'label' => '👨‍🏫 Specialist',
                    'name' => 'specialist',
                    'type' => 'taxonomy',
                    'instructions' => 'Select the specialist/guide for this package.',
                    'taxonomy' => 'specialists',
                    'field_type' => 'select',
                    'required' => 1,
                    'return_format' => 'id',
                    'ui' => 1,
                ],

                // ===== #16 - DESTINATION * =====
                [
                    'key' => 'field_package_destination',
                    'label' => '🌍 Destination',
                    'name' => 'destination',
                    'type' => 'post_object',
                    'instructions' => 'Select the main destination (from Locations CPT).',
                    'post_type' => ['location'],
                    'return_format' => 'id',
                    'ui' => 1,
                    'required' => 1,
                    'wrapper' => ['width' => 50],
                ],

                // ===== #17 - LOCATIONS =====
                [
                    'key' => 'field_package_locations',
                    'label' => 'Locations',
                    'name' => 'locations',
                    'type' => 'post_object',
                    'instructions' => 'Select locations included in this package.',
                    'post_type' => ['location'],
                    'multiple' => 1,
                    'return_format' => 'id',
                    'ui' => 1,
                    'required' => 0,
                    'wrapper' => ['width' => 50],
                ],

                // ===== #18 - ACTIVITY * =====
                [
                    'key' => 'field_package_activity',
                    'label' => '🏃 Activity Level',
                    'name' => 'activity_level',
                    'type' => 'select',
                    'instructions' => 'Physical intensity level of the package.',
                    'required' => 1,
                    'choices' => [
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'very_high' => 'Very High',
                    ],
                    'default_value' => 'medium',
                    'ui' => 1,
                    'wrapper' => ['width' => 25],
                ],

                // ===== #19 - ALTITUDE =====
                [
                    'key' => 'field_package_altitude',
                    'label' => '⛰️ Altitude',
                    'name' => 'altitude',
                    'type' => 'text',
                    'instructions' => 'Maximum altitude reached in the package.',
                    'required' => 0,
                    'placeholder' => 'e.g. 3,400m / 11,155ft',
                    'wrapper' => ['width' => 25],
                ],

                // ===== #20 - DAYS * =====
                [
                    'key' => 'field_package_days',
                    'label' => 'Days',
                    'name' => 'days',
                    'type' => 'select',
                    'instructions' => 'Select the number of days (1-38).',
                    'required' => 1,
                    'choices' => array_combine(
                        range(1, 38),
                        array_map(function($n) { return $n . ($n === 1 ? ' Day' : ' Days'); }, range(1, 38))
                    ),
                    'default_value' => 1,
                    'ui' => 1,
                    'ajax' => 1,
                    'allow_null' => 0,
                    'wrapper' => ['width' => 25],
                ],




                // ===== #21 - PHYSICAL DIFFICULTY * =====
                [
                    'key' => 'field_package_physical_difficulty',
                    'label' => '💪 Physical Difficulty',
                    'name' => 'physical_difficulty',
                    'type' => 'select',
                    'instructions' => 'Physical demand level required.',
                    'required' => 1,
                    'choices' => [
                        'easy' => 'Easy',
                        'moderate' => 'Moderate',
                        'moderate_demanding' => 'Moderate - Demanding',
                        'difficult' => 'Difficult',
                        'very_difficult' => 'Very Difficult',
                    ],
                    'default_value' => 'moderate',
                    'ui' => 1,
                    'wrapper' => ['width' => 25],
                ],

                // ===== #22 - CULTURAL RATING * =====
                [
                    'key' => 'field_package_cultural_rating',
                    'label' => '🏛️ Cultural Rating',
                    'name' => 'cultural_rating',
                    'type' => 'select',
                    'instructions' => 'Cultural content rating (1-5).',
                    'required' => 1,
                    'choices' => [
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                    ],
                    'default_value' => '3',
                    'ui' => 1,
                    'wrapper' => ['width' => 50],
                ],

                // ===== #23 - WILDLIFE EXPECTATION * =====
                [
                    'key' => 'field_package_wildlife_expectation',
                    'label' => '🦙 Wildlife Expectation',
                    'name' => 'wildlife_expectation',
                    'type' => 'select',
                    'instructions' => 'Wildlife sighting expectation (1-5).',
                    'required' => 1,
                    'choices' => [
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                    ],
                    'default_value' => '3',
                    'ui' => 1,
                    'wrapper' => ['width' => 50],
                ],

                // ===== #24 - THUMBNAIL =====
                // WordPress native featured image - no ACF field needed

                // ===== #25 - OPTIONAL RENTING =====
                [
                    'key' => 'field_package_optional_renting',
                    'label' => '🚴 Optional Renting',
                    'name' => 'optional_renting',
                    'type' => 'taxonomy',
                    'instructions' => 'Select optional rentals available for this package.',
                    'taxonomy' => 'optional_renting',
                    'field_type' => 'multi_select',
                    'required' => 0,
                    'return_format' => 'id',
                    'ui' => 1,
                ],

                // ===== #26 - IS PREPAYMENT =====
                [
                    'key' => 'field_package_is_prepayment',
                    'label' => '💳 Requires Prepayment',
                    'name' => 'is_prepayment',
                    'type' => 'true_false',
                    'instructions' => 'Check if this package requires prepayment.',
                    'required' => 0,
                    'default_value' => 0,
                    'ui' => 1,
                    'wrapper' => ['width' => 20],
                ],

                // ===== #27 - INCATRAIL =====
                [
                    'key' => 'field_package_incatrail',
                    'label' => '🏔️ Inca Trail',
                    'name' => 'incatrail',
                    'type' => 'true_false',
                    'instructions' => 'Check if this package includes Inca Trail.',
                    'required' => 0,
                    'default_value' => 0,
                    'ui' => 1,
                    'wrapper' => ['width' => 20],
                ],

                // ===== #28 - PUBLISHED =====
                // Removed - WordPress native post_status

                // ===== #29 - IS HOME =====
                [
                    'key' => 'field_package_show_on_homepage',
                    'label' => '🏠 Is Home',
                    'name' => 'show_on_homepage',
                    'type' => 'true_false',
                    'instructions' => 'Check to include this package on the homepage.',
                    'required' => 0,
                    'default_value' => 0,
                    'ui' => 1,
                    'wrapper' => ['width' => 20],
                ],

                // ===== #30 - OPTIONAL =====
                [
                    'key' => 'field_package_optional',
                    'label' => '📌 Optional',
                    'name' => 'optional',
                    'type' => 'true_false',
                    'instructions' => 'Mark if this is an optional package.',
                    'required' => 0,
                    'default_value' => 0,
                    'ui' => 1,
                    'wrapper' => ['width' => 20],
                ],

                // ===== #31 - TRAVEL ZOO =====
                [
                    'key' => 'field_package_travel_zoo',
                    'label' => '🦁 Travel Zoo',
                    'name' => 'travel_zoo',
                    'type' => 'true_false',
                    'instructions' => 'Check if this package is affiliated with Travel Zoo.',
                    'required' => 0,
                    'default_value' => 0,
                    'ui' => 1,
                    'wrapper' => ['width' => 20],
                ],

                // ===== #32 - LUXURY =====
                [
                    'key' => 'field_package_luxury',
                    'label' => '💎 Luxury',
                    'name' => 'luxury',
                    'type' => 'true_false',
                    'instructions' => 'Check if this is a luxury package.',
                    'required' => 0,
                    'default_value' => 0,
                    'ui' => 1,
                    'wrapper' => ['width' => 20],
                ],

                // ===== #33 - SHOW SPECIALIST =====
                [
                    'key' => 'field_package_show_specialist',
                    'label' => '👨‍🏫 Show Specialist',
                    'name' => 'show_specialist',
                    'type' => 'true_false',
                    'instructions' => 'Check to display the specialist on the frontend.',
                    'required' => 0,
                    'default_value' => 1,
                    'ui' => 1,
                    'wrapper' => ['width' => 20],
                ],

                // ===== #34 - RECOMMENDATIONS =====
                [
                    'key' => 'field_package_recommendations',
                    'label' => '🌟 Recommendations',
                    'name' => 'recommendations',
                    'type' => 'wysiwyg',
                    'instructions' => 'Recommendations and tips for this package.',
                    'required' => 0,
                    'tabs' => 'visual',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],

                // ===== #35 - FEATURED =====
                [
                    'key' => 'field_package_featured',
                    'label' => '⭐ Featured Package',
                    'name' => 'featured_package',
                    'type' => 'true_false',
                    'instructions' => 'Mark if this package should appear in featured sections.',
                    'required' => 0,
                    'default_value' => 0,
                    'ui' => 1,
                    'wrapper' => ['width' => 20],
                ],

                // ===== #36 - ORDER * =====
                [
                    'key' => 'field_package_order',
                    'label' => '🔢 Order',
                    'name' => 'order',
                    'type' => 'number',
                    'instructions' => 'Display order for sorting packages (lower numbers appear first).',
                    'required' => 1,
                    'default_value' => 0,
                    'min' => 0,
                ],

                // ===== #37 - INCLUDED SERVICES =====
                [
                    'key' => 'field_package_included_services',
                    'label' => '✅ Included Services',
                    'name' => 'included_services',
                    'type' => 'taxonomy',
                    'instructions' => 'Select included services for this package.',
                    'taxonomy' => 'included_services',
                    'field_type' => 'multi_select',
                    'required' => 0,
                    'return_format' => 'id',
                    'ui' => 1,
                ],

                // ===== #38 - ADDITIONAL INFO =====
                [
                    'key' => 'field_package_additional_info',
                    'label' => 'ℹ️ Additional Info',
                    'name' => 'additional_info',
                    'type' => 'taxonomy',
                    'instructions' => 'Select additional information categories.',
                    'taxonomy' => 'additional_info',
                    'field_type' => 'multi_select',
                    'required' => 0,
                    'return_format' => 'id',
                    'ui' => 1,
                ],

                // ===== #39 - TAG LOCATIONS =====
                [
                    'key' => 'field_package_tag_locations',
                    'label' => '📍 Tag Locations',
                    'name' => 'tag_locations',
                    'type' => 'post_object',
                    'instructions' => 'Select tag locations (from Locations CPT).',
                    'post_type' => ['location'],
                    'multiple' => 1,
                    'return_format' => 'id',
                    'ui' => 1,
                    'required' => 0,
                ],

                // ===== #40 - FLIGHTS =====
                [
                    'key' => 'field_package_flights',
                    'label' => '✈️ Flights',
                    'name' => 'flights',
                    'type' => 'post_object',
                    'instructions' => 'Select flight locations (from Locations CPT).',
                    'post_type' => ['location'],
                    'multiple' => 1,
                    'return_format' => 'id',
                    'ui' => 1,
                    'required' => 0,
                ],

                // ===== #41 - TITLE OVERVIEW * =====
                [
                    'key' => 'field_package_title_overview',
                    'label' => '📝 Title: Overview',
                    'name' => 'title_overview',
                    'type' => 'text',
                    'instructions' => 'Custom title for the overview section.',
                    'required' => 1,
                    'default_value' => 'Trip Overview',
                    'wrapper' => ['width' => 50],
                ],

                // ===== #42 - TITLE ITINERARY * =====
                [
                    'key' => 'field_package_title_itinerary',
                    'label' => '📝 Title: Itinerary',
                    'name' => 'title_itinerary',
                    'type' => 'text',
                    'instructions' => 'Custom title for the itinerary section.',
                    'required' => 1,
                    'default_value' => 'Day by day schedule',
                    'wrapper' => ['width' => 50],
                ],

                // ===== #43 - TITLE DATES * =====
                [
                    'key' => 'field_package_title_dates',
                    'label' => '📝 Title: Dates',
                    'name' => 'title_dates',
                    'type' => 'text',
                    'instructions' => 'Custom title for the dates & prices section.',
                    'required' => 1,
                    'default_value' => 'Dates & Prices',
                    'wrapper' => ['width' => 50],
                ],

                // ===== #44 - TITLE INCLUDED * =====
                [
                    'key' => 'field_package_title_included',
                    'label' => '📝 Title: Included',
                    'name' => 'title_included',
                    'type' => 'text',
                    'instructions' => 'Custom title for the inclusions section.',
                    'required' => 1,
                    'default_value' => 'Inclusions',
                    'wrapper' => ['width' => 50],
                ],

                // ===== #45 - TITLE OPTIONAL ACT * =====
                [
                    'key' => 'field_package_title_optional_act',
                    'label' => '📝 Title: Optional Activities',
                    'name' => 'title_optional_act',
                    'type' => 'text',
                    'instructions' => 'Custom title for the optional activities section.',
                    'required' => 1,
                    'default_value' => 'Optional Activities',
                    'wrapper' => ['width' => 50],
                ],

                // ===== #46 - TITLE ADDITIONAL INFO * =====
                [
                    'key' => 'field_package_title_additional_info',
                    'label' => '📝 Title: Additional Info',
                    'name' => 'title_additional_info',
                    'type' => 'text',
                    'instructions' => 'Custom title for the additional information section.',
                    'required' => 1,
                    'default_value' => 'Additional Information',
                    'wrapper' => ['width' => 50],
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
            'menu_order' => 10,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ]);
    }
}
