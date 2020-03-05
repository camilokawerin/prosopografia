<?php
function is_associative( $array ) {
  return is_array( $array ) && count( array_filter( array_keys( $array ), 'is_string' ) ) > 0;
}

function replace_key( & $array, $old_key, $new_key ) {
  $keys = array_keys( $array );
  if ( false === $index = array_search( $old_key, $keys, true ) ) {
    throw new Exception( sprintf( 'Key "%s" does not exist', $old_key ) );
  }
  $keys[ $index ] = $new_key;
  return array_combine( $keys, array_values( $array ) );
}

function notice() {
  $args = func_get_args();
  ob_start();
  foreach ( $args as $arg ) {
    var_dump( $arg );
  }
  return ob_get_clean();
}
?>