<?php
/**
 * Expressions
 */

namespace Model;

class Expression {

  public static function expression( $property, $properties ) {
    if ( is_array( $property ) ) {
      $expression = array_shift( $property );
      $expression = vsprintf( '( ' . $expression . ' )' , array_map( function( $name ) use ( $properties ) {
        return $properties[ $name ];
      }, $property ) );
    }
    return $expression;
  }
}