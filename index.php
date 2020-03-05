<?php
/*
 * The New Awesome Framework
 */

// Configuration first
require( 'config.php' );

// Parse and sanitize HTTP request - a $request_uri variable is created
require( 'app/request.php' );

if ( $request_uri == '' ) {
  header( $_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden' );
  exit;
}

// Load global functions
require( 'app/utils.php' );
require( 'app/routing.php' );
require( 'app/template-tags.php' );

// Initiates the app
require( 'app/init.php' );

// Routing
if ( strpos( $_SERVER['HTTP_HOST'], 'api' ) !== false ) {
  // api
  response( $request_uri );
} else {
  // ui
  document( $request_uri );
}

?>