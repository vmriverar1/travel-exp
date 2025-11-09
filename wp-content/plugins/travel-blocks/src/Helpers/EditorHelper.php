<?php
/**
 * Editor Helper
 *
 * Utility functions for editor detection and preview mode
 *
 * @package Travel\Blocks\Helpers
 * @since 1.0.0
 */

namespace Travel\Blocks\Helpers;

class EditorHelper
{
    /**
     * Check if we're in editor/preview mode
     *
     * @param int|null $post_id Optional post ID to check
     * @return bool True if in editor mode
     */
    public static function is_editor_mode(?int $post_id = null): bool
    {
        // Site Editor canvas parameter (primary check for template editing)
        if (isset($_GET['canvas']) && $_GET['canvas'] === 'edit') {
            return true;
        }

        // REST API request (Gutenberg editor)
        if (defined('REST_REQUEST') && REST_REQUEST) {
            return true;
        }

        // Edit context parameter
        if (isset($_GET['context']) && $_GET['context'] === 'edit') {
            return true;
        }

        // Site editor page check
        if (isset($_GET['page']) && (
            $_GET['page'] === 'gutenberg-edit-site' ||
            strpos($_GET['page'], 'site-editor') !== false
        )) {
            return true;
        }

        // Admin area (but not for actual page views)
        if (is_admin() && !wp_doing_ajax()) {
            return true;
        }

        // No post ID available (definitely preview/editor)
        if (!$post_id) {
            return true;
        }

        // Auto-draft posts (new posts being edited)
        if ($post_id && get_post_status($post_id) === 'auto-draft') {
            return true;
        }

        // Check if we're rendering a template (not an actual post)
        global $wp_query;
        if (isset($wp_query->query_vars['is_template']) ||
            (isset($wp_query->query['template']) && $wp_query->query['template'])) {
            return true;
        }

        return false;
    }

    /**
     * Check if current context is Site Editor
     *
     * @return bool
     */
    public static function is_site_editor(): bool
    {
        return (
            is_admin() &&
            function_exists('wp_is_block_theme') &&
            wp_is_block_theme() &&
            isset($_GET['page']) &&
            $_GET['page'] === 'gutenberg-edit-site'
        );
    }
}
