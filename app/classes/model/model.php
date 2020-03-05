<?php
/**
 * Base class for model interaction
 * Entities extends this class to implement specific interface
 */

namespace Model;

class Model {

  protected static $table;
  protected static $primary_key;

  function __construct( $row, $_references = [] ) {
    foreach ( get_class_vars( get_called_class() ) as $name => $property ) {
      if ( is_array( $property ) ) {
        $type = array_shift( $property );
        switch ( $type ) {
          case 'Model\Datalist':
          $_references[ get_called_class() ] =  & $this;
          $this->{ $name } = new $type( $row, $property, $_references );
          break;
          default:
            $this->{ $name } = new $type( $row->{ $name }, $property );
          break;
        }
      }
    }
  }

  public function add( $row, $_references = [] ) {
    foreach ( get_class_vars( get_called_class() ) as $name => $property ) {
      if ( is_array( $property ) ) {
        $type = array_shift( $property );
        if ( $type == 'Model\Datalist' ) {
          $_references[ get_called_class() ] =  & $this;
          $this->{ $name }->add( $row, $property, $_references );
        }
      }
    }
  }

  public static function primary_key() {
    return isset( static::$primary_key ) ? static::$primary_key : 'id';
  }

  public static function table() {
    return isset( static::$table ) ? static::$table : self::name();
  }

  public static function name() {
    $class = explode( '\\', get_called_class() );
    return strtolower( array_pop( $class ) );
  }

  public static function namespace() {
    $class = explode( '\\', get_called_class() );
    array_pop( $class );
    return implode( '\\', $class );
  }

  public static function attribute( $property, $name ) {
    if ( is_array( $property ) && isset( $property['attributes'] ) && is_array( $property['attributes'] ) ) {
      return ! isset( $property['attributes'][ $name ] ) ? true : $property['attributes'][ $name ]; 
    }
  }

  public static function properties( $_stack = [] ) {
    $_stack[ get_called_class() ] =  __METHOD__;
    //trigger_error( notice( $_stack ) );

    static $properties;
    if ( isset( $properties) ) {
      return $properties;
    }

    // New properties collection
    $properties = [];
    // Assign full database name
    foreach ( get_class_vars( get_called_class() ) as $name => $property ) {
      if ( is_array( $property ) ) {
        $type = array_shift( $property );
        switch ( $type ) {
          case 'Type\Number':
          case 'Type\Text':
          case 'Type\Set':
          case 'Type\Checkbox':
          case 'Type\Date':
            $properties[] = [
              'name' => $name,
              'type' => $type,
              'attributes' => current( $property )
            ];
          break;
        }
      }
    }
    // Add properties from referenced entities
    foreach ( self::references( $_stack ) as $reference_type ) {
      foreach ( $reference_type as $key => $reference ) {
        if ( ! in_array( $reference, array_keys( $_stack ) ) && count( $_stack ) < 16 ) {
          $_properties = $reference::properties( $_stack );
          foreach ( $_properties as $property ) {
            $properties[] = array_merge( [
              'reference' => array_merge( array_keys( $_stack ), [ $reference ] )
            ], $property );
          }
        }
      }
    }
    return $properties;
  }

  public static function references( $_stack = [] ) {
    $_stack[ get_called_class() ] = __METHOD__;
    //trigger_error( notice( $_stack ) );

    $inner = [];
    $outer = [];
    foreach ( get_class_vars( get_called_class() ) as $property ) {
      if ( is_array( $property ) && count( $property ) >= 2 ) {
        list( $type, $reference ) = array_values( $property );
        switch ( $type ) {
          case 'Model\Datalist':
            if ( is_array( $reference ) ) {
              list( $entity, $key ) = array_values( $reference );
              if ( $key != static::primary_key() ) {
                $inner[ $key ] = $entity;
              } else {
                $self = get_called_class();
                $key = array_keys( array_filter( $entity::references( $_stack )['inner'], function ( $entity ) use ( $self ) {
                  return $self == $entity;
                }) );
                $outer[ current( $key ) ] = $entity;
              }
            }
          break;
        }
      }
    }
    return [
      'inner' => $inner,
      'outer' => $outer
    ];
  }

}
