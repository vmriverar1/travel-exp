<?php

namespace Travel\Blocks\Services;

/**
 * Image Import Service
 *
 * Handles downloading images from external URLs and creating WordPress attachments.
 * Includes duplicate detection to avoid re-downloading existing images.
 *
 * @package Travel\Blocks\Services
 * @since 1.0.0
 */
class ImageImportService
{
    /**
     * Cache for URL to attachment_id mappings
     * Format: ['url' => attachment_id]
     *
     * @var array
     */
    private array $url_cache = [];

    /**
     * Import image from URL and return attachment ID
     *
     * @param string $image_url External image URL
     * @param int $post_id Post to attach image to
     * @param string $title Optional title for the image
     * @return int|null Attachment ID or null on failure
     */
    public function import_image(string $image_url, int $post_id, string $title = ''): ?int
    {
        if (empty($image_url)) {
            return null;
        }

        // Check cache first
        if (isset($this->url_cache[$image_url])) {
            $this->log_debug("Image found in cache: {$image_url}");
            return $this->url_cache[$image_url];
        }

        // Check if image already exists in database
        $existing_id = $this->find_existing_image($image_url);
        if ($existing_id) {
            $this->log_debug("Image already exists (ID: {$existing_id}): {$image_url}");
            $this->url_cache[$image_url] = $existing_id;
            return $existing_id;
        }

        // Download and create new attachment
        $attachment_id = $this->download_and_create_attachment($image_url, $post_id, $title);

        if ($attachment_id) {
            // Store URL in post meta for future duplicate detection
            update_post_meta($attachment_id, '_source_url', $image_url);

            // Cache the result
            $this->url_cache[$image_url] = $attachment_id;

            $this->log_debug("Image downloaded successfully (ID: {$attachment_id}): {$image_url}");
        }

        return $attachment_id;
    }

    /**
     * Import multiple images from gallery array
     *
     * @param array $images Array of image data from API
     * @param int $post_id Post to attach images to
     * @return array Array of attachment IDs
     */
    public function import_gallery(array $images, int $post_id): array
    {
        $attachment_ids = [];

        foreach ($images as $image) {
            $url = $image['originalImage'] ?? $image['image'] ?? '';
            $alt = $image['altText'] ?? $image['alt'] ?? '';

            if ($url) {
                $attachment_id = $this->import_image($url, $post_id, $alt);
                if ($attachment_id) {
                    $attachment_ids[] = $attachment_id;

                    // Set alt text if provided
                    if ($alt) {
                        update_post_meta($attachment_id, '_wp_attachment_image_alt', sanitize_text_field($alt));
                    }
                }
            }
        }

        return $attachment_ids;
    }

    /**
     * Find existing image attachment by URL
     *
     * @param string $url Image URL
     * @return int|null Attachment ID or null if not found
     */
    private function find_existing_image(string $url): ?int
    {
        global $wpdb;

        // Search by _source_url meta
        $attachment_id = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta}
             WHERE meta_key = '_source_url'
             AND meta_value = %s
             LIMIT 1",
            $url
        ));

        if ($attachment_id) {
            return (int) $attachment_id;
        }

        // Fallback: search by guid (uploaded URL)
        $filename = basename(parse_url($url, PHP_URL_PATH));

        $attachment_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts}
             WHERE post_type = 'attachment'
             AND guid LIKE %s
             LIMIT 1",
            '%' . $wpdb->esc_like($filename)
        ));

        return $attachment_id ? (int) $attachment_id : null;
    }

    /**
     * Download image from URL and create WordPress attachment
     *
     * @param string $url Image URL
     * @param int $post_id Post to attach to
     * @param string $title Image title
     * @return int|null Attachment ID or null on failure
     */
    private function download_and_create_attachment(string $url, int $post_id, string $title = ''): ?int
    {
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->log_error("Invalid image URL: {$url}");
            return null;
        }

        // Download image
        $temp_file = $this->download_image($url);
        if (!$temp_file) {
            return null;
        }

        // Get filename
        $filename = $this->get_safe_filename($url, $title);

        // Prepare file array for wp_handle_sideload
        $file = [
            'name' => $filename,
            'type' => $this->get_mime_type($temp_file),
            'tmp_name' => $temp_file,
            'error' => 0,
            'size' => filesize($temp_file),
        ];

        // Move to uploads directory
        $uploaded = wp_handle_sideload($file, ['test_form' => false]);

        if (isset($uploaded['error'])) {
            $this->log_error("Upload error: {$uploaded['error']}");
            @unlink($temp_file);
            return null;
        }

        // Create attachment post
        $attachment_data = [
            'post_mime_type' => $uploaded['type'],
            'post_title' => $title ?: sanitize_title($filename),
            'post_content' => '',
            'post_status' => 'inherit',
        ];

        $attachment_id = wp_insert_attachment($attachment_data, $uploaded['file'], $post_id);

        if (is_wp_error($attachment_id)) {
            $this->log_error("Failed to create attachment: " . $attachment_id->get_error_message());
            return null;
        }

        // Generate attachment metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attachment_id, $uploaded['file']);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        return $attachment_id;
    }

    /**
     * Download image to temporary file
     *
     * @param string $url Image URL
     * @return string|null Temporary file path or null on failure
     */
    private function download_image(string $url): ?string
    {
        // Use wp_remote_get to download
        $response = wp_remote_get($url, [
            'timeout' => 30,
            'sslverify' => false,
        ]);

        if (is_wp_error($response)) {
            $this->log_error("Download failed: " . $response->get_error_message());
            return null;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            $this->log_error("HTTP {$status_code} when downloading: {$url}");
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            $this->log_error("Empty response body for: {$url}");
            return null;
        }

        // Save to temporary file
        $temp_file = wp_tempnam();
        if (!file_put_contents($temp_file, $body)) {
            $this->log_error("Failed to write temp file");
            return null;
        }

        return $temp_file;
    }

    /**
     * Get safe filename from URL
     *
     * @param string $url Image URL
     * @param string $title Optional title
     * @return string Safe filename
     */
    private function get_safe_filename(string $url, string $title = ''): string
    {
        // Try to use title first
        if ($title) {
            $filename = sanitize_file_name($title);
            if ($filename) {
                // Add extension from URL
                $ext = $this->get_extension_from_url($url);
                return $filename . ($ext ? '.' . $ext : '.jpg');
            }
        }

        // Fallback to URL basename
        $filename = basename(parse_url($url, PHP_URL_PATH));
        return sanitize_file_name($filename) ?: 'image-' . time() . '.jpg';
    }

    /**
     * Get file extension from URL
     *
     * @param string $url Image URL
     * @return string Extension without dot
     */
    private function get_extension_from_url(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        return strtolower($ext);
    }

    /**
     * Get MIME type from file
     *
     * @param string $file File path
     * @return string MIME type
     */
    private function get_mime_type(string $file): string
    {
        $mime = mime_content_type($file);
        return $mime ?: 'image/jpeg';
    }

    /**
     * Clear internal cache
     *
     * @return void
     */
    public function clear_cache(): void
    {
        $this->url_cache = [];
    }

    /**
     * Get cache statistics
     *
     * @return array
     */
    public function get_cache_stats(): array
    {
        return [
            'size' => count($this->url_cache),
            'urls' => array_keys($this->url_cache),
        ];
    }

    /**
     * Log error message
     */
    private function log_error(string $message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ImageImportService: ' . $message);
        }
    }

    /**
     * Log debug message
     */
    private function log_debug(string $message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ImageImportService: ' . $message);
        }
    }
}
