<?php
/**
 * Mock Data Generator for Package CPT
 * Creates realistic sample packages for testing
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aurora_Mock_Data_Generator
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Sample packages data - Load from external file
     */
    private function get_sample_packages()
    {
        $mock_data_file = plugin_dir_path(__FILE__) . 'mock-packages-data.php';

        if (file_exists($mock_data_file)) {
            return require $mock_data_file;
        }

        // Fallback to empty array if file not found
        error_log('Aurora Package Builder: mock-packages-data.php not found');
        return [];
    }

    /**
     * Sample blog posts data - Load from external file
     */
    private function get_sample_blog_posts()
    {
        $mock_data_file = plugin_dir_path(__FILE__) . 'mock-blog-posts-data.php';

        if (file_exists($mock_data_file)) {
            return require $mock_data_file;
        }

        // Fallback to empty array if file not found
        error_log('Aurora Package Builder: mock-blog-posts-data.php not found');
        return [];
    }

    /**
     * Sample locations data - Load from external file
     */
    private function get_sample_locations()
    {
        $mock_data_file = plugin_dir_path(__FILE__) . 'mock-locations-data.php';

        if (file_exists($mock_data_file)) {
            return require $mock_data_file;
        }

        // Fallback to empty array if file not found
        error_log('Aurora Package Builder: mock-locations-data.php not found');
        return [];
    }

    /**
     * Sample collaborators data - Load from external file
     */
    private function get_sample_collaborators()
    {
        $mock_data_file = plugin_dir_path(__FILE__) . 'mock-collaborators-data.php';

        if (file_exists($mock_data_file)) {
            return require $mock_data_file;
        }

        // Fallback to empty array if file not found
        error_log('Aurora Package Builder: mock-collaborators-data.php not found');
        return [];
    }

    /**
     * Sample guides data - Load from external file
     */
    private function get_sample_guides()
    {
        $mock_data_file = plugin_dir_path(__FILE__) . 'mock-guides-data.php';

        if (file_exists($mock_data_file)) {
            return require $mock_data_file;
        }

        // Fallback to empty array if file not found
        error_log('Aurora Package Builder: mock-guides-data.php not found');
        return [];
    }

    /**
     * Sample reviews data - Load from external file
     */
    private function get_sample_reviews()
    {
        $mock_data_file = plugin_dir_path(__FILE__) . 'mock-reviews-data.php';

        if (file_exists($mock_data_file)) {
            return require $mock_data_file;
        }

        // Fallback to empty array if file not found
        error_log('Aurora Package Builder: mock-reviews-data.php not found');
        return [];
    }

    /**
     * Sample deals data - Load from external file
     */
    private function get_sample_deals()
    {
        $mock_data_file = plugin_dir_path(__FILE__) . 'mock-deals-data.php';

        if (file_exists($mock_data_file)) {
            return require $mock_data_file;
        }

        // Fallback to empty array if file not found
        error_log('Aurora Package Builder: mock-deals-data.php not found');
        return [];
    }

    /**
     * ========================================
     * TAXONOMY GENERATION METHODS - PHASE 1
     * ========================================
     */

    /**
     * Generate all taxonomies (wrapper method)
     * Phase 1: Complete taxonomy generation
     */
    public function generate_all_taxonomies()
    {
        $results = [
            'success' => true,
            'created' => 0,
            'errors' => [],
        ];

        try {
            // Generate location taxonomies
            $location_result = $this->generate_location_taxonomies();
            $results['created'] += $location_result['created'];
            if (!empty($location_result['errors'])) {
                $results['errors'] = array_merge($results['errors'], $location_result['errors']);
            }

            // Generate package taxonomies
            $package_result = $this->generate_package_taxonomies();
            $results['created'] += $package_result['created'];
            if (!empty($package_result['errors'])) {
                $results['errors'] = array_merge($results['errors'], $package_result['errors']);
            }

            // Generate collaborator taxonomies
            $collaborator_result = $this->generate_collaborator_taxonomies();
            $results['created'] += $collaborator_result['created'];
            if (!empty($collaborator_result['errors'])) {
                $results['errors'] = array_merge($results['errors'], $collaborator_result['errors']);
            }

        } catch (Exception $e) {
            $results['success'] = false;
            $results['errors'][] = 'Fatal error: ' . $e->getMessage();
        }

        return $results;
    }

    /**
     * Generate Location taxonomies
     * Creates: countries, destinations, locations, flights
     */
    public function generate_location_taxonomies()
    {
        $created = 0;
        $errors = [];

        // 1. Countries taxonomy (hierarchical)
        $countries_data = [
            'Peru' => [
                'Cusco Region',
                'Lima Region',
                'Arequipa Region',
                'Puno Region',
                'Sacred Valley',
            ],
            'Bolivia' => [
                'La Paz Department',
                'Potosí Department',
            ],
            'Chile' => [
                'Atacama Region',
                'Santiago Metropolitan',
            ],
            'Ecuador' => [
                'Galápagos Province',
                'Pichincha Province',
            ],
            'Colombia' => [
                'Bogotá',
                'Cartagena',
            ],
        ];

        foreach ($countries_data as $parent => $children) {
            $parent_term = wp_insert_term($parent, 'countries');
            if (!is_wp_error($parent_term)) {
                $created++;
                $parent_id = $parent_term['term_id'];

                foreach ($children as $child) {
                    $child_term = wp_insert_term($child, 'countries', ['parent' => $parent_id]);
                    if (!is_wp_error($child_term)) {
                        $created++;
                    } else {
                        $errors[] = "Countries - {$child}: " . $child_term->get_error_message();
                    }
                }
            } else {
                $errors[] = "Countries - {$parent}: " . $parent_term->get_error_message();
            }
        }

        // 2. Destinations taxonomy (non-hierarchical)
        $destinations = [
            'Machu Picchu',
            'Sacred Valley',
            'Cusco City',
            'Rainbow Mountain',
            'Lake Titicaca',
            'Colca Canyon',
            'Amazon Rainforest',
            'Nazca Lines',
            'Paracas',
            'Huacachina',
            'Arequipa',
            'Uyuni Salt Flats',
            'Galápagos Islands',
            'Cartagena',
        ];

        foreach ($destinations as $destination) {
            $term = wp_insert_term($destination, 'destinations');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Destinations - {$destination}: " . $term->get_error_message();
            }
        }

        // 3. Locations taxonomy (non-hierarchical)
        $locations_terms = [
            'Cusco',
            'Lima',
            'Arequipa',
            'Puno',
            'Ollantaytambo',
            'Aguas Calientes',
            'La Paz',
            'Uyuni',
            'Quito',
            'Galápagos',
        ];

        foreach ($locations_terms as $location) {
            $term = wp_insert_term($location, 'locations');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Locations - {$location}: " . $term->get_error_message();
            }
        }

        // 4. Flights taxonomy (non-hierarchical)
        $flights = [
            'Cusco - Lima',
            'Lima - Cusco',
            'Lima - Arequipa',
            'Arequipa - Lima',
            'Lima - Iquitos',
            'Iquitos - Lima',
            'La Paz - Uyuni',
            'Quito - Galápagos',
        ];

        foreach ($flights as $flight) {
            $term = wp_insert_term($flight, 'flights');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Flights - {$flight}: " . $term->get_error_message();
            }
        }

        return [
            'created' => $created,
            'errors' => $errors,
        ];
    }

    /**
     * Generate Package taxonomies
     * Creates: package_type, interest, optional_renting, included_services,
     * additional_info, landing_packages, activity, type_service, hotel, specialists, spot_calendar
     */
    public function generate_package_taxonomies()
    {
        $created = 0;
        $errors = [];

        // 1. Package Type (hierarchical)
        $package_types = [
            'Adventure Tours' => [
                'Trekking',
                'Mountain Biking',
                'Rafting',
            ],
            'Cultural Tours' => [
                'Archaeological Sites',
                'Colonial Cities',
                'Museums',
            ],
            'Luxury Tours' => [
                'Premium Hotels',
                'Private Guides',
            ],
            'Family Tours' => [],
            'Honeymoon Tours' => [],
        ];

        foreach ($package_types as $parent => $children) {
            $parent_term = wp_insert_term($parent, 'package_type');
            if (!is_wp_error($parent_term)) {
                $created++;
                if (!empty($children)) {
                    $parent_id = $parent_term['term_id'];
                    foreach ($children as $child) {
                        $child_term = wp_insert_term($child, 'package_type', ['parent' => $parent_id]);
                        if (!is_wp_error($child_term)) {
                            $created++;
                        } else {
                            $errors[] = "Package Type - {$child}: " . $child_term->get_error_message();
                        }
                    }
                }
            } else {
                $errors[] = "Package Type - {$parent}: " . $parent_term->get_error_message();
            }
        }

        // 2. Interest (non-hierarchical)
        $interests = [
            'Archaeology',
            'Nature & Wildlife',
            'Photography',
            'Gastronomy',
            'Adventure',
            'Culture & History',
            'Spirituality',
            'Wellness',
            'Birdwatching',
            'Hiking',
        ];

        foreach ($interests as $interest) {
            $term = wp_insert_term($interest, 'interest');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Interest - {$interest}: " . $term->get_error_message();
            }
        }

        // 3. Optional Renting (non-hierarchical)
        $rentings = [
            'Sleeping Bag',
            'Trekking Poles',
            'Duffle Bag',
            'Rain Poncho',
            'Camping Equipment',
        ];

        foreach ($rentings as $renting) {
            $term = wp_insert_term($renting, 'optional_renting');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Optional Renting - {$renting}: " . $term->get_error_message();
            }
        }

        // 4. Included Services (non-hierarchical)
        $services = [
            'Professional Guide',
            'Hotel Accommodation',
            'Meals Included',
            'Transportation',
            'Entrance Tickets',
            '24/7 Support',
            'Travel Insurance',
            'Airport Transfers',
        ];

        foreach ($services as $service) {
            $term = wp_insert_term($service, 'included_services');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Included Services - {$service}: " . $term->get_error_message();
            }
        }

        // 5. Additional Info (non-hierarchical)
        $additional_info = [
            'Solo Traveler Friendly',
            'Group Discount Available',
            'Private Tour Available',
            'Flexible Itinerary',
            'COVID-19 Safety Measures',
        ];

        foreach ($additional_info as $info) {
            $term = wp_insert_term($info, 'additional_info');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Additional Info - {$info}: " . $term->get_error_message();
            }
        }

        // 6. Landing Packages (non-hierarchical)
        $landing_packages = [
            'Featured Package',
            'Best Seller',
            'New Arrival',
            'Limited Offer',
        ];

        foreach ($landing_packages as $landing) {
            $term = wp_insert_term($landing, 'landing_packages');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Landing Packages - {$landing}: " . $term->get_error_message();
            }
        }

        // 7. Activity (non-hierarchical)
        $activities = [
            'Hiking',
            'Camping',
            'Kayaking',
            'Rock Climbing',
            'Wildlife Watching',
            'Cultural Immersion',
            'Cooking Class',
            'Yoga & Meditation',
        ];

        foreach ($activities as $activity) {
            $term = wp_insert_term($activity, 'activity');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Activity - {$activity}: " . $term->get_error_message();
            }
        }

        // 8. Type Service (non-hierarchical)
        $type_services = [
            'Group Tour',
            'Private Tour',
            'Semi-Private Tour',
            'Self-Guided',
        ];

        foreach ($type_services as $type_service) {
            $term = wp_insert_term($type_service, 'type_service');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Type Service - {$type_service}: " . $term->get_error_message();
            }
        }

        // 9. Hotel (non-hierarchical)
        $hotels = [
            '3-Star Hotel',
            '4-Star Hotel',
            '5-Star Hotel',
            'Boutique Hotel',
            'Eco-Lodge',
            'Camping',
        ];

        foreach ($hotels as $hotel) {
            $term = wp_insert_term($hotel, 'hotel');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Hotel - {$hotel}: " . $term->get_error_message();
            }
        }

        // 10. Specialists (non-hierarchical)
        $specialists = [
            'Archaeology Expert',
            'Nature Guide',
            'Mountain Guide',
            'Cultural Specialist',
            'Photography Guide',
            'Gastronomy Expert',
        ];

        foreach ($specialists as $specialist) {
            $term = wp_insert_term($specialist, 'specialists');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Specialists - {$specialist}: " . $term->get_error_message();
            }
        }

        // 11. Spot Calendar (non-hierarchical)
        $spot_calendar = [
            'Available Year-Round',
            'Seasonal (Apr-Oct)',
            'Seasonal (Nov-Mar)',
            'Limited Availability',
        ];

        foreach ($spot_calendar as $spot) {
            $term = wp_insert_term($spot, 'spot_calendar');
            if (!is_wp_error($term)) {
                $created++;
            } else {
                $errors[] = "Spot Calendar - {$spot}: " . $term->get_error_message();
            }
        }

        return [
            'created' => $created,
            'errors' => $errors,
        ];
    }

    /**
     * Generate Collaborator taxonomies
     * Creates: roles
     */
    public function generate_collaborator_taxonomies()
    {
        $created = 0;
        $errors = [];

        // Roles taxonomy (hierarchical)
        $roles_data = [
            'Management' => [
                'CEO',
                'Director',
                'Operations Manager',
            ],
            'Guides' => [
                'Senior Guide',
                'Tour Guide',
                'Local Guide',
            ],
            'Support Staff' => [
                'Customer Service',
                'Logistics Coordinator',
                'Marketing Specialist',
            ],
            'Field Staff' => [
                'Driver',
                'Chef',
                'Porter',
            ],
        ];

        foreach ($roles_data as $parent => $children) {
            $parent_term = wp_insert_term($parent, 'roles');
            if (!is_wp_error($parent_term)) {
                $created++;
                $parent_id = $parent_term['term_id'];

                foreach ($children as $child) {
                    $child_term = wp_insert_term($child, 'roles', ['parent' => $parent_id]);
                    if (!is_wp_error($child_term)) {
                        $created++;
                    } else {
                        $errors[] = "Roles - {$child}: " . $child_term->get_error_message();
                    }
                }
            } else {
                $errors[] = "Roles - {$parent}: " . $parent_term->get_error_message();
            }
        }

        return [
            'created' => $created,
            'errors' => $errors,
        ];
    }

    /**
     * ========================================
     * LOCATION CPT GENERATION - PHASE 2
     * ========================================
     */

    /**
     * Generate locations (without images)
     * Phase 2: Create 30 locations
     */
    public function generate_locations()
    {
        $locations = $this->get_sample_locations();
        $created = 0;
        $errors = [];

        foreach ($locations as $location) {
            try {
                $post_id = $this->create_location($location);
                if ($post_id) {
                    $created++;
                }
            } catch (Exception $e) {
                $errors[] = $location['title'] . ': ' . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'created' => $created,
            'total' => count($locations),
            'errors' => $errors,
        ];
    }

    /**
     * Create a single location post with ACF fields and taxonomies
     */
    private function create_location($data)
    {
        // Create the post
        $post_id = wp_insert_post([
            'post_title' => $data['title'],
            'post_content' => $data['content'],
            'post_status' => 'publish',
            'post_type' => 'location',
        ]);

        if (is_wp_error($post_id)) {
            throw new Exception($post_id->get_error_message());
        }

        // Set ACF fields
        if (isset($data['subtitle'])) {
            update_field('subtitle', $data['subtitle'], $post_id);
        }

        if (isset($data['active'])) {
            update_field('active', $data['active'], $post_id);
        }

        if (isset($data['seo_title'])) {
            update_field('seo_title', $data['seo_title'], $post_id);
        }

        if (isset($data['seo_description'])) {
            update_field('seo_description', $data['seo_description'], $post_id);
        }

        if (isset($data['seo_keywords'])) {
            update_field('seo_keywords', $data['seo_keywords'], $post_id);
        }

        // Assign taxonomies
        if (isset($data['countries']) && !empty($data['countries'])) {
            $this->assign_taxonomy_terms($post_id, 'countries', $data['countries']);
        }

        if (isset($data['destinations']) && !empty($data['destinations'])) {
            $this->assign_taxonomy_terms($post_id, 'destinations', $data['destinations']);
        }

        if (isset($data['locations']) && !empty($data['locations'])) {
            $this->assign_taxonomy_terms($post_id, 'locations', $data['locations']);
        }

        if (isset($data['flights']) && !empty($data['flights'])) {
            $this->assign_taxonomy_terms($post_id, 'flights', $data['flights']);
        }

        return $post_id;
    }

    /**
     * Helper method to assign taxonomy terms by name
     */
    private function assign_taxonomy_terms($post_id, $taxonomy, $term_names)
    {
        $term_ids = [];

        foreach ($term_names as $term_name) {
            $term = get_term_by('name', $term_name, $taxonomy);
            if ($term) {
                $term_ids[] = $term->term_id;
            }
        }

        if (!empty($term_ids)) {
            wp_set_object_terms($post_id, $term_ids, $taxonomy);
        }
    }

    /**
     * ========================================
     * COLLABORATOR CPT GENERATION - PHASE 3
     * ========================================
     */

    /**
     * Generate collaborators (without images)
     * Phase 3: Create 20 collaborators
     */
    public function generate_collaborators()
    {
        $collaborators = $this->get_sample_collaborators();
        $created = 0;
        $errors = [];

        foreach ($collaborators as $collaborator) {
            try {
                $post_id = $this->create_collaborator($collaborator);
                if ($post_id) {
                    $created++;
                }
            } catch (Exception $e) {
                $errors[] = $collaborator['first_name'] . ' ' . $collaborator['last_name'] . ': ' . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'created' => $created,
            'total' => count($collaborators),
            'errors' => $errors,
        ];
    }

    /**
     * Create a single collaborator post with ACF fields and taxonomies
     */
    private function create_collaborator($data)
    {
        // Create the post - first name is the title
        $post_id = wp_insert_post([
            'post_title' => $data['first_name'],
            'post_status' => 'publish',
            'post_type' => 'collaborator',
        ]);

        if (is_wp_error($post_id)) {
            throw new Exception($post_id->get_error_message());
        }

        // Set ACF fields
        if (isset($data['last_name'])) {
            update_field('last_name', $data['last_name'], $post_id);
        }

        if (isset($data['job'])) {
            update_field('job', $data['job'], $post_id);
        }

        if (isset($data['description'])) {
            update_field('description', $data['description'], $post_id);
        }

        if (isset($data['hobbies'])) {
            update_field('hobbies', $data['hobbies'], $post_id);
        }

        // Assign roles taxonomy
        if (isset($data['roles']) && !empty($data['roles'])) {
            $this->assign_taxonomy_terms($post_id, 'roles', $data['roles']);
        }

        return $post_id;
    }

    /**
     * ========================================
     * GUIDE CPT GENERATION - PHASE 4
     * ========================================
     */

    /**
     * Generate guides (without images)
     * Phase 4: Create 15 guides
     */
    public function generate_guides()
    {
        $guides = $this->get_sample_guides();
        $created = 0;
        $errors = [];

        foreach ($guides as $guide) {
            try {
                $post_id = $this->create_guide($guide);
                if ($post_id) {
                    $created++;
                }
            } catch (Exception $e) {
                $errors[] = $guide['title'] . ': ' . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'created' => $created,
            'total' => count($guides),
            'errors' => $errors,
        ];
    }

    /**
     * Create a single guide post
     * Guides use only native WordPress fields (no ACF)
     */
    private function create_guide($data)
    {
        // Create the post
        $post_id = wp_insert_post([
            'post_title' => $data['title'],
            'post_content' => $data['content'],
            'post_excerpt' => $data['excerpt'],
            'post_status' => 'publish',
            'post_type' => 'guide',
        ]);

        if (is_wp_error($post_id)) {
            throw new Exception($post_id->get_error_message());
        }

        return $post_id;
    }

    /**
     * ========================================
     * REVIEW CPT GENERATION - PHASE 5
     * ========================================
     */

    /**
     * Generate reviews (without images)
     * Phase 5: Create 30 reviews
     */
    public function generate_reviews()
    {
        $reviews = $this->get_sample_reviews();
        $created = 0;
        $errors = [];

        foreach ($reviews as $review) {
            try {
                $post_id = $this->create_review($review);
                if ($post_id) {
                    $created++;
                }
            } catch (Exception $e) {
                $errors[] = $review['title'] . ': ' . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'created' => $created,
            'total' => count($reviews),
            'errors' => $errors,
        ];
    }

    /**
     * Create a single review post
     * Reviews use only native WordPress fields (no ACF)
     */
    private function create_review($data)
    {
        // Create the post
        $post_id = wp_insert_post([
            'post_title' => $data['title'],
            'post_content' => $data['content'],
            'post_excerpt' => $data['excerpt'],
            'post_status' => 'publish',
            'post_type' => 'review',
        ]);

        if (is_wp_error($post_id)) {
            throw new Exception($post_id->get_error_message());
        }

        // Add country as post meta (optional)
        if (isset($data['country'])) {
            update_post_meta($post_id, 'country', $data['country']);
        }

        return $post_id;
    }

    /**
     * ========================================
     * DEAL CPT GENERATION - PHASE 6
     * ========================================
     */

    /**
     * Generate deals (without images)
     * Phase 6: Create 10 deals
     */
    public function generate_deals()
    {
        $deals = $this->get_sample_deals();
        $created = 0;
        $errors = [];

        foreach ($deals as $deal) {
            try {
                $post_id = $this->create_deal($deal);
                if ($post_id) {
                    $created++;
                }
            } catch (Exception $e) {
                $errors[] = $deal['title'] . ': ' . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'created' => $created,
            'total' => count($deals),
            'errors' => $errors,
        ];
    }

    /**
     * Create a single deal post with ACF fields
     */
    private function create_deal($data)
    {
        // Create the post
        $post_id = wp_insert_post([
            'post_title' => $data['title'],
            'post_status' => 'publish',
            'post_type' => 'deal',
        ]);

        if (is_wp_error($post_id)) {
            throw new Exception($post_id->get_error_message());
        }

        // Set ACF fields
        if (isset($data['active'])) {
            update_field('active', $data['active'], $post_id);
        }

        if (isset($data['start_date'])) {
            update_field('start_date', $data['start_date'], $post_id);
        }

        if (isset($data['end_date'])) {
            update_field('end_date', $data['end_date'], $post_id);
        }

        if (isset($data['discount_percentage'])) {
            update_field('discount_percentage', $data['discount_percentage'], $post_id);
        }

        if (isset($data['description'])) {
            update_field('description', $data['description'], $post_id);
        }

        if (isset($data['terms'])) {
            update_field('terms', $data['terms'], $post_id);
        }

        // Note: banner image and packages relationship will be added in image phase

        return $post_id;
    }

    /**
     * ========================================
     * PACKAGE CPT GENERATION - PHASE 7
     * ========================================
     */

    /**
     * Generate mock packages
     */
    public function generate_packages($with_images = false)
    {
        // Delete ALL existing packages first (not just mock ones)
        $this->delete_all_existing_packages();

        $packages = $this->get_sample_packages();
        $created = 0;
        $errors = [];

        foreach ($packages as $package) {
            try {
                $post_id = $this->create_package($package, $with_images);
                if ($post_id) {
                    $created++;
                }
            } catch (Exception $e) {
                $errors[] = $package['title'] . ': ' . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'created' => $created,
            'total' => count($packages),
            'errors' => $errors,
        ];
    }

    /**
     * Delete ALL existing packages before creating new ones
     * Uses direct SQL for speed to avoid timeout
     */
    private function delete_all_existing_packages()
    {
        global $wpdb;

        // Get all package IDs
        $package_ids = $wpdb->get_col("
            SELECT ID FROM {$wpdb->posts}
            WHERE post_type = 'package'
        ");

        if (empty($package_ids)) {
            return 0;
        }

        $ids_string = implode(',', array_map('intval', $package_ids));

        // Delete all post meta
        $wpdb->query("
            DELETE FROM {$wpdb->postmeta}
            WHERE post_id IN ($ids_string)
        ");

        // Delete all term relationships
        $wpdb->query("
            DELETE FROM {$wpdb->term_relationships}
            WHERE object_id IN ($ids_string)
        ");

        // Delete all posts
        $wpdb->query("
            DELETE FROM {$wpdb->posts}
            WHERE ID IN ($ids_string)
        ");

        // Clear cache
        wp_cache_flush();

        return count($package_ids);
    }

    /**
     * Clean mock packages (only packages with exact mock titles)
     */
    private function clean_mock_packages()
    {
        // Get all titles from the mock data file
        $packages = $this->get_sample_packages();
        $mock_titles = [];

        foreach ($packages as $package) {
            if (isset($package['title'])) {
                $mock_titles[] = $package['title'];
            }
        }

        // Delete each mock package by title
        foreach ($mock_titles as $title) {
            // Use WP_Query instead of deprecated get_page_by_title
            $query = new WP_Query([
                'post_type' => 'package',
                'title' => $title,
                'posts_per_page' => 1,
                'post_status' => 'any',
                'fields' => 'ids',
            ]);

            if ($query->have_posts()) {
                wp_delete_post($query->posts[0], true);
            }
        }
    }

    /**
     * Create a single package
     */
    private function create_package($package, $with_images = false)
    {
        // Create post
        $post_id = wp_insert_post([
            'post_title' => $package['title'],
            'post_type' => 'package',
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
        ]);

        if (is_wp_error($post_id)) {
            throw new Exception($post_id->get_error_message());
        }

        // Add package data - using BOTH ACF and native post meta
        if (!empty($package['data'])) {
            // Native WordPress blocks (FASE 2-6) - use update_post_meta
            $native_blocks_fields = [
                // FASE 2
                'best_months',
                'inclusions',
                'reviews',
                'price_offer',
                'price_normal',

                // FASE 3
                'overview',
                'quick_facts',
                'highlights',
                'itinerary',
                'brochure_url',
                'guide_profiles',

                // FASE 4
                'departures',
                'inclusions_full',
                'exclusions',
                'currency_options',
                'show_price_calculator',

                // FASE 5
                'contact_email',
                'whatsapp_number',
                'contact_cta_text',
                'faqs',
                'review_platforms',
                'show_review_filter',

                // FASE 6
                'related_posts_manual',
                'impact_title',
                'impact_items',
                'impact_background',
                'trust_badges',
                'certifications_text',
            ];

            // ACF fields (original fields that use ACF)
            $acf_fields = [
                'duration',
                'price_from',
                'max_group_size',
                'tripadvisor_rating',
                'total_reviews',
                'tripadvisor_url',
                'physical_difficulty',
                'departure',
                'service_type',
                'promo_tag',
                'promo_color',
                'active_promotion',
                'rating',
                // FASE 7B: Pricing fields
                'price_normal',
                'price_offer',
                'price_per_person',
                'price_single_supplement',
                'price_child',
                'price_tiers',
                // FASE 7B: BaseInfo repeaters
                'highlights',
                'custom_guarantees',
            ];

            foreach ($package['data'] as $field_name => $value) {
                // Use update_post_meta for native WordPress blocks
                if (in_array($field_name, $native_blocks_fields)) {
                    update_post_meta($post_id, $field_name, $value);
                }
                // Use update_field for ACF fields (if ACF is available)
                elseif (function_exists('update_field') && in_array($field_name, $acf_fields)) {
                    update_field($field_name, $value, $post_id);
                }
            }

            // FASE 7B: Generate missing pricing fields dynamically
            if (function_exists('update_field')) {
                $base_price = $data['price_normal'] ?? $data['price_from'] ?? 200;
                $offer_price = $data['price_offer'] ?? $base_price;

                // Add price_single_supplement if not exists (40% of base price)
                if (!isset($data['price_single_supplement'])) {
                    update_field('price_single_supplement', round($base_price * 0.4), $post_id);
                }

                // Add price_child if not exists (70% of base price)
                if (!isset($data['price_child'])) {
                    update_field('price_child', round($base_price * 0.7), $post_id);
                }

                // Add price_tiers repeater if not exists
                if (!isset($data['price_tiers'])) {
                    $price_tiers = [
                        ['min_passengers' => 2, 'price' => round($base_price * 0.95), 'offer' => round($offer_price * 0.95)],
                        ['min_passengers' => 4, 'price' => round($base_price * 0.85), 'offer' => round($offer_price * 0.85)],
                        ['min_passengers' => 6, 'price' => round($base_price * 0.75), 'offer' => round($offer_price * 0.75)],
                        ['min_passengers' => 10, 'price' => round($base_price * 0.65), 'offer' => round($offer_price * 0.65)],
                    ];
                    update_field('price_tiers', $price_tiers, $post_id);
                }

                // Convert simple highlights array to ACF repeater format
                if (isset($data['highlights']) && is_array($data['highlights']) && !empty($data['highlights'])) {
                    // Check if it's already in repeater format (has 'icon' key)
                    $first_highlight = reset($data['highlights']);
                    if (is_string($first_highlight)) {
                        // Convert from simple array to repeater format
                        $icons = ['check', 'star', 'compass', 'camera', 'heart', 'sun', 'map-pin', 'clock', 'backpack', 'users'];
                        $highlights_repeater = [];

                        foreach ($data['highlights'] as $index => $text) {
                            if (is_string($text) && !empty($text)) {
                                $highlights_repeater[] = [
                                    'icon' => $icons[$index % count($icons)],
                                    'text' => $text
                                ];
                            }
                        }

                        if (!empty($highlights_repeater)) {
                            update_field('highlights', $highlights_repeater, $post_id);
                        }
                    }
                }

                // Add custom_guarantees if not exists
                if (!isset($data['custom_guarantees'])) {
                    $guarantees = [
                        ['icon' => 'shield', 'text' => 'Best price guarantee'],
                        ['icon' => 'check', 'text' => 'Free cancellation up to 24h before'],
                    ];
                    update_field('custom_guarantees', $guarantees, $post_id);
                }

                // FASE 7C: Generate itinerary if not exists
                if (!isset($data['itinerary']) && isset($data['days'])) {
                    $num_days = intval($data['days']);
                    $package_title = $package['title'];

                    // Only generate itinerary if package has days (skip if 0 or not set)
                    if ($num_days > 0 && $num_days <= 38) {
                        $itinerary = $this->generate_itinerary_for_package($num_days, $package_title, $data);
                        if (!empty($itinerary)) {
                            update_field('itinerary', $itinerary, $post_id);
                        }
                    }
                }

                // FASE 7D: Generate availability fields if not exist
                // Generate fixed_departures (future dates)
                if (!isset($data['fixed_departures'])) {
                    $fixed_departures = $this->generate_fixed_departures($package_title, $data);
                    if (!empty($fixed_departures)) {
                        update_field('fixed_departures', $fixed_departures, $post_id);
                    }
                }

                // Generate available_months if not exists (from best_months if exists)
                if (!isset($data['available_months'])) {
                    $available_months = isset($data['best_months']) && is_array($data['best_months'])
                        ? $data['best_months']
                        : ['may', 'june', 'july', 'august', 'september'];
                    update_field('available_months', $available_months, $post_id);
                }

                // Generate best_months if not exists
                if (!isset($data['best_months'])) {
                    $best_months = ['june', 'july', 'august']; // Default best months (dry season)
                    update_field('best_months', $best_months, $post_id);
                }

                // Calendar settings (default: disabled)
                if (!isset($data['calendar_enabled'])) {
                    update_field('calendar_enabled', false, $post_id);
                }

                if (!isset($data['calendar_type'])) {
                    update_field('calendar_type', 'manual', $post_id);
                }

                if (!isset($data['min_booking_days'])) {
                    update_field('min_booking_days', 2, $post_id); // Default 2 days advance
                }

                // FASE 7E: Generate media structures (without actual images - FASE 8)
                // Gallery - empty array for now (will be filled with image IDs in FASE 8)
                if (!isset($data['gallery'])) {
                    update_field('gallery', [], $post_id);
                }

                // Map image - empty for now (will be filled in FASE 8)
                if (!isset($data['map_image'])) {
                    update_field('map_image', '', $post_id);
                }

                // Video thumbnail - empty for now (will be filled in FASE 8)
                if (!isset($data['video_thumbnail'])) {
                    update_field('video_thumbnail', '', $post_id);
                }

                // Banners repeater - create structure with placeholders
                if (!isset($data['banners'])) {
                    $banners = $this->generate_banner_structure($package_title);
                    update_field('banners', $banners, $post_id);
                }

                // FASE 7F: Generate additional content sections
                if (!isset($data['additional_sections'])) {
                    $additional_sections = $this->generate_additional_sections($package_title, $data);
                    if (!empty($additional_sections)) {
                        update_field('additional_sections', $additional_sections, $post_id);
                    }
                }

                // Important notes
                if (!isset($data['important_notes'])) {
                    $important_notes = $this->generate_important_notes($package_title, $data);
                    update_field('important_notes', $important_notes, $post_id);
                }

                // Terms & conditions
                if (!isset($data['terms'])) {
                    $terms = $this->generate_terms_conditions($package_title, $data);
                    update_field('terms', $terms, $post_id);
                }

                // FASE 7F: Generate SEO fields
                if (!isset($data['seo_title'])) {
                    $seo_title = $this->generate_seo_title($package_title, $data);
                    update_field('seo_title', $seo_title, $post_id);
                }

                if (!isset($data['seo_description'])) {
                    $seo_description = $this->generate_seo_description($package_title, $data);
                    update_field('seo_description', $seo_description, $post_id);
                }

                if (!isset($data['seo_keywords'])) {
                    $seo_keywords = $this->generate_seo_keywords($package_title, $data);
                    update_field('seo_keywords', $seo_keywords, $post_id);
                }

                if (!isset($data['seo_canonical'])) {
                    update_field('seo_canonical', '', $post_id); // Empty by default
                }

                if (!isset($data['seo_og_image'])) {
                    update_field('seo_og_image', '', $post_id); // Will use featured image by default
                }

                if (!isset($data['seo_robots'])) {
                    update_field('seo_robots', 'index_follow', $post_id); // Default: indexable
                }

                if (!isset($data['seo_schema'])) {
                    update_field('seo_schema', 'tour', $post_id); // Default: tour schema
                }
            }
        }

        // Add taxonomies
        if (isset($package['taxonomies'])) {
            foreach ($package['taxonomies'] as $taxonomy => $terms) {
                wp_set_object_terms($post_id, $terms, $taxonomy);
            }
        }

        // Only add featured image and gallery if requested
        if ($with_images) {
            $this->set_featured_image_from_placeholder($post_id, $package['title']);

            // Generate gallery images (4-6 images)
            error_log("Aurora Package Builder: Generating gallery for package ID $post_id - {$package['title']}");
            $gallery_ids = $this->generate_gallery_images($post_id, $package['title']);
            error_log("Aurora Package Builder: Gallery IDs generated: " . print_r($gallery_ids, true));

            if (!empty($gallery_ids) && function_exists('update_field')) {
                $result = update_field('gallery', $gallery_ids, $post_id);
                error_log("Aurora Package Builder: update_field('gallery') result: " . ($result ? 'SUCCESS' : 'FAILED'));
            } else {
                error_log("Aurora Package Builder: Gallery NOT saved. Empty: " . (empty($gallery_ids) ? 'YES' : 'NO') . ", update_field exists: " . (function_exists('update_field') ? 'YES' : 'NO'));
            }
        }

        return $post_id;
    }

    /**
     * FASE 7C: Generate itinerary for a package based on number of days
     *
     * @param int $num_days Number of days (1-38)
     * @param string $package_title Package title for context
     * @param array $data Package data array
     * @return array Itinerary repeater array
     */
    private function generate_itinerary_for_package($num_days, $package_title, $data)
    {
        $itinerary = [];

        // Determine package type from title or data
        $is_trek = (stripos($package_title, 'trek') !== false || stripos($package_title, 'trail') !== false);
        $is_jungle = (stripos($package_title, 'amazon') !== false || stripos($package_title, 'jungle') !== false);
        $is_tour = (stripos($package_title, 'tour') !== false || $num_days == 1);

        // Generate days
        for ($day = 1; $day <= $num_days; $day++) {
            $day_data = [
                'active' => true,
                'order' => $day,
                'limit' => 0,
                'title' => $this->generate_day_title($day, $num_days, $package_title, $is_trek, $is_jungle, $is_tour),
                'content' => $this->generate_day_content($day, $num_days, $package_title, $is_trek, $is_jungle, $is_tour),
                'gallery' => [], // Will be filled in FASE 7E
                'accommodation' => $this->generate_accommodation($day, $num_days, $is_trek, $is_jungle),
                'altitude' => $this->generate_altitude($day, $num_days, $is_trek),
                'optional_activities' => [], // Taxonomy - will be set separately if needed
                'items' => $this->generate_day_items($day, $num_days, $is_trek, $is_jungle, $is_tour),
            ];

            $itinerary[] = $day_data;
        }

        return $itinerary;
    }

    /**
     * Generate day title based on context
     */
    private function generate_day_title($day, $total_days, $title, $is_trek, $is_jungle, $is_tour)
    {
        if ($total_days == 1) {
            return 'Full Day Tour';
        }

        // Trek-specific titles
        if ($is_trek) {
            if ($day == 1) return 'Trek Start - Acclimatization';
            if ($day == $total_days) return 'Summit & Machu Picchu Visit';
            if ($day == $total_days - 1) return 'Approach to Machu Picchu';
            if ($day == 2) return 'Ascent to High Pass';
            return "Day $day - Mountain Trekking";
        }

        // Jungle-specific titles
        if ($is_jungle) {
            if ($day == 1) return 'Arrival & Rainforest Introduction';
            if ($day == $total_days) return 'Jungle Exploration & Return';
            return "Day $day - Jungle Adventures";
        }

        // Tour-specific titles
        if ($is_tour) {
            if ($day == 1) return 'Arrival & City Orientation';
            if ($day == $total_days) return 'Final Exploration & Departure';
            return "Day $day - Cultural Exploration";
        }

        // Default
        return "Day $day";
    }

    /**
     * Generate day content description
     */
    private function generate_day_content($day, $total_days, $title, $is_trek, $is_jungle, $is_tour)
    {
        if ($total_days == 1) {
            return '<p>Full day tour with all activities included. Professional guide will accompany you throughout the day, providing insights and ensuring a smooth experience.</p>';
        }

        if ($is_trek && $day == 1) {
            return '<p>Begin your trekking adventure! After breakfast, we depart from Cusco and transfer to the trailhead. Our expert guide will provide a safety briefing and distribute equipment. The first day includes moderate hiking to help with altitude acclimatization.</p><p>Arrive at the first campsite where our team will have camp set up. Enjoy a hot meal and rest for tomorrow\'s adventure.</p>';
        }

        if ($is_trek && $day == $total_days) {
            return '<p>Wake early for the highlight of the trek! Hike to the Sun Gate to witness sunrise over Machu Picchu. Descend to the citadel for a comprehensive guided tour of this ancient wonder.</p><p>After free time to explore, take the bus down to Aguas Calientes and board the train back to Cusco.</p>';
        }

        if ($is_jungle && $day == 1) {
            return '<p>Transfer to the jungle lodge via motorized canoe. Your naturalist guide will point out wildlife along the river. After settling into your comfortable lodge, take an introductory jungle walk to learn about the rainforest ecosystem.</p><p>Night safari to spot nocturnal creatures after dinner.</p>';
        }

        // Default content
        return "<p>Day $day of your adventure includes guided activities, cultural experiences, and opportunities to explore this fascinating destination. Your expert guide will provide detailed commentary and ensure you don't miss any highlights.</p>";
    }

    /**
     * Generate accommodation for the day
     */
    private function generate_accommodation($day, $total_days, $is_trek, $is_jungle)
    {
        if ($day == $total_days) {
            return ''; // Last day typically no accommodation
        }

        if ($is_trek) {
            return $day == 1 ? 'Mountain Camp' : 'Wilderness Camp';
        }

        if ($is_jungle) {
            return 'Jungle Eco-Lodge';
        }

        return 'Hotel in Cusco';
    }

    /**
     * Generate altitude for the day (meters)
     */
    private function generate_altitude($day, $total_days, $is_trek)
    {
        if (!$is_trek) {
            return 0; // Non-trek packages don't need altitude
        }

        // Simulate altitude progression on treks
        if ($day == 1) return 2800;
        if ($day == 2) return 3900;
        if ($day == 3 && $total_days >= 4) return 4200;
        if ($day == $total_days - 1) return 3600;
        if ($day == $total_days) return 2400;

        return 3500; // Default mid-altitude
    }

    /**
     * Generate items sub-repeater for a day
     */
    private function generate_day_items($day, $total_days, $is_trek, $is_jungle, $is_tour)
    {
        $items = [];

        // Morning activities
        if ($day > 1) {
            $items[] = [
                'order' => 1,
                'type_service' => null, // Taxonomy - meal
                'text' => 'Breakfast at camp/hotel',
                'hotel' => null,
                'alternative_hotels' => [],
            ];
        }

        // Main activity
        if ($day == 1) {
            $items[] = [
                'order' => count($items) + 1,
                'type_service' => null, // Taxonomy - transport
                'text' => 'Hotel pickup and transfer to starting point',
                'hotel' => null,
                'alternative_hotels' => [],
            ];
        }

        if ($is_trek) {
            $items[] = [
                'order' => count($items) + 1,
                'type_service' => null, // Taxonomy - activity
                'text' => $day == $total_days ? 'Guided hike to Machu Picchu' : 'Full day trekking through mountains',
                'hotel' => null,
                'alternative_hotels' => [],
            ];
        } elseif ($is_jungle) {
            $items[] = [
                'order' => count($items) + 1,
                'type_service' => null,
                'text' => 'Jungle exploration and wildlife spotting',
                'hotel' => null,
                'alternative_hotels' => [],
            ];
        } else {
            $items[] = [
                'order' => count($items) + 1,
                'type_service' => null,
                'text' => 'Guided tour of main attractions',
                'hotel' => null,
                'alternative_hotels' => [],
            ];
        }

        // Lunch
        $items[] = [
            'order' => count($items) + 1,
            'type_service' => null, // Taxonomy - meal
            'text' => $is_trek ? 'Trail lunch (box lunch or camp meal)' : 'Lunch at local restaurant',
            'hotel' => null,
            'alternative_hotels' => [],
        ];

        // Evening
        if ($day < $total_days) {
            $items[] = [
                'order' => count($items) + 1,
                'type_service' => null, // Taxonomy - meal
                'text' => 'Dinner and overnight at ' . ($is_trek ? 'camp' : ($is_jungle ? 'jungle lodge' : 'hotel')),
                'hotel' => null,
                'alternative_hotels' => [],
            ];
        } else {
            $items[] = [
                'order' => count($items) + 1,
                'type_service' => null, // Taxonomy - transport
                'text' => 'Return transfer to Cusco',
                'hotel' => null,
                'alternative_hotels' => [],
            ];
        }

        return $items;
    }

    /**
     * FASE 7D: Generate fixed departures for a package
     *
     * @param string $package_title Package title for context
     * @param array $data Package data
     * @return array Fixed departures repeater array
     */
    private function generate_fixed_departures($package_title, $data)
    {
        $departures = [];

        // Determine if package needs fixed departures
        $is_trek = (stripos($package_title, 'trek') !== false || stripos($package_title, 'trail') !== false);
        $is_day_tour = (isset($data['days']) && $data['days'] == 1);

        // Day tours typically have daily departures, no need for fixed dates
        if ($is_day_tour) {
            return []; // Empty - available daily
        }

        // Generate 8-12 future departure dates
        $num_departures = $is_trek ? 12 : 8; // More dates for treks
        $start_month = date('n'); // Current month (1-12)
        $start_year = date('Y');

        // Get available months from data or use defaults
        $available_months = isset($data['best_months']) && is_array($data['best_months'])
            ? $data['best_months']
            : ['may', 'june', 'july', 'august', 'september'];

        // Convert month names to numbers
        $month_map = [
            'january' => 1, 'jan' => 1,
            'february' => 2, 'feb' => 2,
            'march' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'may' => 5,
            'june' => 6, 'jun' => 6,
            'july' => 7, 'jul' => 7,
            'august' => 8, 'aug' => 8,
            'september' => 9, 'sep' => 9,
            'october' => 10, 'oct' => 10,
            'november' => 11, 'nov' => 11,
            'december' => 12, 'dec' => 12,
        ];

        $available_month_numbers = [];
        foreach ($available_months as $month_name) {
            $month_lower = strtolower($month_name);
            if (isset($month_map[$month_lower])) {
                $available_month_numbers[] = $month_map[$month_lower];
            }
        }

        // If no valid months, use peak season
        if (empty($available_month_numbers)) {
            $available_month_numbers = [5, 6, 7, 8, 9]; // May-Sep
        }

        // Generate departure dates
        $current_date = time();
        $dates_added = 0;

        for ($i = 0; $i < 365 && $dates_added < $num_departures; $i += 7) { // Check weekly
            $check_date = strtotime("+$i days", $current_date);
            $check_month = date('n', $check_date);

            // Only add if in available months
            if (in_array($check_month, $available_month_numbers)) {
                // Determine status (randomized but weighted)
                $rand = rand(1, 100);
                if ($rand <= 60) {
                    $status = 'available';
                    $spots = rand(6, 12);
                } elseif ($rand <= 85) {
                    $status = 'few_spots';
                    $spots = rand(1, 3);
                } elseif ($rand <= 95) {
                    $status = 'guaranteed';
                    $spots = 12;
                } else {
                    $status = 'sold_out';
                    $spots = 0;
                }

                // Calculate price (slightly higher in peak months)
                $base_price = $data['price_normal'] ?? $data['price_from'] ?? 500;
                $is_peak = in_array($check_month, [6, 7, 8]); // Jun-Aug peak
                $price = $is_peak ? round($base_price * 1.1) : $base_price;

                // Special notes for some dates
                $notes = '';
                if ($check_month == 12 || $check_month == 1) {
                    $notes = 'Holiday season';
                } elseif ($status == 'guaranteed') {
                    $notes = 'Guaranteed departure';
                }

                $departures[] = [
                    'date' => date('Y-m-d', $check_date),
                    'status' => $status,
                    'spots_available' => $spots,
                    'price' => $price,
                    'notes' => $notes,
                ];

                $dates_added++;
            }
        }

        return $departures;
    }

    /**
     * FASE 7E: Generate banner structure for package
     * Creates banner repeater structure without actual images (FASE 8)
     *
     * @param string $package_title Package title for banner titles
     * @return array Banners repeater array
     */
    private function generate_banner_structure($package_title)
    {
        $banners = [];

        // Extract key words from title for banner titles
        $title_words = explode(' ', $package_title);
        $main_destination = '';

        // Try to find destination name (Machu Picchu, Cusco, etc.)
        if (stripos($package_title, 'machu picchu') !== false) {
            $main_destination = 'Machu Picchu';
        } elseif (stripos($package_title, 'salkantay') !== false) {
            $main_destination = 'Salkantay Trek';
        } elseif (stripos($package_title, 'amazon') !== false || stripos($package_title, 'jungle') !== false) {
            $main_destination = 'Amazon Rainforest';
        } elseif (stripos($package_title, 'cusco') !== false) {
            $main_destination = 'Cusco';
        } elseif (stripos($package_title, 'sacred valley') !== false) {
            $main_destination = 'Sacred Valley';
        } else {
            // Use first 2-3 words as destination
            $main_destination = implode(' ', array_slice($title_words, 0, 3));
        }

        // Generate 3-5 banner entries (without image IDs - will be added in FASE 8)
        $num_banners = rand(3, 5);

        $banner_titles = [
            "Discover $main_destination",
            "Experience the Adventure",
            "$main_destination Awaits",
            "Your Journey Begins Here",
            "Explore Peru's Wonders",
        ];

        for ($i = 0; $i < $num_banners; $i++) {
            $banners[] = [
                'order' => $i + 1,
                'image' => '', // Empty - will be filled in FASE 8
                'orientation' => 'horizontal', // Default to horizontal
                'title' => $banner_titles[$i % count($banner_titles)],
            ];
        }

        return $banners;
    }

    /**
     * FASE 7F: Generate additional content sections (FAQ, Tips, Equipment)
     *
     * @param string $package_title Package title
     * @param array $data Package data
     * @return array Additional sections repeater
     */
    private function generate_additional_sections($package_title, $data)
    {
        $sections = [];

        $is_trek = (stripos($package_title, 'trek') !== false || stripos($package_title, 'trail') !== false);
        $is_jungle = (stripos($package_title, 'amazon') !== false || stripos($package_title, 'jungle') !== false);
        $is_tour = (stripos($package_title, 'tour') !== false);
        $num_days = isset($data['days']) ? intval($data['days']) : 1;

        // 1. FAQ Section
        $faq_items = [];
        if ($is_trek) {
            $faq_items = [
                ['label' => 'How difficult is this trek?', 'content' => 'This trek is rated as moderate to challenging. Good physical fitness is required. We recommend training 2-3 months before departure.'],
                ['label' => 'What is the maximum altitude?', 'content' => isset($data['altitude']) ? 'The maximum altitude reached is ' . $data['altitude'] . '.' : 'Altitude information will be provided in the detailed itinerary.'],
                ['label' => 'Do I need previous trekking experience?', 'content' => 'While previous trekking experience is helpful, it is not mandatory. However, you should be comfortable hiking for 5-7 hours per day.'],
                ['label' => 'What happens if I get altitude sickness?', 'content' => 'Our guides are trained in altitude sickness recognition and first aid. We carry oxygen and medical supplies. Serious cases will be evacuated to lower altitude immediately.'],
                ['label' => 'Can I rent trekking equipment?', 'content' => 'Yes, sleeping bags, trekking poles, and duffel bags are available for rent. Please book these items in advance.'],
            ];
        } elseif ($is_jungle) {
            $faq_items = [
                ['label' => 'What wildlife will we see?', 'content' => 'Common sightings include monkeys, macaws, toucans, caimans, and various species of birds and insects. Jaguar and tapir sightings are rare but possible.'],
                ['label' => 'Is it safe in the jungle?', 'content' => 'Yes, our experienced guides know the area well and follow safety protocols. We provide mosquito nets, first aid, and emergency communication equipment.'],
                ['label' => 'What should I bring for the jungle?', 'content' => 'Bring lightweight, long-sleeved clothing, insect repellent, rain gear, waterproof bags, and closed-toe shoes. A detailed packing list will be provided.'],
                ['label' => 'Are vaccinations required?', 'content' => 'Yellow fever vaccination is recommended for jungle areas. Consult your doctor about malaria prophylaxis and other recommended vaccinations.'],
            ];
        } else {
            $faq_items = [
                ['label' => 'What is included in the tour price?', 'content' => 'The tour includes transportation, professional guide, entrance fees to all sites, and meals as specified in the itinerary.'],
                ['label' => 'How large are the tour groups?', 'content' => isset($data['group_size']) ? 'Maximum group size is ' . $data['group_size'] . ' people.' : 'We keep small group sizes for personalized experience.'],
                ['label' => 'Can I customize this tour?', 'content' => 'Yes! We can customize most tours to match your interests, schedule, and budget. Contact us to discuss your preferences.'],
                ['label' => 'What if I need to cancel?', 'content' => 'Cancellation policies vary by tour. Please refer to our terms and conditions or contact us for specific information about this package.'],
            ];
        }

        $sections[] = [
            'active' => true,
            'order' => 1,
            'type' => 'faq',
            'title' => 'Frequently Asked Questions',
            'icon' => '❓',
            'style' => 'accordion',
            'content' => '<p>Find answers to common questions about this package below.</p>',
            'items' => $faq_items,
        ];

        // 2. Equipment/Packing List (for treks and multi-day tours)
        if ($is_trek || $num_days >= 3) {
            $equipment_items = [];

            if ($is_trek) {
                $equipment_items = [
                    ['label' => 'Daypack (30-40L)', 'content' => 'For carrying personal items during daily hiking'],
                    ['label' => 'Sleeping bag (comfort -10°C)', 'content' => 'Available for rent if needed'],
                    ['label' => 'Trekking poles', 'content' => 'Highly recommended for knee protection'],
                    ['label' => 'Headlamp with extra batteries', 'content' => 'Essential for early morning starts'],
                    ['label' => 'Water bottles or hydration system (2-3L)', 'content' => 'Stay hydrated at altitude'],
                    ['label' => 'Warm layers (fleece, down jacket)', 'content' => 'Temperatures drop significantly at night'],
                    ['label' => 'Rain gear (jacket and pants)', 'content' => 'Weather can change quickly in mountains'],
                    ['label' => 'Sun protection (hat, sunglasses, sunscreen)', 'content' => 'UV radiation is strong at altitude'],
                    ['label' => 'Personal first aid kit', 'content' => 'Include any personal medications'],
                    ['label' => 'Toiletries and biodegradable soap', 'content' => 'Limited facilities on trail'],
                ];
            } else {
                $equipment_items = [
                    ['label' => 'Comfortable walking shoes', 'content' => 'Broken-in shoes are essential'],
                    ['label' => 'Daypack', 'content' => 'For carrying water, camera, and personal items'],
                    ['label' => 'Light rain jacket', 'content' => 'Weather can be unpredictable'],
                    ['label' => 'Sun protection', 'content' => 'Hat, sunglasses, and sunscreen'],
                    ['label' => 'Reusable water bottle', 'content' => 'Reduce plastic waste'],
                    ['label' => 'Camera and extra batteries', 'content' => 'Capture amazing memories'],
                ];
            }

            $sections[] = [
                'active' => true,
                'order' => 2,
                'type' => 'equipment',
                'title' => $is_trek ? 'Essential Trekking Equipment' : 'What to Bring',
                'icon' => '🎒',
                'style' => 'list',
                'content' => '<p>Make sure you bring the following items for a comfortable experience.</p>',
                'items' => $equipment_items,
            ];
        }

        // 3. Travel Tips
        $tips_items = [];
        if ($is_trek) {
            $tips_items = [
                ['label' => 'Acclimatization', 'content' => 'Spend at least 2 days in Cusco (3,400m) before starting the trek to acclimatize properly.'],
                ['label' => 'Training', 'content' => 'Start training 2-3 months before with regular cardio exercise and hiking with a loaded backpack.'],
                ['label' => 'Book Early', 'content' => 'Permits sell out months in advance, especially for high season (May-September). Book at least 6 months ahead.'],
                ['label' => 'Pack Light', 'content' => 'Porters carry group equipment, but you carry your own daypack. Keep it under 6kg for comfort.'],
            ];
        } else {
            $tips_items = [
                ['label' => 'Best Time to Visit', 'content' => isset($data['months']) && is_array($data['months']) ? 'This tour is available ' . implode(', ', array_slice($data['months'], 0, 3)) . ' and other months.' : 'Check seasonal availability for best weather.'],
                ['label' => 'Currency', 'content' => 'Peruvian Soles (PEN) are the local currency. US Dollars are widely accepted. ATMs available in major cities.'],
                ['label' => 'Tipping', 'content' => 'Tipping is customary in Peru. Budget $5-10 per day for guides and $3-5 for drivers.'],
                ['label' => 'Arrive Early', 'content' => 'Arrive at least one day before the tour starts to account for possible flight delays and to rest.'],
            ];
        }

        $sections[] = [
            'active' => true,
            'order' => 3,
            'type' => 'tips',
            'title' => 'Important Travel Tips',
            'icon' => '💡',
            'style' => 'cards',
            'content' => '<p>Follow these tips for the best experience on your adventure.</p>',
            'items' => $tips_items,
        ];

        return $sections;
    }

    /**
     * FASE 7F: Generate important notes content
     *
     * @param string $package_title Package title
     * @param array $data Package data
     * @return string Important notes HTML
     */
    private function generate_important_notes($package_title, $data)
    {
        $is_trek = (stripos($package_title, 'trek') !== false || stripos($package_title, 'trail') !== false);
        $is_incatrail = isset($data['incatrail']) && $data['incatrail'] === true;
        $is_jungle = (stripos($package_title, 'amazon') !== false || stripos($package_title, 'jungle') !== false);

        $notes = '<ul>';

        if ($is_incatrail) {
            $notes .= '<li><strong>Permit Required:</strong> Inca Trail permits are strictly limited and non-transferable. Passport details must be provided at booking.</li>';
            $notes .= '<li><strong>No Availability:</strong> If permits are sold out, alternative treks are available (Salkantay, Lares, etc.).</li>';
        }

        if ($is_trek) {
            $notes .= '<li><strong>Physical Fitness:</strong> Good physical condition is required. Consult your doctor before booking if you have health concerns.</li>';
            $notes .= '<li><strong>Altitude:</strong> This trek reaches high altitude. Proper acclimatization is essential to avoid altitude sickness.</li>';
            $notes .= '<li><strong>Weather:</strong> Mountain weather is unpredictable. Tours operate rain or shine. Cancellations due to weather are rare.</li>';
            $notes .= '<li><strong>Porter Weight Limit:</strong> Personal duffel bags are limited to 7kg (including sleeping bag if rented).</li>';
        }

        if ($is_jungle) {
            $notes .= '<li><strong>Vaccinations:</strong> Yellow fever vaccination is strongly recommended. Consult your doctor about malaria prophylaxis.</li>';
            $notes .= '<li><strong>Weather:</strong> Jungle weather is hot and humid year-round. Rain is possible any time.</li>';
            $notes .= '<li><strong>Wildlife:</strong> While wildlife sightings are common, they cannot be guaranteed as animals are wild and free.</li>';
        }

        $notes .= '<li><strong>Travel Insurance:</strong> Comprehensive travel insurance is mandatory and must include emergency evacuation coverage.</li>';
        $notes .= '<li><strong>Minimum Age:</strong> Participants must be at least 12 years old unless specified otherwise.</li>';

        if (isset($data['is_prepayment']) && $data['is_prepayment']) {
            $notes .= '<li><strong>Payment:</strong> A non-refundable deposit is required to secure your booking. Full payment is due 45 days before departure.</li>';
        }

        $notes .= '<li><strong>Changes & Cancellations:</strong> Please review our cancellation policy carefully. Last-minute cancellations may result in forfeiture of payment.</li>';
        $notes .= '</ul>';

        return $notes;
    }

    /**
     * FASE 7F: Generate terms and conditions
     *
     * @param string $package_title Package title
     * @param array $data Package data
     * @return string Terms HTML
     */
    private function generate_terms_conditions($package_title, $data)
    {
        $is_prepayment = isset($data['is_prepayment']) && $data['is_prepayment'];

        $terms = '<h4>Booking & Payment</h4>';
        $terms .= '<p>A deposit is required to confirm your reservation. ';
        $terms .= $is_prepayment ? 'Full payment must be received 45 days prior to departure. ' : 'Payment terms will be provided at booking. ';
        $terms .= 'We accept credit cards, PayPal, and bank transfers.</p>';

        $terms .= '<h4>Cancellation Policy</h4>';
        $terms .= '<ul>';
        $terms .= '<li>60+ days before departure: Full refund minus $100 processing fee</li>';
        $terms .= '<li>30-59 days before departure: 50% refund</li>';
        $terms .= '<li>15-29 days before departure: 25% refund</li>';
        $terms .= '<li>Less than 15 days: No refund</li>';
        $terms .= '</ul>';
        $terms .= '<p>Cancellations must be submitted in writing via email.</p>';

        $terms .= '<h4>Changes & Modifications</h4>';
        $terms .= '<p>Changes to confirmed bookings may be possible subject to availability and may incur fees. ';
        $terms .= 'Date changes are subject to permit availability and seasonal pricing differences.</p>';

        $terms .= '<h4>Travel Insurance</h4>';
        $terms .= '<p>Comprehensive travel insurance is mandatory for all participants. Insurance must cover trip cancellation, ';
        $terms .= 'medical expenses, emergency evacuation, and personal liability. Proof of insurance must be provided before departure.</p>';

        $terms .= '<h4>Health & Safety</h4>';
        $terms .= '<p>Participants must be in good health and able to complete the activities described in the itinerary. ';
        $terms .= 'Any pre-existing medical conditions must be disclosed at the time of booking. ';
        $terms .= 'We reserve the right to refuse participation if health concerns pose a risk to the individual or group.</p>';

        $terms .= '<h4>Liability</h4>';
        $terms .= '<p>While we take every precaution to ensure your safety, adventure travel involves inherent risks. ';
        $terms .= 'Participants engage in all activities at their own risk. Our company and partners are not liable for ';
        $terms .= 'injury, loss, damage, delay, or expense arising from circumstances beyond our control.</p>';

        $terms .= '<h4>Force Majeure</h4>';
        $terms .= '<p>We are not responsible for failure to perform our obligations due to circumstances beyond our control ';
        $terms .= 'including but not limited to: natural disasters, political unrest, strikes, or government actions. ';
        $terms .= 'In such cases, we will work with you to find alternative solutions.</p>';

        return $terms;
    }

    /**
     * FASE 7F: Generate SEO title (max 70 chars)
     *
     * @param string $package_title Original package title
     * @param array $data Package data
     * @return string SEO optimized title
     */
    private function generate_seo_title($package_title, $data)
    {
        $num_days = isset($data['days']) ? intval($data['days']) : null;

        // Extract key destination from title
        $destination = '';
        if (stripos($package_title, 'machu picchu') !== false) {
            $destination = 'Machu Picchu';
        } elseif (stripos($package_title, 'cusco') !== false) {
            $destination = 'Cusco';
        } elseif (stripos($package_title, 'amazon') !== false) {
            $destination = 'Amazon';
        } elseif (stripos($package_title, 'lima') !== false) {
            $destination = 'Lima';
        }

        // Build SEO title
        $seo_title = $package_title;

        // Add "Peru" if not already in title
        if (stripos($seo_title, 'peru') === false && !empty($destination)) {
            $seo_title .= ', Peru';
        }

        // Add call-to-action if space allows
        if (strlen($seo_title) < 55) {
            $seo_title .= ' | Book Now';
        }

        // Truncate if too long
        if (strlen($seo_title) > 70) {
            $seo_title = substr($seo_title, 0, 67) . '...';
        }

        return $seo_title;
    }

    /**
     * FASE 7F: Generate SEO meta description (max 160 chars)
     *
     * @param string $package_title Package title
     * @param array $data Package data
     * @return string SEO meta description
     */
    private function generate_seo_description($package_title, $data)
    {
        $num_days = isset($data['days']) ? intval($data['days']) : 1;
        $rating = isset($data['rating']) ? $data['rating'] : null;
        $summary = isset($data['summary']) ? $data['summary'] : '';

        // Use summary if it exists and is short enough
        if (!empty($summary) && strlen($summary) <= 160) {
            return strip_tags($summary);
        }

        // Build description from components
        $description = '';

        // Extract main activity
        if (stripos($package_title, 'trek') !== false) {
            $description .= "Trek to $package_title over $num_days days. ";
        } elseif (stripos($package_title, 'tour') !== false) {
            $description .= "Explore $package_title. ";
        } else {
            $description .= "$package_title adventure. ";
        }

        // Add key selling points
        $description .= "Expert guides, quality service";

        if ($rating && $rating >= 4.5) {
            $description .= ", $rating★ rated";
        }

        $description .= ". Book your Peru adventure today!";

        // Truncate if needed
        if (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }

        return $description;
    }

    /**
     * FASE 7F: Generate SEO keywords
     *
     * @param string $package_title Package title
     * @param array $data Package data
     * @return string Comma-separated keywords
     */
    private function generate_seo_keywords($package_title, $data)
    {
        $keywords = [];

        // Extract main keywords from title
        $title_lower = strtolower($package_title);

        // Destinations
        if (stripos($title_lower, 'machu picchu') !== false) {
            $keywords[] = 'machu picchu tour';
            $keywords[] = 'machu picchu trek';
        }
        if (stripos($title_lower, 'cusco') !== false) {
            $keywords[] = 'cusco tour';
        }
        if (stripos($title_lower, 'inca trail') !== false) {
            $keywords[] = 'inca trail';
            $keywords[] = 'inca trail permits';
        }
        if (stripos($title_lower, 'salkantay') !== false) {
            $keywords[] = 'salkantay trek';
        }
        if (stripos($title_lower, 'amazon') !== false || stripos($title_lower, 'jungle') !== false) {
            $keywords[] = 'amazon jungle tour';
            $keywords[] = 'peru rainforest';
        }

        // Activity types
        if (stripos($title_lower, 'trek') !== false) {
            $keywords[] = 'peru trekking';
            $keywords[] = 'hiking peru';
        }

        // General Peru keywords
        $keywords[] = 'peru tours';
        $keywords[] = 'peru travel';
        $keywords[] = 'visit peru';

        // Duration
        if (isset($data['days'])) {
            $keywords[] = $data['days'] . ' day tour peru';
        }

        // Remove duplicates and limit to 10 keywords
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 10);

        return implode(', ', $keywords);
    }

    /**
     * Generate gallery images for a package
     * Creates 4-6 images from local placeholder images
     *
     * @param int $post_id Package post ID
     * @param string $title Package title for naming
     * @return array Array of attachment IDs
     */
    private function generate_gallery_images($post_id, $title)
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Setup debug logging
        $upload_dir = wp_upload_dir();
        $debug_file = $upload_dir['basedir'] . '/package-debug.log';

        $gallery_ids = [];
        $num_images = rand(4, 6); // Random 4-6 images per package

        file_put_contents($debug_file, "  Attempting to generate $num_images gallery images...\n", FILE_APPEND);

        for ($i = 1; $i <= $num_images; $i++) {
            // Select different placeholder images for variety (cycling through 1-55)
            $image_number = ((($post_id - 1) * 10 + $i - 1) % 55) + 1;
            $source_image = plugin_dir_path(dirname(__FILE__)) . 'assets/placeholder-images/placeholder-' . $image_number . '.jpg';

            file_put_contents($debug_file, "  Image $i: using local placeholder-$image_number.jpg\n", FILE_APPEND);

            // Verify source file exists
            if (!file_exists($source_image)) {
                file_put_contents($debug_file, "  Image $i ERROR: source file not found at $source_image\n", FILE_APPEND);
                continue;
            }

            $filesize = filesize($source_image);
            file_put_contents($debug_file, "  Image $i: source file size = $filesize bytes\n", FILE_APPEND);

            if ($filesize === 0) {
                file_put_contents($debug_file, "  Image $i ERROR: source file is empty\n", FILE_APPEND);
                continue;
            }

            // Create temporary copy
            $tmp = wp_tempnam($source_image);
            copy($source_image, $tmp);

            file_put_contents($debug_file, "  Image $i: copied to temp file $tmp\n", FILE_APPEND);

            // Prepare file array
            $file_array = [
                'name' => sanitize_title($title) . '-gallery-' . $i . '.jpg',
                'tmp_name' => $tmp,
            ];

            file_put_contents($debug_file, "  Image $i: calling media_handle_sideload...\n", FILE_APPEND);

            // Upload to media library
            $id = media_handle_sideload($file_array, $post_id, $title . ' - Gallery Image ' . $i);

            // Clean up temporary file
            if (file_exists($tmp)) {
                @unlink($tmp);
            }

            if (is_wp_error($id)) {
                $error_msg = $id->get_error_message();
                file_put_contents($debug_file, "  Image $i ERROR: media_handle_sideload failed - $error_msg\n", FILE_APPEND);
                error_log("Aurora Package Builder: Failed to create gallery attachment $i for $title: " . $error_msg);
                continue;
            }

            file_put_contents($debug_file, "  Image $i SUCCESS: attachment ID = $id\n", FILE_APPEND);

            // Add to gallery array
            $gallery_ids[] = $id;
        }

        file_put_contents($debug_file, "  Gallery generation complete: " . count($gallery_ids) . " images created\n", FILE_APPEND);

        return $gallery_ids;
    }

    /**
     * Set featured image from local placeholder images
     * Uses pre-downloaded placeholder images from plugin assets
     */
    private function set_featured_image_from_placeholder($post_id, $title)
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Setup debug logging
        $upload_dir = wp_upload_dir();
        $debug_file = $upload_dir['basedir'] . '/package-debug.log';

        // Select a random placeholder image from local assets (1-55)
        $image_number = (($post_id - 1) % 55) + 1;
        $source_image = plugin_dir_path(dirname(__FILE__)) . 'assets/placeholder-images/placeholder-' . $image_number . '.jpg';

        file_put_contents($debug_file, "  Featured image: using local placeholder-$image_number.jpg\n", FILE_APPEND);

        // Verify source file exists
        if (!file_exists($source_image)) {
            file_put_contents($debug_file, "  Featured image ERROR: source file not found at $source_image\n", FILE_APPEND);
            return false;
        }

        $filesize = filesize($source_image);
        file_put_contents($debug_file, "  Featured image: source file size = $filesize bytes\n", FILE_APPEND);

        if ($filesize === 0) {
            file_put_contents($debug_file, "  Featured image ERROR: source file is empty\n", FILE_APPEND);
            return false;
        }

        // Create temporary copy
        $tmp = wp_tempnam($source_image);
        copy($source_image, $tmp);

        file_put_contents($debug_file, "  Featured image: copied to temp file $tmp\n", FILE_APPEND);

        // Prepare file array
        $file_array = [
            'name' => sanitize_title($title) . '-featured.jpg',
            'tmp_name' => $tmp,
        ];

        file_put_contents($debug_file, "  Featured image: calling media_handle_sideload...\n", FILE_APPEND);

        // Upload to media library
        $id = media_handle_sideload($file_array, $post_id, $title . ' - Featured Image');

        // Clean up temporary file
        if (file_exists($tmp)) {
            @unlink($tmp);
        }

        if (is_wp_error($id)) {
            $error_msg = $id->get_error_message();
            file_put_contents($debug_file, "  Featured image ERROR: media_handle_sideload failed - $error_msg\n", FILE_APPEND);
            error_log("Aurora Package Builder: Failed to create attachment for $title: " . $error_msg);
            return false;
        }

        file_put_contents($debug_file, "  Featured image SUCCESS: attachment ID = $id\n", FILE_APPEND);

        // Set as featured image
        set_post_thumbnail($post_id, $id);

        file_put_contents($debug_file, "  Featured image set as post thumbnail\n", FILE_APPEND);

        return $id;
    }

    /**
     * FASE 8A: Generate single image from placeholder
     * Helper method to upload a specific placeholder image and return attachment ID
     *
     * @param int $post_id Post ID to attach the image to
     * @param string $title Base title for the image
     * @param int $placeholder_num Placeholder number (1-55)
     * @param string $image_type Type of image (Map, Banner, etc.) for naming
     * @return int|false Attachment ID or false on failure
     */
    public function generate_single_image_from_placeholder($post_id, $title, $placeholder_num, $image_type = 'Image')
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Ensure placeholder number is in valid range (1-55)
        $placeholder_num = max(1, min(55, intval($placeholder_num)));

        // Get source image path
        $source_image = plugin_dir_path(dirname(__FILE__)) . 'assets/placeholder-images/placeholder-' . $placeholder_num . '.jpg';

        // Verify source file exists
        if (!file_exists($source_image)) {
            error_log("Aurora Package Builder: Placeholder image not found: $source_image");
            return false;
        }

        // Verify file is not empty
        $filesize = filesize($source_image);
        if ($filesize === 0) {
            error_log("Aurora Package Builder: Placeholder image is empty: $source_image");
            return false;
        }

        // Create temporary copy
        $tmp = wp_tempnam($source_image);
        copy($source_image, $tmp);

        // Prepare file array with descriptive name
        $file_array = [
            'name' => sanitize_title($title) . '-' . strtolower(str_replace(' ', '-', $image_type)) . '-' . $placeholder_num . '.jpg',
            'tmp_name' => $tmp,
        ];

        // Upload to media library
        $id = media_handle_sideload($file_array, $post_id, $title . ' - ' . $image_type);

        // Clean up temporary file
        if (file_exists($tmp)) {
            @unlink($tmp);
        }

        // Check for errors
        if (is_wp_error($id)) {
            $error_msg = $id->get_error_message();
            error_log("Aurora Package Builder: Failed to create $image_type attachment for $title: " . $error_msg);
            return false;
        }

        return $id;
    }

    /**
     * Extract relevant keywords from package title for image search
     */
    private function get_image_keywords_from_title($title)
    {
        // Map common package types to better search keywords
        $keyword_map = [
            'machu picchu' => 'machu,picchu,peru,inca',
            'inca trail' => 'inca,trail,peru,hiking',
            'rainbow mountain' => 'rainbow,mountain,vinicunca,peru',
            'sacred valley' => 'sacred,valley,cusco,peru',
            'humantay lake' => 'humantay,lake,peru,turquoise',
            'cusco' => 'cusco,peru,architecture',
            'lima' => 'lima,peru,city',
            'arequipa' => 'arequipa,peru,colca',
            'titicaca' => 'titicaca,lake,peru,bolivia',
            'amazon' => 'amazon,rainforest,peru',
            'colca canyon' => 'colca,canyon,peru,condor',
            'nazca lines' => 'nazca,lines,peru,desert',
        ];

        $title_lower = strtolower($title);

        // Check if title contains any mapped keywords
        foreach ($keyword_map as $key => $keywords) {
            if (strpos($title_lower, $key) !== false) {
                return $keywords;
            }
        }

        // Default: use generic Peru travel keywords
        return 'peru,travel,landscape,mountains';
    }

    /**
     * Delete all mock packages
     */
    public function delete_all_packages()
    {
        $packages = get_posts([
            'post_type' => 'package',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        $deleted = 0;
        foreach ($packages as $package) {
            if (wp_delete_post($package->ID, true)) {
                $deleted++;
            }
        }

        return [
            'success' => true,
            'deleted' => $deleted,
        ];
    }

    /**
     * Add featured images and gallery to existing packages
     * DISABLED: This functionality has been deactivated
     */
    public function add_images_to_packages()
    {
        // DISABLED: Adding images to packages via mock data has been deactivated
<<<<<<< Updated upstream
=======
        return [
            'success' => false,
            'updated' => 0,
            'total' => 0,
            'fixed_broken' => 0,
            'errors' => ['Adding images to packages has been disabled. This functionality is no longer available.'],
        ];

        file_put_contents($debug_file, "Found " . count($packages) . " packages\n", FILE_APPEND);
        error_log("Aurora Package Builder: Found " . count($packages) . " packages to process");

        $updated = 0;
        $errors = [];
        $fixed_broken = 0;

        foreach ($packages as $package) {
            file_put_contents($debug_file, "\n--- Package {$package->ID}: {$package->post_title} ---\n", FILE_APPEND);
            $updated_package = false;

            // IMPROVED: Check if featured image exists AND is valid (not 404)
            $has_thumbnail = has_post_thumbnail($package->ID);
            $thumbnail_id = get_post_thumbnail_id($package->ID);
            $is_broken = false;

            if ($has_thumbnail && $thumbnail_id) {
                // Check if attachment exists and file is accessible
                $attachment_exists = get_post($thumbnail_id);
                $file_path = get_attached_file($thumbnail_id);

                if (!$attachment_exists || !$file_path || !file_exists($file_path)) {
                    $is_broken = true;
                    file_put_contents($debug_file, "Featured image is BROKEN (404) - ID: $thumbnail_id\n", FILE_APPEND);
                    $fixed_broken++;
                }
            }

            file_put_contents($debug_file, "Has thumbnail: " . ($has_thumbnail ? 'YES' : 'NO') . " | Broken: " . ($is_broken ? 'YES' : 'NO') . "\n", FILE_APPEND);

            // Add or REPLACE featured image if missing OR broken
            if (!$has_thumbnail || $is_broken) {
                if ($is_broken) {
                    file_put_contents($debug_file, "REPLACING broken featured image...\n", FILE_APPEND);
                    // Delete broken attachment reference
                    delete_post_thumbnail($package->ID);
                } else {
                    file_put_contents($debug_file, "Adding featured image...\n", FILE_APPEND);
                }
                file_put_contents($debug_file, "Adding featured image...\n", FILE_APPEND);
                $result = $this->set_featured_image_from_placeholder($package->ID, $package->post_title);
                if ($result) {
                    file_put_contents($debug_file, "Featured image added (ID: $result)\n", FILE_APPEND);
                    $updated_package = true;
                } else {
                    file_put_contents($debug_file, "Featured image FAILED\n", FILE_APPEND);
                    $errors[] = $package->post_title . ' (featured image failed)';
                }
            } else {
                file_put_contents($debug_file, "Skipping featured image (already exists)\n", FILE_APPEND);
            }

            // IMPROVED: Check gallery for broken images
            $gallery = get_field('gallery', $package->ID);
            $gallery_count = is_array($gallery) ? count($gallery) : 0;
            file_put_contents($debug_file, "Gallery count: $gallery_count\n", FILE_APPEND);
            file_put_contents($debug_file, "update_field exists: " . (function_exists('update_field') ? 'YES' : 'NO') . "\n", FILE_APPEND);

            $has_broken_gallery_images = false;
            if (is_array($gallery) && !empty($gallery)) {
                // Check each gallery image for broken files
                $valid_gallery_ids = [];
                foreach ($gallery as $image_id) {
                    $attachment_exists = get_post($image_id);
                    $file_path = get_attached_file($image_id);

                    if ($attachment_exists && $file_path && file_exists($file_path)) {
                        // Keep valid images
                        $valid_gallery_ids[] = $image_id;
                    } else {
                        // Mark as broken
                        $has_broken_gallery_images = true;
                        file_put_contents($debug_file, "Gallery image BROKEN (404) - ID: $image_id\n", FILE_APPEND);
                        $fixed_broken++;
                    }
                }

                // If some images were removed, update the gallery
                if ($has_broken_gallery_images && count($valid_gallery_ids) !== count($gallery)) {
                    file_put_contents($debug_file, "Removed " . (count($gallery) - count($valid_gallery_ids)) . " broken gallery images\n", FILE_APPEND);
                    $gallery = $valid_gallery_ids;
                }
            }

            // Add or REPLACE gallery if missing OR has broken images
            if ((empty($gallery) || $has_broken_gallery_images) && function_exists('update_field')) {
                if ($has_broken_gallery_images) {
                    file_put_contents($debug_file, "REPLACING broken gallery images...\n", FILE_APPEND);
                } else {
                    file_put_contents($debug_file, "Generating gallery images...\n", FILE_APPEND);
                }

                $gallery_ids = $this->generate_gallery_images($package->ID, $package->post_title);
                file_put_contents($debug_file, "Generated " . count($gallery_ids) . " images: " . implode(',', $gallery_ids) . "\n", FILE_APPEND);

                if (!empty($gallery_ids)) {
                    $update_result = update_field('gallery', $gallery_ids, $package->ID);
                    file_put_contents($debug_file, "update_field result: " . ($update_result ? 'SUCCESS' : 'FAILED') . "\n", FILE_APPEND);
                    $updated_package = true;
                }
            } else {
                $reason = !function_exists('update_field') ? 'update_field not available' : 'gallery already exists and is valid';
                file_put_contents($debug_file, "Skipping gallery ($reason)\n", FILE_APPEND);
            }

            // IMPROVED: Check map_image for broken files
            if (function_exists('get_field') && function_exists('update_field')) {
                $map_image = get_field('map_image', $package->ID);
                $is_map_broken = false;

                if (!empty($map_image)) {
                    // Check if map image is broken
                    $attachment_exists = get_post($map_image);
                    $file_path = get_attached_file($map_image);

                    if (!$attachment_exists || !$file_path || !file_exists($file_path)) {
                        $is_map_broken = true;
                        file_put_contents($debug_file, "Map image is BROKEN (404) - ID: $map_image\n", FILE_APPEND);
                        $fixed_broken++;
                    }
                }

                file_put_contents($debug_file, "Map image: " . ($map_image ? 'EXISTS' : 'MISSING') . " | Broken: " . ($is_map_broken ? 'YES' : 'NO') . "\n", FILE_APPEND);

                // Add or REPLACE map_image if missing OR broken
                if (empty($map_image) || $is_map_broken) {
                    if ($is_map_broken) {
                        file_put_contents($debug_file, "REPLACING broken map image...\n", FILE_APPEND);
                    } else {
                        file_put_contents($debug_file, "Generating map image...\n", FILE_APPEND);
                    }

                    // Use placeholders 51-55 for maps
                    $map_placeholder_num = 51 + ($package->ID % 5);
                    $map_id = $this->generate_single_image_from_placeholder($package->ID, $package->post_title, $map_placeholder_num, 'Map');

                    if ($map_id) {
                        update_field('map_image', $map_id, $package->ID);
                        file_put_contents($debug_file, "Map image added (ID: $map_id)\n", FILE_APPEND);
                        $updated_package = true;
                    } else {
                        file_put_contents($debug_file, "Map image FAILED\n", FILE_APPEND);
                    }
                }
            }

            // IMPROVED: Check video_thumbnail for broken files
            if (function_exists('get_field') && function_exists('update_field')) {
                $video_url = get_field('video_url', $package->ID);
                $video_thumbnail = get_field('video_thumbnail', $package->ID);
                $is_video_thumb_broken = false;

                if (!empty($video_thumbnail)) {
                    // Check if video thumbnail is broken
                    $attachment_exists = get_post($video_thumbnail);
                    $file_path = get_attached_file($video_thumbnail);

                    if (!$attachment_exists || !$file_path || !file_exists($file_path)) {
                        $is_video_thumb_broken = true;
                        file_put_contents($debug_file, "Video thumbnail is BROKEN (404) - ID: $video_thumbnail\n", FILE_APPEND);
                        $fixed_broken++;
                    }
                }

                file_put_contents($debug_file, "Video URL: " . ($video_url ? 'EXISTS' : 'NONE') . "\n", FILE_APPEND);
                file_put_contents($debug_file, "Video thumbnail: " . ($video_thumbnail ? 'EXISTS' : 'MISSING') . " | Broken: " . ($is_video_thumb_broken ? 'YES' : 'NO') . "\n", FILE_APPEND);

                // Add or REPLACE video_thumbnail if video_url exists AND (thumbnail missing OR broken)
                if (!empty($video_url) && (empty($video_thumbnail) || $is_video_thumb_broken)) {
                    if ($is_video_thumb_broken) {
                        file_put_contents($debug_file, "REPLACING broken video thumbnail...\n", FILE_APPEND);
                    } else {
                        file_put_contents($debug_file, "Generating video thumbnail...\n", FILE_APPEND);
                    }

                    // Use placeholders 1-10 for video thumbnails
                    $video_placeholder_num = 1 + ($package->ID % 10);
                    $thumb_id = $this->generate_single_image_from_placeholder($package->ID, $package->post_title, $video_placeholder_num, 'Video Thumbnail');

                    if ($thumb_id) {
                        update_field('video_thumbnail', $thumb_id, $package->ID);
                        file_put_contents($debug_file, "Video thumbnail added (ID: $thumb_id)\n", FILE_APPEND);
                        $updated_package = true;
                    } else {
                        file_put_contents($debug_file, "Video thumbnail FAILED\n", FILE_APPEND);
                    }
                }
            }

            // FASE 8A: Add images to banners repeater
            if (function_exists('get_field') && function_exists('update_field')) {
                $banners = get_field('banners', $package->ID);
                file_put_contents($debug_file, "Banners count: " . (is_array($banners) ? count($banners) : 0) . "\n", FILE_APPEND);

                if (is_array($banners) && !empty($banners)) {
                    $updated_banners = [];
                    $banners_updated = false;

                    foreach ($banners as $index => $banner) {
                        if (empty($banner['image'])) {
                            file_put_contents($debug_file, "  Banner $index: Generating image...\n", FILE_APPEND);
                            // Use placeholders 21-40 for banners (landmarks)
                            $banner_placeholder_num = 21 + (($package->ID * 3 + $index) % 20);
                            $banner_id = $this->generate_single_image_from_placeholder($package->ID, $package->post_title, $banner_placeholder_num, "Banner $index");

                            if ($banner_id) {
                                $banner['image'] = $banner_id;
                                file_put_contents($debug_file, "  Banner $index: Image added (ID: $banner_id)\n", FILE_APPEND);
                                $banners_updated = true;
                            } else {
                                file_put_contents($debug_file, "  Banner $index: Image FAILED\n", FILE_APPEND);
                            }
                        } else {
                            file_put_contents($debug_file, "  Banner $index: Image already exists\n", FILE_APPEND);
                        }
                        $updated_banners[] = $banner;
                    }

                    if ($banners_updated) {
                        update_field('banners', $updated_banners, $package->ID);
                        file_put_contents($debug_file, "Banners repeater updated\n", FILE_APPEND);
                        $updated_package = true;
                    }
                }
            }

            // FASE 8A: Add seo_og_image (use featured image if available)
            if (function_exists('get_field') && function_exists('update_field')) {
                $og_image = get_field('seo_og_image', $package->ID);
                file_put_contents($debug_file, "SEO OG image: " . ($og_image ? 'EXISTS' : 'MISSING') . "\n", FILE_APPEND);

                if (empty($og_image)) {
                    $featured_id = get_post_thumbnail_id($package->ID);
                    if ($featured_id) {
                        update_field('seo_og_image', $featured_id, $package->ID);
                        file_put_contents($debug_file, "SEO OG image set to featured image (ID: $featured_id)\n", FILE_APPEND);
                        $updated_package = true;
                    } else {
                        file_put_contents($debug_file, "SEO OG image SKIPPED (no featured image)\n", FILE_APPEND);
                    }
                }
            }

            // FASE 8A: Add images to itinerary day galleries
            if (function_exists('get_field') && function_exists('update_field')) {
                $itinerary = get_field('itinerary', $package->ID);
                file_put_contents($debug_file, "Itinerary days: " . (is_array($itinerary) ? count($itinerary) : 0) . "\n", FILE_APPEND);

                if (is_array($itinerary) && !empty($itinerary)) {
                    $updated_itinerary = [];
                    $itinerary_updated = false;

                    foreach ($itinerary as $day_index => $day) {
                        $day_gallery = isset($day['gallery']) ? $day['gallery'] : [];
                        $day_num = isset($day['order']) ? $day['order'] : ($day_index + 1);

                        if (empty($day_gallery)) {
                            file_put_contents($debug_file, "  Day $day_num: Generating gallery (2-4 images)...\n", FILE_APPEND);

                            // Generate 2-4 images for this day
                            $num_images = rand(2, 4);
                            $day_gallery_ids = [];

                            for ($img = 0; $img < $num_images; $img++) {
                                // Use placeholders 1-45 for itinerary images
                                $img_placeholder_num = 1 + ((($package->ID * 100) + ($day_index * 10) + $img) % 45);
                                $img_id = $this->generate_single_image_from_placeholder($package->ID, $package->post_title, $img_placeholder_num, "Day $day_num Image " . ($img + 1));

                                if ($img_id) {
                                    $day_gallery_ids[] = $img_id;
                                }
                            }

                            if (!empty($day_gallery_ids)) {
                                $day['gallery'] = $day_gallery_ids;
                                file_put_contents($debug_file, "  Day $day_num: Added " . count($day_gallery_ids) . " images\n", FILE_APPEND);
                                $itinerary_updated = true;
                            } else {
                                file_put_contents($debug_file, "  Day $day_num: Gallery FAILED\n", FILE_APPEND);
                            }
                        } else {
                            file_put_contents($debug_file, "  Day $day_num: Gallery already exists (" . count($day_gallery) . " images)\n", FILE_APPEND);
                        }

                        $updated_itinerary[] = $day;
                    }

                    if ($itinerary_updated) {
                        update_field('itinerary', $updated_itinerary, $package->ID);
                        file_put_contents($debug_file, "Itinerary repeater updated\n", FILE_APPEND);
                        $updated_package = true;
                    }
                }
            }

            if ($updated_package) {
                $updated++;
                file_put_contents($debug_file, "RESULT: UPDATED\n", FILE_APPEND);
            } else {
                file_put_contents($debug_file, "RESULT: SKIPPED\n", FILE_APPEND);
            }
        }

        file_put_contents($debug_file, "\n=== FINAL RESULT ===\n", FILE_APPEND);
        file_put_contents($debug_file, "Updated: $updated / " . count($packages) . "\n", FILE_APPEND);
        file_put_contents($debug_file, "Fixed broken images: $fixed_broken\n", FILE_APPEND);
        file_put_contents($debug_file, "Errors: " . count($errors) . "\n", FILE_APPEND);

        error_log("Aurora Package Builder: Fixed $fixed_broken broken images across $updated packages");

>>>>>>> Stashed changes
        return [
            'success' => false,
            'updated' => 0,
            'total' => 0,
            'fixed_broken' => 0,
            'errors' => ['Adding images to packages has been disabled. This functionality is no longer available.'],
        ];
    }

    public function add_images_to_deals()
    {
        $deals = get_posts([
            'post_type' => 'deal',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        $updated = 0;

        foreach ($deals as $deal) {
            $updated_deal = false;

            // 1. Add featured image if missing
            if (!has_post_thumbnail($deal->ID)) {
                // Use placeholders 21-30 for deals (landmarks/promotional)
                $placeholder_num = 21 + ($deal->ID % 10);
                $img_id = $this->generate_single_image_from_placeholder($deal->ID, $deal->post_title, $placeholder_num, 'Featured');

                if ($img_id) {
                    set_post_thumbnail($deal->ID, $img_id);
                    $updated_deal = true;
                }
            }

            // 2. Add banner image if missing (ACF field)
            if (function_exists('get_field') && function_exists('update_field')) {
                $banner = get_field('banner', $deal->ID);
                if (empty($banner)) {
                    // Use placeholders 31-40 for deal banners
                    $banner_placeholder_num = 31 + ($deal->ID % 10);
                    $banner_id = $this->generate_single_image_from_placeholder($deal->ID, $deal->post_title, $banner_placeholder_num, 'Banner');

                    if ($banner_id) {
                        update_field('banner', $banner_id, $deal->ID);
                        $updated_deal = true;
                    }
                }
            }

            if ($updated_deal) {
                $updated++;
            }
        }

        return [
            'success' => true,
            'updated' => $updated,
            'total' => count($deals),
        ];
    }

    /**
     * FASE 8B-2: Add images to Location CPT
     */
    public function add_images_to_locations()
    {
        $locations = get_posts([
            'post_type' => 'location',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        $updated = 0;

        foreach ($locations as $location) {
            $updated_location = false;

            // 1. Add featured image if missing
            if (!has_post_thumbnail($location->ID)) {
                // Use placeholders 11-25 for locations (landmarks/places)
                $placeholder_num = 11 + ($location->ID % 15);
                $img_id = $this->generate_single_image_from_placeholder($location->ID, $location->post_title, $placeholder_num, 'Thumbnail');

                if ($img_id) {
                    set_post_thumbnail($location->ID, $img_id);
                    $updated_location = true;
                }
            }

            // 2. Add location_image if missing (ACF field - different from thumbnail)
            if (function_exists('get_field') && function_exists('update_field')) {
                $location_image = get_field('location_image', $location->ID);
                if (empty($location_image)) {
                    // Use different placeholder range for main image
                    $main_placeholder_num = 26 + ($location->ID % 15);
                    $main_img_id = $this->generate_single_image_from_placeholder($location->ID, $location->post_title, $main_placeholder_num, 'Main Image');

                    if ($main_img_id) {
                        update_field('location_image', $main_img_id, $location->ID);
                        $updated_location = true;
                    }
                }
            }

            if ($updated_location) {
                $updated++;
            }
        }

        return [
            'success' => true,
            'updated' => $updated,
            'total' => count($locations),
        ];
    }

    /**
     * FASE 8B-3: Add images to Guide CPT
     */
    public function add_images_to_guides()
    {
        $guides = get_posts([
            'post_type' => 'guide',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        $updated = 0;

        foreach ($guides as $guide) {
            // Add featured image if missing (guide photo)
            if (!has_post_thumbnail($guide->ID)) {
                // Use placeholders 46-50 for people photos
                $placeholder_num = 46 + ($guide->ID % 5);
                $img_id = $this->generate_single_image_from_placeholder($guide->ID, $guide->post_title, $placeholder_num, 'Guide Photo');

                if ($img_id) {
                    set_post_thumbnail($guide->ID, $img_id);
                    $updated++;
                }
            }
        }

        return [
            'success' => true,
            'updated' => $updated,
            'total' => count($guides),
        ];
    }

    /**
     * FASE 8B-4: Add images to Review CPT
     */
    public function add_images_to_reviews()
    {
        $reviews = get_posts([
            'post_type' => 'review',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        $updated = 0;

        foreach ($reviews as $review) {
            // Add featured image if missing (reviewer photo)
            if (!has_post_thumbnail($review->ID)) {
                // Use placeholders 46-50 for people photos
                $placeholder_num = 46 + ($review->ID % 5);
                $img_id = $this->generate_single_image_from_placeholder($review->ID, $review->post_title, $placeholder_num, 'Reviewer Photo');

                if ($img_id) {
                    set_post_thumbnail($review->ID, $img_id);
                    $updated++;
                }
            }
        }

        return [
            'success' => true,
            'updated' => $updated,
            'total' => count($reviews),
        ];
    }

    /**
     * FASE 8B-5: Add images to Collaborator CPT
     */
    public function add_images_to_collaborators()
    {
        $collaborators = get_posts([
            'post_type' => 'collaborator',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        $updated = 0;

        foreach ($collaborators as $collaborator) {
            // Add featured image if missing (collaborator photo/logo)
            if (!has_post_thumbnail($collaborator->ID)) {
                // Use placeholders 46-50 for people photos
                $placeholder_num = 46 + ($collaborator->ID % 5);
                $img_id = $this->generate_single_image_from_placeholder($collaborator->ID, $collaborator->post_title, $placeholder_num, 'Collaborator Photo');

                if ($img_id) {
                    set_post_thumbnail($collaborator->ID, $img_id);
                    $updated++;
                }
            }
        }

        return [
            'success' => true,
            'updated' => $updated,
            'total' => count($collaborators),
        ];
    }

    /**
     * FASE 8B-6: Add images to Destination CPT
     */
    public function add_images_to_destinations()
    {
        $destinations = get_posts([
            'post_type' => 'destination',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        $updated = 0;

        foreach ($destinations as $destination) {
            // Add featured image if missing
            if (!has_post_thumbnail($destination->ID)) {
                // Use placeholders 11-30 for destinations (landmarks/places)
                $placeholder_num = 11 + ($destination->ID % 20);
                $img_id = $this->generate_single_image_from_placeholder($destination->ID, $destination->post_title, $placeholder_num, 'Destination');

                if ($img_id) {
                    set_post_thumbnail($destination->ID, $img_id);
                    $updated++;
                }
            }
        }

        return [
            'success' => true,
            'updated' => $updated,
            'total' => count($destinations),
        ];
    }

    /**
     * FASE 8C: Add images to taxonomy terms
     * Processes multiple taxonomies with ACF image fields
     */
    public function add_images_to_taxonomy_terms()
    {
        if (!function_exists('get_field') || !function_exists('update_field')) {
            return [
                'success' => false,
                'error' => 'ACF not available',
                'updated' => 0,
                'total' => 0,
            ];
        }

        $updated = 0;
        $total = 0;

        // Taxonomy configuration: taxonomy_name => [fields_to_process]
        $taxonomy_configs = [
            'destinations' => [
                'image' => ['placeholder_range' => [11, 30], 'label' => 'Main Image'],
                'thumbnail' => ['placeholder_range' => [31, 45], 'label' => 'Thumbnail'],
            ],
            'countries' => [
                'banner' => ['placeholder_range' => [11, 25], 'label' => 'Banner'],
                'thumbnail' => ['placeholder_range' => [26, 40], 'label' => 'Thumbnail'],
            ],
            'specialists' => [
                'thumbnail' => ['placeholder_range' => [46, 50], 'label' => 'Photo'],
            ],
            // optional_renting, flights, roles, spot_calendar don't have image fields
        ];

        // Process each configured taxonomy
        foreach ($taxonomy_configs as $taxonomy => $fields) {
            $terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ]);

            if (is_wp_error($terms) || empty($terms)) {
                continue;
            }

            foreach ($terms as $term) {
                $total++;
                $term_updated = false;

                // Process each image field for this term
                foreach ($fields as $field_name => $config) {
                    $current_value = get_field($field_name, $term);

                    // Skip if field already has value
                    if (!empty($current_value)) {
                        continue;
                    }

                    // Generate placeholder number within range
                    list($min, $max) = $config['placeholder_range'];
                    $range = $max - $min + 1;
                    $placeholder_num = $min + ($term->term_id % $range);

                    // Upload image
                    $img_id = $this->generate_single_image_from_placeholder(
                        0, // No post association for taxonomy terms
                        $term->name,
                        $placeholder_num,
                        $taxonomy . ' ' . $config['label']
                    );

                    if ($img_id) {
                        // Update taxonomy term field
                        update_field($field_name, $img_id, $term);
                        $term_updated = true;
                    }
                }

                if ($term_updated) {
                    $updated++;
                }
            }
        }

        return [
            'success' => true,
            'updated' => $updated,
            'total' => $total,
        ];
    }

    /**
     * FASE 1: Generate mock blog posts (without images)
     */
    public function generate_blog_posts($with_images = false)
    {
        $posts = $this->get_sample_blog_posts();
        $created = 0;
        $errors = [];

        foreach ($posts as $post_data) {
            try {
                $post_id = $this->create_blog_post($post_data, $with_images);
                if ($post_id) {
                    $created++;
                }
            } catch (Exception $e) {
                $errors[] = $post_data['title'] . ': ' . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'created' => $created,
            'total' => count($posts),
            'errors' => $errors,
        ];
    }

    /**
     * Create a single blog post
     */
    private function create_blog_post($post_data, $with_images = false)
    {
        // Create post
        $post_id = wp_insert_post([
            'post_title' => $post_data['title'],
            'post_content' => $post_data['content'],
            'post_excerpt' => $post_data['excerpt'],
            'post_type' => 'post',
            'post_status' => $post_data['status'] ?? 'publish',
            'post_author' => get_current_user_id(),
        ]);

        if (is_wp_error($post_id)) {
            throw new Exception($post_id->get_error_message());
        }

        // Add categories
        if (isset($post_data['categories'])) {
            $category_ids = [];
            foreach ($post_data['categories'] as $category_name) {
                $category = get_term_by('name', $category_name, 'category');
                if (!$category) {
                    // Create category if it doesn't exist
                    $result = wp_insert_term($category_name, 'category');
                    if (!is_wp_error($result)) {
                        $category_ids[] = $result['term_id'];
                    }
                } else {
                    $category_ids[] = $category->term_id;
                }
            }
            if (!empty($category_ids)) {
                wp_set_post_categories($post_id, $category_ids);
            }
        }

        // Add tags
        if (isset($post_data['tags'])) {
            wp_set_post_tags($post_id, $post_data['tags'], false);
        }

        // Only add featured image if requested
        if ($with_images) {
            $this->set_featured_image_from_placeholder($post_id, $post_data['title']);
        }

        return $post_id;
    }

    /**
     * FASE 2: Add featured images to existing blog posts
     */
    public function add_images_to_blog_posts()
    {
        $posts = get_posts([
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        $updated = 0;
        $errors = [];

        foreach ($posts as $post) {
            // Skip if already has featured image
            if (has_post_thumbnail($post->ID)) {
                continue;
            }

            $result = $this->set_featured_image_from_placeholder($post->ID, $post->post_title);
            if ($result) {
                $updated++;
            } else {
                $errors[] = $post->post_title;
            }
        }

        return [
            'success' => true,
            'updated' => $updated,
            'total' => count($posts),
            'errors' => $errors,
        ];
    }

    /**
     * Delete all blog posts
     */
    public function delete_all_blog_posts()
    {
        $posts = get_posts([
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        $deleted = 0;
        foreach ($posts as $post) {
            if (wp_delete_post($post->ID, true)) {
                $deleted++;
            }
        }

        return [
            'success' => true,
            'deleted' => $deleted,
        ];
    }

    /**
     * Generate header mock data (ACF Options)
     */
    public function generate_header_data()
    {
        if (!function_exists('update_field')) {
            return [
                'success' => false,
                'message' => 'ACF is not active',
            ];
        }

        // Upload and set logos (white for header, color for aside)
        $logo_white_id = $this->upload_logo_from_plugin('white');
        $logo_color_id = $this->upload_logo_from_plugin('color');

        $logo_message = '';

        if ($logo_white_id && !is_wp_error($logo_white_id)) {
            update_field('header_logo', $logo_white_id, 'option');
            $logo_message .= " Logo white uploaded (ID: $logo_white_id).";
        } else {
            $logo_message .= " Warning: White logo upload failed.";
        }

        if ($logo_color_id && !is_wp_error($logo_color_id)) {
            update_field('aside_logo', $logo_color_id, 'option');
            $logo_message .= " Logo color uploaded (ID: $logo_color_id).";
        } else {
            $logo_message .= " Warning: Color logo upload failed.";
        }

        // Header main data
        update_field('header_phone', '+1-(888)-803-8004', 'option');
        update_field('header_phone_link', '+18888038004', 'option');
        update_field('header_language', 'EN', 'option');
        update_field('header_favorites_url', home_url('/favorites'), 'option');

        // Aside menu data
        update_field('aside_tour_title', 'Tour Packages', 'option');

        // Reviews section
        update_field('aside_reviews_title', 'Hear from travelers', 'option');
        update_field('aside_reviews_text', '+2315 Real stories traveling with us', 'option');

        // FAQs section
        update_field('aside_faqs_title', 'FAQs', 'option');
        update_field('aside_faqs_text', 'Clear, simple answers to help you plan with confidence.', 'option');
        update_field('aside_faqs_url', home_url('/faqs'), 'option');
        update_field('aside_faqs_button_text', 'Get my answers', 'option');

        // Tailor Made Tours section
        update_field('aside_tailor_title', 'Tailor Made Tours', 'option');
        update_field('aside_tailor_text', 'Your trip, your way fully customized to your style, time and interests.', 'option');
        update_field('aside_tailor_url', home_url('/custom-tours'), 'option');
        update_field('aside_tailor_button_text', 'Design my journey', 'option');

        // Favorites section
        update_field('aside_favorites_title', 'Favorites', 'option');
        update_field('aside_favorites_text', 'Here you will find your chosen experiences of Machu Picchu Peru.', 'option');
        update_field('aside_favorites_url', home_url('/favorites'), 'option');
        update_field('aside_favorites_button_text', 'My Favs', 'option');

        // Contact section
        update_field('aside_contact_url', home_url('/contact'), 'option');
        update_field('aside_contact_button_text', 'Contact Us', 'option');

        // Review Badges (from plugin assets)
        $review_badges = $this->upload_review_badges_from_plugin();
        if (!empty($review_badges)) {
            update_field('aside_review_badges', $review_badges, 'option');
            $logo_message .= ' Review badges uploaded.';
        }

        // Social Media Icons (from plugin assets)
        $social_icons = $this->upload_social_icons_from_plugin();
        if (!empty($social_icons)) {
            update_field('aside_social_icons', $social_icons, 'option');
            $logo_message .= ' Social icons uploaded.';
        }

        return [
            'success' => true,
            'message' => 'Header data created successfully.' . $logo_message,
        ];
    }

    /**
     * Generate navigation menus
     */
    public function generate_navigation_menus()
    {
        // Note: create_menu() already handles deletion of existing menus with same name

        // Primary menu location NO LONGER USED - header shows only logo + actions
        // Delete existing primary menu if it exists
        $primary_menu = wp_get_nav_menu_object('Primary Navigation');
        if ($primary_menu) {
            wp_delete_nav_menu($primary_menu->term_id);
        }
        // Unassign primary location
        $locations = get_theme_mod('nav_menu_locations');
        if (isset($locations['primary'])) {
            unset($locations['primary']);
            set_theme_mod('nav_menu_locations', $locations);
        }

        // Secondary menu (main pages menu below header)
        $secondary_menu_id = $this->create_menu('Secondary Menu', 'secondary', [
            'Top Experiences' => home_url('/top-experiences'),
            'Destinations' => home_url('/destinations'),
            'Treks & Adventure' => home_url('/treks-adventure'),
            'Culture & History' => home_url('/culture-history'),
            'Deals' => home_url('/deals'),
        ]);

        // Aside menu (Tour Packages section in mobile menu)
        $aside_menu_id = $this->create_menu('Aside Menu', 'aside', [
            'Top Experiences' => home_url('/top-experiences'),
            'Destinations' => home_url('/destinations'),
            'Treks & Adventure' => home_url('/treks-adventure'),
            'Culture & History' => home_url('/culture-history'),
        ]);

        // Aside secondary menu (footer links in mobile menu)
        $aside_secondary_id = $this->create_menu('Aside Secondary', 'aside-secondary', [
            'Blog' => home_url('/blog'),
            'About Us' => home_url('/about'),
            'Our Team' => home_url('/team'),
            'Travel Guides' => home_url('/guides'),
            'Sustainability' => home_url('/sustainability'),
        ]);

        return [
            'success' => true,
            'primary_menu_id' => 'deleted', // Primary menu not used in this design
            'secondary_menu_id' => $secondary_menu_id,
            'aside_menu_id' => $aside_menu_id,
            'aside_secondary_id' => $aside_secondary_id,
        ];
    }

    /**
     * Helper: Create WordPress menu
     * Automatically deletes existing menu with same name to prevent duplicates
     */
    private function create_menu($menu_name, $location, $items)
    {
        // Check if menu already exists
        $menu_exists = wp_get_nav_menu_object($menu_name);

        if ($menu_exists) {
            // Delete existing menu and all its items
            wp_delete_nav_menu($menu_exists->term_id);
        }

        // Create new menu
        $menu_id = wp_create_nav_menu($menu_name);

        if (is_wp_error($menu_id)) {
            return false;
        }

        // Add menu items
        foreach ($items as $title => $url) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title' => $title,
                'menu-item-url' => $url,
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom',
            ]);
        }

        // Assign menu to location (replaces any previous menu at this location)
        $locations = get_theme_mod('nav_menu_locations');
        if (!is_array($locations)) {
            $locations = [];
        }
        $locations[$location] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);

        return $menu_id;
    }

    /**
     * Generate footer mock data (ACF Options)
     */
    public function generate_footer_data()
    {
        if (!function_exists('update_field')) {
            return [
                'success' => false,
                'message' => 'ACF is not active',
            ];
        }

        // Footer Logo (white logo)
        $logo_white_id = $this->upload_logo_from_plugin('white');
        if ($logo_white_id && !is_wp_error($logo_white_id)) {
            update_field('footer_logo', $logo_white_id, 'option');
            // Also update header_logo to ensure consistency
            update_field('header_logo', $logo_white_id, 'option');
        }

        // Aside Logo (color logo) - needed to avoid deletion when regenerating footer
        $logo_color_id = $this->upload_logo_from_plugin('color');
        if ($logo_color_id && !is_wp_error($logo_color_id)) {
            update_field('aside_logo', $logo_color_id, 'option');
        }

        // Company Information
        update_field('company_name', 'Machu Picchu Peru by Valencia Travel Cusco, Inc.', 'option');
        update_field('company_ruc', '20490568957', 'option');
        update_field('company_address', 'Portal Panes #123 / Centro Comercial Ruiseñores Office #306–307 Cusco — Peru', 'option');

        // Contact Information
        update_field('contact_toll_free', '1-(888)-803-8004', 'option');
        update_field('contact_peru_phone', '+51 84 255907', 'option');
        update_field('contact_phone_24_7_1', '+51 992 236 677', 'option');
        update_field('contact_phone_24_7_2', '+51 979706446', 'option');
        update_field('contact_email', 'info@machupicchuperu.com', 'option');

        // Office Hours
        update_field('office_weekdays', 'Monday through Saturday', 'option');
        update_field('office_morning', '8AM – 1:30PM', 'option');
        update_field('office_afternoon', '3PM – 5:30PM', 'option');
        update_field('office_sunday', 'Sunday 8AM – 1:30PM', 'option');

        // Social Media
        $social_networks = [
            [
                'platform' => 'facebook',
                'url' => 'https://facebook.com/machupicchuperu',
            ],
            [
                'platform' => 'instagram',
                'url' => 'https://instagram.com/machupicchuperu',
            ],
            [
                'platform' => 'pinterest',
                'url' => 'https://pinterest.com/machupicchuperu',
            ],
            [
                'platform' => 'linkedin',
                'url' => 'https://linkedin.com/company/machupicchuperu',
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://youtube.com/@machupicchuperu',
            ],
        ];
        update_field('social_networks', $social_networks, 'option');

        // Review Platforms
        $review_platforms = [
            [
                'platform' => 'tripadvisor',
                'url' => 'https://www.tripadvisor.com/machupicchuperu',
            ],
            [
                'platform' => 'google',
                'url' => 'https://g.page/r/machupicchuperu',
            ],
            [
                'platform' => 'facebook',
                'url' => 'https://facebook.com/machupicchuperu/reviews',
            ],
        ];
        update_field('review_platforms', $review_platforms, 'option');

        // Footer Map Image
        $map_id = $this->upload_image_from_plugin('foot-map-1.png', 'Footer World Map', '_aurora_mock_footer_map');
        if ($map_id && !is_wp_error($map_id)) {
            update_field('footer_map_image', $map_id, 'option');
        }

        // Payment Methods (credit cards)
        $payment_methods = $this->upload_payment_methods_from_plugin();
        if (!empty($payment_methods)) {
            update_field('payment_methods', $payment_methods, 'option');
        }

        // Payment Gateways
        $payment_gateways = [
            [
                'name' => 'Stripe',
                'url' => 'https://stripe.com',
            ],
            [
                'name' => 'Flywire',
                'url' => 'https://flywire.com',
            ],
        ];
        update_field('payment_gateways', $payment_gateways, 'option');

        // Footer Review Logos
        $review_logos = $this->upload_footer_review_logos_from_plugin();
        if (!empty($review_logos)) {
            update_field('footer_review_logos', $review_logos, 'option');
        }

        return [
            'success' => true,
            'message' => 'Footer data created successfully with map image, payment methods, and review logos',
        ];
    }

    /**
     * Generate footer navigation menus
     */
    public function generate_footer_menus()
    {
        // Note: create_menu() already handles deletion of existing menus with same name

        // Top Experiences
        $top_experiences_id = $this->create_menu('Top Experiences', 'footer-top-experiences', [
            'Machu Picchu & Inca Trail' => home_url('/machu-picchu-inca-trail'),
            'Sacred Valley & Cusco' => home_url('/sacred-valley-cusco'),
            'Lake Titicaca' => home_url('/lake-titicaca'),
            'Rainbow Mountain' => home_url('/rainbow-mountain'),
        ]);

        // Treks & Adventure
        $treks_id = $this->create_menu('Treks & Adventure', 'footer-treks-adventure', [
            'Classic Inca Trail' => home_url('/classic-inca-trail'),
            'Alternative Treks' => home_url('/alternative-treks'),
            'Day Hikes' => home_url('/day-hikes'),
            'Multi-day Expeditions' => home_url('/multi-day-expeditions'),
        ]);

        // Culture & History
        $culture_id = $this->create_menu('Culture & History', 'footer-culture-history', [
            'Cusco Tours' => home_url('/cusco-tours'),
            'Day Tours' => home_url('/day-tours'),
            'Living Culture' => home_url('/living-culture'),
        ]);

        // Destinations
        $destinations_id = $this->create_menu('Destinations', 'footer-destinations', [
            'Cusco' => home_url('/cusco'),
            'Lima' => home_url('/lima'),
            'Arequipa' => home_url('/arequipa'),
            'Puno' => home_url('/puno'),
            'Puerto Maldonado' => home_url('/puerto-maldonado'),
        ]);

        // About Machu Picchu Peru
        $about_id = $this->create_menu('About Machu Picchu Peru', 'footer-about', [
            'About Us' => home_url('/about'),
            'Our Team' => home_url('/team'),
            'Blog' => home_url('/blog'),
            'Sustainability' => home_url('/sustainability'),
            'Agents community' => home_url('/agents'),
        ]);

        // Extra Information
        $extra_id = $this->create_menu('Extra Information', 'footer-extra-info', [
            'Tailor-Made Tours' => home_url('/custom-tours'),
            'Inca Trail permits guide' => home_url('/inca-trail-permits'),
            'Packing List' => home_url('/packing-list'),
            'FAQs' => home_url('/faqs'),
        ]);

        return [
            'success' => true,
            'top_experiences' => $top_experiences_id,
            'treks' => $treks_id,
            'culture' => $culture_id,
            'destinations' => $destinations_id,
            'about' => $about_id,
            'extra' => $extra_id,
        ];
    }

    /**
     * Upload logo from plugin assets to WordPress media library
     * @param string $type 'white' or 'color'
     * @return int|false Attachment ID on success, false on failure
     */
    private function upload_logo_from_plugin($type = 'white')
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Determine which logo to upload
        if ($type === 'color') {
            $source_filename = 'logo-color.png';
            $upload_filename = 'machu-picchu-peru-logo-color.png';
            $title = 'Machu Picchu Peru Logo (Color)';
            $meta_key = '_aurora_mock_logo_color';
        } else {
            $source_filename = 'logo.png';
            $upload_filename = 'machu-picchu-peru-logo.png';
            $title = 'Machu Picchu Peru Logo';
            $meta_key = '_aurora_mock_logo';
        }

        // Path to logo in plugin
        $logo_path = plugin_dir_path(dirname(__FILE__)) . 'assets/images/' . $source_filename;

        if (!file_exists($logo_path)) {
            error_log("Aurora Package Builder: Logo file not found at $logo_path");
            return false;
        }

        // Check if logo already exists in media library
        $existing_logo = get_posts([
            'post_type' => 'attachment',
            'meta_query' => [
                [
                    'key' => $meta_key,
                    'value' => '1',
                ],
            ],
            'posts_per_page' => 1,
        ]);

        // If logo already exists, delete it to upload fresh one
        if (!empty($existing_logo)) {
            wp_delete_attachment($existing_logo[0]->ID, true);
        }

        // Prepare file for upload
        $filetype = wp_check_filetype(basename($logo_path), null);
        $upload_dir = wp_upload_dir();

        // Copy file to uploads directory with unique name
        $filename = $upload_filename;
        $new_file = $upload_dir['path'] . '/' . $filename;

        // If file exists, delete it first
        if (file_exists($new_file)) {
            unlink($new_file);
        }

        if (!copy($logo_path, $new_file)) {
            error_log('Aurora Package Builder: Failed to copy logo to uploads directory');
            return false;
        }

        // Create attachment
        $attachment = [
            'guid' => $upload_dir['url'] . '/' . basename($new_file),
            'post_mime_type' => $filetype['type'],
            'post_title' => $title,
            'post_content' => '',
            'post_status' => 'inherit',
        ];

        $attach_id = wp_insert_attachment($attachment, $new_file);

        if (is_wp_error($attach_id)) {
            error_log("Aurora Package Builder: Failed to create attachment: " . $attach_id->get_error_message());
            return false;
        }

        // Generate attachment metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $new_file);
        wp_update_attachment_metadata($attach_id, $attach_data);

        // Mark this as our mock logo for easy identification/cleanup
        update_post_meta($attach_id, $meta_key, '1');

        return $attach_id;
    }

    /**
     * Upload review badges from plugin assets and return ACF repeater data
     * @return array ACF repeater data
     */
    private function upload_review_badges_from_plugin()
    {
        $badges = [
            [
                'filename' => 'tripadvisor-reviews.svg-1.png',
                'title' => 'TripAdvisor Reviews Badge',
                'url' => 'https://www.tripadvisor.com/machupicchuperu',
            ],
            [
                'filename' => 'facebook-reviews.svg_.png',
                'title' => 'Facebook Reviews Badge',
                'url' => 'https://facebook.com/machupicchuperu/reviews',
            ],
            [
                'filename' => 'Google.png',
                'title' => 'Google Reviews Badge',
                'url' => 'https://g.page/r/machupicchuperu',
            ],
        ];

        $repeater_data = [];

        foreach ($badges as $badge) {
            $attach_id = $this->upload_image_from_plugin($badge['filename'], $badge['title'], '_aurora_mock_review_badge');

            if ($attach_id && !is_wp_error($attach_id)) {
                $repeater_data[] = [
                    'badge_image' => $attach_id, // Save attachment ID, not URL (ACF will convert to URL with return_format)
                    'badge_url' => $badge['url'],
                ];
            }
        }

        return $repeater_data;
    }

    /**
     * Upload social icons from plugin assets and return ACF repeater data
     * @return array ACF repeater data
     */
    private function upload_social_icons_from_plugin()
    {
        $icons = [
            [
                'filename' => 'Facebook-1.png',
                'type' => 'facebook',
                'url' => 'https://facebook.com/machupicchuperu',
            ],
            [
                'filename' => 'Instagram-1.png',
                'type' => 'instagram',
                'url' => 'https://instagram.com/machupicchuperu',
            ],
            [
                'filename' => 'Pinteres.png',
                'type' => 'pinterest',
                'url' => 'https://pinterest.com/machupicchuperu',
            ],
            [
                'filename' => 'Youtube.png',
                'type' => 'youtube',
                'url' => 'https://youtube.com/@machupicchuperu',
            ],
            [
                'filename' => 'Tiktop.png',
                'type' => 'tiktok',
                'url' => 'https://tiktok.com/@machupicchuperu',
            ],
        ];

        $repeater_data = [];

        foreach ($icons as $icon) {
            $repeater_data[] = [
                'icon_type' => $icon['type'],
                'icon_url' => $icon['url'],
            ];
        }

        return $repeater_data;
    }

    /**
     * Upload payment method cards from plugin assets and return ACF repeater data
     * @return array ACF repeater data
     */
    private function upload_payment_methods_from_plugin()
    {
        $cards = [
            [
                'filename' => 'Visa.png',
                'name' => 'Visa',
            ],
            [
                'filename' => 'Mastercard.png',
                'name' => 'Mastercard',
            ],
            [
                'filename' => 'American-Express.png',
                'name' => 'American Express',
            ],
            [
                'filename' => 'Discover.png',
                'name' => 'Discover',
            ],
            [
                'filename' => 'Diners-Club.png',
                'name' => 'Diners Club',
            ],
            [
                'filename' => 'Japan-Credit-Bureau.png',
                'name' => 'JCB',
            ],
        ];

        $repeater_data = [];

        foreach ($cards as $card) {
            $attach_id = $this->upload_image_from_plugin($card['filename'], $card['name'] . ' Card Logo', '_aurora_mock_payment_card');

            if ($attach_id && !is_wp_error($attach_id)) {
                // For ACF repeater image fields, save the attachment ID
                // ACF will convert to URL based on return_format when retrieved
                $repeater_data[] = [
                    'image' => $attach_id,
                    'name' => $card['name'],
                ];
            }
        }

        return $repeater_data;
    }

    /**
     * Upload footer review logos from plugin assets and return ACF repeater data
     * @return array ACF repeater data
     */
    private function upload_footer_review_logos_from_plugin()
    {
        $logos = [
            [
                'filename' => 'Group-1075.png',
                'title' => 'TripAdvisor Reviews',
                'url' => 'https://www.tripadvisor.com/machupicchuperu',
            ],
            [
                'filename' => 'Group-1068.png',
                'title' => 'Google Reviews',
                'url' => 'https://g.page/r/machupicchuperu',
            ],
            [
                'filename' => 'Group-1078.png',
                'title' => 'Facebook Reviews',
                'url' => 'https://facebook.com/machupicchuperu/reviews',
            ],
        ];

        $repeater_data = [];

        foreach ($logos as $logo) {
            $attach_id = $this->upload_image_from_plugin($logo['filename'], $logo['title'], '_aurora_mock_review_logo');

            if ($attach_id && !is_wp_error($attach_id)) {
                $repeater_data[] = [
                    'logo_image' => $attach_id,
                    'logo_url' => $logo['url'],
                ];
            }
        }

        return $repeater_data;
    }

    /**
     * Generic method to upload image from plugin assets
     * @param string $filename Source filename in plugin assets/images
     * @param string $title Attachment title
     * @param string $meta_key Meta key for identification
     * @return int|false Attachment ID on success, false on failure
     */
    private function upload_image_from_plugin($filename, $title, $meta_key)
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Path to image in plugin
        $image_path = plugin_dir_path(dirname(__FILE__)) . 'assets/images/' . $filename;

        if (!file_exists($image_path)) {
            error_log("Aurora Package Builder: Image file not found at $image_path");
            return false;
        }

        // Check if image already exists in media library
        $existing_image = get_posts([
            'post_type' => 'attachment',
            'meta_query' => [
                [
                    'key' => $meta_key . '_' . $filename,
                    'value' => '1',
                ],
            ],
            'posts_per_page' => 1,
        ]);

        // If image already exists, delete it to upload fresh one
        if (!empty($existing_image)) {
            wp_delete_attachment($existing_image[0]->ID, true);
        }

        // Prepare file for upload
        $filetype = wp_check_filetype(basename($image_path), null);
        $upload_dir = wp_upload_dir();

        // Copy file to uploads directory
        $new_file = $upload_dir['path'] . '/' . $filename;

        // If file exists, delete it first
        if (file_exists($new_file)) {
            unlink($new_file);
        }

        if (!copy($image_path, $new_file)) {
            error_log("Aurora Package Builder: Failed to copy $filename to uploads directory");
            return false;
        }

        // Create attachment
        $attachment = [
            'guid' => $upload_dir['url'] . '/' . basename($new_file),
            'post_mime_type' => $filetype['type'],
            'post_title' => $title,
            'post_content' => '',
            'post_status' => 'inherit',
        ];

        $attach_id = wp_insert_attachment($attachment, $new_file);

        if (is_wp_error($attach_id)) {
            error_log("Aurora Package Builder: Failed to create attachment for $filename: " . $attach_id->get_error_message());
            return false;
        }

        // Generate attachment metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $new_file);
        wp_update_attachment_metadata($attach_id, $attach_data);

        // Mark this with unique meta key
        update_post_meta($attach_id, $meta_key . '_' . $filename, '1');

        return $attach_id;
    }
}
