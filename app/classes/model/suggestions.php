<?php
/**
 * 
 */
class Suggestions extends Model {

  static $blacklisted = array( 'grs', 'litro', 'kilo');

  function __construct() {

    $sql = $this->search_sql( $params );
    return \Suggestions::result( $sql, $params['q'], 'array' );

  }

  static function result( $sql, $q, $type ) {
    $result = parent::query( $sql, 'array' );
    if ( is_array( $result ) && ! empty( $result  ) ) {
      return $type == 'array' ? self::get_suggestions( $result, $q ) : self::get_suggestions_object( $result );
    } else {
      return $result;
    }
  }

  private static function get_suggestions_object( $result ) {
    $result = self::get_suggestions( $result );
    return new Object_Result( $result );
  }

  private static function get_suggestions( $rows, $q ) {
    $cols = array_map( function ( $col ) {
      return strpos( $col, '__priority__' ) !== false ? $col : null;
    }, array_keys( current( $rows ) ) );
    $cols = array_filter( $cols, function ( $col ) {
      return ! is_null( $col);
    } );

    $matches = array();
    foreach ( $cols as $col ) {
      list( $name, $priority ) = explode( '__priority__', $col );
      foreach ( $rows as & $row ) {
        $id = isset( $row[ $name . '__id' ] ) ? $row[ $name . '__id' ] : '';
        if ( isset( $q ) ) {
          $words = self::get_words( $row[ $col ] );
          foreach ( $q as $_q) {
            foreach ( $words as $offset => $word ) {
              if ( mb_strpos( $word, $_q ) !== false ) {
                $length = 1;
                $move_offset = false;
                do {
                  $slice = implode( ' ', self::trim_words( array_slice( $words, $offset, $length ) ) );
                  if ( mb_strpos( $slice, $_q ) !== false ) {
                    $matches[] =  array(
                      'name' => $name,
                      'priority' => (int) $priority,
                      'text' => $slice,
                      'id' => $id
                    );
                  }
                  if ( $move_offset && $offset > 0 ) {
                    $offset--;
                  } else {
                    $length++;
                  }
                  $move_offset = ! $move_offset;
                } while ( $offset >= 0 && $length <= count( $words ) );
              }
            }
          }
        } else {
          $matches[] =  array(
                      'name' => $name,
                      'priority' => (int) $priority,
                      'text' => $row[ $col ],
                      'id' => $id
                    );
        }
      }
    }
    
    $suggestions_test = array();
    $suggestions = array();
    foreach ( $cols as $col ) {
      foreach ( $rows as $row ) {
        foreach ( $matches as $match ) {
          if ( mb_strpos( $row[ $col ], $match['text'] ) !== false ) {
            if ( ( $i = array_search( $match['text'], $suggestions_test ) ) !== false ) {
              $suggestion = & $suggestions[ $i ];
              $suggestion['name'] = $match['priority'] > $suggestion['priority'] ? $match['name'] : $suggestion['name'];
              $suggestion['priority'] = $match['priority'] > $suggestion['priority'] ? $match['priority'] : $suggestion['priority'];
              $suggestion['count'] += 1;
            } else {
              $suggestions_test[] = $match['text'];
              $suggestions[ count( $suggestions_test ) - 1 ] = array(
                'name' => $match['name'],
                'priority' => $match['priority'],
                'count' => 1,
                'text' => $match['text'],
                'id' => $match['id']
              );
            }
          }
        }      
      }
    }

    $suggestions = array_filter( $suggestions, function ( $suggestion ) {
      return $suggestion['count'] > count( explode( ' ', $suggestion['text'] ) );
    } );

    $suggestions_results = array();
    foreach ( $suggestions as $suggestion ) {
      if ( ! isset( $suggestions_results[ $suggestion['name'] ] ) ) {
        $suggestions_results[ $suggestion['name'] ] = array();
      }
      if ( $suggestion['id'] ) {
        $suggestions_results[ $suggestion['name'] ][ $suggestion['id'] ] = $suggestion;
      } else {
        $suggestions_results[ $suggestion['name'] ][] = $suggestion;
      }
    }

    foreach ( $suggestions_results as & $result ) {
      usort( $result, function ( $suggestion, $_suggestion ) {
        if ( $suggestion['count'] == $_suggestion['count'] ) {
            return 0;
        }
        return ( $suggestion['count'] < $_suggestion['count'] ) ? 1 : -1;
      } );

      $result = array_values( $result );
    }

    return $suggestions_results;
  }

  private static function trim_words( $words ) {
    do {
      $removed_last_word = false;
      $last_word = $words[ count( $words ) - 1 ];
      if ( ! preg_match( '/^[\pL]{2,}$/u', $last_word ) 
        || preg_match( '/^' . implode( '|', self::$blacklisted ) . '$/u', $last_word ) ) {
        $removed_last_word = array_pop( $words );
      }
      $removed_first_word = false;
      $first_word = $words[0];
      if ( ! preg_match( '/^[\pL]{2,}$/u', $first_word ) 
        || preg_match( '/^' . implode( '|', self::$blacklisted ) . '$/u', $first_word ) ) {
        $removed_first_word = array_shift( $words );
      }
    } while ( $removed_last_word || $removed_first_word );
    return $words;
  }

  private static function get_words( & $text ) {
    $text = mb_strtolower( trim( $text ) );
    $words = preg_split( '/[\s]+/', $text );
    return self::trim_words( $words );
  }

}