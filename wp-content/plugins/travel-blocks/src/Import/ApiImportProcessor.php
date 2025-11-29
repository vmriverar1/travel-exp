<?php



namespace Travel\Blocks\Import;



use Travel\Blocks\Services\PackageApiService;



/**

 * API Import Processor

 *

 * Handles the complete import process from Valencia API to WordPress packages.

 * Processes tours individually with full error handling and reporting.

 *

 * @package Travel\Blocks\Import

 * @since 1.0.0

 */

class ApiImportProcessor

{

    /**

     * Package API Service

     */

    private PackageApiService $api_service;



    /**

     * Package Lookup Service

     */

    private PackageLookupService $lookup_service;



    /**

     * API Data Mapper

     */

    private ApiDataMapper $mapper;



    /**

     * Image Import Service

     */

    private ImageImportService $image_service;



    /**

     * Options for processing

     */

    private array $options = [];



    /**

     * Debug info collector

     */

    private array $debug_info = [];



    /**

     * Constructor

     */

    public function __construct()

    {

        $this->api_service = new PackageApiService();

        $this->lookup_service = new PackageLookupService();

        $this->mapper = new ApiDataMapper();

        $this->image_service = new ImageImportService();

    }



    /**

     * Set processing options

     *

     * @param array $options Available options:

     *  - update_existing: bool (default true) - Update existing packages

     *  - dry_run: bool (default false) - Don't save changes, just report

     *  - skip_images: bool (default true) - Skip image download for now

     * @return self

     */

    public function set_options(array $options): self

    {

        $this->options = array_merge([

            'update_existing' => true,

            'dry_run' => false,

            'skip_images' => true,

        ], $options);



        return $this;

    }



    /**

     * Process a single tour by ID

     *

     * @param int $tour_id Tour ID from Valencia API

     * @return array Result with status, message, and data

     */

    public function process_single(int $tour_id): array

    {

        $start_time = microtime(true);



        try {

            // Step 1: Check if tour exists in API

            $this->log_debug("Processing tour_id={$tour_id}");



            $api_data = $this->api_service->fetch_package($tour_id);



            if (empty($api_data)) {

                return $this->error_result($tour_id, 'El tour no existe en la API o no se pudo obtener los datos');

            }



            // Validate essential data exists

            if (empty($api_data['title'])) {

                return $this->error_result($tour_id, 'Datos incompletos: El tour no tiene título');

            }



            // Step 2: Check if package already exists locally

            $existing_post_id = $this->lookup_service->find_by_tour_id($tour_id);



            if ($existing_post_id && !$this->options['update_existing']) {

                return $this->skipped_result($tour_id, 'Package already exists (update disabled)', [

                    'post_id' => $existing_post_id,

                ]);

            }



            // Step 3: Map API data to WordPress format

            $mapped_data = $this->mapper->map_to_package($api_data);



            if (empty($mapped_data['post_data']['post_title'])) {

                return $this->error_result($tour_id, 'Mapped data has no title');

            }



            // Step 4: Dry run check

            if ($this->options['dry_run']) {

                return $this->success_result($tour_id, 'Dry run - would ' . ($existing_post_id ? 'update' : 'create'), [

                    'action' => $existing_post_id ? 'update' : 'create',

                    'existing_post_id' => $existing_post_id,

                    'title' => $mapped_data['post_data']['post_title'],

                    'mapped_fields_count' => count($mapped_data['meta_fields']),

                ]);

            }



            // Step 5: Create or update package

            if ($existing_post_id) {

                $result = $this->update_package($existing_post_id, $mapped_data);

            } else {

                $result = $this->create_package($mapped_data);

            }



            // Calculate execution time

            $execution_time = round(microtime(true) - $start_time, 2);

            $result['execution_time'] = $execution_time;



            return $result;



        } catch (\Exception $e) {

            return $this->error_result($tour_id, 'Exception: ' . $e->getMessage(), [

                'exception' => get_class($e),

                'file' => $e->getFile(),

                'line' => $e->getLine(),

            ]);

        }

    }



    /**

     * Create new package post

     *

     * @param array $mapped_data Mapped data from ApiDataMapper

     * @return array Result

     */

    private function create_package(array $mapped_data): array

