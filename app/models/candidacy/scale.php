<?php
/**
 * Products
 *
 */
namespace Candidacy;

class Scale extends \Model\Model {

  static $table = 'scale';
  static $primary_key = 'id';

  protected $id = [ 'Type\Number', [ 'label' => 'ID' ]  ];
  protected $name = [ 'Type\Text', [ 'label' => 'Name' ] ];

}
