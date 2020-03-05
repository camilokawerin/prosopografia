<?php
/**
 * Candidacies
 *
 */
namespace Candidacy;

class Candidacy extends \Model\Model {

  protected $id = [ 'Type\Number', [ 'label' => 'ID' ]  ];
  protected $person = [ 'Model\Datalist', [ 'Person\Person', 'person_id' ], [ 'label' => 'Person' ] ];
  protected $position = [ 'Model\Datalist', [ 'Candidacy\Position', 'position_id' ], [ 'label' => 'Position' ] ];
  protected $scale = [ 'Model\Datalist', [ 'Candidacy\Scale', 'scale_id'],  [ 'label' => 'Scale' ] ];
  protected $election = [ 'Model\Datalist', [ 'Candidacy\Election', 'election_id' ], [ 'label' => 'Election' ] ];
  protected $party = [ 'Model\Datalist', [ 'Candidacy\Party', 'party_id'],  [ 'label' => 'Party' ] ];
  protected $district = [ 'Type\Text',  [ 'label' => 'District' ] ];
  protected $is_surrogate = [ 'Type\Checkbox', [ 'label' => 'Surrogate' ] ];
  protected $office = [ 'Model\Datalist', [ 'Candidacy\Office', 'id' ], [ 'label' => 'Office' ] ];

}
