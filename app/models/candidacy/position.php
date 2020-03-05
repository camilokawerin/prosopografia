<?php
/**
 * Products
 *
 */
namespace Candidacy;

class Position extends \Model\Model {

  static $table = 'position';
  static $primary_key = 'id';

  protected $id = [ 'Type\Number', [ 'label' => 'ID' ]  ];
  protected $name = [ 'Type\Text', [ 'label' => 'Name' ] ];

}
