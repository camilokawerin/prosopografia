<?php
/**
 * Loop
 */

namespace Model;

class Loop extends Iterator {

  public $entity;
  public $params;
  
  function __construct( $entity, $params, $query ) {
    if ( is_object( $query ) ) {
      $_rows = [];
      while ( $row = $query->fetch_object() ) {
        if ( isset( $_rows[ $row->{ $entity::primary_key() } ] ) ) {
          $_rows[ $row->{ $entity::primary_key() } ]->add( $row );
        } else {
          $_rows[ $row->{ $entity::primary_key() } ] = new $entity( $row );
        }  
      }
      $this->entity = $entity;
      $this->params = $params;
      $this->_rows = array_values( $_rows );
    }
  }

  function __toString() {
    $name = $this->entity::namespace() . '\Loop';
    $class = class_exists( $name ) ? $name : 'View\Loop';
    $view = new $class( $this );
    return $view->render();
  }

}