<?php
/**
 * Datalist
 */

namespace Model;

class Datalist extends Iterator {

  private $_entity;
  private $_attributes;
  
  function __construct( $row, $property, $_references ) {
    list( $entity ) = array_shift( $property );
    if ( ! in_array( $entity, array_keys( $_references ) ) ) {
      $this->_rows[] = new $entity( self::extract( $row, $entity ), $_references );
    } else {
      $this->_rows[] = $_references[ $entity ];
    }
    $this->_entity = $entity;
    $this->_attributes = $property;
  }

  public function add( $row, $property, $_references ) {
    list( $entity ) = array_shift( $property );
    $this->_rows[] = new $entity( self::extract( $row, $entity ), $_references );
  }

  private static function extract( $row, $entity ) {
    $_row = new \stdClass();
    foreach ( $row as $col => $value ) {
      $name = $entity::name() . '__';
      if ( strrpos( $col, $name ) === 0 ) {
        $_row->{ str_replace( $name, '', $col ) } = $value;
      }
    }
    return $_row;
  }

}