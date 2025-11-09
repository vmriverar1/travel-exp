<?php
/**
 * FASE 9A: Aurora Wizard Processor
 *
 * Handles batch processing for the interactive mock data wizard
 * Processes data in small batches via AJAX to avoid timeouts
 * Supports checkpoints for pause/resume functionality
 *
 * @package Travel_Package_Wizard
 * @since FASE 9A
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aurora_Wizard_Processor {

    /**
     * Generator instance
     * @var Aurora_Mock_Data_Generator
     */
    private $generator;

    /**
     * Batch sizes for each step
     * @var array
     */
    private $batch_sizes = [
        1 => 20, // Step 1: 20 taxonomy terms per batch
        2 => 10, // Step 2: 10 CPT posts per batch (locations, deals, guides, reviews, collaborators)
        3 => 10, // Step 3: 10 CPT images per batch
        4 => 5,  // Step 4: 5 packages content per batch
        5 => 3,  // Step 5: 3 packages images per batch (heavy)
        6 => 1,  // Step 6: Single execution (finalization)
    ];

    /**
     * Constructor
     */
    public function __construct() {
        $this->generator = Aurora_Mock_Data_Generator::get_instance();
    }

    /**
     * Clean up all mock data created by the wizard
     *
     * @return array Result with counts of deleted items
     */
    public function cleanup_all_mock_data() {
        $results = [
            'packages' => 0,
            'deals' => 0,
            'locations' => 0,
            'guides' => 0,
            'reviews' => 0,
            'collaborators' => 0,
            'attachments' => 0,
            'terms' => 0
        ];

        try {
            // Delete all packages
            $packages = get_posts([
                'post_type' => 'package',
                'posts_per_page' => -1,
                'post_status' => 'any',
                'fields' => 'ids'
            ]);
            foreach ($packages as $post_id) {
                wp_delete_post($post_id, true);
                $results['packages']++;
            }

            // Delete CPTs
            $cpt_types = ['deal', 'location', 'guide', 'review', 'collaborator'];
            foreach ($cpt_types as $cpt) {
                $posts = get_posts([
                    'post_type' => $cpt,
                    'posts_per_page' => -1,
                    'post_status' => 'any',
                    'fields' => 'ids'
                ]);
                foreach ($posts as $post_id) {
                    wp_delete_post($post_id, true);
                    $results[$cpt . 's']++;
                }
            }

            // Delete all attachments (images) - DISABLED: Keep existing images to avoid re-uploading
            // REASON: We are now using real images that shouldn't be deleted
            /*
            $attachments = get_posts([
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_status' => 'any',
                'fields' => 'ids'
            ]);
            foreach ($attachments as $attachment_id) {
                wp_delete_attachment($attachment_id, true);
                $results['attachments']++;
            }
            */
            $results['attachments'] = 0; // No images deleted

            // Delete taxonomy terms
            $taxonomies = ['package_category', 'package_tag', 'difficulty', 'region'];
            foreach ($taxonomies as $taxonomy) {
                if (taxonomy_exists($taxonomy)) {
                    $terms = get_terms([
                        'taxonomy' => $taxonomy,
                        'hide_empty' => false,
                        'fields' => 'ids'
                    ]);
                    if (!is_wp_error($terms)) {
                        foreach ($terms as $term_id) {
                            wp_delete_term($term_id, $taxonomy);
                            $results['terms']++;
                        }
                    }
                }
            }

            return [
                'success' => true,
                'results' => $results,
                'message' => 'All mock data cleaned successfully'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process a specific batch of a step
     *
     * @param int $step Step number (1-6)
     * @param int $batch Batch number (0-based)
     * @param array $checkpoint_data Optional checkpoint data from previous runs
     * @return array Result with status, progress, and data
     */
    public function process_batch($step, $batch, $checkpoint_data = []) {
        // Validate step
        if ($step < 1 || $step > 6) {
            return [
                'success' => false,
                'error' => 'Invalid step number. Must be between 1 and 6.'
            ];
        }

        // Call step-specific method
        $method = "process_step_{$step}_batch";

        if (!method_exists($this, $method)) {
            return [
                'success' => false,
                'error' => "Step method {$method} not found."
            ];
        }

        try {
            $result = $this->$method($batch, $checkpoint_data);
            return array_merge(['success' => true], $result);
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * STEP 1: Taxonomy Terms Creation (Batch)
     * Creates taxonomy terms and adds images
     * (This was OLD Step 5)
     *
     * @param int $batch Batch number
     * @param array $checkpoint_data Checkpoint data
     * @return array Result
     */
    private function process_step_1_batch($batch, $checkpoint_data) {
        if (!function_exists('get_field') || !function_exists('update_field')) {
            return [
                'step_finished' => true,
                'message' => 'ACF not available for taxonomies',
                'updated_count' => 0
            ];
        }

        $batch_size = $this->batch_sizes[1];
        $offset = $batch * $batch_size;

        // Taxonomies to process
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
        ];

        // Get all terms from all configured taxonomies
        $all_terms = [];
        foreach ($taxonomy_configs as $taxonomy => $fields) {
            $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
            if (!is_wp_error($terms) && !empty($terms)) {
                foreach ($terms as $term) {
                    $term->taxonomy_type = $taxonomy;
                    $term->fields_config = $fields;
                    $all_terms[] = $term;
                }
            }
        }

        $total_terms = count($all_terms);
        $batch_terms = array_slice($all_terms, $offset, $batch_size);

        // Check if finished
        if (empty($batch_terms)) {
            return [
                'step_finished' => true,
                'message' => 'All taxonomy images added',
                'updated_count' => 0,
                'progress' => [
                    'current' => $total_terms,
                    'total' => $total_terms,
                    'percentage' => 100
                ]
            ];
        }

        // Process this batch
        $updated = [];
        $errors = [];

        foreach ($batch_terms as $term) {
            $term_updated = false;
            $images_added = 0;

            foreach ($term->fields_config as $field_name => $config) {
                $current_value = get_field($field_name, $term);

                if (!empty($current_value)) {
                    continue;
                }

                list($min, $max) = $config['placeholder_range'];
                $range = $max - $min + 1;
                $placeholder_num = $min + ($term->term_id % $range);

                $img_id = $this->generator->generate_single_image_from_placeholder(
                    0,
                    $term->name,
                    $placeholder_num,
                    $term->taxonomy_type . ' ' . $config['label']
                );

                if ($img_id) {
                    update_field($field_name, $img_id, $term);
                    $term_updated = true;
                    $images_added++;
                }
            }

            if ($term_updated) {
                $updated[] = [
                    'id' => $term->term_id,
                    'title' => $term->name,
                    'taxonomy' => $term->taxonomy_type,
                    'images_added' => $images_added
                ];
            }
        }

        $current_total = $offset + count($updated);
        $percentage = $total_terms > 0 ? round(($current_total / $total_terms) * 100) : 100;

        return [
            'step_finished' => false,
            'updated_count' => count($updated),
            'updated_items' => $updated,
            'errors' => $errors,
            'message' => 'Taxonomy images in progress',
            'progress' => [
                'current' => $current_total,
                'total' => $total_terms,
                'percentage' => $percentage
            ]
        ];
    }

    /**
     * STEP 2: CPT Creation (Batch)
     * Creates CPTs: locations, deals, guides, reviews, collaborators
     * (This was OLD Step 1)
     *
     * @param int $batch Batch number
     * @param array $checkpoint_data Checkpoint data
     * @return array Result
     */
    private function process_step_2_batch($batch, $checkpoint_data) {
        // CPT sequence
        $cpt_sequence = ['deal', 'location', 'guide', 'review', 'collaborator'];
        $current_cpt_index = isset($checkpoint_data['current_cpt_index']) ? intval($checkpoint_data['current_cpt_index']) : 0;
        $current_cpt_batch = isset($checkpoint_data['current_cpt_batch']) ? intval($checkpoint_data['current_cpt_batch']) : 0;

        // Check if all CPTs are done
        if ($current_cpt_index >= count($cpt_sequence)) {
            return [
                'step_finished' => true,
                'message' => 'All CPTs created',
                'created_count' => 0
            ];
        }

        $current_cpt = $cpt_sequence[$current_cpt_index];

        // Process batch for current CPT
        $result = $this->create_cpt_batch($current_cpt, $current_cpt_batch);

        // Update checkpoint data
        if ($result['cpt_finished']) {
            $current_cpt_index++;
            $current_cpt_batch = 0;
        } else {
            $current_cpt_batch++;
        }

        // Determine if entire step is finished (all CPTs processed)
        $all_cpts_finished = $current_cpt_index >= count($cpt_sequence);

        return array_merge($result, [
            'step_finished' => $all_cpts_finished, // Step only finishes when ALL CPTs are done
            'checkpoint_data' => [
                'current_cpt_index' => $current_cpt_index,
                'current_cpt_batch' => $current_cpt_batch,
                'current_cpt' => $current_cpt_index < count($cpt_sequence) ? $cpt_sequence[$current_cpt_index] : null
            ]
        ]);
    }

    /**
     * STEP 3: CPT Images (Batch)
     * Adds images to all other CPTs
     * (This was OLD Step 2)
     *
     * @param int $batch Batch number
     * @param array $checkpoint_data Checkpoint data
     * @return array Result
     */
    private function process_step_3_batch($batch, $checkpoint_data) {
        // CPT sequence (same as step 2)
        $cpt_sequence = ['deal', 'location', 'guide', 'review', 'collaborator'];
        $current_cpt_index = isset($checkpoint_data['current_cpt_index']) ? intval($checkpoint_data['current_cpt_index']) : 0;
        $current_cpt_batch = isset($checkpoint_data['current_cpt_batch']) ? intval($checkpoint_data['current_cpt_batch']) : 0;

        // Check if all CPTs are done
        if ($current_cpt_index >= count($cpt_sequence)) {
            return [
                'step_finished' => true,
                'message' => 'All CPT images added',
                'updated_count' => 0
            ];
        }

        $current_cpt = $cpt_sequence[$current_cpt_index];

        // Process batch for current CPT
        $result = $this->add_images_to_cpt_batch($current_cpt, $current_cpt_batch);

        // Update checkpoint data
        if ($result['cpt_finished']) {
            $current_cpt_index++;
            $current_cpt_batch = 0;
        } else {
            $current_cpt_batch++;
        }

        // Determine if entire step is finished (all CPTs processed)
        $all_cpts_finished = $current_cpt_index >= count($cpt_sequence);

        return array_merge($result, [
            'step_finished' => $all_cpts_finished, // Step only finishes when ALL CPTs are done
            'checkpoint_data' => [
                'current_cpt_index' => $current_cpt_index,
                'current_cpt_batch' => $current_cpt_batch,
                'current_cpt' => $current_cpt_index < count($cpt_sequence) ? $cpt_sequence[$current_cpt_index] : null
            ]
        ]);
    }

    /**
     * STEP 4: Package Content Creation (Batch)
     * Creates packages with title, content, and ACF fields (no images yet)
     * (This was OLD Step 3)
     *
     * @param int $batch Batch number
     * @param array $checkpoint_data Checkpoint data
     * @return array Result
     */
    private function process_step_4_batch($batch, $checkpoint_data) {
        $batch_size = $this->batch_sizes[4];
        $offset = $batch * $batch_size;

        // Load packages data source
        $data_file = plugin_dir_path(__FILE__) . 'mock-packages-data.php';

        if (!file_exists($data_file)) {
            return [
                'step_finished' => true,
                'error' => 'Packages data file not found',
                'created_count' => 0
            ];
        }

        $packages_data = include $data_file;
        $total_packages = count($packages_data);
        $batch_data = array_slice($packages_data, $offset, $batch_size);

        // Check if this step is complete
        if (empty($batch_data)) {
            return [
                'step_finished' => true,
                'message' => 'All packages created',
                'created_count' => 0,
                'progress' => [
                    'current' => $total_packages,
                    'total' => $total_packages,
                    'percentage' => 100
                ]
            ];
        }

        // Create packages in this batch
        $created = [];
        $errors = [];

        foreach ($batch_data as $package_data) {
            $result = $this->create_single_package_content($package_data);

            if ($result['success']) {
                $created[] = [
                    'id' => $result['post_id'],
                    'title' => $package_data['title']
                ];
            } else {
                $errors[] = [
                    'title' => $package_data['title'],
                    'error' => $result['error']
                ];
            }
        }

        $current_total = $offset + count($created);
        $percentage = round(($current_total / $total_packages) * 100);

        return [
            'step_finished' => false,
            'created_count' => count($created),
            'created_items' => $created,
            'errors' => $errors,
            'next_batch' => $batch + 1,
            'progress' => [
                'current' => $current_total,
                'total' => $total_packages,
                'percentage' => $percentage
            ]
        ];
    }

    /**
     * STEP 5: Package Images (Batch)
     * Adds images to existing packages
     * (This was OLD Step 4)
     *
     * @param int $batch Batch number
     * @param array $checkpoint_data Checkpoint data
     * @return array Result
     */
    private function process_step_5_batch($batch, $checkpoint_data) {
        $batch_size = $this->batch_sizes[5];
        $offset = $batch * $batch_size;

        // Get packages that need images
        $packages = get_posts([
            'post_type' => 'package',
            'posts_per_page' => $batch_size,
            'offset' => $offset,
            'post_status' => 'publish',
            'orderby' => 'ID',
            'order' => 'ASC'
        ]);

        $total_packages = wp_count_posts('package')->publish;

        // Check if step is complete
        if (empty($packages)) {
            return [
                'step_finished' => true,
                'message' => 'All package images added',
                'updated_count' => 0,
                'progress' => [
                    'current' => $total_packages,
                    'total' => $total_packages,
                    'percentage' => 100
                ]
            ];
        }

        // Process images for this batch
        $updated = [];
        $errors = [];

        foreach ($packages as $package) {
            $result = $this->add_images_to_single_package($package->ID);

            if ($result['success']) {
                $updated[] = [
                    'id' => $package->ID,
                    'title' => $package->post_title,
                    'images_added' => $result['images_count']
                ];
            } else {
                $errors[] = [
                    'id' => $package->ID,
                    'title' => $package->post_title,
                    'error' => $result['error']
                ];
            }
        }

        $current_total = $offset + count($updated);
        $percentage = round(($current_total / $total_packages) * 100);

        return [
            'step_finished' => false,
            'updated_count' => count($updated),
            'updated_items' => $updated,
            'errors' => $errors,
            'next_batch' => $batch + 1,
            'progress' => [
                'current' => $current_total,
                'total' => $total_packages,
                'percentage' => $percentage
            ]
        ];
    }

    /**
     * STEP 6: Finalization (Single execution)
     * Cleanup, flush rules, generate statistics
     *
     * @param int $batch Batch number
     * @param array $checkpoint_data Checkpoint data
     * @return array Result
     */
    private function process_step_6_batch($batch, $checkpoint_data) {
        // Cleanup wizard checkpoints
        delete_transient('aurora_wizard_checkpoint');

        // Flush rewrite rules
        flush_rewrite_rules();

        // Get final statistics
        $stats = $this->get_final_statistics();

        return [
            'step_finished' => true,
            'message' => 'Mock data generation completed successfully!',
            'statistics' => $stats
        ];
    }

    /**
     * Helper: Create single package content (no images)
     *
     * @param array $package_data Package data from source
     * @return array Result with post_id or error
     */
    private function create_single_package_content($package_data) {
        // Extract data from nested structure
        $data = $package_data['data'] ?? $package_data;

        // Create post
        $post_id = wp_insert_post([
            'post_title' => $package_data['title'],
            'post_excerpt' => $data['summary'] ?? '',
            'post_content' => $data['description'] ?? '',
            'post_type' => 'package',
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            return [
                'success' => false,
                'error' => $post_id->get_error_message()
            ];
        }

        // Add ACF fields (without images)
        if (function_exists('update_field')) {
            // PackageGeneral fields
            update_field('service_type', $data['service_type'] ?? 'shared', $post_id);
            update_field('rating', $data['rating'] ?? 4.5, $post_id);
            update_field('stars', $data['stars'] ?? 5, $post_id);
            update_field('summary', $data['summary'] ?? '', $post_id);
            update_field('description', $data['description'] ?? '', $post_id);
            update_field('included', $data['included'] ?? '', $post_id);
            update_field('not_included', $data['not_included'] ?? '', $post_id);
            update_field('video_url', $data['video_url'] ?? '', $post_id);

            // PackageBaseInfo fields
            update_field('duration', $data['duration'] ?? '', $post_id);
            update_field('group_size', $data['group_size'] ?? 12, $post_id);
            update_field('departure', $data['departure'] ?? '', $post_id);
            update_field('arrival', $data['arrival'] ?? '', $post_id);
            update_field('departure_time', $data['departure_time'] ?? '', $post_id);
            update_field('return_time', $data['return_time'] ?? '', $post_id);
            update_field('activity_level', $data['activity_level'] ?? 'moderate', $post_id);
            update_field('altitude', $data['altitude'] ?? '', $post_id);
            update_field('days', $data['days'] ?? 1, $post_id);
            update_field('physical_difficulty', $data['physical_difficulty'] ?? 'easy', $post_id);

            // FASE 1: Ratings & Reviews
            update_field('tripadvisor_rating', $data['tripadvisor_rating'] ?? 0, $post_id);
            update_field('tripadvisor_url', $data['tripadvisor_url'] ?? '', $post_id);
            update_field('total_reviews', $data['total_reviews'] ?? 0, $post_id);

            // FASE 1: Difficulty & Culture
            update_field('cultural_rating', $data['cultural_rating'] ?? 3, $post_id);
            update_field('wildlife_expectation', $data['wildlife_expectation'] ?? 2, $post_id);

            // FASE 1: Pricing
            update_field('price_from', $data['price_from'] ?? $data['price_normal'] ?? 0, $post_id);
            update_field('price_normal', $data['price_normal'] ?? 0, $post_id);
            update_field('price_offer', $data['price_offer'] ?? 0, $post_id);
            update_field('price_per_person', $data['per_person'] ?? 1, $post_id);

            // FASE 1: Promotions
            update_field('promo_enabled', $data['active_promotion'] ?? 0, $post_id);
            update_field('promo_tag', $data['promo_tag'] ?? '', $post_id);
            update_field('promo_tag_color', $data['promo_color'] ?? '#ff6b6b', $post_id);

            // FASE 1: SEO
            update_field('seo_title', $data['meta_title'] ?? '', $post_id);
            update_field('seo_description', $data['meta_description'] ?? '', $post_id);
            update_field('seo_keywords', $data['focus_keyword'] ?? '', $post_id);

            // FASE 1: Display Options
            update_field('show_on_homepage', $data['show_on_homepage'] ?? 0, $post_id);
            update_field('featured_package', $data['featured_package'] ?? 0, $post_id);

            // FASE 2: Section Titles (defaults in Spanish)
            update_field('title_overview', $data['title_overview'] ?? 'Descripción General', $post_id);
            update_field('title_itinerary', $data['title_itinerary'] ?? 'Itinerario', $post_id);
            update_field('title_dates', $data['title_dates'] ?? 'Fechas y Precios', $post_id);
            update_field('title_included', $data['title_included'] ?? 'Qué Incluye', $post_id);
            update_field('title_optional_act', $data['title_optional_act'] ?? 'Actividades Opcionales', $post_id);
            update_field('title_additional_info', $data['title_additional_info'] ?? 'Información Adicional', $post_id);

            // FASE 2: Order (use post ID as default order)
            update_field('order', $data['order'] ?? $post_id, $post_id);

            // FASE 2: Availability (map from spaces_available string to select value)
            $availability_map = [
                'daily' => 'daily',
                'on_request' => 'on_request',
                'seasonal' => 'seasonal',
            ];
            $spaces = $data['spaces_available'] ?? 'daily';
            update_field('availability', $availability_map[$spaces] ?? 'on_request', $post_id);

            // FASE 2: Smart boolean detection
            $is_inca_trail = (stripos($package_data['title'], 'inca trail') !== false);
            update_field('incatrail', $is_inca_trail ? 1 : 0, $post_id);

            $is_luxury = (isset($data['price_normal']) && $data['price_normal'] > 1000);
            update_field('luxury', $is_luxury ? 1 : 0, $post_id);

            // FASE 2: Additional display options with smart defaults
            update_field('tagline', $data['tagline'] ?? '', $post_id);
            update_field('google_rating', $data['google_rating'] ?? 0, $post_id);
            update_field('show_rating_badge', $data['show_rating_badge'] ?? 1, $post_id);
            update_field('video_label', $data['video_label'] ?? '', $post_id);
            update_field('show_reserve_later', $data['show_reserve_later'] ?? 1, $post_id);
            update_field('show_international_standards', $data['show_international_standards'] ?? 1, $post_id);

            // FASE 7: Additional fields from enriched mock data
            update_field('video_url', $data['video_url'] ?? '', $post_id);
            update_field('is_prepayment', $data['is_prepayment'] ?? 0, $post_id);
            update_field('optional', $data['optional'] ?? 0, $post_id);
            update_field('travel_zoo', $data['travel_zoo'] ?? 0, $post_id);
            update_field('show_specialist', $data['show_specialist'] ?? 1, $post_id);
            update_field('recommendations', $data['recommendations'] ?? '', $post_id);
            update_field('price_single_supplement', $data['price_single_supplement'] ?? 0, $post_id);
            update_field('price_child', $data['price_child'] ?? 0, $post_id);
            update_field('available_months', $data['available_months'] ?? [], $post_id);
            update_field('calendar_enabled', $data['calendar_enabled'] ?? 0, $post_id);
            update_field('important_notes', $data['important_notes'] ?? '', $post_id);
            update_field('terms', $data['terms'] ?? '', $post_id);
            update_field('seo_canonical', $data['seo_canonical'] ?? '', $post_id);
            update_field('seo_robots', $data['seo_robots'] ?? 'index, follow', $post_id);
            update_field('seo_schema', $data['seo_schema'] ?? 'TouristAttraction', $post_id);

            // Taxonomies (use names, WordPress will create terms if they don't exist)
            // Taxonomies are in top-level, not inside 'data'
            $taxonomies = $package_data['taxonomies'] ?? [];

            // Core taxonomies
            if (!empty($taxonomies['package_type'])) {
                wp_set_object_terms($post_id, $taxonomies['package_type'], 'package_type');
            }
            if (!empty($taxonomies['interest'])) {
                wp_set_object_terms($post_id, $taxonomies['interest'], 'interest');
            }
            if (!empty($taxonomies['destination'])) {
                wp_set_object_terms($post_id, $taxonomies['destination'], 'destinations');
            }

            // FASE 3: Map type_service to both taxonomies (keep original + add to included_services)
            if (!empty($taxonomies['type_service'])) {
                wp_set_object_terms($post_id, $taxonomies['type_service'], 'type_service');
                wp_set_object_terms($post_id, $taxonomies['type_service'], 'included_services');
            }

            // FASE 3: Activity taxonomy
            if (!empty($taxonomies['activity'])) {
                wp_set_object_terms($post_id, $taxonomies['activity'], 'activity');
            }

            // FASE 3: Auto-assign specialist based on destination or package title
            if (!empty($taxonomies['destination']) && is_array($taxonomies['destination'])) {
                $main_destination = $taxonomies['destination'][0];
                $specialist = $main_destination . ' Expert';
                wp_set_object_terms($post_id, $specialist, 'specialists');
            } else {
                // Fallback: detect from title
                $title_lower = strtolower($package_data['title']);
                if (stripos($title_lower, 'machu picchu') !== false || stripos($title_lower, 'inca trail') !== false) {
                    wp_set_object_terms($post_id, 'Machu Picchu Expert', 'specialists');
                } elseif (stripos($title_lower, 'cusco') !== false) {
                    wp_set_object_terms($post_id, 'Cusco Expert', 'specialists');
                } elseif (stripos($title_lower, 'sacred valley') !== false) {
                    wp_set_object_terms($post_id, 'Sacred Valley Expert', 'specialists');
                }
            }

            // FASE 3: Optional renting (check both activity taxonomy and package_type)
            $needs_rental = false;
            if (!empty($taxonomies['activity'])) {
                $rentable_activities = ['Trekking', 'Hiking', 'Mountain Biking', 'Adventure'];
                $activities = is_array($taxonomies['activity']) ? $taxonomies['activity'] : [$taxonomies['activity']];
                foreach ($activities as $activity) {
                    if (in_array($activity, $rentable_activities)) {
                        $needs_rental = true;
                        break;
                    }
                }
            }
            // Also check package_type for trekking/hiking indicators
            if (!$needs_rental && !empty($taxonomies['package_type'])) {
                $pkg_types = is_array($taxonomies['package_type']) ? $taxonomies['package_type'] : [$taxonomies['package_type']];
                foreach ($pkg_types as $type) {
                    if (stripos($type, 'trek') !== false || stripos($type, 'hik') !== false) {
                        $needs_rental = true;
                        break;
                    }
                }
            }
            if ($needs_rental) {
                wp_set_object_terms($post_id, ['Trekking Poles', 'Sleeping Bag', 'Backpack'], 'optional_renting');
            }

            // FASE 3: Additional info (auto-assign based on package type)
            if (!empty($taxonomies['package_type'])) {
                $pkg_types = is_array($taxonomies['package_type']) ? $taxonomies['package_type'] : [$taxonomies['package_type']];
                $additional_info = [];

                foreach ($pkg_types as $type) {
                    if (stripos($type, 'trek') !== false || stripos($type, 'multi') !== false) {
                        $additional_info = array_merge($additional_info, ['Trekking Information', 'Altitude Advice', 'Equipment List']);
                    } elseif (stripos($type, 'adventure') !== false) {
                        $additional_info = array_merge($additional_info, ['Safety Requirements', 'Physical Requirements']);
                    } elseif (stripos($type, 'full day') !== false || stripos($type, 'day tour') !== false) {
                        $additional_info = array_merge($additional_info, ['What to Bring', 'Important Notes']);
                    }
                }

                if (!empty($additional_info)) {
                    wp_set_object_terms($post_id, array_unique($additional_info), 'additional_info');
                }
            }

            // Complex/Repeater fields - need transformation

            // best_months: ACF expects checkbox with month keys
            if (!empty($data['best_months'])) {
                update_field('best_months', $data['best_months'], $post_id);
            }

            // FASE 5: highlights - Smart icon detection based on content
            if (!empty($data['highlights']) && is_array($data['highlights'])) {
                $icon_keywords = [
                    'train' => 'train',
                    'railway' => 'train',
                    'guide' => 'user',
                    'guided' => 'user',
                    'mountain' => 'mountain',
                    'trek' => 'hiking',
                    'hike' => 'hiking',
                    'photo' => 'camera',
                    'photography' => 'camera',
                    'camera' => 'camera',
                    'free time' => 'clock',
                    'temple' => 'landmark',
                    'palace' => 'landmark',
                    'ruins' => 'landmark',
                    'citadel' => 'landmark',
                    'scenic' => 'eye',
                    'view' => 'eye',
                    'panoramic' => 'eye',
                    'breakfast' => 'utensils',
                    'lunch' => 'utensils',
                    'dinner' => 'utensils',
                    'meal' => 'utensils',
                    'hotel' => 'bed',
                    'accommodation' => 'bed',
                    'transfer' => 'bus',
                    'transport' => 'bus',
                    'entrance' => 'ticket-alt',
                    'ticket' => 'ticket-alt',
                ];

                $highlights_repeater = [];
                foreach ($data['highlights'] as $highlight_text) {
                    $icon = 'check'; // Default icon
                    $text_lower = strtolower($highlight_text);

                    // Check for keyword matches
                    foreach ($icon_keywords as $keyword => $icon_name) {
                        if (stripos($text_lower, $keyword) !== false) {
                            $icon = $icon_name;
                            break;
                        }
                    }

                    $highlights_repeater[] = [
                        'icon' => $icon,
                        'text' => $highlight_text
                    ];
                }
                update_field('highlights', $highlights_repeater, $post_id);
            }

            // itinerary: Transform from old structure to ACF repeater structure
            if (!empty($data['itinerary']) && is_array($data['itinerary'])) {
                $itinerary_repeater = [];
                foreach ($data['itinerary'] as $day) {
                    $day_data = [
                        'active' => 1,
                        'order' => $day['day_number'] ?? 1,
                        'limit' => 0, // No limit by default
                        'title' => $day['title'] ?? '',
                        'content' => $day['description'] ?? '',
                        'accommodation' => $day['accommodation'] ?? '',
                        'altitude' => 0,
                        'optional_activities' => [],
                        'items' => [], // Could map activities later if needed
                    ];

                    // Add gallery field if it exists
                    if (!empty($day['gallery'])) {
                        if (is_array($day['gallery'])) {
                            // Already an array of IDs
                            $day_data['gallery'] = array_map('intval', $day['gallery']);
                        } elseif (is_string($day['gallery'])) {
                            // Convert comma-separated string to array of integers
                            $gallery_ids = explode(',', $day['gallery']);
                            $day_data['gallery'] = array_map('intval', array_filter($gallery_ids));
                        } else {
                            $day_data['gallery'] = [];
                        }
                    } else {
                        $day_data['gallery'] = [];
                    }

                    $itinerary_repeater[] = $day_data;
                }
                update_field('itinerary', $itinerary_repeater, $post_id);
            }

            // manual_departure_dates: Use the repeater field
            // ACF expects: date, status, spots_available, price, notes
            if (!empty($data['departures']) && is_array($data['departures'])) {
                $departures_repeater = [];
                foreach ($data['departures'] as $departure) {
                    $departures_repeater[] = [
                        'date' => $departure['date'] ?? '',
                        'status' => $departure['availability'] ?? 'available', // Map 'availability' to 'status'
                        'price' => $departure['price'] ?? 0,
                        'spots_available' => $departure['spaces_available'] ?? 0, // Map 'spaces_available' to 'spots_available'
                        'notes' => $departure['notes'] ?? ''
                    ];
                }
                update_field('manual_departure_dates', $departures_repeater, $post_id);
            }

            // FASE 5: custom_guarantees - Use quick_facts or trust_badges from mock data
            if (!empty($data['quick_facts']) && is_array($data['quick_facts'])) {
                $guarantees_repeater = [];
                foreach ($data['quick_facts'] as $fact) {
                    $guarantees_repeater[] = [
                        'icon' => $fact['icon'] ?? 'check',
                        'text' => ($fact['label'] ?? '') . ': ' . ($fact['value'] ?? '')
                    ];
                }
                update_field('custom_guarantees', $guarantees_repeater, $post_id);
            } elseif (!empty($data['trust_badges']) && is_array($data['trust_badges'])) {
                // Alternative: Use trust_badges as guarantees
                $guarantees_repeater = [];
                foreach ($data['trust_badges'] as $badge) {
                    if (!empty($badge['name'])) {
                        $guarantees_repeater[] = [
                            'icon' => 'shield-alt',
                            'text' => $badge['name']
                        ];
                    }
                }
                if (!empty($guarantees_repeater)) {
                    update_field('custom_guarantees', $guarantees_repeater, $post_id);
                }
            }

            // FASE 5: price_tiers - Create tiered pricing if available
            // For now, create a simple tier based on main pricing
            // Future: Could extract from mock data if price tiers structure is added
            if (!empty($data['price_normal']) && $data['price_normal'] > 0) {
                $price_tiers = [];

                // Single traveler tier
                $price_tiers[] = [
                    'min_passengers' => 1,
                    'price' => $data['price_normal'] ?? 0,
                    'offer' => $data['price_offer'] ?? 0
                ];

                // Group discount tier (2-4 people)
                if ($data['price_normal'] > 100) {
                    $price_tiers[] = [
                        'min_passengers' => 2,
                        'price' => round($data['price_normal'] * 0.9), // 10% discount
                        'offer' => !empty($data['price_offer']) ? round($data['price_offer'] * 0.9) : 0
                    ];
                }

                // Large group tier (5+ people)
                if ($data['price_normal'] > 200) {
                    $price_tiers[] = [
                        'min_passengers' => 5,
                        'price' => round($data['price_normal'] * 0.8), // 20% discount
                        'offer' => !empty($data['price_offer']) ? round($data['price_offer'] * 0.8) : 0
                    ];
                }

                if (!empty($price_tiers)) {
                    update_field('price_tiers', $price_tiers, $post_id);
                }
            }

            // FASE 5: banners - Skip for now (requires image uploads)
            // Note: banners require image field which needs file uploads
            // This can be added in FASE 6 (Image fields)

            // FASE 5: additional_sections - Create from FAQs and impact_items
            $additional_sections = [];
            $section_order = 1;

            // Add FAQs section if available
            if (!empty($data['faqs']) && is_array($data['faqs'])) {
                $faq_content = '<div class="faqs-container">';
                foreach ($data['faqs'] as $faq) {
                    $question = $faq['question'] ?? '';
                    $answer = $faq['answer'] ?? '';
                    if ($question && $answer) {
                        $faq_content .= '<div class="faq-item">';
                        $faq_content .= '<h4>' . esc_html($question) . '</h4>';
                        $faq_content .= '<p>' . esc_html($answer) . '</p>';
                        $faq_content .= '</div>';
                    }
                }
                $faq_content .= '</div>';

                $additional_sections[] = [
                    'active' => 1,
                    'order' => $section_order++,
                    'type' => 'faq',
                    'title' => 'Frequently Asked Questions',
                    'icon' => 'question-circle',
                    'style' => 'default',
                    'content' => $faq_content,
                    'items' => [] // Could create sub-items repeater if needed
                ];
            }

            // Add Impact/Sustainability section if available
            if (!empty($data['impact_items']) && is_array($data['impact_items'])) {
                $impact_content = '<div class="impact-items">';
                foreach ($data['impact_items'] as $item) {
                    $title = $item['title'] ?? '';
                    $description = $item['description'] ?? '';
                    $icon = $item['icon'] ?? 'leaf';

                    if ($title && $description) {
                        $impact_content .= '<div class="impact-item">';
                        $impact_content .= '<i class="fa fa-' . esc_attr($icon) . '"></i>';
                        $impact_content .= '<h5>' . esc_html($title) . '</h5>';
                        $impact_content .= '<p>' . esc_html($description) . '</p>';
                        $impact_content .= '</div>';
                    }
                }
                $impact_content .= '</div>';

                $additional_sections[] = [
                    'active' => 1,
                    'order' => $section_order++,
                    'type' => 'impact',
                    'title' => $data['impact_title'] ?? 'Sustainable Tourism & Local Impact',
                    'icon' => 'leaf',
                    'style' => 'highlighted',
                    'content' => $impact_content,
                    'items' => []
                ];
            }

            // Update additional_sections field if we have any sections
            if (!empty($additional_sections)) {
                update_field('additional_sections', $additional_sections, $post_id);
            }

            // Locations: Assign location CPT
            // Priority 1: Use location names from mock data if provided
            // Priority 2: Auto-search based on package title
            $location_ids = [];

            if (!empty($data['location_names']) && is_array($data['location_names'])) {
                // Method 1: Find locations by names from mock data
                foreach ($data['location_names'] as $location_name) {
                    $location_id = $this->find_location_by_name($location_name);
                    if ($location_id) {
                        $location_ids[] = $location_id;
                    }
                }
            }

            // Fallback: Auto-search if no locations found from mock data
            if (empty($location_ids)) {
                $location_ids = $this->find_related_locations($package_data['title']);
            }

            // Assign to ACF fields
            if (!empty($location_ids)) {
                // Set main destination (first location found)
                update_field('destination', $location_ids[0], $post_id);

                // Set locations (all found locations)
                update_field('locations', $location_ids, $post_id);

                // Also set tag_locations for additional tagging
                update_field('tag_locations', $location_ids, $post_id);
            }

            // FASE 4: CPT Relationships - shared_package
            // Auto-detect similar packages from the same destination or with similar characteristics
            if (!empty($location_ids)) {
                $similar_packages = get_posts([
                    'post_type' => 'package',
                    'posts_per_page' => 1,
                    'post__not_in' => [$post_id],
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'meta_query' => [
                        [
                            'key' => 'destination',
                            'value' => $location_ids[0],
                            'compare' => '='
                        ]
                    ]
                ]);

                if (!empty($similar_packages)) {
                    update_field('shared_package', $similar_packages[0]->ID, $post_id);
                }
            }

            // FASE 4: Flights CPT (if flight CPT exists and data available)
            // For now, leave empty as there's no flights CPT or mock data
            // update_field('flights', [], $post_id);
        }

        return [
            'success' => true,
            'post_id' => $post_id
        ];
    }

    /**
     * Helper: Find location by exact name match
     *
     * @param string $location_name Location name to find
     * @return int|null Location post ID or null if not found
     */
    private function find_location_by_name($location_name) {
        // Try exact match first
        $locations = get_posts([
            'post_type' => 'location',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'title' => $location_name
        ]);

        if (!empty($locations)) {
            return $locations[0]->ID;
        }

        // Try fuzzy match (case-insensitive, trim whitespace)
        $all_locations = get_posts([
            'post_type' => 'location',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ]);

        $search_name = trim(strtolower($location_name));
        foreach ($all_locations as $location) {
            if (trim(strtolower($location->post_title)) === $search_name) {
                return $location->ID;
            }
        }

        return null;
    }

    /**
     * Helper: Find related location CPT IDs based on package title
     *
     * @param string $package_title Package title to search for
     * @return array Array of location post IDs
     */
    private function find_related_locations($package_title) {
        $location_ids = [];

        // Get all locations
        $all_locations = get_posts([
            'post_type' => 'location',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);

        if (empty($all_locations)) {
            return $location_ids;
        }

        // Normalize package title for comparison
        $title_lower = strtolower($package_title);

        // Keywords to search for in package title
        $location_keywords = [];
        foreach ($all_locations as $location) {
            $location_keywords[$location->ID] = [
                'title' => $location->post_title,
                'keywords' => $this->extract_location_keywords($location->post_title)
            ];
        }

        // Search for locations mentioned in package title
        // Use scoring system: longer/more specific matches score higher
        $matches = [];
        foreach ($location_keywords as $location_id => $location_data) {
            foreach ($location_data['keywords'] as $keyword) {
                if (stripos($title_lower, strtolower($keyword)) !== false) {
                    // Score based on keyword length (longer = more specific)
                    $score = strlen($keyword);
                    // Bonus if it's the full title
                    if ($keyword === $location_data['title']) {
                        $score += 50;
                    }
                    $matches[$location_id] = max($matches[$location_id] ?? 0, $score);
                    break; // Only add once per location
                }
            }
        }

        // Sort by score (highest first) and return IDs
        arsort($matches);
        return array_keys($matches);
    }

    /**
     * Helper: Extract keywords from location title for matching
     *
     * @param string $location_title Location title
     * @return array Array of keywords
     */
    private function extract_location_keywords($location_title) {
        $keywords = [];

        // Add full title
        $keywords[] = $location_title;

        // Remove common suffixes/prefixes and add variations
        $clean_title = str_replace([
            ' City Center', ' City', ' National Park', ' National Reserve',
            ' Oasis', ' Mountain', ' Lake', ' Valley', ' Historic Center',
            ' Rainforest', ' Canyon', ' Lines', ' Monastery', ' Mines'
        ], '', $location_title);

        if ($clean_title !== $location_title) {
            $keywords[] = $clean_title;
        }

        // Add words from parentheses (e.g., "Rainbow Mountain (Vinicunca)" -> "Vinicunca")
        if (preg_match('/\(([^)]+)\)/', $location_title, $matches)) {
            $keywords[] = $matches[1];
        }

        // Add individual significant words (3+ chars)
        $words = explode(' ', $clean_title);
        foreach ($words as $word) {
            if (strlen($word) >= 3 && !in_array(strtolower($word), ['the', 'and', 'del'])) {
                $keywords[] = $word;
            }
        }

        return $keywords;
    }

    /**
     * Helper: Add images to single package
     *
     * @param int $post_id Package post ID
     * @return array Result with images_count or error
     */
    private function add_images_to_single_package($post_id) {
        $images_added = 0;
        $errors = [];

        try {
            // Use existing generator method
            $package = get_post($post_id);

            if (!$package) {
                return [
                    'success' => false,
                    'error' => 'Package not found',
                    'images_count' => 0
                ];
            }

            // Featured image
            if (!has_post_thumbnail($post_id)) {
                $placeholder_num = 1 + ($post_id % 10);
                $img_id = $this->generator->generate_single_image_from_placeholder(
                    $post_id,
                    $package->post_title,
                    $placeholder_num,
                    'Featured'
                );

                if ($img_id) {
                    set_post_thumbnail($post_id, $img_id);
                    $images_added++;
                }
            }

            // Gallery (3-5 images)
            if (function_exists('get_field') && function_exists('update_field')) {
                $gallery = get_field('gallery', $post_id);

                if (empty($gallery)) {
                    $num_images = rand(3, 5);
                    $gallery_ids = [];

                    for ($i = 0; $i < $num_images; $i++) {
                        $placeholder_num = 1 + ((($post_id * 10) + $i) % 45);
                        $img_id = $this->generator->generate_single_image_from_placeholder(
                            $post_id,
                            $package->post_title,
                            $placeholder_num,
                            "Gallery Image " . ($i + 1)
                        );

                        if ($img_id) {
                            $gallery_ids[] = $img_id;
                            $images_added++;
                        }
                    }

                    if (!empty($gallery_ids)) {
                        update_field('gallery', $gallery_ids, $post_id);
                    }
                }

                // FASE 6: Map Image
                $map_image = get_field('map_image', $post_id);
                if (empty($map_image)) {
                    $map_num = 41 + ($post_id % 5); // Placeholders 41-45 (mapas)
                    $map_id = $this->generator->generate_single_image_from_placeholder(
                        $post_id,
                        $package->post_title,
                        $map_num,
                        'Map'
                    );

                    if ($map_id) {
                        update_field('map_image', $map_id, $post_id);
                        $images_added++;
                    }
                }

                // FASE 6: Video Thumbnail (si hay video_url)
                $video_url = get_field('video_url', $post_id);
                if (!empty($video_url)) {
                    $video_thumb = get_field('video_thumbnail', $post_id);
                    if (empty($video_thumb)) {
                        $thumb_num = 1 + ($post_id % 20);
                        $thumb_id = $this->generator->generate_single_image_from_placeholder(
                            $post_id,
                            $package->post_title,
                            $thumb_num,
                            'Video Thumbnail'
                        );

                        if ($thumb_id) {
                            update_field('video_thumbnail', $thumb_id, $post_id);
                            $images_added++;
                        }
                    }
                }

                // FASE 6: SEO OG Image (usar featured image si no existe)
                $seo_og = get_field('seo_og_image', $post_id);
                if (empty($seo_og) && has_post_thumbnail($post_id)) {
                    $featured_id = get_post_thumbnail_id($post_id);
                    update_field('seo_og_image', $featured_id, $post_id);
                    // No incrementamos counter porque estamos reutilizando featured image
                }
            }

            return [
                'success' => true,
                'images_count' => $images_added
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'images_count' => $images_added
            ];
        }
    }

    /**
     * Helper: Get data file path for CPT type
     *
     * @param string $cpt_type CPT type
     * @return string File path
     */
    private function get_cpt_data_file($cpt_type) {
        $file_map = [
            'deal' => 'mock-deals-data.php',
            'location' => 'mock-locations-data.php',
            'guide' => 'mock-guides-data.php',
            'review' => 'mock-reviews-data.php',
            'collaborator' => 'mock-collaborators-data.php'
        ];

        $filename = isset($file_map[$cpt_type]) ? $file_map[$cpt_type] : '';
        return plugin_dir_path(__FILE__) . $filename;
    }

    /**
     * Helper: Create batch of CPT posts
     *
     * @param string $cpt_type CPT type (deal, location, etc.)
     * @param int $batch Batch number
     * @return array Result
     */
    private function create_cpt_batch($cpt_type, $batch) {
        $batch_size = $this->batch_sizes[2];
        $offset = $batch * $batch_size;

        // Get data file for this CPT
        $data_file = $this->get_cpt_data_file($cpt_type);

        if (!file_exists($data_file)) {
            return [
                'cpt_finished' => true,
                'created_count' => 0,
                'message' => ucfirst($cpt_type) . ' data file not found',
                'error' => 'Data file missing: ' . basename($data_file)
            ];
        }

        $cpt_data = include $data_file;
        $total_items = count($cpt_data);
        $batch_data = array_slice($cpt_data, $offset, $batch_size);

        // Check if finished
        if (empty($batch_data)) {
            return [
                'cpt_finished' => true,
                'created_count' => 0,
                'message' => 'All ' . $cpt_type . 's created',
                'progress' => [
                    'current' => $total_items,
                    'total' => $total_items,
                    'percentage' => 100,
                    'cpt_type' => $cpt_type
                ]
            ];
        }

        // Create posts in this batch
        $created = [];
        $errors = [];

        foreach ($batch_data as $data) {
            $result = $this->create_single_cpt_post($cpt_type, $data);

            // Build display title (handle collaborators specially)
            $display_title = $data['title'] ?? '';
            if ($cpt_type === 'collaborator' && empty($display_title)) {
                $first_name = $data['first_name'] ?? '';
                $last_name = $data['last_name'] ?? '';
                $display_title = trim($first_name . ' ' . $last_name);
            }

            if ($result['success']) {
                $created[] = [
                    'id' => $result['post_id'],
                    'title' => $display_title,
                    'type' => $cpt_type
                ];
            } else {
                $errors[] = [
                    'title' => $display_title,
                    'error' => $result['error']
                ];
            }
        }

        $current_total = $offset + count($created);
        $percentage = round(($current_total / $total_items) * 100);

        return [
            'cpt_finished' => false,
            'created_count' => count($created),
            'created_items' => $created,
            'errors' => $errors,
            'message' => ucfirst($cpt_type) . 's in progress',
            'progress' => [
                'current' => $current_total,
                'total' => $total_items,
                'percentage' => $percentage,
                'cpt_type' => $cpt_type
            ]
        ];
    }

    /**
     * Helper: Create single CPT post
     *
     * @param string $cpt_type CPT type
     * @param array $data Post data
     * @return array Result with post_id or error
     */
    private function create_single_cpt_post($cpt_type, $data) {
        // Create post title
        // For collaborators, build title from first_name + last_name
        $post_title = $data['title'] ?? '';
        if ($cpt_type === 'collaborator' && empty($post_title)) {
            $first_name = $data['first_name'] ?? '';
            $last_name = $data['last_name'] ?? '';
            $post_title = trim($first_name . ' ' . $last_name);
        }

        $post_data = [
            'post_title' => $post_title,
            'post_type' => $cpt_type,
            'post_status' => 'publish',
        ];

        // Add content and excerpt if available
        if (isset($data['description'])) {
            $post_data['post_content'] = $data['description'];
        }
        if (isset($data['excerpt'])) {
            $post_data['post_excerpt'] = $data['excerpt'];
        }

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            return [
                'success' => false,
                'error' => $post_id->get_error_message()
            ];
        }

        // Add ACF fields based on CPT type
        if (function_exists('update_field')) {
            switch ($cpt_type) {
                case 'deal':
                    $this->add_deal_fields($post_id, $data);
                    break;
                case 'location':
                    $this->add_location_fields($post_id, $data);
                    break;
                case 'guide':
                    $this->add_guide_fields($post_id, $data);
                    break;
                case 'review':
                    $this->add_review_fields($post_id, $data);
                    break;
                case 'collaborator':
                    $this->add_collaborator_fields($post_id, $data);
                    break;
            }
        }

        return [
            'success' => true,
            'post_id' => $post_id
        ];
    }

    /**
     * Helper: Add Deal ACF fields
     */
    private function add_deal_fields($post_id, $data) {
        if (isset($data['active'])) update_field('active', $data['active'], $post_id);
        if (isset($data['start_date'])) update_field('start_date', $data['start_date'], $post_id);
        if (isset($data['end_date'])) update_field('end_date', $data['end_date'], $post_id);
        if (isset($data['discount_percentage'])) update_field('discount_percentage', $data['discount_percentage'], $post_id);
        if (isset($data['terms'])) update_field('terms', $data['terms'], $post_id);
        if (isset($data['promo_tag'])) update_field('promo_tag', $data['promo_tag'], $post_id);
    }

    /**
     * Helper: Add Location ACF fields
     */
    private function add_location_fields($post_id, $data) {
        if (isset($data['country'])) update_field('country', $data['country'], $post_id);
        if (isset($data['region'])) update_field('region', $data['region'], $post_id);
        if (isset($data['coordinates'])) update_field('coordinates', $data['coordinates'], $post_id);
        if (isset($data['elevation'])) update_field('elevation', $data['elevation'], $post_id);
        if (isset($data['climate'])) update_field('climate', $data['climate'], $post_id);
        if (isset($data['best_time'])) update_field('best_time', $data['best_time'], $post_id);
    }

    /**
     * Helper: Add Guide ACF fields
     */
    private function add_guide_fields($post_id, $data) {
        if (isset($data['specialty'])) update_field('specialty', $data['specialty'], $post_id);
        if (isset($data['experience_years'])) update_field('experience_years', $data['experience_years'], $post_id);
        if (isset($data['languages'])) update_field('languages', $data['languages'], $post_id);
        if (isset($data['bio'])) update_field('bio', $data['bio'], $post_id);
        if (isset($data['certifications'])) update_field('certifications', $data['certifications'], $post_id);
    }

    /**
     * Helper: Add Review ACF fields
     */
    private function add_review_fields($post_id, $data) {
        if (isset($data['reviewer_name'])) update_field('reviewer_name', $data['reviewer_name'], $post_id);
        if (isset($data['rating'])) update_field('rating', $data['rating'], $post_id);
        if (isset($data['review_date'])) update_field('review_date', $data['review_date'], $post_id);
        if (isset($data['package_id'])) update_field('package_id', $data['package_id'], $post_id);
        if (isset($data['verified'])) update_field('verified', $data['verified'], $post_id);
    }

    /**
     * Helper: Add Collaborator ACF fields
     */
    private function add_collaborator_fields($post_id, $data) {
        if (isset($data['company_name'])) update_field('company_name', $data['company_name'], $post_id);
        if (isset($data['website'])) update_field('website', $data['website'], $post_id);
        if (isset($data['partnership_type'])) update_field('partnership_type', $data['partnership_type'], $post_id);
    }

    /**
     * Helper: Add images to CPT batch (Step 3)
     *
     * @param string $cpt_type CPT type
     * @param int $batch Batch number
     * @return array Result
     */
    private function add_images_to_cpt_batch($cpt_type, $batch) {
        $batch_size = $this->batch_sizes[3];
        $offset = $batch * $batch_size;

        // Get posts of this type that need images
        $posts = get_posts([
            'post_type' => $cpt_type,
            'posts_per_page' => $batch_size,
            'offset' => $offset,
            'post_status' => 'publish',
            'orderby' => 'ID',
            'order' => 'ASC'
        ]);

        $total_posts = wp_count_posts($cpt_type);
        $total_count = isset($total_posts->publish) ? $total_posts->publish : 0;

        // Check if finished
        if (empty($posts)) {
            return [
                'cpt_finished' => true,
                'updated_count' => 0,
                'message' => 'All ' . $cpt_type . ' images added',
                'progress' => [
                    'current' => $total_count,
                    'total' => $total_count,
                    'percentage' => 100,
                    'cpt_type' => $cpt_type
                ]
            ];
        }

        // Add images to this batch
        $updated = [];
        $errors = [];

        foreach ($posts as $post) {
            $result = $this->add_images_to_single_cpt($cpt_type, $post->ID);

            if ($result['success']) {
                $updated[] = [
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'images_added' => $result['images_count']
                ];
            } else {
                $errors[] = [
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'error' => $result['error']
                ];
            }
        }

        $current_total = $offset + count($updated);
        $percentage = $total_count > 0 ? round(($current_total / $total_count) * 100) : 100;

        return [
            'cpt_finished' => false,
            'updated_count' => count($updated),
            'updated_items' => $updated,
            'errors' => $errors,
            'message' => ucfirst($cpt_type) . ' images in progress',
            'progress' => [
                'current' => $current_total,
                'total' => $total_count,
                'percentage' => $percentage,
                'cpt_type' => $cpt_type
            ]
        ];
    }

    /**
     * Helper: Add images to single CPT post
     *
     * @param string $cpt_type CPT type
     * @param int $post_id Post ID
     * @return array Result
     */
    private function add_images_to_single_cpt($cpt_type, $post_id) {
        $images_added = 0;

        try {
            // Add featured image if missing
            if (!has_post_thumbnail($post_id)) {
                $post = get_post($post_id);

                // Use different placeholder ranges for different CPTs
                $placeholder_ranges = [
                    'deal' => [21, 30],
                    'location' => [11, 30],
                    'guide' => [46, 50],
                    'review' => [46, 50],
                    'collaborator' => [46, 50]
                ];

                $range = isset($placeholder_ranges[$cpt_type]) ? $placeholder_ranges[$cpt_type] : [1, 45];
                $placeholder_num = $range[0] + ($post_id % ($range[1] - $range[0] + 1));

                $img_id = $this->generator->generate_single_image_from_placeholder(
                    $post_id,
                    $post->post_title,
                    $placeholder_num,
                    'Featured'
                );

                if ($img_id) {
                    set_post_thumbnail($post_id, $img_id);
                    $images_added++;
                }
            }

            // Add CPT-specific images
            if (function_exists('get_field') && function_exists('update_field')) {
                switch ($cpt_type) {
                    case 'deal':
                        // Add banner image if missing
                        $banner = get_field('banner', $post_id);
                        if (empty($banner)) {
                            $post = get_post($post_id);
                            $banner_num = 31 + ($post_id % 10);
                            $banner_id = $this->generator->generate_single_image_from_placeholder(
                                $post_id,
                                $post->post_title,
                                $banner_num,
                                'Banner'
                            );

                            if ($banner_id) {
                                update_field('banner', $banner_id, $post_id);
                                $images_added++;
                            }
                        }
                        break;

                    case 'location':
                        // Add location_image if missing
                        $location_image = get_field('location_image', $post_id);
                        if (empty($location_image)) {
                            $post = get_post($post_id);
                            $loc_num = 31 + ($post_id % 15);
                            $loc_id = $this->generator->generate_single_image_from_placeholder(
                                $post_id,
                                $post->post_title,
                                $loc_num,
                                'Location'
                            );

                            if ($loc_id) {
                                update_field('location_image', $loc_id, $post_id);
                                $images_added++;
                            }
                        }
                        break;
                }
            }

            return [
                'success' => true,
                'images_count' => $images_added
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'images_count' => $images_added
            ];
        }
    }

    /**
     * Get final statistics
     *
     * @return array Statistics
     */
    private function get_final_statistics() {
        $stats = [
            'packages' => 0,
            'deals' => 0,
            'locations' => 0,
            'guides' => 0,
            'reviews' => 0,
            'collaborators' => 0,
            'destinations' => 0,
            'images' => 0,
            'taxonomy_terms' => []
        ];

        // Count posts
        $post_types = ['package', 'deal', 'location', 'guide', 'review', 'collaborator', 'destination'];
        foreach ($post_types as $post_type) {
            $count = wp_count_posts($post_type);
            $stats[$post_type . 's'] = isset($count->publish) ? $count->publish : 0;
        }

        // Count attachments
        $attachment_count = wp_count_posts('attachment');
        $stats['images'] = isset($attachment_count->inherit) ? $attachment_count->inherit : 0;

        // Count taxonomy terms
        $taxonomies = ['destinations', 'countries', 'specialists'];
        foreach ($taxonomies as $taxonomy) {
            $count = wp_count_terms($taxonomy, ['hide_empty' => false]);
            $stats['taxonomy_terms'][$taxonomy] = is_wp_error($count) ? 0 : $count;
        }

        return $stats;
    }

    /**
     * Calculate total batches for a step
     *
     * @param int $step Step number
     * @return int Total batches
     */
    public function get_total_batches_for_step($step) {
        switch ($step) {
            case 1:
                // Step 1: Add Taxonomy Images (TODO: Calculate based on actual data)
                return 10;

            case 2:
                // Step 2: Create CPTs (TODO: Calculate based on actual data)
                return 10;

            case 3:
                // Step 3: Add CPT Images (TODO: Calculate based on actual data)
                return 10;

            case 4:
                // Step 4: Create Packages
                $data_file = plugin_dir_path(dirname(__FILE__)) . 'mock-packages-data-phase7a.php';
                if (file_exists($data_file)) {
                    $packages_data = include $data_file;
                    return ceil(count($packages_data) / $this->batch_sizes[4]);
                }
                return 0;

            case 5:
                // Step 5: Add Package Images
                $package_count = wp_count_posts('package');
                $total = isset($package_count->publish) ? $package_count->publish : 0;
                return ceil($total / $this->batch_sizes[5]);

            case 6:
                // Step 6: Finalize
                return 1;

            default:
                return 0;
        }
    }

    /**
     * Get step info
     *
     * @param int $step Step number
     * @return array Step information
     */
    public function get_step_info($step) {
        $step_info = [
            1 => [
                'title' => 'Setting Up Taxonomies',
                'description' => 'Creating categories, tags, and taxonomy terms',
                'icon' => '🏷️'
            ],
            2 => [
                'title' => 'Creating Other Content',
                'description' => 'Creating deals, locations, guides, reviews, and more',
                'icon' => '📝'
            ],
            3 => [
                'title' => 'Adding Content Images',
                'description' => 'Uploading images to all content types',
                'icon' => '🎨'
            ],
            4 => [
                'title' => 'Creating Packages',
                'description' => 'Creating travel package posts with content and details',
                'icon' => '📦'
            ],
            5 => [
                'title' => 'Adding Package Images',
                'description' => 'Uploading featured images and galleries to packages',
                'icon' => '🖼️'
            ],
            6 => [
                'title' => 'Finalizing',
                'description' => 'Cleaning up and generating final statistics',
                'icon' => '✅'
            ]
        ];

        return isset($step_info[$step]) ? $step_info[$step] : [
            'title' => 'Unknown Step',
            'description' => '',
            'icon' => '❓'
        ];
    }
}
