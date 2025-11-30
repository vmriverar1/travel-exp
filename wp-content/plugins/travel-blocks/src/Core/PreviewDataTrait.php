<?php
/**
 * Preview Data Trait
 *
 * Provides reusable preview/sample data for Template Blocks
 *
 * @package Travel\Blocks\Core
 * @since 2.0.0
 */

namespace Travel\Blocks\Core;

trait PreviewDataTrait
{
    /**
     * Get sample package data for preview
     *
     * @return array Sample package data
     */
    protected function get_preview_package_data(): array
    {
        return [
            'title' => '4-Day Inca Trail Trek to Machu Picchu',
            'subtitle' => 'Classic Route to the Lost City of the Incas',
            'overview' => 'Experience the magic of the Inca Trail on this unforgettable 4-day journey to Machu Picchu. Trek through stunning mountain scenery, explore ancient Inca ruins, and arrive at the Sun Gate to witness a breathtaking sunrise over Machu Picchu.',
            'price_from' => 450,
            'price_normal' => 550,
            'price_offer' => 450,
            'duration' => '4 Days / 3 Nights',
            'days' => 4,
            'departure' => 'Cusco',
            'arrival' => 'Cusco',
            'departure_time' => '05:00 AM',
            'return_time' => '08:00 PM',
            'physical_difficulty' => 'Moderate',
            'difficulty_level' => 3,
            'service_type' => 'Small Group',
            'group_size' => 16,
            'rating' => 4.8,
            'tripadvisor_rating' => 4.9,
            'total_reviews' => 1250,
            'tripadvisor_url' => '#',
        ];
    }

    /**
     * Get sample itinerary days for preview
     *
     * @return array Sample itinerary
     */
    protected function get_preview_itinerary(): array
    {
        return [
            [
                'day_number' => 1,
                'title' => 'Cusco to Wayllabamba',
                'description' => 'Begin your Inca Trail adventure with hotel pickup and transfer to Km 82. Start hiking through the beautiful Urubamba Valley.',
                'activities' => 'Hiking, Archaeological site visit',
                'meals' => 'Lunch, Dinner',
                'accommodation' => 'Camping',
                'distance' => '12 km',
                'elevation_gain' => '+800m',
            ],
            [
                'day_number' => 2,
                'title' => 'Wayllabamba to Pacaymayo',
                'description' => 'The most challenging day. Ascend to Dead Woman\'s Pass at 4,215m, the highest point of the trek.',
                'activities' => 'Mountain pass crossing, Photography',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Camping',
                'distance' => '11 km',
                'elevation_gain' => '+600m',
            ],
            [
                'day_number' => 3,
                'title' => 'Pacaymayo to Wiñay Wayna',
                'description' => 'Trek through cloud forest and visit impressive Inca ruins including Runkurakay and Wiñay Wayna.',
                'activities' => 'Ruins exploration, Cloud forest hiking',
                'meals' => 'Breakfast, Lunch, Dinner',
                'accommodation' => 'Camping',
                'distance' => '16 km',
                'elevation_gain' => '+200m',
            ],
            [
                'day_number' => 4,
                'title' => 'Wiñay Wayna to Machu Picchu',
                'description' => 'Early morning hike to the Sun Gate for sunrise over Machu Picchu. Guided tour of the citadel and return to Cusco.',
                'activities' => 'Machu Picchu tour, Sun Gate visit',
                'meals' => 'Breakfast',
                'accommodation' => 'None',
                'distance' => '5 km',
                'elevation_gain' => '-400m',
            ],
        ];
    }

    /**
     * Get sample images for preview
     *
     * @param int $count Number of images
     * @return array Sample image URLs
     */
    protected function get_preview_images(int $count = 4): array
    {
        $images = [];
        for ($i = 1; $i <= $count; $i++) {
            $images[] = [
                'url' => "https://picsum.photos/1200/800?random={$i}",
                'alt' => "Sample image {$i}",
                'title' => "Gallery Image {$i}",
            ];
        }
        return $images;
    }

    /**
     * Get sample inclusions/exclusions for preview
     *
     * @return array Sample inclusions and exclusions
     */
    protected function get_preview_inclusions(): array
    {
        return [
            'included' => [
                'Professional bilingual guide',
                'All camping equipment',
                'Porter service (7kg personal gear)',
                'All meals during trek (3B, 3L, 3D)',
                'Entrance fees: Inca Trail + Machu Picchu',
                'Train ticket: Aguas Calientes to Ollantaytambo',
                'First aid kit and oxygen',
            ],
            'not_included' => [
                'Sleeping bag (rental available: $20)',
                'Trekking poles (rental available: $15)',
                'Travel insurance',
                'Tips for guide and porters',
                'First breakfast and last dinner',
                'Personal expenses',
            ],
        ];
    }

