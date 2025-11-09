<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'travel' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'W6:8C5>aRZOOOwQ2X52ob,7EZ.=+Rz/|C9v8.x8i<hU>qUb4YxhZYa R><)f@TgL' );
define( 'SECURE_AUTH_KEY',  '(iEfQ[X?7vTNnf7n`M/DPqF&g%;P1eMz<Qy>H7xd$c@)EI}Bu7hKRd`JkP:Gpjzo' );
define( 'LOGGED_IN_KEY',    'fM$W]GnqOr,dgh(*]pjK%+AoB; VBVr0p.KZk(rzZ.j92!`LHC< %u75eqcerw0A' );
define( 'NONCE_KEY',        'yKgb1Oi]$ML<XrFyVx,G{G;]7Vrwd.1=910Map-u,|LC1]y.gy=t$!?t,-o@`zdP' );
define( 'AUTH_SALT',        'xw2L?-/E3,8,63w,dlQx..8`tvhn_B7}p,t;;NP@pp$`}1D#GIY#j@&Y4{sxI7&-' );
define( 'SECURE_AUTH_SALT', 'gmvbKt/a $LM}g/f6Y5G&=~[rwS3 >+,yPd]0CO*6m/9,19t3vvO_1#})xSv&JRm' );
define( 'LOGGED_IN_SALT',   '~xE9i9}z7l}KZS-LBpT/}|%pe9 _D,u]YpR;6:/C1=P~BtPKBmw2B%|prK+E:<*O' );
define( 'NONCE_SALT',       'q*N#~2g,?q<e];EVr;hj&#kYfbNaq8YH_u-yy04%]k|-/Q8TPdlMJ?3DM_jx3(uM' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wptra_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
