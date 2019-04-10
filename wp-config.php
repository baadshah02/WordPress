<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'WP_database' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'mysql' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'h5;Sz3&J!zqGsq&Lzwn=Nja;nRMDTM}Gbz~G+My<JvL`N7F4 /2$aeJ2D|E~trrP' );
define( 'SECURE_AUTH_KEY',  'ZIdu3Fa4G>XR|F-&zBo)eiY@e7lkUaG@=v4yKacbR$x+8Xu9:7#g_}gyNUD2,lU!' );
define( 'LOGGED_IN_KEY',    'WQ>99E7k?Fm+cnLUIxmH}`JBKT1232|^c=X3U;MKHS$WSZ^<rt1Fl7hS03s7jG `' );
define( 'NONCE_KEY',        'XMY)HUV+mPKq3g+Bn,h1=a<ro2%<EF/<CNT~TK-@H-]:Gl/_/Fu2PDz/~IbCbyv/' );
define( 'AUTH_SALT',        'E8iDACB~Is{H{AN]MfSEiwio^8s:h(y7P;Q6Z7fE(w;R?j*y,5 !:%l`#n.Ik:1h' );
define( 'SECURE_AUTH_SALT', '{X1aX~eV]b-DeWSv%LnSV@g.xenu#6.IpeaFpesVK/?.iBIrqq);O1;).*8?S Ae' );
define( 'LOGGED_IN_SALT',   '417!.$wbGlfl=Tv*!4idzSogUmLG>?;q,nl~3.y}cX+-,pXu<v0j-;u9M[84$V=M' );
define( 'NONCE_SALT',       'C3,Ky<MNg4phb4c|_44c?##Au@*4rMkW{I;H6ET+M[+6-?s&hkkb1@,B[ybl0^Nn' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
