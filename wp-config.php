<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'hamroreview');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'kr7kog5zkpvbdw4pssk1rpu34lu18p4nkrfnzkk1iadnkzjziujktgqmckrtgpmy');
define('SECURE_AUTH_KEY',  '0hfdg18d5c6ynknc2yhwmwhswfrexhl7fdvxrka1nbevteochpvkizgjvbs2djmo');
define('LOGGED_IN_KEY',    '73m3ozith3pjoqlgbq440f5row94qpqshmhrm8chttkhwqlo7xcbqm0cbzssxbgh');
define('NONCE_KEY',        'uaojhfujyi9n8dxgliyyfnib9hq7qs8ydgbi4rugurwm24zmettpla41lpxzzyoq');
define('AUTH_SALT',        '0ppynibnmhzbozocmm8ydlrblngmik9ussifyyhvgcpupuya5bo20ettrltiujcw');
define('SECURE_AUTH_SALT', 't0f3ty0e4euuanjr4pd9kxymq5eoyn2x9yyhqyw3ob6vlhkmbxe0jofzevf0chf2');
define('LOGGED_IN_SALT',   'wsedpkxz4upcyyvkmyotfoeeq3jtm0sv9vpzw8qd3sm2xujlkfg2oshnvypupzrc');
define('NONCE_SALT',       'zjlxn004igi7zm6xgpdttdvndnstd9ppylrc5cdhbccmeuqkzgclofda8cotrxru');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'hr_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
define('FS_METHOD', 'direct');