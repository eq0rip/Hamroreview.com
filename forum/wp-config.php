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
define('DB_NAME', 'hamrorev_wp742');

/** MySQL database username */
define('DB_USER', 'hamrorev_wp742');

/** MySQL database password */
define('DB_PASSWORD', '4b8BP[PS7!');

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
define('AUTH_KEY',         'vxfq6mitw3ndger5wtyeardhaiplsg9igmiv8rpvti0y6cggc3dbuwpbjo3djc5e');
define('SECURE_AUTH_KEY',  'sodlxuyenibtqcy8kcyznckinkusxqdefuh00y1na9k3lqq93zbhqkokomp0fqko');
define('LOGGED_IN_KEY',    'iujrpfoi1ioqkjngja1qyxkgnbmqu607b9gjyyfjskoynlw9ohvf10yqeanl7mxf');
define('NONCE_KEY',        'p1dljoloveecnsivq8zwkxxhr0ve0smf5ycssnyfzwsguwgt0o5ngywqj0bls4sk');
define('AUTH_SALT',        '1nndo5uyfdipbfljgzqcwrwt2lqedsfowguxokpbwcyszo6p5g11l4mr1a5umxhm');
define('SECURE_AUTH_SALT', 'lmfu7hmaqqdhj5b8t8srdrcciryvlcpoaheafyzgu7kebkmq1yi6rwbor1k2firw');
define('LOGGED_IN_SALT',   'z9fg7g2q7jtwqo09hoemcnyq3bdjduiozk4mvssb1ikchp02om3rt3coodad9woy');
define('NONCE_SALT',       'acpy590cmtwv0kk4beubqpuhoprzcri7a3xd01ceyx2tq7osmlqzgv5d8juqouqq');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