    /**
     * Get sample reviews for preview
     *
     * @return array Sample reviews
     */
    protected function get_preview_reviews(): array
    {
        return [
            [
                'author' => 'Sarah M.',
                'country' => 'United States',
                'rating' => 5,
                'date' => '2025-09-15',
                'content' => 'Amazing experience! The Inca Trail exceeded all expectations. Our guide was knowledgeable and the camping was comfortable.',
                'avatar' => 'https://i.pravatar.cc/150?img=1',
            ],
            [
                'author' => 'James T.',
                'country' => 'United Kingdom',
                'rating' => 5,
                'date' => '2025-09-10',
                'content' => 'Best trek of my life! The sunrise at the Sun Gate was absolutely breathtaking. Highly recommend!',
                'avatar' => 'https://i.pravatar.cc/150?img=2',
            ],
            [
                'author' => 'Maria G.',
                'country' => 'Spain',
                'rating' => 4,
                'date' => '2025-09-05',
                'content' => 'Great adventure and well organized. The only downside was the weather on day 2, but that\'s nature!',
                'avatar' => 'https://i.pravatar.cc/150?img=3',
            ],
        ];
    }

    /**
     * Get sample FAQs for preview
     *
     * @return array Sample FAQs
     */
    protected function get_preview_faqs(): array
    {
        return [
            [
                'question' => 'What is the best time to do the Inca Trail?',
                'answer' => 'The best months are April to October during the dry season. July and August are the busiest months.',
            ],
            [
                'question' => 'How difficult is the Inca Trail?',
                'answer' => 'The trek is considered moderate to challenging. Day 2 (Dead Woman\'s Pass) is the most difficult. Good physical fitness is recommended.',
            ],
            [
                'question' => 'Do I need to book in advance?',
                'answer' => 'Yes! The Inca Trail permits are limited to 500 people per day and sell out months in advance. Book at least 3-6 months ahead.',
            ],
            [
                'question' => 'What should I pack?',
                'answer' => 'Essential items: hiking boots, warm sleeping bag, rain gear, sun protection, water bottle, headlamp, and personal medications.',
            ],
        ];
    }

    /**
     * Get sample related packages for preview
     *
     * @return array Sample related packages
     */
    protected function get_preview_related_packages(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Salkantay Trek 5 Days',
                'image' => 'https://picsum.photos/400/300?random=10',
                'price' => 380,
                'duration' => '5 Days',
                'rating' => 4.7,
                'url' => '#',
            ],
            [
                'id' => 2,
                'title' => 'Rainbow Mountain Trek',
                'image' => 'https://picsum.photos/400/300?random=11',
                'price' => 85,
                'duration' => 'Full Day',
                'rating' => 4.6,
                'url' => '#',
            ],
            [
                'id' => 3,
                'title' => 'Sacred Valley Tour',
                'image' => 'https://picsum.photos/400/300?random=12',
                'price' => 65,
                'duration' => 'Full Day',
                'rating' => 4.8,
                'url' => '#',
            ],
        ];
    }

    /**
     * Get sample trust badges for preview
     *
     * @return array Sample trust badges
     */
    protected function get_preview_trust_badges(): array
    {
        return [
            [
                'name' => 'TripAdvisor Certificate of Excellence',
                'image' => 'https://via.placeholder.com/150x150?text=TripAdvisor',
            ],
            [
                'name' => 'Safe Travels Certified',
                'image' => 'https://via.placeholder.com/150x150?text=Safe+Travels',
            ],
            [
                'name' => 'Ministry of Culture Authorized',
                'image' => 'https://via.placeholder.com/150x150?text=Peru+Culture',
            ],
        ];
    }

    /**
     * Get sample best months for preview
     *
     * @return array Sample best months
     */
    protected function get_preview_best_months(): array
    {
        return [
            'apr' => 'April',
            'may' => 'May',
            'jun' => 'June',
            'jul' => 'July',
            'aug' => 'August',
            'sep' => 'September',
            'oct' => 'October',
        ];
    }

    /**
     * Get sample map data for preview
     *
     * @return array Sample map data
     */
    protected function get_preview_map_data(): array
    {
        return [
            'latitude' => -13.1631,
            'longitude' => -72.5450,
            'zoom' => 10,
            'markers' => [
                ['lat' => -13.1631, 'lng' => -72.5450, 'title' => 'Machu Picchu'],
                ['lat' => -13.5319, 'lng' => -71.9675, 'title' => 'Cusco'],
            ],
        ];
    }
}