    {

        $tour_id = $mapped_data['meta_fields']['tour_id'] ?? 0;



        // Insert post

        $post_id = wp_insert_post($mapped_data['post_data'], true);



        if (is_wp_error($post_id)) {

            return $this->error_result($tour_id, 'Failed to create post: ' . $post_id->get_error_message());

        }



        // Save meta fields and relations

        $save_result = $this->save_package_data($post_id, $mapped_data);



        if (!$save_result['success']) {

            // Rollback: delete the post

            wp_delete_post($post_id, true);

            return $this->error_result($tour_id, 'Failed to save data: ' . $save_result['message']);

        }



        // Process images (after package is saved)

        $images_count = 0;

        $this->debug_info = []; // Reset debug info



        $this->debug_info[] = "skip_images=" . var_export($this->options['skip_images'], true);

        $this->debug_info[] = "thumbnail_url=" . ($mapped_data['meta_fields']['thumbnail_url'] ?? 'NULL');

        $this->debug_info[] = "gallery_count=" . count($mapped_data['repeaters']['gallery'] ?? []);



        if (!$this->options['skip_images']) {

            $images_count = $this->process_images($post_id, $mapped_data);

        }



        $this->debug_info[] = "images_processed=" . $images_count;



        $this->log_debug("Created package post_id={$post_id} for tour_id={$tour_id}");



        return $this->success_result($tour_id, 'Package created successfully', [

            'action' => 'create',

            'post_id' => $post_id,

            'title' => $mapped_data['post_data']['post_title'],

            'url' => get_permalink($post_id),

            'images_count' => $images_count,

            'images_skipped' => $this->options['skip_images'],

            'debug' => implode(' | ', $this->debug_info),

        ]);

    }



    /**

     * Update existing package post

     *

     * @param int $post_id WordPress post ID

     * @param array $mapped_data Mapped data from ApiDataMapper

     * @return array Result

     */

    private function update_package(int $post_id, array $mapped_data): array

    {

        $tour_id = $mapped_data['meta_fields']['tour_id'] ?? 0;



        // Prepare update data (remove post_date to preserve original)

        $update_data = $mapped_data['post_data'];

        $update_data['ID'] = $post_id;



        // Don't update post_date on existing posts - preserve original creation date

        unset($update_data['post_date']);

        unset($update_data['post_date_gmt']);



        $result = wp_update_post($update_data, true);



        if (is_wp_error($result)) {

            return $this->error_result($tour_id, 'Failed to update post: ' . $result->get_error_message());

        }



        // Save meta fields and relations

        $save_result = $this->save_package_data($post_id, $mapped_data);



        if (!$save_result['success']) {

            return $this->error_result($tour_id, 'Failed to update data: ' . $save_result['message']);

        }



        // Process images (after package is saved)

        $images_count = 0;

        $this->debug_info = []; // Reset debug info



        $this->debug_info[] = "skip_images=" . var_export($this->options['skip_images'], true);

        $this->debug_info[] = "thumbnail_url=" . ($mapped_data['meta_fields']['thumbnail_url'] ?? 'NULL');

        $this->debug_info[] = "gallery_count=" . count($mapped_data['repeaters']['gallery'] ?? []);



        if (!$this->options['skip_images']) {

            $images_count = $this->process_images($post_id, $mapped_data);

        }



        $this->debug_info[] = "images_processed=" . $images_count;



        $this->log_debug("Updated package post_id={$post_id} for tour_id={$tour_id}");



        return $this->success_result($tour_id, 'Package updated successfully', [

            'action' => 'update',

            'post_id' => $post_id,

            'title' => $mapped_data['post_data']['post_title'],

            'url' => get_permalink($post_id),

            'images_count' => $images_count,

            'images_skipped' => $this->options['skip_images'],

            'debug' => implode(' | ', $this->debug_info),

        ]);

    }



    /**

     * Save all package data (meta fields, taxonomies, relations, repeaters)

     * Only updates fields that have non-empty values from API to preserve existing data

     *

     * @param int $post_id WordPress post ID

     * @param array $mapped_data Mapped data

     * @return array ['success' => bool, 'message' => string]

     */

    private function save_package_data(int $post_id, array $mapped_data): array

