<?php
/**
 * 
 */

namespace View;

class Loop extends View {

  protected $iterator;

  function __construct( $iterator ) {
    $this->iterator = $iterator;
  }
  
  function render() {
    $template = isset( $this->template ) ? $this->template : parent::_template( $this->iterator->entity );
    ob_start();
    parent::get_template( $template, [
      'iterator' => $this->iterator,
      'params' => $this->iterator->params,
      'entity' => $this->iterator->entity
    ] );
    $rendered = ob_get_contents();
    ob_end_clean();
    return $rendered;
  }
}