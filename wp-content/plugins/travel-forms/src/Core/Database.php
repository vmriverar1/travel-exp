<?php
/**
 * Database Manager
 *
 * Handles creation and management of form submissions table.
 *
 * @package Travel\Forms\Core
 * @since 1.0.0
 */

namespace Travel\Forms\Core;

class Database
{
    /**
     * Table name (without prefix).
     */
    const TABLE_NAME = 'form_submissions';

    /**
     * Create the form submissions table.
     *
     * @return void
     */
    public static function create_table(): void
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            form_type varchar(50) NOT NULL,
            form_data longtext NOT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent varchar(255) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            hubspot_sent tinyint(1) DEFAULT 0,
            hubspot_contact_id varchar(50) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY form_type (form_type),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Insert a form submission.
     *
     * @param string $form_type Form type identifier
     * @param array  $form_data Form data
     *
     * @return int|false Submission ID on success, false on failure
     */
    public static function insert_submission(string $form_type, array $form_data)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $result = $wpdb->insert(
            $table_name,
            [
                'form_type' => $form_type,
                'form_data' => wp_json_encode($form_data),
                'ip_address' => self::get_client_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'status' => 'pending',
            ],
            ['%s', '%s', '%s', '%s', '%s']
        );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Update submission status.
     *
     * @param int    $submission_id Submission ID
     * @param string $status        New status
     *
     * @return bool
     */
    public static function update_status(int $submission_id, string $status): bool
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        return (bool) $wpdb->update(
            $table_name,
            ['status' => $status],
            ['id' => $submission_id],
            ['%s'],
            ['%d']
        );
    }

    /**
     * Mark submission as sent to HubSpot.
     *
     * @param int    $submission_id    Submission ID
     * @param string $hubspot_contact_id HubSpot contact ID
     *
     * @return bool
     */
    public static function mark_hubspot_sent(int $submission_id, string $hubspot_contact_id = ''): bool
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        return (bool) $wpdb->update(
            $table_name,
            [
                'hubspot_sent' => 1,
                'hubspot_contact_id' => $hubspot_contact_id,
            ],
            ['id' => $submission_id],
            ['%d', '%s'],
            ['%d']
        );
    }

    /**
     * Get client IP address.
     *
     * @return string
     */
    private static function get_client_ip(): string
    {
        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }

        return sanitize_text_field($ip);
    }

    /**
     * Get submissions by form type.
     *
     * @param string $form_type Form type
     * @param int    $limit     Number of submissions to retrieve
     * @param int    $offset    Offset for pagination
     *
     * @return array
     */
    public static function get_submissions(string $form_type = '', int $limit = 50, int $offset = 0): array
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        if ($form_type) {
            $query = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE form_type = %s ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $form_type,
                $limit,
                $offset
            );
        } else {
            $query = $wpdb->prepare(
                "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $limit,
                $offset
            );
        }

        $results = $wpdb->get_results($query, ARRAY_A);

        // Decode JSON form_data for each submission
        foreach ($results as &$submission) {
            $submission['form_data'] = json_decode($submission['form_data'], true);
        }

        return $results;
    }
}
