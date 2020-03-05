<?php
/**
 * Base class for database interaction
 * Entities extends this to implement specific interface
 */

 class Database {

   static $mysql;

   static function connect() {
    if ( ! is_object( self::$mysql ) ) {
      self::$mysql = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
      self::$mysql->set_charset(DB_CHARSET);
    }
   }
 
   static function real_escape_string( & $value ) {
    self::connect();
    if ( preg_match( '/["]*(.+[^"])["]*/', $value, $match ) and count( $match ) == 2 ) {
      $value = self::$mysql->real_escape_string( $match[1] );
    } else {
      $value = self::$mysql->real_escape_string( $value );
    }
    return $value;
  }

  static function query( $sql ) {
    self::connect();
    $result = self::$mysql->query( $sql );
    if ( is_object( $result ) ) {
      return $result;
    } else {
      trigger_error( Database::error() . PHP_EOL . PHP_EOL . $sql );
      return null;
    }
  }

  static function error() {
    self::connect();
    return self::$mysql->error;
  }

 }