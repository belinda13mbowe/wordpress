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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

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
define( 'AUTH_KEY',         '6n+;t<-?4.vU?dE*N nkoy!;}LaR+X8e36UYW{O|7/e[Op@dm?G!=~pSgi|*5ryQ' );
define( 'SECURE_AUTH_KEY',  'Nl9G!i[7akrnwnmC5+aJr2*1o8nVJD/9h![N0]M$Ed|A{KH1/z%!Ow.SXzjx73Pm' );
define( 'LOGGED_IN_KEY',    'w0|V8;ck<635@ Da):9fG5pT>H~y.n(xEOgoQCEE(DkWb.sTq6Nwp1+4ka)3rdi4' );
define( 'NONCE_KEY',        'j8&}Ai1sP$]$V*g)evXQN*^(yk.n`l{MA#r`6~Vx3i.7M~Ez3vU],WW>-Kx%oQ)~' );
define( 'AUTH_SALT',        '|f[U0y.;w.k/j$&Y |=%/KhSy{=s{y%.xIDn<RLbX0{ub/082lN[6j%:lRM@b2~Y' );
define( 'SECURE_AUTH_SALT', 'sQgqVb[`z[$`D1Z:H/c1dTbQG[BkFmIG[s`B_{1<=E97PCJzzd,psx34K98kloZ=' );
define( 'LOGGED_IN_SALT',   'd8I6AXa--$IRlr8|4;;LSFDM6eJRs6+$YW@~+L5?/Gyjv VLVv-mNsIzMj6>$n]c' );
define( 'NONCE_SALT',       'E!4nos[>>~1!5ha=HO{Zz4pdn:l2?jA[[9I0Ivf.IJp32=s%tG+X3Oz*r??)Nv+~' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
