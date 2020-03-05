<?php
/**
 * Model template
 *
 */
namespace Sample;

class Sample extends \Model\Model {

  // optional: defaults to class name
  static $table = 'table';
  // optional: defaults to 'id'
  static $primary_key = 'key';

  // at least one property must be set
  // available plain data types are:
  // 'Type\Text'
  // 'Type\Checkbox'
  // available multiple data types are
  // 'Model\Datalist'
  // 'Model\Datalist'
  protected $property = [ 'Type\Type', [ 'label' => 'Human name' ] ];
  protected $property = [ 'Model\Datalist', [ 'Sample\Sample', 'id' ], [ 'label' => 'Human name' ] ];
  protected $property = [ 'Model\Datalist', [ 'Sample\Sample', 'property_id' ], [ 'label' => 'Human name' ] ];
  
  // optional
  const ROWS_PER_PAGE = 20;

}
