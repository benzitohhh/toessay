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
define('DB_NAME', 'wp-toessay-local');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'toessay.co.uk.ben');

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
define('AUTH_KEY',         '#N4)=|.K]YA.x3 Dg&GY[1^8(4hhZunSKU7,a@%R2s36%3;YbZUd$a$R<Zxs[5(B');
define('SECURE_AUTH_KEY',  'SQ&av@V(XyPoR6f3u)l*c1{2SooCGq!&-/cR)>Na?L<DmgTP  yd2)}{hPp~(. F');
define('LOGGED_IN_KEY',    'm7+?s~ K]+Ul4PvFS9K)w433~pzx+B4!h&A^gn&P+$o+Y5L>K8BUo TFpsh>]}Am');
define('NONCE_KEY',        '5d bCewLt0?|TxQe7e*~{;*mhb{_3+|z?n0]tQ=yq|Z!p/qxuE-r6;(K[wN&e|#U');
define('AUTH_SALT',        '4*a1[4/!SJ|- 8bZ3+-,}zY&_4j,o41r?+|TREk6tcD!AaTT ~ef@&v0Q-ltT+=]');
define('SECURE_AUTH_SALT', ',5ubz9#3SA]XAZ:yXw_J68U&~y}a!Jk?W20U%1nzUKUA4|s9=cl:^C+g?9=X+QAp');
define('LOGGED_IN_SALT',   'Jx5RN[66||g!Es9+;e7e=6g*6_+ALD|V5,5QS)Nc`+K@lNXofd|b~1K5qJmf<=??');
define('NONCE_SALT',       'I@oGda:XY</GwW?|FcJcD_V%GThtg:/bN2.=)G,(Kl,X4*3Z+M+IO#E>wKS#Oscd');
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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
