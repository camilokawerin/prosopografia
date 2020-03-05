<?php
/**
 * Functions to be used whithin the views' template files
  */


/*
 * Includes template partial file 
 * Allows to use variables for the columns in a result's row
 * Returns void
 */
function get_template_part( $part_name, $data = [] ) {
  global $params;
  extract( $data );
  include( 'app/templates/partials/' . $part_name . '.php' );
}

function get_content( $paths, $default, $params = [], $debug_sql = false ) {
  if ( is_bool( $params ) ) {
    $debug_sql = $params;
  }
  if ( is_array( $default ) ) {
    $params = $default;
  }
  if ( is_string( $paths ) ) {
    $default = $paths;
  }
  $entity = is_array( $paths ) && ! empty( $paths ) ? get_entity( $paths ) : $default;
  echo Query::select( $entity, $params, $debug_sql );
}

/*
 * Determines if a result set has rows to iterate through 
 * Returns true/false
 */
function has_rows() {
  global $iterator;
  if ( is_null( $iterator ) ) {
    $iterator = func_get_arg( 0 );
  }
  return is_iterable( $iterator ) && $iterator->count();
}

/*
 * Fetchs a row from a result set 
 * Returns the row's object
 */
function fetch_row() {
  global $iterator;
  if ( is_null( $iterator ) ) {
    $iterator = func_get_arg( 0 );
  }
  if ( $row = $iterator->fetch() ) {
    return $row;
  }
  $result = null;
  return null;
}

/*
 * Formats an UNIX timestamp as an user friendly date
 * Returns a string
 */
function friendly_date( $time, $hours ) {
  $diff = time() - $time;
  if ( $diff <= 3600 * $hours ) {
    $hours = floor( $diff / 3600 );
    $minutes = floor( ( $diff - $hours * 3600 ) / 60 );
    return 'Hace ' . ( $hours > 0 ? $hours . ' horas y ' : '' ) . $minutes . ' minutos';
  } else {
    return date("d-m-Y h:i:s" , $time);
  }
}

/*
 * Formats a float as an user friendly price
 * Returns a string
 */
function price_format( $price ) {
  $price = number_format( $price, 2, ',', '.' );
  $price = preg_replace( '/(,)([\d]{2})/', '<span class="sr-only">$1</span><sup class="decimal">$2</sup>', $price );
  $price = '<span class="currency">$</span>' . $price;
  return $price;
}

/*
 * Determines if a file is an image and get some data from it
 * Returns an object
 * FIXME: needs work
 */
function fetch_image( $file ) {
  if ( is_file( $file ) ) {
    list( $width, $height ) = getimagesize( $file );
    return (object) array(
      'file' => $file,
      'width' => $width,
      'height' => $height
    );
  }
}

function inline_image( $file, $type ) {
  if ( is_file( $file ) ) {
    $svg = file_get_contents( $file );
    return 'data:image/' . $type . ';base64,' . base64_encode( $svg );
  }
}

function serialize_vars( $vars, $replace ) {
  list( $key, $val ) = explode( '=', $replace );
  $vars[ $key ] = $val;
  $string = '';
  foreach ( $vars as $key => $val ) {
    $string .= '/' . Localization::output( $key ) . '=' . htmlspecialchars( is_array( $val ) ? implode( ' ', $val ) : $val );
  }
  return $string;
}

function textify_vars( $vars, $options ) {
  $string = '';
  foreach ( $vars as $key => $val ) {
    if ( $key == 'q' ) {
      $string .= htmlspecialchars( implode( ' ', $val ) . ' ' );
    } elseif ( is_array( $options ) && is_array( $options[ $key ] ) && isset( $options[ $key ][ $val ] ) ) {
      $string .= htmlspecialchars( '#' . $options[ $key ][ $val ] . ' ' );
    }
  }
  return $string;
}

function stringify_vars( $vars ) {
  $_vars = array();
  foreach ( $vars as $key => & $value ) {
    if ( $key == 'q' ) {
      $_vars['q'] = implode( ' ', $value );
    } else {
      $_vars = replace_key( $vars, $key, Localization::output( $key ) );
    }
  }
  return htmlspecialchars( json_encode( $_vars ) );
}

function stringify_var( $key, $value ) {
  return htmlspecialchars( json_encode( array( Localization::output( $key ) => $value ) ) );
}

function inline_q_var( $vars ) {
  return isset( $vars['q'] ) ? htmlspecialchars( implode( ' ', $vars['q'] ) ) : '';
}

function get_link( $format, $val ) {
  return htmlspecialchars( html_entity_decode( sprintf( $format, $val ) ) );
}

function path( $base ) {
  global $request_uri;
  if ( strpos( $request_uri, $base ) !== false ) {
    return $request_uri;
  } else {
    return $base;
  }
}

function __( $var ) {
  return Localization::output( $var );
}