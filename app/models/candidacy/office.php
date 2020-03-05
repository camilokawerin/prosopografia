<?php
/**
 * Office
 *
 */
namespace Candidacy;

class Office extends \Model\Model {

  static $table = 'office';
  static $primary_key = 'id';

  protected $id = [ 'Type\Number', [ 'label' => 'ID' ]  ];
  protected $candidacy = [ 'Model\Datalist', [ 'Candidacy\Candidacy', 'candidacy_id' ], [ 'label' => 'Candidacy' ] ];
  protected $term_start = [ 'Type\Date', [ 'label' => 'Term start' ] ];
  protected $term_end = [ 'Type\Date', [ 'label' => 'Term end' ] ];
 
}
