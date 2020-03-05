<?php
/**
 * Query
 */

class Query {

  const DEBUG_SQL = 1;
  const ROWS_PER_PAGE = 10;
  const COLUMNS_NAME_AND_ALIAS = 1;
  const COLUMNS_FULL_NAME = 2;
  const COLUMNS_NAME_OR_ALIAS = 3;

  protected static $table_aliases = [];

  public static function select( $entity, $params, $debug_sql = false ) {
    $columns = self::columns( $entity );
    $from = self::from( $entity );
    $where = self::where( $entity, $params );
    $group_by = self::group_by( $entity );
    $order_by = self::order_by( $entity, $params );
    $limit = self::limit( $entity, $params );

    if ( isset( $params['q'] ) && ! empty( $params['q'] ) ) {
      $primary_key = self::column_name( $entity::primary_key() );

      // Filter properties that can be use to search
      $properties = array_filter( $entity::properties(), function( $property ) {
        switch ( $property['type'] ) {
          case 'Type\Text':
            return true;
          break;
        }
      } );

      // Add special columns for search results
      $_columns = [];
      $priority = count( $properties );
      foreach ( $properties as $property ) {
        $_columns[] = sprintf( '%s AS `%s__priority__%d`', self::_column( $entity, $property, self::COLUMNS_FULL_NAME ), self::_column( $entity, $property, self::COLUMNS_NAME_OR_ALIAS ), $priority );
        $priority--;
      }
      $columns .= ', ' . implode( ',', array_values( $_columns ) );

      $sql  = '';
      foreach ( $params['q'] as $n => $_q ) {
        Database::real_escape_string( $_q );
        if ( $sql == '' ) {
          $sql  = 'SELECT *, COUNT(*) AS `total`, `priority` + `occurrences` * COUNT(*) * 2 AS `relevance` FROM ( ' .
            '( ' . PHP_EOL;
        } else {
          $sql .= ') UNION (' . PHP_EOL;
        }
        $_sql = '';
        $priority = count( $properties );
        foreach ( $properties as $property ) {
          $column = self::_column( $entity, $property, self::COLUMNS_FULL_NAME );
          if ( $_q != '' ) {
            $_priority = 1;
            foreach ( [ $_q . '%', '%'. $_q . '%' ] as $_n => $__q ) {
              if ( $_sql == '' ) {
                $_sql = 'SELECT *, COUNT(*) AS `occurrences` FROM ( ' .
                  '( ' . PHP_EOL;
              } else {
                $_sql .= ') UNION (' . PHP_EOL;
              }
              $_sql .= sprintf( 'SELECT %2$s, %1$d AS `priority`'. PHP_EOL . 'FROM %3$s '. PHP_EOL . 'WHERE %4$s ' . PHP_EOL . 'AND %5$s LIKE \'%6$s\'' . PHP_EOL, 
                ( $priority + $_priority ), 
                $columns, 
                $from, 
                $where,
                $column, 
                $__q );
              $_priority--;
            }
          }
          $priority--;
        }
        $_sql .= sprintf( ') ' . PHP_EOL .
          'ORDER BY `priority` DESC' . PHP_EOL .
          ') AS `set_%d`' . PHP_EOL .
          'GROUP BY %s ', $n, $primary_key );
        $sql .= $_sql . PHP_EOL;
      }
      $sql .= sprintf( ') ORDER BY `priority` DESC' . PHP_EOL .
        ') AS `p` ' . PHP_EOL .
        'GROUP BY %s ' . PHP_EOL .
        'ORDER BY %s ' . PHP_EOL .
        'LIMIT %s ', $primary_key, $order_by, $limit );
    } else {
      $sql = sprintf( 'SELECT %1$s ' . PHP_EOL . 'FROM %2$s ' . PHP_EOL . 'WHERE %3$s ' . PHP_EOL . 'GROUP BY %4$s ' . PHP_EOL . 'ORDER BY %5$s ' . PHP_EOL . 'LIMIT %6$s ' . PHP_EOL,
        $columns,
        $from,
        $where,
        $group_by,
        $order_by,
        $limit
      );
    }

    $sql = self::clean( $sql );
  
    $query = Database::query( $sql );
    if ( $debug_sql == self::DEBUG_SQL ) {
      trigger_error( $sql . PHP_EOL . notice( $query ) );
    }

    if ( isset( $params['output'] ) && class_exists( $params['output'] ) ) {
      return new $params['output']( $entity, $params, $query );
    }

    return new Model\Loop( $entity, $params, $query );
  }

