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
define( 'DB_NAME', 'test' );

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
define( 'AUTH_KEY',         'o9~9MA}=YMCx<a%jxqZ}13AhoB$5h1xydY#~#_f|<,jd^a(]kia[,!<NN0dj2%EW' );
define( 'SECURE_AUTH_KEY',  '>83b3T28LLKt$%KSjt^+`D`uZ*=aT4U ;%5M1Thk-3Z,`p,IxD_+_H57Dx,HyYuy' );
define( 'LOGGED_IN_KEY',    'e;`;}?[h N!-HC 3S~1nFLLO%^$gtl:qVeI;bK(vUVT[&&wV+S4bDp(>CEwFm~~V' );
define( 'NONCE_KEY',        '&s9K94$jVHn+lJ7Tw!r>Vhd29&(ryhy(:*E9#JM<2;WWtCBPg;l,`T9YHG_S!86s' );
define( 'AUTH_SALT',        'tHmL3`mX<PI6r^n19azo(NLYT-Z&t[:+t$_=f*JZWe9MFA|$8w9*u}4Wv?V}FJ-@' );
define( 'SECURE_AUTH_SALT', '$Fqx @3=Je5&%]AF=$)cQ72yLqNELqGwLqVNFZ*J=uMf5A}GUgjIUe3<F&UK|:[~' );
define( 'LOGGED_IN_SALT',   ' ^&0wuft`&t[E:rCbCbt!}y>3b!Gnv3H.XJ|8.OF(UE<v&)S^<t>O^(tFZ=)w(@}' );
define( 'NONCE_SALT',       '|#JX=t$82p<d{p;ZJ?Rfzn^]2Ka2ebshB*W9_2%$:;-s>!>s~}+c:<?_+9u$[(12' );

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
