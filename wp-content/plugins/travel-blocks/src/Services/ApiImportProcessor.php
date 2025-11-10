<?php

namespace Travel\Blocks\Services;

/**
 * API Import Processor
 *
 * Handles the complete import process from Valencia API to WordPress packages.
 * Processes tours individually with full error handling and reporting.
 *
 * @package Travel\Blocks\Services
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
     * Options for processing
     */
    private array $options = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->api_service = new PackageApiService();
        $this->lookup_service = new PackageLookupService();
        $this->mapper = new ApiDataMapper();
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
                return $this->error_result($tour_id, 'API returned empty data or tour does not exist');
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

        $this->log_debug("Created package post_id={$post_id} for tour_id={$tour_id}");

        return $this->success_result($tour_id, 'Package created successfully', [
            'action' => 'create',
            'post_id' => $post_id,
            'title' => $mapped_data['post_data']['post_title'],
            'url' => get_permalink($post_id),
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

        // Update post data
        $mapped_data['post_data']['ID'] = $post_id;
        $result = wp_update_post($mapped_data['post_data'], true);

        if (is_wp_error($result)) {
            return $this->error_result($tour_id, 'Failed to update post: ' . $result->get_error_message());
        }

        // Save meta fields and relations
        $save_result = $this->save_package_data($post_id, $mapped_data);

        if (!$save_result['success']) {
            return $this->error_result($tour_id, 'Failed to update data: ' . $save_result['message']);
        }

        $this->log_debug("Updated package post_id={$post_id} for tour_id={$tour_id}");

        return $this->success_result($tour_id, 'Package updated successfully', [
            'action' => 'update',
            'post_id' => $post_id,
            'title' => $mapped_data['post_data']['post_title'],
            'url' => get_permalink($post_id),
        ]);
    }

    /**
     * Save all package data (meta fields, taxonomies, relations, repeaters)
     *
     * @param int $post_id WordPress post ID
     * @param array $mapped_data Mapped data
     * @return array ['success' => bool, 'message' => string]
     */
    private function save_package_data(int $post_id, array $mapped_data): array
    {
        try {
            // 1. Save simple meta fields
            foreach ($mapped_data['meta_fields'] as $field_key => $field_value) {
                update_field($field_key, $field_value, $post_id);
            }

            // 2. Assign taxonomies
            foreach ($mapped_data['taxonomies'] as $taxonomy => $term_ids) {
                if (!empty($term_ids)) {
                    wp_set_object_terms($post_id, $term_ids, $taxonomy);
                }
            }

            // 3. Save post object relations
            foreach ($mapped_data['post_objects'] as $field_key => $post_ids) {
                if (!empty($post_ids)) {
                    update_field($field_key, $post_ids, $post_id);
                }
            }

            // 4. Save repeater fields
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
}
