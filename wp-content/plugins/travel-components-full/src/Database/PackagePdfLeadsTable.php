<?php
/**
 * Package PDF Leads Table
 *
 * Manages the database table for PDF download leads
 *
 * @package Travel\Components\Database
 * @since 1.0.0
 */

namespace Travel\Components\Database;

class PackagePdfLeadsTable
{
    /**
     * Table name
     *
     * @var string
     */
    private $table_name;

    /**
     * Constructor
     */
    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'package_pdf_leads';
    }

    /**
     * Create the table
     */
    public function create_table(): void
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            package_id INT(11) NOT NULL,
            user_name VARCHAR(255) NOT NULL,
            user_email VARCHAR(255) NOT NULL,
            downloaded_at DATETIME NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            INDEX idx_package_id (package_id),
            INDEX idx_email (user_email),
            INDEX idx_downloaded (downloaded_at)
        ) {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Insert a new lead
     *
     * @param int $package_id Package post ID
     * @param string $user_name User name
     * @param string $user_email User email
     * @return int|false Insert ID or false on failure
     */
    public function insert_lead(int $package_id, string $user_name, string $user_email)
    {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table_name,
            [
                'package_id' => $package_id,
                'user_name' => sanitize_text_field($user_name),
                'user_email' => sanitize_email($user_email),
                'downloaded_at' => current_time('mysql'),
                'ip_address' => $this->get_user_ip(),
                'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
            ],
            ['%d', '%s', '%s', '%s', '%s', '%s']
        );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Get user IP address
     *
     * @return string
     */
    private function get_user_ip(): string
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
     * Get leads by package ID
     *
     * @param int $package_id Package post ID
     * @param int $limit Number of leads to retrieve
     * @return array
     */
    public function get_leads_by_package(int $package_id, int $limit = 100): array
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE package_id = %d ORDER BY downloaded_at DESC LIMIT %d",
            $package_id,
            $limit
        );

        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * Get total leads count
     *
     * @return int
     */
    public function get_total_leads(): int
    {
        global $wpdb;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
    }

    /**
     * Check if email already downloaded this package
     *
     * @param int $package_id Package post ID
     * @param string $email User email
     * @return bool
     */
    public function has_downloaded(int $package_id, string $email): bool
    {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name} WHERE package_id = %d AND user_email = %s",
                $package_id,
                sanitize_email($email)
            )
        );

        return (int) $count > 0;
    }
}