  private static function columns( $entity, $output = self::COLUMNS_NAME_AND_ALIAS ) {
    $columns = [];
    foreach ( $entity::properties() as $property ) {
      if ( is_array( $property ) && isset( $property['type'] ) ) {
        $columns[] = self::_column( $entity, $property, $output );
      }
    }
    return implode( ', ', $columns );
  }

  private static function _column( $entity, $property, $output ) {
    if ( isset( $property['reference'] ) ) {
      list( $entity, $alias ) = self::_reference( $entity, $property ); 
      $column = $property['type']::column( self::column_full_name( $entity::table(), $property['name'] ), $property['attributes'] );
      $alias = $alias . $property['name'];
      switch ( $output ) {
        case self::COLUMNS_NAME_AND_ALIAS:
          return self::column_name_alias( $column, $alias );
        break;
        case self::COLUMNS_FULL_NAME:
          return $column;
        break;
        case self::COLUMNS_NAME_OR_ALIAS:
          return $alias;
        break;
      }
    } else {
      switch ( $output ) {
        case self::COLUMNS_NAME_AND_ALIAS:
        case self::COLUMNS_FULL_NAME:
          return $property['type']::column( self::column_full_name( $entity::table(), $property['name'] ), $property['attributes'] );
        break;
        case self::COLUMNS_NAME_OR_ALIAS:
          return $property['type']::column( $property['name'], $property['attributes'] );
        break;
      }
    }
  }

  private static function _reference( $_entity, $property ) {
    $alias = '';
    $entity = null;
    if ( is_array( $property['reference'] ) ) {
      while ( $reference = array_pop( $property['reference'] ) ) {
        if ( is_null( $entity ) ) {
          $entity = $reference;
        }
        $name = $reference::name();
        if ( $name != $_entity::name() ) {
          $alias = $name . '__' . $alias;
        }
      }
    }
    return [
      $entity,
      $alias
    ];
  }

  private static function column_name( $name ) {
    return '`' . $name . '`';
  }

  private static function column_name_alias( $column, $alias ) {
    return $column . ' AS `' . $alias . '`';
  }

  private static function column_full_name( $table, $column ) {
    return '`' . self::table_alias( $table ) . '`.`' . $column . '`';
  }

  private static function table_alias( $table ) {
    if ( ! isset( self::$table_aliases[ $table ] ) ) {
      self::$table_aliases[ $table ] = chr( count( self::$table_aliases ) + 65 );
    }
    return self::$table_aliases[ $table ];
  }

  private static function table_name_alias( $table ) {
    return '`' . $table . '` AS `' . self::table_alias( $table ) . '`';
  }

  private static function from( $entity ) {
    $from  = '`' . $entity::table() . '` AS `' . self::table_alias( $entity::table() ) . '`';

    $_stack = [ $entity => __METHOD__ ];
    $from .= self::_from( $entity, $_stack );
    return $from;
  }

