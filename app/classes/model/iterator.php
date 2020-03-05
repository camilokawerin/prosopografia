<?php
/**
 * Iterator
 */
namespace Model;

class Iterator implements \Iterator {
  
  protected $position = 0;
  protected $_rows = [];

  public function fetch() {
    if ( $this->valid() ) {
      $_row = $this->current();
      $this->next();
      return $_row;
    }
  }

  public function count() {
    return count( $this->_rows );
  }

  public function rewind() {
    $this->position = 0;
  }

  public function current() {
    return $this->_rows[ $this->position ];
  }

  public function key() {
    return $this->position;
  }

  public function next() {
    $this->position++;
  }

  public function valid() {
    return isset( $this->_rows[ $this->position ] );
  }

}