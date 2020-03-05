<?php
/**
 * The base configuration for The New Awesome API
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 *
 */
/** The name of the database */

define( 'DB_NAME', 'prosopografia' );
/** MySQL database username */
define( 'DB_USER', 'root' );
/** MySQL database password */
define( 'DB_PASSWORD', '' );
/** MySQL hostname */
define( 'DB_HOST', 'localhost' );
/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );
/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );


/** Development settings */

define( 'DISPLAY_ERRORS', true );
define( 'DISPLAY_ERRORS_TAG', 'main' );
define( 'DISPLAY_API_OUTPUT', true );
define( 'ALLOW_DEBUG', true );

define( 'LOCALHOST', true );
define( 'API_LOCAL_URI', '' );
define( 'API_REMOTE_URI', '' );

?>