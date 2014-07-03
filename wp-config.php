<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', '-');

/** MySQL database username */
define('DB_USER', '-');

/** MySQL database password */
define('DB_PASSWORD', '-');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '4uC3CMRCIbT8abG4x8madKE8QPlbV9ETr6Krfo57e7bMXIFwoxibjG1uzl3H8VXO');
define('SECURE_AUTH_KEY',  'VNbhgB9rbqiGeo9nVgqvSvEyVCQAfKX52o4sHoj7gRpAVj7Uol9NBVMAA3rwNdVd');
define('LOGGED_IN_KEY',    'bxgorpVWciBxvtjblaSlbFln4A7vvyXyrvqOyBBM31fdATNIJUoP6p5Y1aw43JmJ');
define('NONCE_KEY',        'HiAzA3CENwr6XHJShY3ru4Zmy9uChFQjndwI9M0DA2gleh1BMACVvBST3JQ44g9Z');
define('AUTH_SALT',        'nuXkn5RF6AJrDFeeFhAKBdu8oTFrIio4WXmmaKaBpRuTIxXCb203VPaNyvjFJT8W');
define('SECURE_AUTH_SALT', 'dR6eoI6rdba2Lvp0d5SUDezu5g9WPqOAgQwDNqEG9rcE1xTqnVozWsw5XSlRDtvP');
define('LOGGED_IN_SALT',   'wUcCW4Ca1esp7XATbuqSsG3LaIyv4JWECgDgU1EJ5Ho4keC0dEhxjBbNQco8DBbY');
define('NONCE_SALT',       'ILKNwilH4fWOiROn2af60gy4zDSgfA4qF3TlEHcpU3JJAGSxIuld20WZcHFGuIFP');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
/** Disable the WP Admin Bar */
define('BP_DISABLE_ADMIN_BAR', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
