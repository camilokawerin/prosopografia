<?php
/**
 * Products
 *
 */
namespace Candidacy;

class Election extends \Model\Model {

  static $table = 'election';
  static $primary_key = 'id';

  protected $id = [ 'Type\Number', [ 'label' => 'ID' ]  ];
  protected $date = [ 'Type\Date', [ 'label' => 'Date']  ];
  protected $description = [ 'Type\Text', [ 'label' => 'Description'] ];
  protected $candidacy = [ 'Model\Datalist', [ 'Candidacy\Candidacy', 'id' ], [ 'label' => 'Candidacy' ] ];
 
}
