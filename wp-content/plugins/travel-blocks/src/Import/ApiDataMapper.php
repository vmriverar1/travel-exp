<?php

namespace Travel\Blocks\Import;

/**
 * API Data Mapper
 *
 * Transforms data from Valencia Travel API to WordPress CPT Package format.
 * Handles mapping of simple fields, taxonomies, post objects, and repeaters.
 *
 * @package Travel\Blocks\Import
 * @since 1.0.0
 */
class ApiDataMapper
{
    /**
     * Map API data to CPT package structure
     *
     * @param array $api_data Raw data from Valencia API
     * @return array Mapped data ready for WordPress
     */
    public function map_to_package(array $api_data): array
    {
        if (empty($api_data)) {
            return [];
        }

        return [
            'post_data' => $this->map_post_data($api_data),
            'meta_fields' => $this->map_meta_fields($api_data),
            'taxonomies' => $this->map_taxonomies($api_data),
            'post_objects' => $this->map_post_objects($api_data),
            'repeaters' => $this->map_repeaters($api_data),
        ];
    }

    /**
     * Map post data (title, content, status, excerpt, slug, date)
     *
     * Maps API data to WordPress native post fields:
     * - title → post_title
     * - summary → post_excerpt
     * - slugs.en → post_name
     * - created → post_date
     *
     * @param array $api_data
     * @return array
     */
    private function map_post_data(array $api_data): array
    {
        $post_data = [
            'post_title' => $this->sanitize_text($api_data['title'] ?? ''),
            'post_type' => 'package',
            'post_status' => 'publish',
            'comment_status' => 'closed', // Packages don't need comments
            'ping_status' => 'closed',
        ];

        // Post excerpt (summary)
        if (!empty($api_data['summary'])) {
            $post_data['post_excerpt'] = $this->sanitize_text($api_data['summary']);
        }

        // Post slug (prefer English slug)
        if (!empty($api_data['slugs']['en'])) {
            $post_data['post_name'] = sanitize_title($api_data['slugs']['en']);
        } elseif (!empty($api_data['slugs']['es'])) {
            $post_data['post_name'] = sanitize_title($api_data['slugs']['es']);
        }

        // Post date (created date from API)
        if (!empty($api_data['created'])) {
            $date = $this->parse_date($api_data['created']);
            if ($date) {
                $post_data['post_date'] = $date;
                $post_data['post_date_gmt'] = get_gmt_from_date($date);
            }
        }

        return $post_data;
    }

    /**
     * Map simple meta fields
     *
     * @param array $api_data
     * @return array
     */
    private function map_meta_fields(array $api_data): array
    {
        return [
            // ID from API
            'tour_id' => (int) ($api_data['id'] ?? 0),

            // Basic info
            'summary' => $this->sanitize_textarea($api_data['summary'] ?? ''),
            'description' => $this->sanitize_html($api_data['description'] ?? ''),
            'days' => intval($api_data['days'] ?? 0),

            // Pricing
            'price_from' => $this->parse_price($api_data['price'] ?? 0),
            'price_offer' => $this->parse_price($api_data['offer'] ?? 0),
            'price_single_supplement' => $this->parse_price($api_data['singleSupp'] ?? 0),

            // Physical difficulty
            'physical_difficulty' => $this->map_physical_difficulty($api_data['physicalRating'] ?? ''),

            // Included/Not Included
            'included' => $this->sanitize_html($api_data['whatsIncluded'] ?? ''),
            'not_included' => $this->sanitize_html($api_data['whatsNotIncluded'] ?? ''),

            // Media
            'thumbnail_url' => $this->map_thumbnail_from_images($api_data['images'] ?? []),
            'map_image' => $this->map_image($api_data['mapImage'] ?? ''),
            'video_url' => $this->sanitize_url($api_data['video_URL'] ?? ''),

            // Months
            'months' => $this->map_months($api_data['month'] ?? []),

            // Promo
            'promo_enabled' => !empty($api_data['promo']),

            // Calendar
            'free_spot_calendar' => (int) ($api_data['freeSpotCalendar'] ?? 0),
            'free_spot_start_day' => (int) ($api_data['freeSpotStartDay'] ?? 0),

            // Prepayment
            'is_prepayment' => !empty($api_data['isPrepayment']),

            // Rating and Stars
            'total_reviews' => (int) ($api_data['rating'] ?? 0),
            'google_rating' => (float) ($api_data['stars'] ?? 0),

            // Activity Level
            'activity_level' => $this->map_activity_level($api_data['activityName'] ?? ''),

            // Custom titles
            'title_overview' => $this->sanitize_text($api_data['titleOverview'] ?? ''),
            'title_itinerary' => $this->sanitize_text($api_data['titleItinerary'] ?? ''),
            'title_dates' => $this->sanitize_text($api_data['titleDates'] ?? ''),
            'title_included' => $this->sanitize_text($api_data['titleIncluded'] ?? ''),
            'title_optional_act' => $this->sanitize_text($api_data['titleOptionalAct'] ?? ''),
            'title_additional_info' => $this->sanitize_text($api_data['titleAdditionalInfo'] ?? ''),

            // Defaults for fields not in API
            'active' => true,
            'featured_package' => false,
            'show_on_homepage' => false,
        ];
    }