  private static function _from( $entity, $_stack ) {
    $references = $entity::references( $_stack );
    $from = '';
    foreach ( $references['inner'] as $key => $referenced ) {
      if ( ! in_array( $referenced, array_keys( $_stack ) ) ) {
        $from .= PHP_EOL . 'INNER JOIN ' . self::table_name_alias( $referenced::table() ) . 
          ' ON ' . self::column_full_name( $entity::table(), $key ) . ' = ' . self::column_full_name( $referenced::table(), $referenced::primary_key() );
        if ( count( $_stack ) < 16 ) {
          $_stack[ $referenced ] = __METHOD__;
          $from .= self::_from( $referenced, $_stack );
        }
      }
    }
    foreach ( $references['outer'] as $outer_key => $referencer ) {
      if ( ! in_array( $referencer, array_keys( $_stack ) ) )  {
        $from .= PHP_EOL . 'LEFT JOIN ' . self::table_name_alias( $referencer::table() ) .
          ' ON ' . self::column_full_name( $entity::table(), $entity::primary_key() ) . ' = ' . self::column_full_name( $referencer::table(), $outer_key );
        if ( count( $_stack ) < 16 ) {
          $_stack[ $referencer ] = __METHOD__;
          $from .= self::_from( $referencer, $_stack );
        }
      }
    }
    //trigger_error( notice( $_stack, $from ) );
    return $from;
  }

  private static function where( $entity, $params ) {
    if ( ! empty( $params ) ) {
      $comparisons = [
        'et' => ' = ',
        'gt' => ' > ',
        'lt' => ' < ',
        'net' => ' <> ',
        'gte' => ' >= ',
        'lte' => ' <= '
      ];
      $operators = [
        'or' => ' OR ',
        'and' => ' AND '
      ];
      $where = [];
      //trigger_error( notice( $entity::properties() ) );
      foreach ( $params as $name => $rule ) {

        // the param:
        // must have a property
        $property = null;
        // may have a reference to another entity
        $reference = null;
        // may have an operator
        $operator = null;

        // does the param's name reference another entity?
        if ( strpos( $name, ':' ) !== false ) {
          list( $reference, $name ) = explode( ':', $name );
        }
        // iterate properties to find which one the param is based
        foreach ( $entity::properties() as $_property ) {
          if ( isset( $_property['reference'] ) ) {
            $__reference = array_pop( $_property['reference'] );
             if ( isset( $reference ) && $reference == $__reference::name() && $name == $_property['name'] ) {
              // is a property from a referenced entity
              $reference = $__reference;
              $property = $_property;
             }
          } elseif ( $name == $_property['name'] ) {
            // is a property from the property itself
            $property = $_property;
          }
        }

        if ( ! is_null( $property ) ) {
          // we found a property for the param
          $table = isset( $reference ) ? $reference::table() : $entity::table();
          extract( $property );
          if ( is_array( $rule ) ) {
            if ( count( $rule ) > 1 ) {
              // the rule has two or more operands
              // 'name' => [ X, Y, ... ]
              // /name=X/name=Y
              $operands = [];
              $operator = 'or';
              foreach ( $rule as $value ) {
                $comparison = 'et';
                // if ( ! is_array( $value ) )
                  // the operand is part of a disjunction of two or more 'equal to' comparisons
                  // 'name' => [ 'value1', 'value2', ... ]
                  // /name=value1/name=value2
                  // comparison type and operator are implied
                if ( is_array( $value ) ) {
                  // the operand doesn't imply comparison type and/or operator 
                  // 'name' => [
                  //  [ x1, x2 ],
                  //  [ y1, y2, y3 ]
                  // ]
                  // /name=x1:x2/name=y1:y2:y3
                  if ( count( $value ) == 3 ) {
                    // the operand explicites comparison type and operator
                    // [ 'and', 'gte', 'value' ]
                    // /name=and:gte:value
                    list( $operator, $comparison, $value ) = $value;
                  } elseif ( count( $value ) == 2 ) {
                    // the operand explicites comparison type or operator
                    // [ 'and', 'value' ] || [ 'gte', 'value' ]
                    // /name=and:value || /name=gte:value
                    list( $element, $value ) = $value;
                    if ( in_array( $element, array_keys( $operators ) ) ) {
                      $operator = $element;
                    } elseif ( in_array( $element, array_keys( $comparisons ) ) ) {
                      $comparison = $element;
                    }
                  } else {
                    // remaining  operand without any explicit
                    // inherits from the other operands
                    list( $value ) = $value;
                  }
                }
                $operands[] = self::_column( $entity, $property, self::COLUMNS_NAME_OR_ALIAS ) . $comparisons[ $comparison ] . '\'' . Database::real_escape_string( $value ) . '\'';
              }
              $where[] = count( $params ) > 1 ? '( ' . implode( $operators[ $operator ], $operands ) . ' )' : implode( '', $operands );
            } else {
              // the rule is a single 'equal to' comparison
              // 'name' => [ 'value' ]
              // /name=value
              // comparison type is implied
              $value = current( $rule );
              $where[] = self::_column( $entity, $property, self::COLUMNS_NAME_OR_ALIAS ) . ' = \'' . Database::real_escape_string( $value ) . '\'';
            }
          } else {
            trigger_error( 'Parameters must be set as an array. "' . $rule . '" given for "' . $name . '"' );
          }
        }
      }
      return implode( PHP_EOL . ' AND ', $where );
    }
    return '';
  }

