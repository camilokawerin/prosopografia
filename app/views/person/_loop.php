<?php
/**
 * 
 */

namespace Person;

class Loop extends \View\Loop {

  //protected $template = 'person-loop';
  
  function __construct( $iterator ) {
    parent::__construct( $iterator );
  }

  function render() {
    return parent::render();
  }
}