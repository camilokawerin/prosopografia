<?php
/**
 * Base functions for input and response routing
 *
 */

function get_params( $request_uri ) {
  $params = [];

  foreach ( explode( '/', $request_uri ) as $param ) {
    if ( strpos( $param, '=' ) ) {
      list( $key, $value ) = explode( '=', $param );
      $params[ Localized::input( $key ) ] = split_param_value( $value );
    }
  }

  return $params;
}

function split_param_value( $value ) {
  if ( preg_match_all( '/"(?:\\\\.|[^\\\\"])*"|\S+/', urldecode( $value ), $matches ) ) {
    return current( $matches );
  }
  return explode( ':', $value );
}

function get_paths( $request_uri ) {
  if ( preg_match_all('/\/([A-Za-z_]+)/', $request_uri, $matches) ) {
    return count( $matches ) == 2 ? $matches[1] : [];
  }
  return [];
}

function get_entity( $paths ) {
  $namespace = '';
  $entity = '';
  $_paths = '/';
  if ( is_array( $paths ) && ! empty( $paths ) ) {
    foreach ( $paths as $path ) { 
      if ( file_exists( 'app/models' . $_paths . $path ) ) {
        $_paths .= $path . '/';
        $namespace .= $path . '\\';
      }
      if ( class_exists( $namespace . $path ) ) {
        $entity = $namespace . $path;
      }
    }
    return $entity;
  }
}

function get_template( $_paths, $_params ) {
  $template = '';
  $_template = '';

  global $paths, $params;

  $paths = $_paths;
  $params = $_params;

  while ( $__template = current( $paths ) ) {
    next( $paths );
    if ( file_exists( 'app/templates/' . $_template . ( $_template != '' ? '-' : '' ) . $__template . '.php' ) ) {
      $template .= ( $template != '' ? '-' : '' ) . $__template;
    }
    $_template .= ( $_template != '' ? '-' : '' ) . $__template;
  }
  if ( $template != '') {
    include( 'app/templates/' . $template . '.php' );
  } else {
    include( 'app/templates/index.php' );
  }
}

/*
 * Includes template file for current view
 * Allows to use $query_vars in templates files
 * Returns void
 */
function document( $request_uri ) {

  define( 'DEBUG_SQL', ALLOW_DEBUG ? Query::DEBUG_SQL : 0 );

  $paths = get_paths( $request_uri );
  $params = get_params( $request_uri );

  ob_start();
  get_template( $paths, $params );

  $output = ob_get_contents();
  ob_end_clean();

  if ( DISPLAY_ERRORS && Errors::count() ) {
    echo preg_replace( '/<' . DISPLAY_ERRORS_TAG . '(.*?)>/', '<' . DISPLAY_ERRORS_TAG . '$1>' . PHP_EOL . Errors::show(), $output );
  } else {
    echo $output;
  }
  exit;
}

/*
 * Generate JSON response for a given API request
 * Returns void
 */
function response( $request_uri ) {

  $params = get_params( $request_uri );
  $paths = get_paths( $request_uri );

  $entity = get_entity( $paths );

  ob_start();
  if ( ! isset( $entity ) || ! isset( $method ) ) {
    trigger_error( ' Entity or method doesn\'t exist.' );
  } else {
    $result = \Model\Query::{ $method }( $entity, $params );

    if ( is_associative( $result ) ) {
      $output = array();
      foreach ( $result as $key => $rows ) {
        $output[ $key ] = $rows;
      }
    } elseif ( is_array( $result ) ) {
      $output = array(
        'result' => $result
      );
    }
  }

  if ( DISPLAY_API_OUTPUT ) {
    foreach ( Errors::get() as $message ) {
      echo "<pre>" . $message[0] . "\n\n<small>\t" . $message[1] . "</small>\n\n</pre>" ;  
    }
    echo "<pre>" . print_r( $output, true ) . "</pre>" ;
    exit();
  } elseif ( DEBUG ) {
      $output['errors'] = Errors::get();
  }
  
  ob_end_clean();
  header( 'Access-Control-Allow-Origin: *' );
  header( 'Content-type: application/json; charset=utf-8' );
  $json = json_encode( $output );
  if ( $error = json_last_error() ) {
    switch ( $error ) {
          case JSON_ERROR_DEPTH:
              trigger_error( 'Maximum stack depth exceeded' );
          break;
          case JSON_ERROR_STATE_MISMATCH:
              trigger_error( 'Underflow or the modes mismatch' );
          break;
          case JSON_ERROR_CTRL_CHAR:
              trigger_error( 'Unexpected control character found' );
          break;
          case JSON_ERROR_SYNTAX:
              trigger_error( 'Syntax error, malformed JSON' );
          break;
          case JSON_ERROR_UTF8:
              trigger_error( 'Malformed UTF-8 characters, possibly incorrectly encoded' );
          break;
          default:
              trigger_error( 'Unknown error' );
          break;
      }
      $json = json_encode( array( 'errors' => Error::get() ) );
  }
  echo $json;
  exit;
}
