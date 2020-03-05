<?php
/*
 * Sanitize and parse HTTP request
 */

$request_uri = filter_var( $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL );
$query_string = filter_var( $_SERVER['QUERY_STRING'], FILTER_SANITIZE_URL );

if ( $request_uri != '' && $query_string != '' ) {
  $request_uri = str_replace( '?' . $query_string, '', $request_uri );
  $vars = explode( '&', $query_string );
  foreach ( $vars as $var ) {
    $_var = substr( $var, 0, strpos( $var, '=' ) );
    if ( preg_match( '/\/' . $_var . '=.+?\//', $request_uri, $match ) ) {
      $request_uri = str_replace( $match[0], '/' . $var . '/', $request_uri );
    } else {
      $request_uri .= $var . '/';
    }
  }
  $request_uri = str_replace('/q=/', '/', $request_uri );
  header( 'Location: ' . $request_uri );
  exit;
}