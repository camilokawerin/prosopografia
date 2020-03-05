<?php
/**
 * Products
 *
 */
namespace Candidacy;

class Party extends \Model\Model {

  static $table = 'party';
  static $primary_key = 'id';

  protected $id = [ 'Type\Number', [ 'label' => 'ID' ]  ];
  protected $name = [ 'Type\Text', [ 'label' => 'Name' ] ];
  protected $description = [ 'Type\Text', [ 'label' => 'Description' ] ];

}