    /**
     * Map taxonomies
     *
     * @param array $api_data
     * @return array
     */
    private function map_taxonomies(array $api_data): array
    {
        return [
            'interest' => $this->map_interests($api_data['interests'] ?? []),
            'included_services' => $this->map_included_services($api_data['includedServices'] ?? []),
            'package_type' => $this->map_package_type($api_data['packageTypes'] ?? []),
            'optional_renting' => $this->map_optional_renting_taxonomy($api_data['optionalRenting'] ?? []),
            'specialist' => $this->map_specialist($api_data['specialist'] ?? []),
        ];
    }

    /**
     * Map post object relationships
     *
     * @param array $api_data
     * @return array
     */
    private function map_post_objects(array $api_data): array
    {
        return [
            'locations' => $this->map_locations($api_data['locations'] ?? []),
            'tag_locations' => $this->map_tag_locations($api_data['tagLocations'] ?? []),
            'flights' => $this->map_flights($api_data['flights'] ?? []),
        ];
    }

    /**
     * Map repeater fields
     *
     * @param array $api_data
     * @return array
     */
    private function map_repeaters(array $api_data): array
    {
        return [
            'itinerary' => $this->map_itinerary($api_data['itineraries'] ?? []),
            'gallery' => $this->map_gallery($api_data['images'] ?? []),
            'price_tiers' => $this->map_price_tiers($api_data['prices']['values'] ?? []),
            'highlights' => $this->map_highlights($api_data),
            'additional_sections' => $this->map_additional_sections($api_data['additionalInfo'] ?? []),
        ];
    }

    // ============================================
    // SANITIZATION METHODS
    // ============================================

    /**
     * Sanitize plain text without wptexturize
     */
    private function sanitize_text(string $text): string
    {
        // Temporarily disable wptexturize to prevent -- becoming –
        remove_filter('sanitize_text_field', 'wptexturize');
        $sanitized = sanitize_text_field($text);
        add_filter('sanitize_text_field', 'wptexturize');

        return $sanitized;
    }

    /**
     * Sanitize textarea without wptexturize
     */
    private function sanitize_textarea(string $text): string
    {
        // Temporarily disable wptexturize to prevent -- becoming –
        remove_filter('sanitize_textarea_field', 'wptexturize');
        $sanitized = sanitize_textarea_field($text);
        add_filter('sanitize_textarea_field', 'wptexturize');

        return $sanitized;
    }

    /**
     * Sanitize HTML content without wptexturize
     */
    private function sanitize_html(string $html): string
    {
        // Temporarily disable wptexturize to prevent -- becoming –
        $priority = has_filter('the_content', 'wptexturize');
        if ($priority !== false) {
            remove_filter('the_content', 'wptexturize', $priority);
        }

        $sanitized = wp_kses_post($html);

        if ($priority !== false) {
            add_filter('the_content', 'wptexturize', $priority);
        }

        return $sanitized;
    }

