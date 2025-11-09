<?php

define( 'DB_NAME', 'wordpress_travel' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'Root@123' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', 'utf8mb4_unicode_520_ci' );

define( 'REDIS_HOST', '127.0.0.1' );
define( 'REDIS_PORT', 6379 );
define( 'REDIS_AUTH', null );
define( 'REDIS_DATABASE', 0 );

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG_LOG', '/home/web/sites/travel/logs/debug.log' );

define( 'AUTH_KEY', 'vw3tn96l4koqj3n9z3ayswa86m2gkmcofwzn3rgokdgyxwkg0cvxxkg9ix6ybhj2' );
define( 'AUTH_SALT', 'ly5e86f198al3pla382p8l8we231y56zdjy1n8h3s9kpfl5d9aoctdphqhg8w21u' );

define( 'SECURE_AUTH_KEY', 're9avxyxhcxfnglld8sunqdzs56acrvh2ajnvo0t99uptp2046qwh94y8phorwcd' );
define( 'SECURE_AUTH_SALT', 'ukf7r3cwp80faurung3i6zzw2edmuwf279ador614zr512hvwpm6vk3ugikmtqah' );
define( 'LOGGED_IN_KEY', 'y5jafsqjf0g5uhaq0eykkqpxl3c2v0sz9pvloyk4v5fscprikbivrpdwn9onvoon' );
define( 'LOGGED_IN_SALT', 'jfii4bnjpsvsnfrure307bhiz4xxj4mhr8wndwcj5jx23vm1l48b9gr71wbs5n38' );
define( 'NONCE_KEY', '8ylxykhbcfbcesm0wpu2ca39wftid6yaw6gloxs47uic5qv37gn56dc8pyagnil6' );
define( 'NONCE_SALT', 'dkup8drrjsoa8hcounx2wmtzi2mynlxr5glw7dqf5brafa5j6qmp966nni5p8d32' );
define( 'WP_CACHE_KEY_SALT', 'wp003_' );

define( 'WP_MEMORY_LIMIT', '64M' );  // front
define( 'WP_MAX_MEMORY_LIMIT', '128M' ); // admin

define( 'WP_ENVIRONMENT_TYPE', 'production' );
define( 'WP_DEFAULT_THEME', 'travel-theme' );
define( 'DISABLE_WP_CRON', true );

define( 'WP_POST_REVISIONS', -1 );
define( 'AUTOSAVE_INTERVAL', 60 );

define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );

define( 'WP_AUTO_UPDATE_CORE', false );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'CORE_UPGRADE_SKIP_NEW_BUNDLE', true );

define( 'CONCATENATE_SCRIPTS', false );
define( 'COMPRESS_SCRIPTS', false );
define( 'COMPRESS_CSS', false );

$table_prefix = 'wp003_';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
