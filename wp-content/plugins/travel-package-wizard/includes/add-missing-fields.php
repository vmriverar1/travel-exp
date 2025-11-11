<?php
/**
 * Script to add missing fields (Phases 2-6) to all packages in mock-packages-data.php
 *
 * This script reads the existing mock data and adds all missing fields from Phases 2-6
 * to packages that don't have them yet.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Template for missing fields
function get_missing_fields_template($package_data) {
    $title = $package_data['title'] ?? 'Tour';
    $is_multiday = ($package_data['days'] ?? 1) > 1;
    $difficulty = $package_data['physical_difficulty'] ?? 'moderate';

    return [
        // FASE 2
        'best_months' => ['apr', 'may', 'jun', 'jul', 'aug', 'sep'],
        'inclusions' => [
            'Professional bilingual guide',
            'Transportation included',
            'All entrance fees',
        ],
        'reviews' => [
            [
                'author' => 'John D.',
                'rating' => 5,
                'date' => '2025-08-15',
                'content' => 'Excellent tour! Well organized and our guide was very knowledgeable. Highly recommended.',
                'country' => 'USA',
            ],
            [
                'author' => 'Maria S.',
                'rating' => 4,
                'date' => '2025-07-22',
                'content' => 'Great experience overall. The sites were amazing and the logistics were smooth.',
                'country' => 'Spain',
            ],
        ],

        // FASE 3
        'overview' => 'Experience this unforgettable tour through Peru\'s stunning landscapes and rich cultural heritage.',
        'quick_facts' => [
            ['icon' => 'clock', 'label' => 'Duration', 'value' => ($package_data['duration'] ?? '1 day')],
            ['icon' => 'users', 'label' => 'Group Size', 'value' => 'Max ' . ($package_data['group_size'] ?? 16) . ' people'],
            ['icon' => 'globe', 'label' => 'Languages', 'value' => 'English, Spanish'],
        ],
        'highlights' => [
            'Professional expert guide',
            'Stunning scenery and views',
            'Cultural immersion',
            'Small group experience',
        ],
        'itinerary' => [
            [
                'day_number' => 1,
                'title' => $title,
                'description' => 'Full day experiencing the highlights of this tour with expert guidance and comfortable transportation.',
                'meals' => $is_multiday ? 'Breakfast, Lunch, Dinner' : 'Lunch',
                'accommodation' => $is_multiday ? 'Hotel or camping' : 'None (day tour)',
                'activities' => ['Sightseeing', 'Cultural activities', 'Photography'],
            ],
        ],
        'brochure_url' => '',
        'guide_profiles' => [],

        // FASE 4
        'departures' => [
            ['date' => '2025-11-15', 'status' => 'available', 'price' => ($package_data['price_offer'] ?? 50), 'spaces_available' => 12, 'permits_left' => null],
            ['date' => '2025-12-01', 'status' => 'available', 'price' => ($package_data['price_offer'] ?? 50), 'spaces_available' => 10, 'permits_left' => null],
        ],
        'inclusions_full' => [
            'Professional bilingual guide',
            'All transportation',
            'Entrance fees to sites',
            'Meals as specified',
            'First aid kit',
        ],
        'exclusions' => [
            'Travel insurance',
            'Tips for guide',
            'Personal expenses',
            'Meals not specified',
        ],
        'currency_options' => ['USD', 'EUR', 'GBP'],
        'show_price_calculator' => false,

        // FASE 5
        'contact_email' => 'info@peruviantours.com',
        'whatsapp_number' => '+51987654321',
        'contact_cta_text' => 'Book Your Adventure Today!',
        'faqs' => [
            ['question' => 'What should I bring?', 'answer' => 'Comfortable shoes, sun protection, camera, water bottle, and light jacket.'],
            ['question' => 'Is this suitable for families?', 'answer' => 'Yes, this tour is suitable for all ages and fitness levels.'],
            ['question' => 'What is the cancellation policy?', 'answer' => 'Free cancellation up to 48 hours before departure. Contact us for details.'],
        ],
        'review_platforms' => ['tripadvisor', 'google'],
        'show_review_filter' => false,

        // FASE 6
        'related_posts_manual' => [],
        'impact_title' => 'Sustainable Tourism',
        'impact_items' => [
            ['icon' => 'users', 'title' => 'Local Guides', 'description' => 'Supporting local communities through employment'],
            ['icon' => 'leaf', 'title' => 'Eco-Friendly', 'description' => 'Minimizing environmental impact'],
            ['icon' => 'heart', 'title' => 'Community Support', 'description' => 'Contributing to local development'],
        ],
        'impact_background' => null,
        'trust_badges' => [
            ['logo_id' => null, 'name' => 'TripAdvisor Recommended', 'url' => 'https://www.tripadvisor.com'],
        ],
        'certifications_text' => 'Committed to Sustainable and Responsible Tourism',
    ];
}

// Function to check if package has all required fields
function package_needs_fields($package_data) {
    $required_fields = ['best_months', 'inclusions', 'reviews', 'overview', 'quick_facts',
                       'highlights', 'itinerary', 'departures', 'inclusions_full', 'exclusions',
                       'contact_email', 'faqs', 'impact_items'];

    foreach ($required_fields as $field) {
        if (!isset($package_data[$field]) || empty($package_data[$field])) {
            return true;
        }
    }
    return false;
}

// Main execution
function add_missing_fields_to_packages() {
    $data_file = __DIR__ . '/mock-packages-data.php';
    $packages = include $data_file;

    $updated_count = 0;

    foreach ($packages as &$package) {
        if (package_needs_fields($package['data'])) {
            $missing_fields = get_missing_fields_template($package['data']);

            // Merge missing fields into package data
            foreach ($missing_fields as $key => $value) {
                if (!isset($package['data'][$key]) || empty($package['data'][$key])) {
                    $package['data'][$key] = $value;
                }
            }

            $updated_count++;
        }
    }

    return [
        'updated' => $updated_count,
        'total' => count($packages),
        'packages' => $packages
    ];
}

// Export as function for use in admin
return [
    'add_missing_fields_to_packages' => 'add_missing_fields_to_packages',
    'get_missing_fields_template' => 'get_missing_fields_template',
];
