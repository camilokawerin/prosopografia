<?php
/**
 * Text
 */

namespace Type;

class Type {

  protected $_content;
  protected $_attributes;

  function __construct( $content, $attributes ) {
    $this->_content = $content;
  }

  public function __toString() {
    return $this->_content;
  }

  public function edit() {
    return '<input value="' . $this->_content . '">';
  }
  
  static function column( $column, $attributes = [] ) {
    return $column;
  }


}