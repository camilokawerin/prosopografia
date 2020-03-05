<?php
/**
 * 
 */
class Localization {
  
  static $locale ;

  static function get_locale() {
    self::$locale = array();
    foreach ( file( 'localized_vars' ) as $line ) {
      list( $left, $right ) = preg_split( '/\s+/', $line );
      self::$locale[ $left ] = $right;
    }
  }
  
  static function input( $var ) {
    if ( ! is_array( self::$locale ) ) {
      self::get_locale();
    }
    if ( $key = array_search( $var, self::$locale ) ) {
      return $key;
    }
    return $var;
  }

  static function output( $var ) {
    if ( ! is_array( self::$locale ) ) {
      self::get_locale();
    }
    if ( isset( self::$locale[ $var ] ) ) {
      return self::$locale[ $var ];
    }
    return $var;
  }
}
?>