    {

        try {

            // 1. Save simple meta fields (only if value is not empty)

            foreach ($mapped_data['meta_fields'] as $field_key => $field_value) {

                // Only update if API provides a non-empty value

                // This prevents overwriting existing data with empty values

                if ($this->should_update_field($field_value)) {

                    update_field($field_key, $field_value, $post_id);

                }

            }



            // 2. Assign taxonomies (only if terms provided)

            foreach ($mapped_data['taxonomies'] as $taxonomy => $term_ids) {

                if (!empty($term_ids)) {

                    // For ACF taxonomy fields, use update_field() instead of wp_set_object_terms()

                    // This ensures ACF meta fields are properly synchronized

                    update_field($taxonomy, $term_ids, $post_id);

                }

            }



            // 3. Save post object relations (only if IDs provided)

            foreach ($mapped_data['post_objects'] as $field_key => $post_ids) {

                if (!empty($post_ids)) {

                    update_field($field_key, $post_ids, $post_id);

                }

            }



            // 4. Save repeater fields (only if data provided)

            foreach ($mapped_data['repeaters'] as $field_key => $repeater_data) {

                if (!empty($repeater_data)) {

                    update_field($field_key, $repeater_data, $post_id);

                }

            }



            return [

                'success' => true,

                'message' => 'All data saved successfully',

            ];



        } catch (\Exception $e) {

            $this->log_error("Failed to save data for post_id={$post_id}: " . $e->getMessage());



            return [

                'success' => false,

                'message' => $e->getMessage(),

            ];

        }

    }



    /**

     * Determine if a field value should update existing data

     * Returns false if value is empty/null to preserve existing WordPress data

     *

     * @param mixed $value Value to check

     * @return bool True if should update, false to skip

     */

    private function should_update_field($value): bool

    {

        // Null or false = skip

        if ($value === null || $value === false) {

            return false;

        }



        // Empty string = skip

        if (is_string($value) && trim($value) === '') {

            return false;

        }



        // Empty array = skip

        if (is_array($value) && empty($value)) {

            return false;

        }



        // 0 and "0" are valid values, update them

        // Any other non-empty value = update

        return true;

    }



    // ============================================

    // RESULT HELPERS

    // ============================================



    /**

     * Success result

     */

    private function success_result(int $tour_id, string $message, array $data = []): array

    {

        return [

            'status' => 'success',

            'tour_id' => $tour_id,

            'message' => $message,

            'data' => $data,

        ];

    }



    /**

     * Error result

     */

    private function error_result(int $tour_id, string $message, array $data = []): array

    {

        $this->log_error("Error processing tour_id={$tour_id}: {$message}");



        return [

            'status' => 'error',

            'tour_id' => $tour_id,

            'message' => $message,

            'data' => $data,

        ];

    }



    /**

     * Skipped result

     */

    private function skipped_result(int $tour_id, string $message, array $data = []): array

    {

        return [

            'status' => 'skipped',

            'tour_id' => $tour_id,

            'message' => $message,

            'data' => $data,

        ];

    }



    // ============================================

    // BATCH PROCESSING

    // ============================================



    /**

     * Process multiple tours

     *

     * @param array $tour_ids Array of tour IDs

     * @return array ['results' => array, 'summary' => array]

     */

    public function process_batch(array $tour_ids): array

