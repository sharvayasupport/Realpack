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
define( 'DB_NAME', 'RealPack' );

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
define( 'AUTH_KEY',         '3Dvsnlw[G8&*FqpOe]n%m2wP:+92Qd-:ib:V]L3qj6C<60>tI>*d0lD8v_G-Q{n+' );
define( 'SECURE_AUTH_KEY',  '}|G})I@,MQl7cAR<Dn;{NlUy#!w7;vj[].]SA?`ZX e)JX`]6VmVU^2M>z+_<v{m' );
define( 'LOGGED_IN_KEY',    'd}!pQ&L]JAgKgH)52bkEm9i:wnY]Tn=B=4~cB2ZKiw| dj?D|c{9-?%.!_!Eyo@!' );
define( 'NONCE_KEY',        '>}d#mEaGZ8$Q:v.j>&/HORn!:u,1c/MvvD}2B*`1hFcNtlDd(s][6J?vd#Q>$$M?' );
define( 'AUTH_SALT',        'Po2z1$cJB%Sl+3MajMG87*apvj8.= v!-E/=&DZd<_kB>jO?5@f^<V+_7~ub.[Kg' );
define( 'SECURE_AUTH_SALT', '%|rS?u11>aT7yxYV5lbKBG~@By!Q){2*7`d|*Qm6Z.%pLY2D=hN]:LmThpf??mFo' );
define( 'LOGGED_IN_SALT',   'r|JPJekW/Nvho<8?E1:}r8$F@WKhZ!;[lwTE;/g2p4%:`10Q3I!LnoVb#+9aNXWa' );
define( 'NONCE_SALT',       '.b|#79V)m6u(+Vd[jp#&q~,$CWk3cz{*>$XW 8TXa!b+}UfLO-]7%]$)xFDCep/l' );

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