    /**
     * Sanitize URL
     */
    private function sanitize_url(string $url): string
    {
        return esc_url_raw($url);
    }

    /**
     * Parse price string to float
     */
    private function parse_price($price): float
    {
        if (is_numeric($price)) {
            return (float) $price;
        }

        // Remove currency symbols and convert to float
        $cleaned = preg_replace('/[^0-9.]/', '', (string) $price);
        return (float) $cleaned;
    }

    // ============================================
    // FIELD MAPPERS - SIMPLE
    // ============================================

    /**
     * Map physical difficulty rating
     */
    private function map_physical_difficulty(string $rating): string
    {
        $map = [
            'Easy' => 'easy',
            'Moderate' => 'moderate',
            'Moderate - Demanding' => 'moderate_demanding',
            'Demanding' => 'difficult',
            'Very Difficult' => 'very_difficult',
        ];

        return $map[$rating] ?? 'moderate';
    }

    /**
     * Map duration from days count to readable text
     *
     * @param int $days Number of days from API
     * @return string Formatted duration text (e.g., "4 days / 3 nights")
     */
    private function map_duration(int $days): string
    {
        if ($days <= 0) {
            return '';
        }

        $nights = $days - 1;

        if ($days === 1) {
            return '1 day';
        }

        if ($nights > 0) {
            return sprintf('%d days / %d nights', $days, $nights);
        }

        return sprintf('%d days', $days);
    }

    /**
     * Map months array to select values
     */
    private function map_months(array $months): array
    {
        $month_names = [
            1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april',
            5 => 'may', 6 => 'june', 7 => 'july', 8 => 'august',
            9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december'
        ];

        $mapped = [];
        foreach ($months as $month_num) {
            if (isset($month_names[$month_num])) {
                $mapped[] = $month_names[$month_num];
            }
        }

        return $mapped;
    }

    /**
     * Map image URL (returns URL for later processing)
     */
    private function map_image(string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        // Convert relative URLs to absolute
        $url = $this->normalize_image_url($url);

        // Return URL - will be processed by ImageImportService
        return $this->sanitize_url($url);
    }

    // ============================================
    // TAXONOMY MAPPERS
    // ============================================

    /**
     * Map interests to taxonomy terms
     */
    private function map_interests(array $interests): array
    {
        return array_map(function($interest) {
            return $this->get_or_create_term($interest, 'interest');
        }, $interests);
    }

    /**
     * Map included services to taxonomy terms
     */
    private function map_included_services(array $services): array
    {
        $term_ids = [];

        foreach ($services as $service) {
            if (isset($service['title'])) {
                $term_id = $this->get_or_create_term($service['title'], 'included_services');
                if ($term_id) {
                    $term_ids[] = $term_id;
                }
            }
        }

        return $term_ids;
    }

    /**
     * Map package type to taxonomy term (only first one, ACF allows single value)
     */
    private function map_package_type(array $types): array
    {
        if (empty($types)) {
            return [];
        }

        $term_id = $this->get_or_create_term($types[0], 'package_type');
        return $term_id ? [$term_id] : [];
    }

    /**
     * Map days to taxonomy term
     */
    private function map_days(int $days): array
    {
        if ($days <= 0) {
            return [];
        }

        // Create term name based on days count
        $term_name = $days === 1 ? '1 Day' : "{$days} Days";

        // Handle special ranges
        if ($days >= 11 && $days <= 15) {
            $term_name = '11-15 Days';
        } elseif ($days >= 16 && $days <= 20) {
            $term_name = '16-20 Days';
        } elseif ($days >= 21 && $days <= 30) {
            $term_name = '21-30 Days';
        } elseif ($days > 30) {
            $term_name = '30+ Days';
        }

        $term_id = $this->get_or_create_term($term_name, 'day');
        return $term_id ? [$term_id] : [];
    }

    /**
     * Map optional renting items to taxonomy terms
     */
    private function map_optional_renting_taxonomy(array $items): array
    {
        $term_ids = [];

        foreach ($items as $item) {
            if (isset($item['title'])) {
                $term_id = $this->get_or_create_term($item['title'], 'optional_renting');
                if ($term_id) {
                    $term_ids[] = $term_id;
                }
            }
        }

        return $term_ids;
    }

