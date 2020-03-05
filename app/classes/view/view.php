<?php
/**
 * 
 */

namespace View;

class View {

  public static function get_template( $template, $data ) {
    global $iterator, $params, $entity;
    extract( $data );
    
    include( 'app/templates/views/' . $template . '.php' );
  }

  public static function _template( $entity ) {
    $namespace = strtolower( $entity::namespace() );
    $view = self::_name();
    
    if ( file_exists( 'app/templates/views/' . $namespace . '-' . $view . '.php' ) ) {
      return $namespace . '-' . $view;
    }
  }

  public static function _name() {
    $class = explode( '\\', get_called_class() );
    return strtolower( array_pop( $class ) );
  }

}