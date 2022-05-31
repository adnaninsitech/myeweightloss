<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'creapkxk_weight-loss' );

/** Database username */
define( 'DB_USER', 'creapkxk_weight-loss' );

/** Database password */
define( 'DB_PASSWORD', 'T.XLAR-v,y*g' );

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
define( 'AUTH_KEY',         'x*vbZ[P;hVmP%POvE]f,7Z8aD2=|fyeszXiA~US7 WSd];XJS>po$fckCYn 9MD2' );
define( 'SECURE_AUTH_KEY',  '^~fFkoB7E)6{wBkn;IaNFxS5A)$.2H+YYT}sY`DKBx(:qefoWw@JqGL$&.bOw<mO' );
define( 'LOGGED_IN_KEY',    '[rr?)A`V^B)p.Q&VRNqRTU7Z3Q]ZXwcrPYJ ; D3&9q40e-P@5B&U~pyyjLj*1m*' );
define( 'NONCE_KEY',        'n!C%KUfFg9%tZH*4`WU<MuZ)s?0pe6[=lZo+D|j;RHrDG(h{!n1zWlaT>_6=~fB0' );
define( 'AUTH_SALT',        '/~>3QK;$:x]7O(X@muRPTzL}+4,n{7v)Ioh#1R(_I*YM!LDK9,ZNXz8WN[t6}eFB' );
define( 'SECURE_AUTH_SALT', '~y{pXF#Jo>l9J!:y+26fMBMJGbwkVZTpTzS?-bD&;^9tEtB)?OaRH{LEQ=QLgdc_' );
define( 'LOGGED_IN_SALT',   'H9Z6U~7FlYO`1WOqyKj`q`5}AQkx|F{9x7R>Zlv-^Ti1o+rUbVLbt=~h9jp9R,Ps' );
define( 'NONCE_SALT',       '_>yXMl:3cC^HVa=4y?$lC}kSV|b.P;oP{0613m=g[0rVRIA`..l78ATk9z..u$,(' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