    /**
     * Map specialist to taxonomy term
     *
     * @param array $specialist Specialist data from API (id, fullname, email, calendly, thumbnail)
     * @return array Array with single term ID
     */
    private function map_specialist(array $specialist): array
    {
        if (empty($specialist) || !isset($specialist['fullname'])) {
            return [];
        }

        $fullname = trim($specialist['fullname']);
        if (empty($fullname)) {
            return [];
        }

        $term_id = $this->get_or_create_term($fullname, 'specialists');
        return $term_id ? [$term_id] : [];
    }

    /**
     * Map activity level from API format to ACF select values
     */
    private function map_activity_level(string $activity_name): string
    {
        $activity_lower = strtolower(trim($activity_name));

        // Map API values to ACF select values
        $mapping = [
            'easy' => 'low',
            'light' => 'low',
            'low' => 'low',
            'moderate' => 'medium',
            'medium' => 'medium',
            'challenging' => 'high',
            'difficult' => 'high',
            'hard' => 'high',
            'high' => 'high',
            'strenuous' => 'very_high',
            'very difficult' => 'very_high',
            'extreme' => 'very_high',
            'very high' => 'very_high',
        ];

        return $mapping[$activity_lower] ?? 'medium';
    }

    /**
     * Get or create taxonomy term (with duplicate prevention)
     */
    private function get_or_create_term(string $term_name, string $taxonomy): ?int
    {
        if (empty($term_name)) {
            return null;
        }

        // Normalize for comparison
        $normalized = $this->normalize_string($term_name);

        // Try to find existing term using multiple methods
        // 1. Check by exact name
        $term = get_term_by('name', $term_name, $taxonomy);
        if ($term) {
            return $term->term_id;
        }

        // 2. Check by slug (auto-generated from name)
        $slug = sanitize_title($term_name);
        $term = get_term_by('slug', $slug, $taxonomy);
        if ($term) {
            return $term->term_id;
        }

        // 3. Search all terms and compare normalized versions
        $all_terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'number' => 0,
        ]);

        if (!is_wp_error($all_terms)) {
            foreach ($all_terms as $existing_term) {
                if ($this->normalize_string($existing_term->name) === $normalized) {
                    // Found a match with normalized comparison
                    return $existing_term->term_id;
                }
            }
        }

        // 4. No match found, create new term
        // Temporarily disable wptexturize to prevent -- becoming –
        remove_filter('sanitize_text_field', 'wptexturize');
        remove_filter('sanitize_textarea_field', 'wptexturize');

        $result = wp_insert_term($term_name, $taxonomy);

        add_filter('sanitize_text_field', 'wptexturize');
        add_filter('sanitize_textarea_field', 'wptexturize');

        if (is_wp_error($result)) {
            // Check if error is "term already exists"
            if (isset($result->error_data['term_exists'])) {
                return $result->error_data['term_exists'];
            }

            $this->log_error("Failed to create term '{$term_name}' in taxonomy '{$taxonomy}': " . $result->get_error_message());
            return null;
        }

        return $result['term_id'];
    }

    // ============================================
    // POST OBJECT MAPPERS
    // ============================================

    /**
     * Map locations array to location post IDs
     */
    private function map_locations(array $locations): array
    {
        $location_ids = [];

        foreach ($locations as $location) {
            $title = $location['title'] ?? '';
            $slug = $location['slug'] ?? '';

            if ($title) {
                $location_id = $this->find_or_create_location($title, $slug);
                if ($location_id) {
                    $location_ids[] = $location_id;
                }
            }
        }

        return $location_ids;
    }

    /**
     * Map tag locations to location post IDs
     */
    private function map_tag_locations(array $tag_locations): array
    {
        $location_ids = [];

        foreach ($tag_locations as $location) {
            $title = $location['title'] ?? '';
            $slug = $location['slug'] ?? '';

            if ($title) {
                $location_id = $this->find_or_create_location($title, $slug);
                if ($location_id) {
                    $location_ids[] = $location_id;
                }
            }
        }

        return $location_ids;
    }

    /**
     * Map flights to location post IDs
     */
    private function map_flights(array $flights): array
    {
        // Same structure as locations
        return $this->map_locations($flights);
    }

    /**
     * Find or create location post (with duplicate prevention)
     */
    private function find_or_create_location(string $title, string $slug = ''): ?int
    {
        if (empty($title)) {
            return null;
        }

        // Normalize for comparison
        $normalized_title = $this->normalize_string($title);

        // 1. Try to find by slug first
        if ($slug) {
            $post = get_page_by_path($slug, OBJECT, 'location');
            if ($post) {
                return $post->ID;
            }
        }

        // 2. Try to find by auto-generated slug
        $auto_slug = sanitize_title($title);
        $post = get_page_by_path($auto_slug, OBJECT, 'location');
        if ($post) {
            return $post->ID;
        }

        // 3. Search by exact title
        $args = [
            'post_type' => 'location',
            'post_status' => 'any',
            'title' => $title,
            'posts_per_page' => 1,
            'fields' => 'ids',
        ];

        $query = new \WP_Query($args);
        if (!empty($query->posts)) {
            return $query->posts[0];
        }

        // 4. Search all locations and compare normalized titles
        $all_locations = get_posts([
            'post_type' => 'location',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);

        foreach ($all_locations as $location_id) {
            $location_title = get_the_title($location_id);
            if ($this->normalize_string($location_title) === $normalized_title) {
                // Found a match with normalized comparison
                return $location_id;
            }
        }

        // 5. No match found, create new location
        // Temporarily disable wptexturize to prevent -- becoming –
        remove_filter('sanitize_text_field', 'wptexturize');
        remove_filter('sanitize_textarea_field', 'wptexturize');
        remove_filter('title_save_pre', 'wptexturize');

        $post_id = wp_insert_post([
            'post_title' => $title,
            'post_name' => $slug ?: $auto_slug,
            'post_type' => 'location',
            'post_status' => 'publish',
        ]);

        add_filter('sanitize_text_field', 'wptexturize');
        add_filter('sanitize_textarea_field', 'wptexturize');
        add_filter('title_save_pre', 'wptexturize');

        if (is_wp_error($post_id)) {
            $this->log_error("Failed to create location '{$title}': " . $post_id->get_error_message());
            return null;
        }

        $this->log_debug("Created new location: '{$title}' (ID: {$post_id})");

        return $post_id;
    }

    // ============================================
    // REPEATER MAPPERS
    // ============================================

    /**
     * Map itinerary repeater
     */
    private function map_itinerary(array $itineraries): array
    {
        $mapped = [];

        foreach ($itineraries as $day) {
            $mapped[] = [
                'title' => $this->sanitize_text($day['subtitle'] ?? ''),
                'content' => $this->sanitize_html($day['content'] ?? ''),
                'order' => (int) ($day['order'] ?? 0),
                'active' => !empty($day['active']),
                'limit' => (int) ($day['limit'] ?? 0),
                'gallery' => $this->map_itinerary_images($day['images'] ?? []),
                'items' => $this->map_itinerary_items($day['items'] ?? []),
            ];
        }

        return $mapped;
    }

    /**
     * Map itinerary images (returns image data for later processing)
     */
    private function map_itinerary_images(array $images): array
    {
        // Return image data - will be processed by ImageImportService
        return $images;
    }

    /**
     * Map itinerary items (services)
     */
    private function map_itinerary_items(array $items): array
    {
        $mapped = [];

        foreach ($items as $item) {
            $mapped[] = [
                'text' => $this->sanitize_text($item['text'] ?? ''),
                'order' => (int) ($item['order'] ?? 0),
                'type_service' => $this->map_type_service($item['typeService'] ?? []),
                'hotel' => $this->map_hotel($item['hotel'] ?? []),
            ];
        }

        return $mapped;
    }

    /**
     * Map type service to taxonomy term ID
     */
    private function map_type_service(array $type_service): ?int
    {
        if (empty($type_service['title'])) {
            return null;
        }

        return $this->get_or_create_term($type_service['title'], 'type_service');
    }

    /**
     * Map hotel to taxonomy term ID
     */
    private function map_hotel(array $hotel): ?int
    {
        if (empty($hotel['title'])) {
            return null;
        }

        return $this->get_or_create_term($hotel['title'], 'hotel');
    }

    /**
     * Map gallery images (returns image data for later processing)
     */
    private function map_gallery(array $images): array
    {
        // Return image data - will be processed by ImageImportService
        return $images;
    }

    /**
     * Map thumbnail from images array (featured image)
     * Uses the first image from the gallery as featured image
     * The API's thumbnail field returns relative paths that aren't accessible
     *
     * @param array $images Images array from API
     * @return string|null
     */
    private function map_thumbnail_from_images(array $images): ?string
    {
        if (empty($images) || !isset($images[0])) {
            return null;
        }

        $first_image = $images[0];
        $url = $first_image['originalImage'] ?? $first_image['image'] ?? '';

        if (empty($url)) {
            return null;
        }

        // URLs from S3 are already absolute
        return $this->sanitize_url($url);
    }

    /**
     * Map thumbnail (featured image) - DEPRECATED
     * This method is kept for reference but no longer used
     * The API's thumbnail field returns relative paths that aren't accessible
     *
     * @param array|string|null $thumbnail Thumbnail data (can be array or string URL)
     * @return string|null
     */
    private function map_thumbnail($thumbnail): ?string
    {
        if (empty($thumbnail)) {
            return null;
        }

        // If thumbnail is already a string URL
        if (is_string($thumbnail)) {
            // Convert relative URLs to absolute
            $url = $this->normalize_image_url($thumbnail);
            return $this->sanitize_url($url);
        }

        // If thumbnail is an array - extract originalImage
        if (is_array($thumbnail)) {
            $url = $thumbnail['originalImage'] ?? $thumbnail['image'] ?? '';

            if (empty($url)) {
                return null;
            }

            // URLs from API objects are usually absolute, but normalize anyway
            $url = $this->normalize_image_url($url);
            return $this->sanitize_url($url);
        }

        return null;
    }

    /**
     * Normalize image URL - convert relative URLs to absolute
     *
     * @param string $url Image URL (can be relative or absolute)
     * @return string Absolute URL
     */
    private function normalize_image_url(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        // Already absolute URL
        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }

        // Relative URL starting with / - prepend API base URL
        if (strpos($url, '/') === 0) {
            $base_url = $this->get_api_base_url();
            return rtrim($base_url, '/') . $url;
        }

        // Other cases - return as is
        return $url;
    }

    /**
     * Get API base URL
     *
     * @return string API base URL
     */
    private function get_api_base_url(): string
    {
        // Try to get from ACF Global Options
        $api_url = function_exists('get_field') ? get_field('package_api_base_url', 'option') : '';

        // Validate and sanitize URL
        if (!empty($api_url) && filter_var($api_url, FILTER_VALIDATE_URL)) {
            return rtrim($api_url, '/');
        }

        // Fallback to default
        return 'https://cms.valenciatravelcusco.com';
    }

    /**
     * Map price tiers
     */
    private function map_price_tiers(array $values): array
    {
        $mapped = [];

        foreach ($values as $tier) {
            $mapped[] = [
                'min_passengers' => (int) ($tier['minPassengers'] ?? 1),
                'price' => $this->parse_price($tier['normal'] ?? 0),
                'offer' => $this->parse_price($tier['offer'] ?? 0),
            ];
        }

        return $mapped;
    }

    /**
     * Map highlights (extract from description or API data)
     */
    private function map_highlights(array $api_data): array
    {
        // API doesn't have explicit highlights field
        // Return empty for now, can be added manually
        return [];
    }

    /**
     * Map additional sections (FAQ, tips, etc.)
     */
    private function map_additional_sections(array $additional_info): array
    {
        $mapped = [];
        $order = 1;

        foreach ($additional_info as $section) {
            $items = [];

            foreach ($section['items'] ?? [] as $item) {
                $items[] = [
                    'label' => $this->sanitize_text($item['title'] ?? ''),
                    'content' => $this->sanitize_html($item['content'] ?? ''),
                ];
            }

            $mapped[] = [
                'title' => $this->sanitize_text($section['title'] ?? ''),
                'type' => $this->determine_section_type($section),
                'items' => $items,
                'order' => $order++,
                'active' => true,
                'style' => 'accordion',
            ];
        }

        return $mapped;
    }

    /**
     * Determine section type from additional info
     */
    private function determine_section_type(array $section): string
    {
        $title = strtolower($section['title'] ?? '');

        if (strpos($title, 'faq') !== false || strpos($title, 'frequently') !== false) {
            return 'faq';
        } elseif (strpos($title, 'tip') !== false || strpos($title, 'advice') !== false) {
            return 'tips';
        } elseif (strpos($title, 'equipment') !== false || strpos($title, 'gear') !== false) {
            return 'equipment';
        } elseif (strpos($title, 'requirement') !== false) {
            return 'requirements';
        } elseif (strpos($title, 'insurance') !== false) {
            return 'insurance';
        } elseif (strpos($title, 'cancellation') !== false) {
            return 'cancellation';
        }

        return 'custom';
    }

    // ============================================
    // NORMALIZATION & COMPARISON
    // ============================================

    /**
     * Normalize string for comparison (prevent duplicates)
     *
     * Converts to lowercase, removes accents, trims, and normalizes special characters
     */
    private function normalize_string(string $text): string
    {
        // 1. Decode HTML entities (&amp; → &, &quot; → ", etc.)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 2. Convert to lowercase
        $text = mb_strtolower($text, 'UTF-8');

        // 3. Remove accents/diacritics
        $text = $this->remove_accents($text);

        // 4. Normalize whitespace (múltiples espacios → un espacio)
        $text = preg_replace('/\s+/', ' ', $text);

        // 5. Trim
        $text = trim($text);

        // 6. Remove special characters (keep letters, numbers, spaces, basic punctuation)
        $text = preg_replace('/[^\p{L}\p{N}\s\-_&]/u', '', $text);

        return $text;
    }

    /**
     * Remove accents from string
     */
    private function remove_accents(string $text): string
    {
        // Use WordPress function if available
        if (function_exists('remove_accents')) {
            return remove_accents($text);
        }

        // Fallback: manual accent removal
        $accents = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
            'â' => 'a', 'ê' => 'e', 'î' => 'i', 'ô' => 'o', 'û' => 'u',
            'ã' => 'a', 'õ' => 'o', 'ñ' => 'n', 'ç' => 'c',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'À' => 'A', 'È' => 'E', 'Ì' => 'I', 'Ò' => 'O', 'Ù' => 'U',
            'Ä' => 'A', 'Ë' => 'E', 'Ï' => 'I', 'Ö' => 'O', 'Ü' => 'U',
            'Â' => 'A', 'Ê' => 'E', 'Î' => 'I', 'Ô' => 'O', 'Û' => 'U',
            'Ã' => 'A', 'Õ' => 'O', 'Ñ' => 'N', 'Ç' => 'C',
        ];

        return strtr($text, $accents);
    }

    // ============================================
    // DATE PARSING
    // ============================================

    /**
     * Parse ISO 8601 date to WordPress format
     *
     * @param string $date_string ISO 8601 date string
     * @return string|null WordPress date format (Y-m-d H:i:s) or null
     */
    private function parse_date(string $date_string): ?string
    {
        try {
            // Try to parse ISO 8601 date
            $date = new \DateTime($date_string);

            // Return in WordPress format
            return $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            $this->log_error("Failed to parse date: {$date_string}");
            return null;
        }
    }

    // ============================================
    // LOGGING
    // ============================================

    /**
     * Log error message
     */
    private function log_error(string $message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ApiDataMapper: ' . $message);
        }
    }

    /**
     * Log debug message
     */
    private function log_debug(string $message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ApiDataMapper: ' . $message);
        }
    }
}