    {

        $results = [];

        $summary = [

            'total' => count($tour_ids),

            'success' => 0,

            'errors' => 0,

            'skipped' => 0,

            'total_time' => 0,

        ];



        $batch_start_time = microtime(true);



        foreach ($tour_ids as $tour_id) {

            if (!is_numeric($tour_id) || $tour_id <= 0) {

                $results[] = $this->error_result(0, "Invalid tour_id: {$tour_id}");

                $summary['errors']++;

                continue;

            }



            $result = $this->process_single((int) $tour_id);

            $results[] = $result;



            // Update summary

            if ($result['status'] === 'success') {

                $summary['success']++;

            } elseif ($result['status'] === 'error') {

                $summary['errors']++;

            } elseif ($result['status'] === 'skipped') {

                $summary['skipped']++;

            }

        }



        $summary['total_time'] = round(microtime(true) - $batch_start_time, 2);

        $summary['avg_time'] = $summary['total'] > 0 ? round($summary['total_time'] / $summary['total'], 2) : 0;



        // Clear lookup cache after batch

        $this->lookup_service->clear_cache();



        return [

            'results' => $results,

            'summary' => $summary,

        ];

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

            error_log('ApiImportProcessor: ' . $message);

        }

    }



    /**

     * Log debug message

     */

    private function log_debug(string $message): void

    {

        if (defined('WP_DEBUG') && WP_DEBUG) {

            error_log('ApiImportProcessor: ' . $message);

        }

    }



    // ============================================

    // IMAGE PROCESSING

    // ============================================



    /**

     * Process and import images for a package

     *

     * @param int $post_id WordPress post ID

     * @param array $mapped_data Mapped data containing image URLs

     * @return int Number of images processed

     */

    private function process_images(int $post_id, array $mapped_data): int

    {

        try {

            $images_processed = 0;



            // 1. Process WordPress Featured Image (thumbnail from API)

            $this->debug_info[] = "featured_check=" . (!empty($mapped_data['meta_fields']['thumbnail_url']) ? 'yes' : 'no');

            if (!empty($mapped_data['meta_fields']['thumbnail_url'])) {

                $thumbnail_url = $mapped_data['meta_fields']['thumbnail_url'];

                $this->debug_info[] = "featured_url=" . substr($thumbnail_url, 0, 50);

                $attachment_id = $this->image_service->import_image($thumbnail_url, $post_id, 'Featured Image');

                $this->debug_info[] = "featured_attachment_id=" . ($attachment_id ?? 'NULL');



                if ($attachment_id) {

                    $result = set_post_thumbnail($post_id, $attachment_id);

                    $this->debug_info[] = "featured_set_result=" . ($result ? 'true' : 'false');

                    $images_processed++;

                    $this->log_debug("Featured image set (ID: {$attachment_id}) for post_id={$post_id}");

                } else {

                    $error = $this->image_service->last_error ?? 'unknown';

                    $this->debug_info[] = "featured_error=" . $error;

                }

            }



            // 2. Process map image (map_image field)

            if (!empty($mapped_data['meta_fields']['map_image'])) {

                $map_image_url = $mapped_data['meta_fields']['map_image'];

                $attachment_id = $this->image_service->import_image($map_image_url, $post_id, 'Map Image');



                if ($attachment_id) {

                    update_field('map_image', $attachment_id, $post_id);

                    $images_processed++;

                    $this->log_debug("Map image imported (ID: {$attachment_id}) for post_id={$post_id}");

                }

            }



            // 3. Process gallery

            if (!empty($mapped_data['repeaters']['gallery']) && is_array($mapped_data['repeaters']['gallery'])) {

                $gallery_images = $mapped_data['repeaters']['gallery'];

                $attachment_ids = $this->image_service->import_gallery($gallery_images, $post_id);



                if (!empty($attachment_ids)) {

                    // Gallery field expects a simple array of attachment IDs

                    update_field('gallery', $attachment_ids, $post_id);

                    $images_processed += count($attachment_ids);

                    $this->log_debug("Gallery processed: " . count($attachment_ids) . " images for post_id={$post_id}");

                }

            }



            // 4. Process itinerary images

            if (!empty($mapped_data['repeaters']['itinerary']) && is_array($mapped_data['repeaters']['itinerary'])) {

                $itinerary = $mapped_data['repeaters']['itinerary'];

                $updated_itinerary = [];



                foreach ($itinerary as $day_index => $day) {

                    $updated_day = $day;



                    // Process gallery for this itinerary day

                    if (!empty($day['gallery']) && is_array($day['gallery'])) {

                        $day_gallery_images = $day['gallery'];

                        $day_attachment_ids = $this->image_service->import_gallery($day_gallery_images, $post_id);



                        if (!empty($day_attachment_ids)) {

                            // Gallery field expects a simple array of attachment IDs

                            $updated_day['gallery'] = $day_attachment_ids;

                            $images_processed += count($day_attachment_ids);

                        }

                    }



                    $updated_itinerary[] = $updated_day;

                }



                // Update itinerary with new attachment IDs

                if ($images_processed > 0) {

                    update_field('itinerary', $updated_itinerary, $post_id);

                    $this->log_debug("Itinerary images processed for post_id={$post_id}");

                }

            }



            if ($images_processed > 0) {

                $this->log_debug("Total images processed: {$images_processed} for post_id={$post_id}");

            }



            return $images_processed;



        } catch (\Exception $e) {

            $this->log_error("Image processing error for post_id={$post_id}: " . $e->getMessage());

            // Don't throw - we don't want to fail the entire import if images fail

            return 0;

        }

    }

}