  private static function group_by( $entity ) {
    // filter by property is_unique
    $group_by = [];
    foreach ( $entity::properties() as $property ) {
      if ( isset( $property['attributes']['is_unique'] ) && $property['attributes']['is_unique'] == true ) {
        extract( $property );
        $group_by[] = self::_column( $entity, $property, self::COLUMNS_NAME_OR_ALIAS );
      }
    }
    return implode( ', ', $group_by );
  }

  private static function order_by( $entity, $params ) {
    if ( isset( $params['sort'] ) ) {
      $orders = [
        'asc' => ' ASC',
        'desc' => 'DESC'
      ];
      $order_by = [];
      foreach ( $params['sort'] as $param ) {
        foreach ( $entity::properties() as $property ) {
          if ( ! is_array( $param ) ) {
            trigger_error( 'Parameters must be set as an array. "' . $param . '" given for "sort".' );
          } elseif ( count( $param ) < 2 ) {
            trigger_error( 'Sorting must be set as an array of two elements where the first element is the property and the second is the order. "' . $param . '" given.' );
          }
          list( $name, $order ) = $param;
          if ( ! in_array( $order, $orders ) ) {
            $order = 'asc';
          }
          if ( $property['name'] == $name && ! isset( $property['reference'] ) ) {
            extract( $property );
            $order_by[] = self::column_name( self::_column( $entity, $property, self::COLUMNS_NAME_OR_ALIAS ) ) . $orders[ $order ];
          }
        }
      }
      return implode( ', ', $order_by );
    }
    return '';
  }

  private static function order_search_by( $params ) {
    $properties = isset( $params['order_by'] ) ? array_keys( Model::filter( self::properties(), array_keys( $params['order_by'] ) ) ) : [ 'relevance' ];
    foreach ( $properties as $name ) {
      $order_by[] = '`' . $name . '` ' . ( isset( $params['order_by'][ $name ]) ? $params['order_by'][ $name ] : 'ASC' );
    }
    return implode( ', ', $order_by );
  }

  private static function limit( $params ) {
    $page = 1;
    $limit = self::ROWS_PER_PAGE;
    if ( isset( $params['page'] ) ) {
      if ( ! is_array( $params['page'] ) ) {
        trigger_error( 'Parameters must be set as an array. "' . $params['page'] . '" given for "page".' );
      }
      if ( count( $params['page'] ) == 2 ) {
        list( $page, $limit ) = $params['page'];
      } else {
        list( $page ) = $params['page'];
      }
    }
    $offset = ( $page - 1 ) * $limit;
    return $offset . ', ' . $limit;
  }

  private static function clean( $sql ) {
    $sql = preg_replace( '/WHERE\s\s\s\sAND/', 'WHERE', $sql );
    $sql = preg_replace( '/(SELECT\s\s|FROM\s\s|WHERE\s\s|GROUP BY\s\s|ORDER BY\s\s|LIMIT\s\s)/', '', $sql );
    while ( strpos( $sql, PHP_EOL . PHP_EOL ) !== false ) {
      $sql = str_replace( PHP_EOL . PHP_EOL, PHP_EOL, $sql );
    }
    return $sql;
  }

}