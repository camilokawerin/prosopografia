<?php
/**
 * 
 */
class Errors {
  
  static $errors = array();

  static function handler( $errorno, $errstr, $errfile, $errline ) {
    self::$errors[] = array( $errstr, ' at ' . $errfile . ' (' . $errline . ')');
  }

  static function count() {
    return count( self::$errors );
  }

  static function show() {
    $list = '<pre>
              <ul>' . PHP_EOL;
    foreach ( self::$errors as $error ) {
      $list .= '<li class="xdebug-error-output">' . implode( PHP_EOL, $error ) . '</li>' . PHP_EOL;
    }
    $list .= '</ul>
            </pre>' . PHP_EOL;
    return $list;
  }

  static function get() {
    return self::$errors;
  }
  
}
?>