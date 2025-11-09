<?php
namespace ACF\FECP\DB;
if (!defined('ABSPATH')) exit;

class SchemaB2C {
  public static function create_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'acf_fecp_leads_b2c';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table} (
      id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      first_name VARCHAR(191),
      last_name VARCHAR(191),
      email VARCHAR(191),
      phone VARCHAR(100),
      country_code VARCHAR(10),
      description TEXT,
      company VARCHAR(191),
      rating VARCHAR(50),
      score_spam VARCHAR(50),
      holiday_type VARCHAR(100),
      destination_interes VARCHAR(100),
      package VARCHAR(191),
      package_link TEXT,
      travel_agent TINYINT(1) DEFAULT 0,
      type_lead TINYINT(1) DEFAULT 0,
      endpoint VARCHAR(191),
      base_uri TEXT,  
      sent_to_api TINYINT(1) DEFAULT 0,
      api_code INT DEFAULT NULL,
      api_response LONGTEXT DEFAULT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (id)
    ) {$charset};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
  }
}
