<?php
/*
 * Classes loading
 */
spl_autoload_register( function ( $class ) {
  $class = str_replace( '\\', '/', $class );
  $class = strtolower( $class );
  foreach ( [ 'classes', 'views', 'models' ] as $directory ) {
    if ( file_exists( 'app/' . $directory . '/' . $class . '.php' ) ) {
      include ( 'app/' . $directory . '/' . $class . '.php' );
    }
  }
} );

/*
 * Error handling
 * Collects errors to be shown in the UI or in the API response
 */
set_error_handler( array( 'Errors', 'handler' ) );